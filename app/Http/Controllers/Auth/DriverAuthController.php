<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class DriverAuthController extends Controller
{
    /**
     * Show the driver portal login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === 'driver') {
                return redirect()->route('driver.dashboard');
            }
            return redirect('/admin');
        }

        return view('driver.login');
    }

    /**
     * Handle driver login submission.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Kode sopir atau username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = trim($request->input('login'));
        $password = $request->input('password');

        // Cari berdasarkan username di tabel users
        $user = User::where('username', $loginInput)->first();

        // Jika tidak ditemukan, cari berdasarkan driver_code di tabel drivers
        if (!$user) {
            $driver = Driver::where('driver_code', $loginInput)->first();
            if ($driver) {
                $user = $driver->user;
            }
        }

        // Verifikasi User dan Password
        if (!$user || !Hash::check($password, $user->password)) {
            return back()->withInput($request->only('login', 'remember'))->withErrors([
                'login' => 'Kode sopir/username atau password salah.',
            ]);
        }

        // Verifikasi apakah user aktif
        if (!$user->is_active) {
            return back()->withInput($request->only('login', 'remember'))->withErrors([
                'login' => 'Akun Anda saat ini berstatus non-aktif. Hubungi Sales Admin/Kasir.',
            ]);
        }

        // Login ke session
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Role-based redirection
        if ($user->role === 'driver') {
            // Pastikan relasi driver ada
            if (!$user->driver) {
                Auth::logout();
                return back()->withErrors([
                    'login' => 'Data armada sopir tidak terdaftar pada akun ini.',
                ]);
            }

            return redirect()->intended(route('driver.dashboard'))
                ->with('success', 'Selamat datang kembali, ' . ($user->driver->driver_code ?? $user->name) . '!');
        }

        // Jika role admin / sales_admin / cashier / gm, arahkan ke /admin
        return redirect()->intended('/admin');
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('driver.login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
