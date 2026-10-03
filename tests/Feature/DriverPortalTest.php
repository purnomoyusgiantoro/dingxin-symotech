<?php

namespace Tests\Feature;

use App\Models\CashDeposit;
use App\Models\CreditDelivery;
use App\Models\DailyDelivery;
use App\Models\Driver;
use App\Models\ReturnItem;
use App\Models\TransferPayment;
use App\Models\User;
use App\Services\SettlementCalculationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DriverPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $driverUser;
    protected Driver $driver;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Buat user driver
        $this->driverUser = User::create([
            'name' => 'Sopir Tegal 1',
            'username' => 'sopir_tegal',
            'email' => 'driver1@dingxin.com',
            'password' => Hash::make('password123'),
            'role' => 'driver',
            'is_active' => true,
        ]);

        $this->driver = Driver::create([
            'user_id' => $this->driverUser->id,
            'driver_code' => 'TGL1.2',
            'area_code' => 'TGL1',
            'phone_number' => '081234567890',
            'plate_number' => 'G 1234 DX',
            'is_active' => true,
            'cumulative_balance' => 0,
        ]);

        // Buat user admin
        $this->adminUser = User::create([
            'name' => 'Sales Admin Tegal',
            'username' => 'admin_tegal',
            'email' => 'admin@dingxin.com',
            'password' => Hash::make('password123'),
            'role' => 'sales_admin',
            'is_active' => true,
        ]);
    }

    public function test_driver_can_login_using_driver_code()
    {
        $response = $this->post('/driver/login', [
            'login' => 'TGL1.2',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/driver');
        $this->assertAuthenticatedAs($this->driverUser);
    }

    public function test_driver_can_login_using_username()
    {
        $response = $this->post('/driver/login', [
            'login' => 'sopir_tegal',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/driver');
        $this->assertAuthenticatedAs($this->driverUser);
    }

    public function test_admin_login_redirects_to_admin()
    {
        $response = $this->post('/driver/login', [
            'login' => 'admin_tegal',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->adminUser);
    }

    public function test_non_driver_cannot_access_driver_portal()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/driver');
        $response->assertRedirect('/admin');
    }

    public function test_driver_dashboard_renders_with_settlement_calculation()
    {
        $today = Carbon::today()->toDateString();

        // 1. Barang Dibawa = 10.000.000
        DailyDelivery::create([
            'driver_id' => $this->driver->id,
            'date' => $today,
            'amount' => 10000000,
            'route_notes' => 'Rute Slawi - Adiwerna',
        ]);

        // 2. Retur = 1.000.000
        ReturnItem::create([
            'driver_id' => $this->driver->id,
            'date' => $today,
            'amount' => 1000000,
            'notes' => 'Retur barang rusak',
        ]);

        // 3. Transfer Disetujui = 2.000.000
        TransferPayment::create([
            'driver_id' => $this->driver->id,
            'date' => $today,
            'store_name' => 'Toko Subur',
            'claimed_amount' => 2000000,
            'verified_amount' => 2000000,
            'status' => 'approved',
        ]);

        // 4. Kredit = 1.500.000
        CreditDelivery::create([
            'driver_id' => $this->driver->id,
            'date' => $today,
            'store_name' => 'Toko Barokah',
            'amount' => 1500000,
            'invoice_photo_path' => 'credit_invoices/sample.jpg',
        ]);

        // 5. Kasir Setor = 5.000.000
        CashDeposit::create([
            'driver_id' => $this->driver->id,
            'date' => $today,
            'amount_received' => 5000000,
            'deposit_phase' => '1',
        ]);

        // Wajib Setor = 10.000.000 - 1.000.000 - 2.000.000 - 1.500.000 = 5.500.000
        // Selisih = 5.000.000 - 5.500.000 = -500.000 (kurang_setor)

        $this->actingAs($this->driverUser);

        $response = $this->get('/driver');
        $response->assertStatus(200);
        $response->assertSee('TGL1.2');
        $response->assertSee('5.500.000'); // Wajib setor
        $response->assertSee('5.000.000'); // Sudah setor
        $response->assertSee('KURANG SETOR');
    }

    public function test_driver_can_submit_transfer_payment()
    {
        $this->actingAs($this->driverUser);

        $file = UploadedFile::fake()->create('bukti_transfer.jpg', 100, 'image/jpeg');

        $response = $this->post('/driver/transfer', [
            'store_name' => 'Toko Rejeki Nomplok',
            'claimed_amount' => '750.000',
            'proof_image' => $file,
            'notes' => 'Transfer via BCA',
        ]);

        $response->assertRedirect('/driver');
        $this->assertDatabaseHas('transfer_payments', [
            'driver_id' => $this->driver->id,
            'store_name' => 'Toko Rejeki Nomplok',
            'claimed_amount' => 750000,
            'status' => 'pending',
            'notes' => 'Transfer via BCA',
        ]);
    }

    public function test_driver_can_submit_credit_delivery_with_mandatory_photo()
    {
        $this->actingAs($this->driverUser);

        // Uji validasi gagal jika tanpa foto faktur
        $invalidResponse = $this->post('/driver/credit', [
            'store_name' => 'Toko Lancar Jaya',
            'amount' => '1.200.000',
        ]);
        $invalidResponse->assertSessionHasErrors('invoice_photo');

        // Uji berhasil jika foto faktur disertakan
        $file = UploadedFile::fake()->create('faktur_kredit.jpg', 100, 'image/jpeg');

        $response = $this->post('/driver/credit', [
            'store_name' => 'Toko Lancar Jaya',
            'amount' => '1.200.000',
            'invoice_photo' => $file,
            'notes' => 'Faktur tempo 14 hari',
        ]);

        $response->assertRedirect('/driver');
        $this->assertDatabaseHas('credit_deliveries', [
            'driver_id' => $this->driver->id,
            'store_name' => 'Toko Lancar Jaya',
            'amount' => 1200000,
            'notes' => 'Faktur tempo 14 hari',
        ]);
    }

    public function test_driver_can_view_history()
    {
        $this->actingAs($this->driverUser);

        $response = $this->get('/driver/history');
        $response->assertStatus(200);
        $response->assertSee('Riwayat Harian Sopir');
    }

    public function test_driver_can_logout()
    {
        $this->actingAs($this->driverUser);

        $response = $this->post('/driver/logout');
        $response->assertRedirect('/driver/login');
        $this->assertGuest();
    }
}
