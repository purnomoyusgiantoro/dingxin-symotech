<?php

namespace App\Filament\Pages;

use App\Models\CashDeposit;
use App\Models\Driver;
use App\Services\CashDepositService;
use App\Services\SettlementCalculationService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DailySettlementSummary extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Setoran All Sopir';

    protected static ?string $title = 'Rekapitulasi Setoran Harian All Sopir';

    protected static ?string $navigationGroup = 'Rekapitulasi & Laporan';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.daily-settlement-summary';

    public string $selectedDate = '';

    // Data form modal input setoran tunai
    public ?int $selectedDriverId = null;
    public ?string $selectedDriverName = null;
    public ?float $depositAmount = null;
    public ?string $depositNotes = null;

    // Data modal riwayat setoran tunai sopir
    public ?int $historyDriverId = null;
    public ?string $historyDriverName = null;

    public function mount(): void
    {
        $this->selectedDate = Carbon::today()->format('Y-m-d');
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role, ['cashier', 'gm', 'sales_admin']);
    }

    public function getSettlementDataProperty(): array
    {
        $service = app(SettlementCalculationService::class);
        return $service->calculateAllDriversDaily($this->selectedDate);
    }

    /**
     * Modal trigger untuk kasir menginput setoran tunai sopir.
     */
    public function openDepositModal(int $driverId, string $driverName): void
    {
        $this->selectedDriverId = $driverId;
        $this->selectedDriverName = $driverName;
        $this->depositAmount = null;
        $this->depositNotes = 'Setoran tunai shift';
        $this->dispatch('open-modal', id: 'deposit-modal');
    }

    /**
     * Simpan setoran tunai yang diserahkan sopir ke kasir.
     */
    public function saveDeposit(): void
    {
        $this->validate([
            'selectedDriverId' => 'required|exists:drivers,id',
            'depositAmount' => 'required|numeric|min:1',
            'depositNotes' => 'nullable|string|max:255',
        ], [
            'depositAmount.required' => 'Nominal setoran tunai wajib diisi.',
            'depositAmount.numeric' => 'Nominal harus berupa angka.',
            'depositAmount.min' => 'Nominal minimal Rp 1.',
        ]);

        $cashService = app(CashDepositService::class);
        $deposit = $cashService->recordDeposit(
            $this->selectedDriverId,
            $this->selectedDate,
            (float)$this->depositAmount,
            auth()->id(),
            $this->depositNotes
        );

        // Refresh snapshot
        app(SettlementCalculationService::class)->calculateDriverDaily($this->selectedDriverId, $this->selectedDate);

        $this->dispatch('close-modal', id: 'deposit-modal');

        Notification::make()
            ->title('Setoran Tunai Diterima')
            ->body("Berhasil mencatat setoran Rp " . number_format($deposit->amount_received, 0, ',', '.') . " untuk {$this->selectedDriverName} (Tahap {$deposit->deposit_phase}).")
            ->success()
            ->send();

        $this->selectedDriverId = null;
        $this->selectedDriverName = null;
        $this->depositAmount = null;
        $this->depositNotes = null;
    }

    /**
     * Buka modal rincian multi-setoran tunai sopir hari ini.
     */
    public function openHistoryModal(int $driverId, string $driverName): void
    {
        $this->historyDriverId = $driverId;
        $this->historyDriverName = $driverName;
        $this->dispatch('open-modal', id: 'history-modal');
    }

    /**
     * Ambil koleksi setoran tunai sopir terpilih untuk tanggal aktif.
     */
    public function getDriverHistoryDepositsProperty()
    {
        if (!$this->historyDriverId) {
            return collect();
        }

        return CashDeposit::with('cashier')
            ->where('driver_id', $this->historyDriverId)
            ->whereDate('date', $this->selectedDate)
            ->orderBy('deposit_phase', 'asc')
            ->get();
    }

    /**
     * Hapus entri setoran tunai tertentu (Eksklusif wewenang GM).
     */
    public function deleteDeposit(int $depositId): void
    {
        if (auth()->user()?->role !== 'gm') {
            Notification::make()
                ->title('Akses Ditolak')
                ->body('Hanya GM yang memiliki wewenang untuk menghapus atau membatalkan setoran tunai.')
                ->danger()
                ->send();
            return;
        }

        $deposit = CashDeposit::findOrFail($depositId);
        $driverId = $deposit->driver_id;
        $date = $deposit->date;
        $amount = $deposit->amount_received;
        $deposit->delete();

        // Refresh snapshot
        app(SettlementCalculationService::class)->calculateDriverDaily($driverId, $date);

        Notification::make()
            ->title('Setoran Dihapus')
            ->body("Setoran Rp " . number_format($amount, 0, ',', '.') . " telah dibatalkan oleh GM.")
            ->warning()
            ->send();
    }

    /**
     * Export rekapitulasi setoran harian all sopir ke file Excel (.xlsx).
     */
    public function exportExcel()
    {
        $service = app(SettlementCalculationService::class);
        $data = $service->calculateAllDriversDaily($this->selectedDate);
        $drivers = $data['drivers'];
        $summary = $data['summary_all'];

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Setoran ' . $this->selectedDate);

        // Header Title
        $sheet->setCellValue('A1', 'REKAPITULASI SETORAN HARIAN ALL SOPIR - PT DINGXIN MULTI DISTRIBUSI');
        $sheet->setCellValue('A2', 'Tanggal Operasional: ' . Carbon::parse($this->selectedDate)->translatedFormat('l, d F Y'));
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(11);

        // Column Headers
        $headers = [
            'No', 'Kode Sopir', 'Nama Sopir', 'Bawaan (Rp)', 'Retur (Rp)', 
            'Transfer Conf. (Rp)', 'Kredit (Rp)', 'Wajib Setor (Rp)', 
            'Sudah Setor (Rp)', 'Selisih (Rp)', 'Status'
        ];

        $headerRow = 4;
        foreach ($headers as $col => $header) {
            $sheet->setCellValue([$col + 1, $headerRow], $header);
        }

        $headerStyle = $sheet->getStyle('A4:K4');
        $headerStyle->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFFFFF'));
        $headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('0F172A');
        $headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Data Rows
        $row = 5;
        foreach ($drivers as $idx => $d) {
            $sheet->setCellValue([1, $row], $idx + 1);
            $sheet->setCellValue([2, $row], $d['driver_code']);
            $sheet->setCellValue([3, $row], $d['driver_name']);
            $sheet->setCellValue([4, $row], (float)$d['amount_carried']);
            $sheet->setCellValue([5, $row], (float)$d['amount_returned']);
            $sheet->setCellValue([6, $row], (float)$d['transfer_approved']);
            $sheet->setCellValue([7, $row], (float)$d['amount_credit']);
            $sheet->setCellValue([8, $row], (float)$d['wajib_setor']);
            $sheet->setCellValue([9, $row], (float)$d['sudah_setor']);
            $sheet->setCellValue([10, $row], (float)$d['selisih_setor']);
            $sheet->setCellValue([11, $row], strtoupper(str_replace('_', ' ', $d['status'])));
            $row++;
        }

        // Summary / Total Row
        $sheet->setCellValue([1, $row], '');
        $sheet->setCellValue([2, $row], 'TOTAL');
        $sheet->setCellValue([3, $row], 'BARIS ALL SETOR');
        $sheet->setCellValue([4, $row], (float)$summary['total_carried']);
        $sheet->setCellValue([5, $row], (float)$summary['total_returned']);
        $sheet->setCellValue([6, $row], (float)$summary['total_transfer_approved']);
        $sheet->setCellValue([7, $row], (float)$summary['total_credit']);
        $sheet->setCellValue([8, $row], (float)$summary['total_wajib_setor']);
        $sheet->setCellValue([9, $row], (float)$summary['total_sudah_setor']);
        $sheet->setCellValue([10, $row], (float)$summary['total_selisih_setor']);
        $sheet->setCellValue([11, $row], strtoupper(str_replace('_', ' ', $summary['status'])));

        $totalStyle = $sheet->getStyle("A{$row}:K{$row}");
        $totalStyle->getFont()->setBold(true);
        $totalStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F1F5F9');

        // Number Formatting
        $sheet->getStyle('D5:J' . $row)->getNumberFormat()->setFormatCode('#,##0');
        $sheet->getStyle('A5:A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5:B' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K5:K' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Auto-fit Column Width
        foreach (range(1, 11) as $colIdx) {
            $sheet->getColumnDimensionByColumn($colIdx)->setAutoSize(true);
        }

        $filename = 'rekap_setoran_dingxin_' . $this->selectedDate . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }
}
