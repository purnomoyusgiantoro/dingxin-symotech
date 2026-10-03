<?php

namespace App\Services;

use App\Models\CashDeposit;
use Carbon\Carbon;

class CashDepositService
{
    /**
     * Catat setoran uang tunai (cash) yang diserahkan sopir ke kasir.
     * Mendukung multi-setor (bertahap beberapa kali dalam 1 hari).
     */
    public function recordDeposit(
        int $driverId,
        string $date,
        float $amount,
        int $cashierId,
        ?string $notes = null
    ): CashDeposit {
        $formattedDate = Carbon::parse($date)->format('Y-m-d');

        // Hitung phase ke berapa setoran hari ini untuk sopir tersebut
        $currentCount = CashDeposit::where('driver_id', $driverId)
            ->whereDate('date', $formattedDate)
            ->count();

        $phase = (string)($currentCount + 1);

        $deposit = CashDeposit::create([
            'driver_id' => $driverId,
            'date' => $formattedDate,
            'amount_received' => $amount,
            'deposit_phase' => $phase,
            'cashier_id' => $cashierId,
            'notes' => $notes,
        ]);

        app(SettlementCalculationService::class)->calculateDriverDaily($driverId, $formattedDate);

        return $deposit;
    }
}
