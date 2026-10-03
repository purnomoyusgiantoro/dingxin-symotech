<?php

namespace Tests\Feature;

use App\Models\CashDeposit;
use App\Models\CreditDelivery;
use App\Models\DailyDelivery;
use App\Models\DailySettlement;
use App\Models\Driver;
use App\Models\ReturnItem;
use App\Models\TransferPayment;
use App\Models\User;
use App\Services\CashDepositService;
use App\Services\ExcelDeliveryImportService;
use App\Services\SettlementCalculationService;
use App\Services\TransferVerificationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class DingxinBusinessLogicTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_database_seeder_populates_required_users_and_drivers(): void
    {
        $this->assertDatabaseHas('users', ['username' => 'gm', 'role' => 'gm']);
        $this->assertDatabaseHas('users', ['username' => 'admin1', 'role' => 'sales_admin']);
        $this->assertDatabaseHas('users', ['username' => 'admin2', 'role' => 'sales_admin']);
        $this->assertDatabaseHas('users', ['username' => 'admin3', 'role' => 'sales_admin']);
        $this->assertDatabaseHas('users', ['username' => 'kasir', 'role' => 'cashier']);

        // Check TGL Drivers (7 pairs covering 14 numbers)
        $tglCodes = ['TGL1.2', 'TGL3.4', 'TGL5.6', 'TGL7.8', 'TGL9.10', 'TGL11.12', 'TGL13.14'];
        foreach ($tglCodes as $code) {
            $this->assertDatabaseHas('drivers', ['driver_code' => $code, 'area_code' => 'TGL']);
            $this->assertDatabaseHas('users', ['username' => $code, 'role' => 'driver']);
        }

        // Check BRS Drivers (7 pairs covering 14 numbers)
        $brsCodes = ['BRS1.2', 'BRS3.4', 'BRS5.6', 'BRS7.8', 'BRS9.10', 'BRS11.12', 'BRS13.14'];
        foreach ($brsCodes as $code) {
            $this->assertDatabaseHas('drivers', ['driver_code' => $code, 'area_code' => 'BRS']);
            $this->assertDatabaseHas('users', ['username' => $code, 'role' => 'driver']);
        }

        $this->assertEquals(14, Driver::count());
    }

    public function test_settlement_calculation_driver_daily(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $service = app(SettlementCalculationService::class);

        // Test Driver TGL1.2 (Kurang Setor)
        $driverTgl1 = Driver::where('driver_code', 'TGL1.2')->firstOrFail();
        $calcTgl1 = $service->calculateDriverDaily($driverTgl1->id, $today);

        $this->assertEquals(10000000.0, $calcTgl1['amount_carried']);
        $this->assertEquals(500000.0, $calcTgl1['amount_returned']);
        $this->assertEquals(9500000.0, $calcTgl1['net_carried']);
        $this->assertEquals(2000000.0, $calcTgl1['transfer_approved']);
        $this->assertEquals(1500000.0, $calcTgl1['amount_credit']);
        $this->assertEquals(6000000.0, $calcTgl1['wajib_setor']);
        $this->assertEquals(5000000.0, $calcTgl1['sudah_setor']);
        $this->assertEquals(1000000.0, $calcTgl1['selisih_setor']);
        $this->assertEquals('kurang_setor', $calcTgl1['status']);

        // Check settlement record persisted
        $this->assertDatabaseHas('daily_settlements', [
            'driver_id' => $driverTgl1->id,
            'target_cash' => 6000000,
            'actual_cash' => 5000000,
            'difference' => 1000000,
            'status' => 'kurang_setor',
        ]);

        // Test Driver BRS1.2 (Lunas)
        $driverBrs1 = Driver::where('driver_code', 'BRS1.2')->firstOrFail();
        $calcBrs1 = $service->calculateDriverDaily($driverBrs1->id, $today);

        $this->assertEquals(12000000.0, $calcBrs1['amount_carried']);
        $this->assertEquals(0.0, $calcBrs1['amount_returned']);
        $this->assertEquals(12000000.0, $calcBrs1['net_carried']);
        $this->assertEquals(4000000.0, $calcBrs1['transfer_approved']);
        $this->assertEquals(2000000.0, $calcBrs1['amount_credit']);
        $this->assertEquals(6000000.0, $calcBrs1['wajib_setor']);
        $this->assertEquals(6000000.0, $calcBrs1['sudah_setor']);
        $this->assertEquals(0.0, $calcBrs1['selisih_setor']);
        $this->assertEquals('lunas', $calcBrs1['status']);

        // Test Driver with Surplus (Lebih Setor)
        $driverTgl5 = Driver::where('driver_code', 'TGL5.6')->firstOrFail();
        DailyDelivery::updateOrCreate(
            ['driver_id' => $driverTgl5->id, 'date' => $today],
            ['amount' => 5000000]
        );
        CashDeposit::create([
            'driver_id' => $driverTgl5->id,
            'date' => $today,
            'amount_received' => 6000000,
            'deposit_phase' => '1',
            'cashier_id' => User::where('username', 'kasir')->value('id'),
        ]);

        $calcTgl5 = $service->calculateDriverDaily($driverTgl5->id, $today);
        $this->assertEquals(5000000.0, $calcTgl5['wajib_setor']);
        $this->assertEquals(6000000.0, $calcTgl5['sudah_setor']);
        $this->assertEquals(-1000000.0, $calcTgl5['selisih_setor']);
        $this->assertEquals('lebih_setor', $calcTgl5['status']);
    }

    public function test_calculate_all_drivers_daily_summary(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $service = app(SettlementCalculationService::class);
        $result = $service->calculateAllDriversDaily($today);

        $this->assertArrayHasKey('drivers', $result);
        $this->assertArrayHasKey('summary_all', $result);
        $this->assertCount(14, $result['drivers']);

        $summary = $result['summary_all'];
        $this->assertGreaterThan(0, $summary['total_carried']);
        $this->assertGreaterThan(0, $summary['total_transfer_approved']);
        $this->assertGreaterThan(0, $summary['total_credit']);
        $this->assertGreaterThan(0, $summary['total_wajib_setor']);
        $this->assertGreaterThan(0, $summary['total_sudah_setor']);
    }

    public function test_transfer_verification_service(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $driver = Driver::where('driver_code', 'TGL7.8')->firstOrFail();
        $cashier = User::where('username', 'kasir')->firstOrFail();

        // Create a pending transfer
        $transfer = TransferPayment::create([
            'driver_id' => $driver->id,
            'date' => $today,
            'store_name' => 'Toko Mitra Sejati',
            'claimed_amount' => 1000000,
            'status' => 'pending',
        ]);

        $service = app(TransferVerificationService::class);

        // Approve transfer with verified amount
        $approved = $service->approve($transfer->id, $cashier->id, 950000.0);
        $this->assertEquals('approved', $approved->status);
        $this->assertEquals(950000.0, $approved->verified_amount);
        $this->assertEquals($cashier->id, $approved->verified_by);
        $this->assertNotNull($approved->verified_at);

        // Check reject functionality
        $transfer2 = TransferPayment::create([
            'driver_id' => $driver->id,
            'date' => $today,
            'store_name' => 'Toko Palsu',
            'claimed_amount' => 500000,
            'status' => 'pending',
        ]);

        $rejected = $service->reject($transfer2->id, $cashier->id, 'Bukti transfer tidak valid');
        $this->assertEquals('rejected', $rejected->status);
        $this->assertEquals('Bukti transfer tidak valid', $rejected->rejection_reason);
    }

    public function test_cash_deposit_service(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $driver = Driver::where('driver_code', 'BRS5.6')->firstOrFail();
        $cashier = User::where('username', 'kasir')->firstOrFail();

        $service = app(CashDepositService::class);

        $dep1 = $service->recordDeposit($driver->id, $today, 2000000.0, $cashier->id, 'Deposit 1');
        $this->assertEquals('1', $dep1->deposit_phase);
        $this->assertEquals(2000000.0, $dep1->amount_received);

        $dep2 = $service->recordDeposit($driver->id, $today, 1500000.0, $cashier->id, 'Deposit 2');
        $this->assertEquals('2', $dep2->deposit_phase);
        $this->assertEquals(1500000.0, $dep2->amount_received);
    }

    public function test_excel_delivery_import_service(): void
    {
        $today = Carbon::today()->format('Y-m-d');
        $admin = User::where('username', 'admin1')->firstOrFail();

        // Create an Excel test file
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Tanggal');
        $sheet->setCellValue('B1', 'Kode Sopir');
        $sheet->setCellValue('C1', 'Nominal Bawaan');
        $sheet->setCellValue('D1', 'Keterangan/Rute');

        // Row 2
        $sheet->setCellValue('A2', $today);
        $sheet->setCellValue('B2', 'TGL9.10');
        $sheet->setCellValue('C2', 'Rp 14.500.000');
        $sheet->setCellValue('D2', 'Rute Suradadi - Kramat');

        // Row 3 (Invalid driver code)
        $sheet->setCellValue('A3', $today);
        $sheet->setCellValue('B3', 'INVALID_CODE');
        $sheet->setCellValue('C3', '5000000');
        $sheet->setCellValue('D3', 'Rute Unknown');

        $tempPath = storage_path('app/test_import.xlsx');
        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempPath);

        $importService = app(ExcelDeliveryImportService::class);
        $result = $importService->import($tempPath, $admin->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported']);
        $this->assertCount(1, $result['errors']);

        // Verify DailyDelivery for TGL9.10 was saved
        $driver = Driver::where('driver_code', 'TGL9.10')->firstOrFail();
        $delivery = DailyDelivery::where('driver_id', $driver->id)
            ->whereDate('date', $today)
            ->first();

        $this->assertNotNull($delivery);
        $this->assertEquals(14500000.0, (float) $delivery->amount);
        $this->assertEquals('Rute Suradadi - Kramat', $delivery->route_notes);

        if (file_exists($tempPath)) {
            unlink($tempPath);
        }
    }
}
