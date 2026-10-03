<?php

use App\Http\Controllers\Auth\DriverAuthController;
use App\Http\Controllers\Driver\DriverPortalController;
use Illuminate\Support\Facades\Route;

// Redirect root ke portal sopir atau admin
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->role === 'driver'
            ? redirect()->route('driver.dashboard')
            : redirect('/admin');
    }
    return redirect()->route('driver.login');
});

// Autentikasi Khusus Sopir
Route::get('/login', [DriverAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [DriverAuthController::class, 'login'])->name('login.post');
Route::get('/driver/login', [DriverAuthController::class, 'showLoginForm'])->name('driver.login');
Route::post('/driver/login', [DriverAuthController::class, 'login'])->name('driver.login.post');
Route::post('/driver/logout', [DriverAuthController::class, 'logout'])->name('driver.logout');
Route::post('/logout', [DriverAuthController::class, 'logout'])->name('logout');

// Driver Portal (Diproteksi DriverSessionGuard)
Route::middleware(['web', 'driver.guard'])->prefix('driver')->name('driver.')->group(function () {
    // Dashboard & Settlement Harian
    Route::get('/', [DriverPortalController::class, 'dashboard'])->name('dashboard');

    // Input Transfer Pembayaran Toko
    Route::get('/transfer', [DriverPortalController::class, 'createTransfer'])->name('transfer.create');
    Route::post('/transfer', [DriverPortalController::class, 'storeTransfer'])->name('transfer.store');

    // Input Faktur Kredit Toko
    Route::get('/credit', [DriverPortalController::class, 'createCredit'])->name('credit.create');
    Route::post('/credit', [DriverPortalController::class, 'storeCredit'])->name('credit.store');

    // Riwayat Harian & Mutasi Transaksi
    Route::get('/history', [DriverPortalController::class, 'history'])->name('history');
});
