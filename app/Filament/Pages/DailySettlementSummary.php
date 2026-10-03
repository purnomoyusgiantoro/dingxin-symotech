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
     * Hapus entri setoran tunai tertentu (misal salah input oleh kasir).
     */
    public function deleteDeposit(int $depositId): void
    {
        $deposit = CashDeposit::findOrFail($depositId);
        $driverId = $deposit->driver_id;
        $date = $deposit->date;
        $amount = $deposit->amount_received;
        $deposit->delete();

        // Refresh snapshot
        app(SettlementCalculationService::class)->calculateDriverDaily($driverId, $date);

        Notification::make()
            ->title('Setoran Dihapus')
            ->body("Setoran Rp " . number_format($amount, 0, ',', '.') . " telah dibatalkan.")
            ->warning()
            ->send();
    }
}
