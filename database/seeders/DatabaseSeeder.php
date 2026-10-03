<?php

namespace Database\Seeders;

use App\Models\CashDeposit;
use App\Models\CreditDelivery;
use App\Models\DailyDelivery;
use App\Models\Driver;
use App\Models\ReturnItem;
use App\Models\TransferPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with roles, users, and drivers.
     */
    public function run(): void
    {
        $password = Hash::make('password');
        $today = Carbon::today()->format('Y-m-d');

        // 1. GM / Administrator
        $gm = User::updateOrCreate(
            ['username' => 'gm'],
            [
                'name' => 'General Manager',
                'email' => 'gm@symotech.web.id',
                'password' => $password,
                'role' => 'gm',
                'is_active' => true,
            ]
        );

        // 2. Kasir
        $cashier = User::updateOrCreate(
            ['username' => 'kasir'],
            [
                'name' => 'Kasir Utama',
                'email' => 'kasir@symotech.web.id',
                'password' => $password,
                'role' => 'cashier',
                'is_active' => true,
            ]
        );

        // 3. Admin Penjualan (admin1, admin2, admin3)
        $admins = [];
        foreach ([1, 2, 3] as $num) {
            $admins[$num] = User::updateOrCreate(
                ['username' => "admin{$num}"],
                [
                    'name' => "Admin Penjualan {$num}",
                    'email' => "admin{$num}@symotech.web.id",
                    'password' => $password,
                    'role' => 'sales_admin',
                    'is_active' => true,
                ]
            );
        }

        // 4. Daftar 14 Kode Sopir Tegal (TGL)
        $tglCodes = [
            'TGL1.2', 'TGL3.4', 'TGL5.6', 'TGL7.8',
            'TGL9.10', 'TGL11.12', 'TGL13.14'
        ];

        // 5. Daftar 14 Kode Sopir Brebes (BRS)
        $brsCodes = [
            'BRS1.2', 'BRS3.4', 'BRS5.6', 'BRS7.8',
            'BRS9.10', 'BRS11.12', 'BRS13.14'
        ];

        $allDrivers = [];

        // Buat Sopir TGL
        foreach ($tglCodes as $idx => $code) {
            $user = User::updateOrCreate(
                ['username' => $code],
                [
                    'name' => "Sopir {$code}",
                    'email' => strtolower($code) . '@symotech.web.id',
                    'password' => $password,
                    'role' => 'driver',
                    'is_active' => true,
                ]
            );

            $driver = Driver::updateOrCreate(
                ['driver_code' => $code],
                [
                    'user_id' => $user->id,
                    'area_code' => 'TGL',
                    'phone_number' => '0812' . str_pad((string)(2000000 + $idx), 8, '0', STR_PAD_LEFT),
                    'plate_number' => 'G ' . (8100 + $idx) . ' EZ',
                    'is_active' => true,
                    'cumulative_balance' => 0,
                ]
            );
            $allDrivers[$code] = $driver;
        }

        // Buat Sopir BRS
        foreach ($brsCodes as $idx => $code) {
            $user = User::updateOrCreate(
                ['username' => $code],
                [
                    'name' => "Sopir {$code}",
                    'email' => strtolower($code) . '@symotech.web.id',
                    'password' => $password,
                    'role' => 'driver',
                    'is_active' => true,
                ]
            );

            $driver = Driver::updateOrCreate(
                ['driver_code' => $code],
                [
                    'user_id' => $user->id,
                    'area_code' => 'BRS',
                    'phone_number' => '0813' . str_pad((string)(3000000 + $idx), 8, '0', STR_PAD_LEFT),
                    'plate_number' => 'G ' . (9100 + $idx) . ' BZ',
                    'is_active' => true,
                    'cumulative_balance' => 0,
                ]
            );
            $allDrivers[$code] = $driver;
        }

        // 6. Sample Transaksi Hari Ini untuk Uji Coba

        // --- Contoh 1: TGL1.2 (Ada bawaan, retur, transfer approved, kredit, dan setoran tunai) ---
        if (isset($allDrivers['TGL1.2'])) {
            $d1 = $allDrivers['TGL1.2'];
            DailyDelivery::updateOrCreate(
                ['driver_id' => $d1->id, 'date' => $today],
                ['amount' => 10000000, 'route_notes' => 'Rute Margadana - Kramat', 'created_by' => $admins[1]->id]
            );
            ReturnItem::updateOrCreate(
                ['driver_id' => $d1->id, 'date' => $today, 'amount' => 500000],
                ['notes' => 'Barang rusak/kemasan bocor', 'created_by' => $admins[1]->id]
            );
            TransferPayment::updateOrCreate(
                ['driver_id' => $d1->id, 'date' => $today, 'store_name' => 'Toko Berkah Abadi'],
                [
                    'claimed_amount' => 2000000,
                    'verified_amount' => 2000000,
                    'status' => 'approved',
                    'verified_by' => $cashier->id,
                    'verified_at' => now(),
                    'notes' => 'Mutasi BCA masuk valid'
                ]
            );
            CreditDelivery::updateOrCreate(
                ['driver_id' => $d1->id, 'date' => $today, 'store_name' => 'Toko Jaya Mandiri'],
                ['amount' => 1500000, 'notes' => 'Tempo 14 hari']
            );
            CashDeposit::updateOrCreate(
                ['driver_id' => $d1->id, 'date' => $today, 'amount_received' => 5000000],
                ['deposit_phase' => '1', 'cashier_id' => $cashier->id, 'notes' => 'Setoran tunai tahap 1 sore']
            );
        }

        // --- Contoh 2: TGL3.4 (Transfer status pending) ---
        if (isset($allDrivers['TGL3.4'])) {
            $d2 = $allDrivers['TGL3.4'];
            DailyDelivery::updateOrCreate(
                ['driver_id' => $d2->id, 'date' => $today],
                ['amount' => 8000000, 'route_notes' => 'Rute Mejasem - Suradadi', 'created_by' => $admins[2]->id]
            );
            TransferPayment::updateOrCreate(
                ['driver_id' => $d2->id, 'date' => $today, 'store_name' => 'Toko Sinar Jaya'],
                ['claimed_amount' => 1000000, 'status' => 'pending']
            );
            CreditDelivery::updateOrCreate(
                ['driver_id' => $d2->id, 'date' => $today, 'store_name' => 'Toko Rejeki'],
                ['amount' => 1000000, 'notes' => 'Faktur No. FK-0829']
            );
            CashDeposit::updateOrCreate(
                ['driver_id' => $d2->id, 'date' => $today, 'amount_received' => 4000000],
                ['deposit_phase' => '1', 'cashier_id' => $cashier->id, 'notes' => 'Setoran tunai shift 1']
            );
        }

        // --- Contoh 3: BRS1.2 (Lunas) ---
        if (isset($allDrivers['BRS1.2'])) {
            $d3 = $allDrivers['BRS1.2'];
            DailyDelivery::updateOrCreate(
                ['driver_id' => $d3->id, 'date' => $today],
                ['amount' => 12000000, 'route_notes' => 'Rute Brebes Kota - Jatibarang', 'created_by' => $admins[1]->id]
            );
            TransferPayment::updateOrCreate(
                ['driver_id' => $d3->id, 'date' => $today, 'store_name' => 'Toko Barokah Brebes'],
                [
                    'claimed_amount' => 4000000,
                    'verified_amount' => 4000000,
                    'status' => 'approved',
                    'verified_by' => $cashier->id,
                    'verified_at' => now(),
                ]
            );
            CreditDelivery::updateOrCreate(
                ['driver_id' => $d3->id, 'date' => $today, 'store_name' => 'Toko Mulia'],
                ['amount' => 2000000]
            );
            CashDeposit::updateOrCreate(
                ['driver_id' => $d3->id, 'date' => $today, 'amount_received' => 6000000],
                ['deposit_phase' => '1', 'cashier_id' => $cashier->id, 'notes' => 'Lunas tepat']
            );
        }

        // Kalkulasi seluruh setoran harian hari ini
        app(\App\Services\SettlementCalculationService::class)->calculateAllDriversDaily($today);
    }
}
