<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\CreditDelivery;
use App\Models\DailySettlement;
use App\Models\Driver;
use App\Models\TransferPayment;
use App\Services\SettlementCalculationService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class DriverPortalController extends Controller
{
    protected SettlementCalculationService $calculationService;

    public function __construct(SettlementCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    /**
     * Helper to get the authenticated driver model.
     */
    protected function getDriver(): Driver
    {
        $driver = Auth::user()->driver;

        if (!$driver) {
            abort(403, 'Profil driver tidak ditemukan.');
        }

        return $driver;
    }

    /**
     * Driver Dashboard: ringkasan keuangan dan mutasi transaksi hari ini.
     */
    public function dashboard(Request $request): View
    {
        $driver = $this->getDriver();
        $date = $request->input('date', Carbon::today()->toDateString());

        $summary = $this->calculationService->calculateSummary($driver, $date);

        // Ambil riwayat input harian transfer & kredit hari ini
        $todayTransfers = TransferPayment::where('driver_id', $driver->id)
            ->whereDate('date', $date)
            ->latest()
            ->get();

        $todayCredits = CreditDelivery::where('driver_id', $driver->id)
            ->whereDate('date', $date)
            ->latest()
            ->get();

        return view('driver.dashboard', compact('driver', 'summary', 'date', 'todayTransfers', 'todayCredits'));
    }

    /**
     * Show form input transfer pembayaran toko.
     */
    public function createTransfer(): View
    {
        $driver = $this->getDriver();
        $todayDate = Carbon::today()->toDateString();

        return view('driver.transfer', compact('driver', 'todayDate'));
    }

    /**
     * Store transfer payment request.
     */
    public function storeTransfer(Request $request): RedirectResponse
    {
        $driver = $this->getDriver();

        // Bersihkan input angka (jika ada formatting titik/koma)
        $rawAmount = $request->input('claimed_amount');
        if (is_string($rawAmount)) {
            $cleaned = preg_replace('/[^0-9]/', '', $rawAmount);
            $request->merge(['claimed_amount' => $cleaned]);
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'claimed_amount' => 'required|numeric|min:1',
            'proof_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'notes' => 'nullable|string|max:500',
        ], [
            'store_name.required' => 'Nama toko wajib diisi.',
            'claimed_amount.required' => 'Nominal transfer wajib diisi.',
            'claimed_amount.numeric' => 'Nominal transfer harus berupa angka valid.',
            'claimed_amount.min' => 'Nominal transfer minimal Rp 1.',
            'proof_image.image' => 'Bukti transfer harus berupa file gambar (JPG, PNG, WEBP).',
            'proof_image.max' => 'Ukuran gambar bukti transfer maksimal 5MB.',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_image')) {
            $proofPath = $request->file('proof_image')->store('transfers', 'public');
        }

        $transfer = TransferPayment::create([
            'driver_id' => $driver->id,
            'date' => Carbon::today()->toDateString(),
            'store_name' => trim($request->input('store_name')),
            'claimed_amount' => $request->input('claimed_amount'),
            'proof_image_path' => $proofPath,
            'status' => 'pending',
            'notes' => $request->input('notes'),
        ]);

        // Hitung ulang ringkasan settlement
        $this->calculationService->calculateSummary($driver, Carbon::today()->toDateString());

        return redirect()->route('driver.dashboard')
            ->with('success', 'Transfer sebesar Rp ' . number_format($transfer->claimed_amount, 0, ',', '.') . ' untuk toko "' . $transfer->store_name . '" berhasil diajukan!');
    }

    /**
     * Show form input faktur kredit.
     */
    public function createCredit(): View
    {
        $driver = $this->getDriver();
        $todayDate = Carbon::today()->toDateString();

        return view('driver.credit', compact('driver', 'todayDate'));
    }

    /**
     * Store credit delivery record.
     */
    public function storeCredit(Request $request): RedirectResponse
    {
        $driver = $this->getDriver();

        // Bersihkan input angka jika terformat
        $rawAmount = $request->input('amount');
        if (is_string($rawAmount)) {
            $cleaned = preg_replace('/[^0-9]/', '', $rawAmount);
            $request->merge(['amount' => $cleaned]);
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'invoice_photo' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'notes' => 'nullable|string|max:500',
        ], [
            'store_name.required' => 'Nama toko wajib diisi.',
            'amount.required' => 'Nominal kredit wajib diisi.',
            'amount.numeric' => 'Nominal kredit harus berupa angka valid.',
            'amount.min' => 'Nominal kredit minimal Rp 1.',
            'invoice_photo.required' => 'Foto bukti fisik faktur kredit WAJIB dilampirkan.',
            'invoice_photo.image' => 'Foto faktur harus berupa file gambar (JPG, PNG, WEBP).',
            'invoice_photo.max' => 'Ukuran foto faktur maksimal 5MB.',
        ]);

        $photoPath = $request->file('invoice_photo')->store('credit_invoices', 'public');

        $credit = CreditDelivery::create([
            'driver_id' => $driver->id,
            'date' => Carbon::today()->toDateString(),
            'store_name' => trim($request->input('store_name')),
            'amount' => $request->input('amount'),
            'invoice_photo_path' => $photoPath,
            'notes' => $request->input('notes'),
        ]);

        // Hitung ulang ringkasan settlement
        $this->calculationService->calculateSummary($driver, Carbon::today()->toDateString());

        return redirect()->route('driver.dashboard')
            ->with('success', 'Faktur kredit sebesar Rp ' . number_format($credit->amount, 0, ',', '.') . ' untuk toko "' . $credit->store_name . '" berhasil dicatat!');
    }

    /**
     * Riwayat harian input dan setoran sopir.
     */
    public function history(Request $request): View
    {
        $driver = $this->getDriver();

        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $summary = $this->calculationService->calculateSummary($driver, $selectedDate);

        // Ambil riwayat settlement 14 hari terakhir untuk driver ini
        $settlementHistory = DailySettlement::where('driver_id', $driver->id)
            ->orderBy('date', 'desc')
            ->limit(14)
            ->get();

        // Mutasi transaksi untuk tanggal yang dipilih
        $mutations = collect($summary['mutations']);

        $transfers = TransferPayment::where('driver_id', $driver->id)
            ->whereDate('date', $selectedDate)
            ->latest()
            ->get();

        $credits = CreditDelivery::where('driver_id', $driver->id)
            ->whereDate('date', $selectedDate)
            ->latest()
            ->get();

        return view('driver.history', compact('driver', 'selectedDate', 'summary', 'settlementHistory', 'mutations', 'transfers', 'credits'));
    }
}
