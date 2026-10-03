<?php

namespace App\Services;

use App\Models\TransferPayment;
use Carbon\Carbon;

class TransferVerificationService
{
    /**
     * Konfirmasi (Approve) transfer toko dari sopir.
     * Jika kasir menemukan nominal berbeda pada mutasi bank, dapat mengisi $verifiedAmount.
     */
    public function approve(int $transferId, int $cashierId, ?float $verifiedAmount = null, ?string $notes = null): TransferPayment
    {
        $transfer = TransferPayment::findOrFail($transferId);

        $transfer->status = 'approved';
        $transfer->verified_amount = !is_null($verifiedAmount) ? $verifiedAmount : $transfer->claimed_amount;
        $transfer->verified_by = $cashierId;
        $transfer->verified_at = Carbon::now();
        if ($notes) {
            $transfer->notes = $notes;
        }
        $transfer->save();

        app(SettlementCalculationService::class)->calculateDriverDaily(
            $transfer->driver_id,
            $transfer->date instanceof \DateTimeInterface ? $transfer->date->format('Y-m-d') : (string) $transfer->date
        );

        return $transfer;
    }

    /**
     * Tolak (Reject) transfer toko.
     */
    public function reject(int $transferId, int $cashierId, string $rejectionReason): TransferPayment
    {
        $transfer = TransferPayment::findOrFail($transferId);

        $transfer->status = 'rejected';
        $transfer->verified_by = $cashierId;
        $transfer->verified_at = Carbon::now();
        $transfer->rejection_reason = $rejectionReason;
        $transfer->save();

        app(SettlementCalculationService::class)->calculateDriverDaily(
            $transfer->driver_id,
            $transfer->date instanceof \DateTimeInterface ? $transfer->date->format('Y-m-d') : (string) $transfer->date
        );

        return $transfer;
    }
}
