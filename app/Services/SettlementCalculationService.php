<?php

namespace App\Services;

use App\Models\CashDeposit;
use App\Models\CreditDelivery;
use App\Models\DailyDelivery;
use App\Models\DailySettlement;
use App\Models\Driver;
use App\Models\ReturnItem;
use App\Models\TransferPayment;
use Carbon\Carbon;

class SettlementCalculationService
{
    /**
     * Hitung rekonsiliasi setoran harian untuk 1 sopir spesifik.
     */
    public function calculateDriverDaily(int $driverId, ?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $driver = Driver::with('user')->findOrFail($driverId);

        // 1. Barang Dibawa
        $amountCarried = (float) DailyDelivery::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->sum('amount');

        // 2. Barang Kembali (Retur)
        $amountReturned = (float) ReturnItem::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->sum('amount');

        $netCarried = max(0.0, $amountCarried - $amountReturned);

        // 3. Pembayaran Transfer
        $transfers = TransferPayment::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->get();

        $transferPending = (float) $transfers->where('status', 'pending')->sum('claimed_amount');
        $transferApproved = (float) $transfers->where('status', 'approved')->sum(function ($item) {
            return !is_null($item->verified_amount) ? (float) $item->verified_amount : (float) $item->claimed_amount;
        });
        $transferRejected = (float) $transfers->where('status', 'rejected')->sum('claimed_amount');

        // 4. Pengiriman Kredit
        $amountCredit = (float) CreditDelivery::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->sum('amount');

        // 5. Wajib Setor Tunai
        // Formula: Wajib Setor = (Barang Dibawa - Barang Kembali) - Transfer Terkonfirmasi - Kredit
        $wajibSetor = max(0.0, $netCarried - $transferApproved - $amountCredit);

        // 6. Sudah Setor (Uang Tunai diterima Kasir)
        $cashDeposits = CashDeposit::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->orderBy('created_at', 'asc')
            ->get();

        $sudahSetor = (float) $cashDeposits->sum('amount_received');

        // 7. Selisih Setor
        $selisihSetor = $wajibSetor - $sudahSetor;

        if (abs($selisihSetor) < 0.01) {
            $status = 'lunas';
            $selisihSetor = 0.0;
        } elseif ($selisihSetor > 0) {
            $status = 'kurang_setor';
        } else {
            $status = 'lebih_setor';
        }

        // Susun riwayat mutasi kronologis
        $mutations = [];

        $deliveries = DailyDelivery::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->get();
        foreach ($deliveries as $del) {
            $mutations[] = [
                'type' => 'delivery',
                'title' => 'Barang Bawaan',
                'description' => $del->route_notes ?? 'Muatan armada',
                'amount' => (float)$del->amount,
                'flow' => 'in',
                'status_label' => 'Bawaan Awal',
                'created_at' => $del->created_at ?? Carbon::parse($targetDate)->startOfDay(),
            ];
        }

        $returns = ReturnItem::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->get();
        foreach ($returns as $ret) {
            $mutations[] = [
                'type' => 'return',
                'title' => 'Barang Kembali (Retur)',
                'description' => $ret->notes ?? 'Pengembalian barang',
                'amount' => (float)$ret->amount,
                'flow' => 'deduct',
                'status_label' => 'Retur',
                'created_at' => $ret->created_at ?? Carbon::parse($targetDate),
            ];
        }

        foreach ($transfers as $tr) {
            $mutations[] = [
                'type' => 'transfer',
                'title' => 'Transfer: ' . $tr->store_name,
                'description' => $tr->notes ?? 'Pembayaran non-tunai',
                'amount' => (float)($tr->status === 'approved' && !is_null($tr->verified_amount) ? $tr->verified_amount : $tr->claimed_amount),
                'flow' => 'transfer',
                'status_label' => ucfirst($tr->status),
                'rejection_reason' => $tr->rejection_reason,
                'created_at' => $tr->created_at,
            ];
        }

        $credits = CreditDelivery::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->get();
        foreach ($credits as $cr) {
            $mutations[] = [
                'type' => 'credit',
                'title' => 'Kredit: ' . $cr->store_name,
                'description' => $cr->notes ?? 'Faktur kredit toko',
                'amount' => (float)$cr->amount,
                'flow' => 'credit',
                'status_label' => 'Faktur Kredit',
                'created_at' => $cr->created_at,
            ];
        }

        foreach ($cashDeposits as $cd) {
            $mutations[] = [
                'type' => 'cash',
                'title' => 'Setoran Kasir (Tahap ' . $cd->deposit_phase . ')',
                'description' => $cd->notes ?? 'Uang tunai diterima kasir',
                'amount' => (float)$cd->amount_received,
                'flow' => 'paid',
                'status_label' => 'Diterima Kasir',
                'created_at' => $cd->created_at,
            ];
        }

        // Urutkan mutasi berdasarkan created_at
        usort($mutations, function ($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });

        // Simpan / update snapshot harian ke daily_settlements
        $snapshotData = [
            'amount_carried' => $amountCarried,
            'amount_returned' => $amountReturned,
            'amount_transfer_approved' => $transferApproved,
            'amount_credit' => $amountCredit,
            'target_cash' => $wajibSetor,
            'actual_cash' => $sudahSetor,
            'difference' => $selisihSetor,
            'status' => $status,
        ];

        $settlement = DailySettlement::where('driver_id', $driverId)
            ->whereDate('date', $targetDate)
            ->first();

        if ($settlement) {
            $settlement->update($snapshotData);
        } else {
            $snapshotData['driver_id'] = $driverId;
            $snapshotData['date'] = $targetDate;
            DailySettlement::create($snapshotData);
        }

        return [
            'driver_id' => $driver->id,
            'driver_code' => $driver->driver_code,
            'driver_name' => $driver->user?->name ?? $driver->driver_code,
            'area_code' => $driver->area_code,
            'phone_number' => $driver->phone_number,
            'plate_number' => $driver->plate_number,
            'date' => $targetDate,
            'amount_carried' => $amountCarried,
            'amount_returned' => $amountReturned,
            'net_carried' => $netCarried,
            'transfer_pending' => $transferPending,
            'transfer_claimed' => (float) $transfers->sum('claimed_amount'),
            'transfer_approved' => $transferApproved,
            'transfer_rejected' => $transferRejected,
            'amount_credit' => $amountCredit,
            'wajib_setor' => $wajibSetor,
            'target_cash' => $wajibSetor, // Alias untuk blade
            'sudah_setor' => $sudahSetor,
            'actual_cash' => $sudahSetor, // Alias untuk blade
            'selisih_setor' => $selisihSetor,
            'difference' => $selisihSetor, // Alias untuk blade
            'status' => $status,
            'cash_deposits_count' => $cashDeposits->count(),
            'cash_deposits' => $cashDeposits,
            'transfers' => $transfers,
            'mutations' => $mutations,
        ];
    }

