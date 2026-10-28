<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthSecurityAndRememberMeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Test 1: Verifikasi bahwa login dengan remember me menerbitkan cookie remember_web terenkripsi.
     */
    public function test_login_with_remember_me_issues_encrypted_cookie(): void
    {
        $response = $this->post('/login', [
            'login' => 'TGL1.2',
            'password' => 'password',
            'remember' => '1',
        ]);

        $response->assertRedirect(route('driver.dashboard'));
        $this->assertAuthenticated();

        // Verifikasi bahwa cookie recaller (remember_web_*) diterbitkan di response
        $recallerName = Auth::getRecallerName();
        $response->assertCookie($recallerName);

        // Verifikasi flag keamanan cookie (HttpOnly dan SameSite)
        $cookie = $response->getCookie($recallerName);
        $this->assertNotNull($cookie);
        $this->assertTrue($cookie->isHttpOnly(), 'Cookie remember wajib HttpOnly untuk mencegah pencurian token via script XSS');
        $this->assertSame('lax', strtolower((string) $cookie->getSameSite()), 'Cookie remember wajib SameSite=Lax untuk mitigasi CSRF');
    }

    /**
     * Test 2: Verifikasi auto-login sopir via remember cookie tanpa memasukkan sandi lagi (simulasi tutup browser).
     */
    public function test_auto_login_via_remember_cookie_without_password_for_driver(): void
    {
        // 1. Login pertama kali dengan remember me aktif
        $loginResponse = $this->post('/login', [
            'login' => 'TGL1.2',
            'password' => 'password',
            'remember' => '1',
        ]);

        $driverUser = User::where('username', 'TGL1.2')->firstOrFail();
        $recallerName = Auth::getRecallerName();
        $cookie = $loginResponse->getCookie($recallerName);
        $this->assertNotNull($cookie, 'Cookie remember_web_* harus ada');

        // 2. Simulasi browser ditutup: Hapus session aktif dan flush Auth guard (tanpa logout)
        Auth::forgetGuards();
        $this->flushSession();

        // 3. Masuk kembali hanya dengan membawa cookie remember (tanpa submit password)
        $visitResponse = $this->withCookie($recallerName, $cookie->getValue())
            ->get(route('driver.dashboard'));

        $visitResponse->assertOk();
        $visitResponse->assertSee('TGL1.2');
        $this->assertAuthenticatedAs($driverUser);
    }

    /**
     * Test 3: Verifikasi auto-login kasir/admin via remember cookie tanpa memasukkan sandi lagi.
     */
    public function test_auto_login_via_remember_cookie_without_password_for_cashier(): void
    {
        $loginResponse = $this->post('/login', [
            'login' => 'kasir',
            'password' => 'password',
            'remember' => '1',
        ]);

        $loginResponse->assertRedirect('/admin');
        $cashierUser = User::where('username', 'kasir')->firstOrFail();
        $recallerName = Auth::getRecallerName();
        $cookie = $loginResponse->getCookie($recallerName);
        $this->assertNotNull($cookie);

        // Simulasi tutup browser: flush session dan guard
        Auth::forgetGuards();
        $this->flushSession();

        // Kunjungi kembali root '/' dengan cookie remember -> harus otomatis auto-login dan redirect ke /admin
        $visitRootResponse = $this->withCookie($recallerName, $cookie->getValue())
            ->get('/');

        $visitRootResponse->assertRedirect('/admin');
        $this->assertAuthenticatedAs($cashierUser);
    }

    /**
     * Test 4: Verifikasi sopir yang login via cookie dan membuka /admin/login dialihkan mulus ke portal sopir (mencegah 403).
     */
    public function test_authenticated_driver_visiting_admin_login_is_redirected_to_driver_dashboard(): void
    {
        $driverUser = User::where('username', 'TGL1.2')->firstOrFail();

        // Login sebagai driver
        $this->actingAs($driverUser);

        // Kunjungi /admin/login (Livewire Filament Login page)
        $response = $this->get('/admin/login');

        // Harus dialihkan ke dashboard sopir, bukan error 403
        $response->assertRedirect(route('driver.dashboard'));
    }

    /**
     * Test 5: Perlindungan terhadap Session Fixation Attack (Session ID harus di-regenerate saat login).
     */
    public function test_session_fixation_protection(): void
    {
        // Buat session awal
        $this->get('/login');
        $initialSessionId = session()->getId();

        // Lakukan login
        $this->post('/login', [
            'login' => 'TGL1.2',
            'password' => 'password',
        ]);

        $newSessionId = session()->getId();

        // Session ID setelah login harus berbeda dari session ID sebelum login
        $this->assertNotEmpty($newSessionId);
        $this->assertNotEquals($initialSessionId, $newSessionId, 'Session ID wajib di-regenerate untuk mencegah Session Fixation');
    }

    /**
     * Test 6: Verifikasi rotasi remember_token saat Logout untuk mencegah Replay Attacks.
     */
    public function test_remember_token_revocation_on_logout(): void
    {
        // Login dengan remember me
        $loginResponse = $this->post('/login', [
            'login' => 'TGL1.2',
            'password' => 'password',
            'remember' => '1',
        ]);

        $driverUser = User::where('username', 'TGL1.2')->firstOrFail();
        $initialToken = $driverUser->remember_token;
        $this->assertNotEmpty($initialToken);

        $recallerName = Auth::getRecallerName();
        $cookie = $loginResponse->getCookie($recallerName);

        // Logout
        $logoutResponse = $this->post(route('driver.logout'));
        $logoutResponse->assertRedirect(route('driver.login'));
        $this->assertGuest();

        // Refresh user dari database: token harus sudah dirotasi / berubah
        $driverUser->refresh();
        $this->assertNotEquals($initialToken, $driverUser->remember_token, 'Token remember wajib dirotasi saat logout');

        // Coba gunakan cookie lama untuk mengakses halaman dashboard
        $this->flushSession();
        $revisitResponse = $this->withCookie($recallerName, $cookie->getValue())
            ->get(route('driver.dashboard'));

        // Harus ditolak dan dialihkan ke login karena token lama sudah hangus
        $this->assertGuest();
        $revisitResponse->assertRedirect(route('driver.login'));
    }

    /**
     * Test 7: Verifikasi pencabutan akses instan jika akun dinonaktifkan (Inactive Account Bypass Prevention).
     */
    public function test_disabled_user_cannot_access_via_remember_cookie(): void
    {
        // 1. Login dengan remember me
        $loginResponse = $this->post('/login', [
            'login' => 'TGL1.2',
            'password' => 'password',
            'remember' => '1',
        ]);

        $recallerName = Auth::getRecallerName();
        $cookie = $loginResponse->getCookie($recallerName);

        // 2. Admin menonaktifkan akun driver di database
        $driverUser = User::where('username', 'TGL1.2')->firstOrFail();
        $driverUser->update(['is_active' => false]);

        // 3. Browser ditutup, user kembali dengan membawa remember cookie
        Auth::forgetGuards();
        $this->flushSession();

        $visitResponse = $this->withCookie($recallerName, $cookie->getValue())
            ->get(route('driver.dashboard'));

        // Harus ditolak dan ditendang keluar
        $this->assertGuest();
        $visitResponse->assertRedirect(route('driver.login'));
    }

    /**
     * Test 8: Verifikasi batasan hak akses (Role Boundary) sopir tidak bisa mengakses panel Filament Admin.
     */
    public function test_driver_cannot_access_filament_admin_panel(): void
    {
        $driverUser = User::where('username', 'TGL1.2')->firstOrFail();
        $this->actingAs($driverUser);

        // Kunjungi URL panel admin
        $response = $this->get('/admin');

        // Driver tidak memiliki canAccessPanel(), harus menghasilkan status 403 Forbidden
        $response->assertForbidden();
    }
}