    /**
     * Alias method yang menerima object Driver atau int $driverId.
     */
    public function calculateSummary(Driver|int $driver, ?string $date = null): array
    {
        $driverId = $driver instanceof Driver ? $driver->id : (int)$driver;
        return $this->calculateDriverDaily($driverId, $date);
    }

    /**
     * Hitung rekonsiliasi seluruh sopir aktif beserta baris Total All Setor.
     */
    public function calculateAllDriversDaily(?string $date = null): array
    {
        $targetDate = $date ? Carbon::parse($date)->format('Y-m-d') : Carbon::today()->format('Y-m-d');
        $drivers = Driver::with('user')
            ->where('is_active', true)
            ->orderBy('area_code', 'asc')
            ->orderBy('driver_code', 'asc')
            ->get();

        $items = [];
        $totalCarried = 0.0;
        $totalReturned = 0.0;
        $totalNetCarried = 0.0;
        $totalTransferPending = 0.0;
        $totalTransferApproved = 0.0;
        $totalCredit = 0.0;
        $totalWajibSetor = 0.0;
        $totalSudahSetor = 0.0;
        $totalSelisihSetor = 0.0;

        foreach ($drivers as $driver) {
            $calc = $this->calculateDriverDaily($driver->id, $targetDate);
            $items[] = $calc;

            $totalCarried += $calc['amount_carried'];
            $totalReturned += $calc['amount_returned'];
            $totalNetCarried += $calc['net_carried'];
            $totalTransferPending += $calc['transfer_pending'];
            $totalTransferApproved += $calc['transfer_approved'];
            $totalCredit += $calc['amount_credit'];
            $totalWajibSetor += $calc['wajib_setor'];
            $totalSudahSetor += $calc['sudah_setor'];
            $totalSelisihSetor += $calc['selisih_setor'];
        }

        $summaryAll = [
            'total_drivers' => count($items),
            'date' => $targetDate,
            'total_carried' => $totalCarried,
            'total_returned' => $totalReturned,
            'total_net_carried' => $totalNetCarried,
            'total_transfer_pending' => $totalTransferPending,
            'total_transfer_approved' => $totalTransferApproved,
            'total_credit' => $totalCredit,
            'total_wajib_setor' => $totalWajibSetor,
            'total_sudah_setor' => $totalSudahSetor,
            'total_selisih_setor' => $totalSelisihSetor,
            'total_selisih' => $totalSelisihSetor,
            'status' => $totalSelisihSetor == 0 ? 'lunas' : ($totalSelisihSetor > 0 ? 'kurang_setor' : 'lebih_setor'),
        ];

        return [
            'date' => $targetDate,
            'drivers' => $items,
            'summary_all' => $summaryAll,
        ];
    }
}
