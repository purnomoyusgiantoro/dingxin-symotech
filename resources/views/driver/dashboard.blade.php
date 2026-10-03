@extends('driver.layout')

@section('title', 'Dashboard Sopir')

@section('content')
<div class="space-y-4">

    <!-- Driver Info Banner -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-4 text-white shadow-lg border border-slate-700/50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-slate-400 font-medium">Armada Aktif &bull; Area {{ $driver->area_code }}</p>
                <h2 class="text-lg font-bold tracking-tight text-white flex items-center space-x-1.5">
                    <span>{{ $driver->user->name }}</span>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-brand-500/30 text-brand-300 font-mono border border-brand-500/40">
                        {{ $driver->driver_code }}
                    </span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Plat: <span class="text-slate-200 font-medium">{{ $driver->plate_number ?? '-' }}</span> &bull; 
                    Saldo Kumulatif: <span class="text-slate-200 font-medium">Rp {{ number_format($driver->cumulative_balance, 0, ',', '.') }}</span>
                </p>
            </div>
            <div class="text-right">
                <span class="inline-block text-[11px] bg-slate-700/80 px-2.5 py-1 rounded-lg font-medium text-slate-300">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d M Y') }}
                </span>
            </div>
        </div>
    </div>

    <!-- Hero Settlement Status Card -->
    @php
        $diff = $summary['difference'];
        $status = $summary['status'];
        $isLunas = $status === 'lunas';
        $isLebih = $status === 'lebih_setor';
        $isKurang = $status === 'kurang_setor';
    @endphp

    <div class="rounded-2xl p-5 shadow-lg border text-white transition-all 
        {{ $isLunas ? 'bg-gradient-to-br from-emerald-600 to-teal-700 border-emerald-500 shadow-emerald-900/20' : '' }}
        {{ $isLebih ? 'bg-gradient-to-br from-cyan-600 to-blue-700 border-cyan-500 shadow-cyan-900/20' : '' }}
        {{ $isKurang ? 'bg-gradient-to-br from-rose-600 to-amber-700 border-rose-500 shadow-rose-900/20' : '' }}">
        
        <div class="flex items-center justify-between pb-3 border-b border-white/15">
            <span class="text-xs font-semibold uppercase tracking-wider text-white/90 flex items-center space-x-1.5">
                <i data-lucide="{{ $isKurang ? 'alert-circle' : 'check-circle-2' }}" class="w-4 h-4"></i>
                <span>Status Setoran Hari Ini</span>
            </span>

            @if ($isLunas)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white border border-white/30">
                    LUNAS / PAS
                </span>
            @elseif ($isLebih)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white border border-white/30">
                    LEBIH SETOR
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-white/20 text-white border border-white/30 animate-pulse">
                    KURANG SETOR
                </span>
            @endif
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3">
            <div>
                <p class="text-[11px] text-white/80 font-medium">Wajib Setor Kasir</p>
                <p class="text-lg sm:text-xl font-extrabold tracking-tight mt-0.5">
                    Rp {{ number_format($summary['target_cash'], 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-[11px] text-white/80 font-medium">Sudah Disetor (Fisik)</p>
                <p class="text-lg sm:text-xl font-extrabold tracking-tight mt-0.5">
                    Rp {{ number_format($summary['actual_cash'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-between">
            <span class="text-xs font-medium text-white/90">
                {{ $isKurang ? 'Kekurangan Setoran:' : ($isLebih ? 'Kelebihan Setoran:' : 'Selisih Setor:') }}
            </span>
            <span class="text-base sm:text-lg font-black font-mono">
                @if ($diff < 0)
                    - Rp {{ number_format(abs($diff), 0, ',', '.') }}
                @elseif ($diff > 0)
                    + Rp {{ number_format($diff, 0, ',', '.') }}
                @else
                    Rp 0 (Pas)
                @endif
            </span>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('driver.transfer.create') }}" 
           class="flex items-center justify-center space-x-2 py-3.5 px-4 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-semibold text-sm rounded-xl shadow-md transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>+ Input Transfer</span>
        </a>
        <a href="{{ route('driver.credit.create') }}" 
           class="flex items-center justify-center space-x-2 py-3.5 px-4 bg-slate-800 hover:bg-slate-900 active:scale-95 text-white font-semibold text-sm rounded-xl shadow-md transition border border-slate-700">
            <i data-lucide="file-plus-2" class="w-4 h-4 text-amber-400"></i>
            <span>+ Input Kredit</span>
        </a>
    </div>

    <!-- Breakdown Financial Cards (4 Cards Grid) -->
    <div class="grid grid-cols-2 gap-3">
        <!-- Barang Dibawa -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-medium text-slate-500">Barang Dibawa</span>
                <div class="p-1 rounded-md bg-blue-50 text-blue-600">
                    <i data-lucide="package" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-slate-900">
                Rp {{ number_format($summary['amount_carried'], 0, ',', '.') }}
            </p>
            <p class="text-[10px] text-slate-400 mt-0.5">Total muatan rute</p>
        </div>

        <!-- Barang Kembali -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-medium text-slate-500">Barang Retur</span>
                <div class="p-1 rounded-md bg-rose-50 text-rose-600">
                    <i data-lucide="corner-up-left" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-slate-900">
                Rp {{ number_format($summary['amount_returned'], 0, ',', '.') }}
            </p>
            <p class="text-[10px] text-slate-400 mt-0.5">Pengurang muatan</p>
        </div>

        <!-- Transfer Toko -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-medium text-slate-500">Transfer Toko</span>
                <div class="p-1 rounded-md bg-emerald-50 text-emerald-600">
                    <i data-lucide="arrow-right-left" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-slate-900">
                Rp {{ number_format($summary['transfer_approved'], 0, ',', '.') }}
            </p>
            <div class="flex items-center justify-between text-[10px] text-slate-500 mt-0.5">
                <span>Disetujui</span>
                @if ($summary['transfer_pending'] > 0)
                    <span class="text-amber-600 font-semibold" title="Menunggu verifikasi">
                        (Pending: {{ number_format($summary['transfer_pending'], 0, ',', '.') }})
                    </span>
                @endif
            </div>
        </div>

        <!-- Faktur Kredit -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-sm">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[11px] font-medium text-slate-500">Faktur Kredit</span>
                <div class="p-1 rounded-md bg-amber-50 text-amber-600">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-slate-900">
                Rp {{ number_format($summary['amount_credit'], 0, ',', '.') }}
            </p>
            <p class="text-[10px] text-slate-400 mt-0.5">Penjualan kredit toko</p>
        </div>
    </div>

    <!-- Riwayat Input Sopir Hari Ini (Transfer & Kredit) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="clock" class="w-4 h-4 text-brand-600"></i>
                <h3 class="font-bold text-sm text-slate-800">Riwayat Input Hari Ini</h3>
            </div>
            <span class="text-xs text-slate-400">
                {{ $todayTransfers->count() + $todayCredits->count() }} Transaksi
            </span>
        </div>

        <div class="divide-y divide-slate-100">
            @if ($todayTransfers->isEmpty() && $todayCredits->isEmpty())
                <div class="p-8 text-center text-slate-400 space-y-2">
                    <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300"></i>
                    <p class="text-xs">Belum ada input transfer atau kredit hari ini.</p>
                    <div class="flex justify-center space-x-2 pt-1">
                        <a href="{{ route('driver.transfer.create') }}" class="text-xs text-brand-600 font-semibold hover:underline">
                            + Input Transfer
                        </a>
                        <span class="text-slate-300">&bull;</span>
                        <a href="{{ route('driver.credit.create') }}" class="text-xs text-amber-600 font-semibold hover:underline">
                            + Input Kredit
                        </a>
                    </div>
                </div>
            @else

                <!-- Transfer Items -->
                @foreach ($todayTransfers as $tf)
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-xs font-semibold text-slate-800">{{ $tf->store_name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">&bull; Transfer</span>
                                </div>
                                <p class="text-sm font-bold text-slate-900 mt-0.5">
                                    Rp {{ number_format($tf->claimed_amount, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    {{ $tf->created_at->format('H:i') }} WIB 
                                    @if ($tf->notes) &bull; {{ Str::limit($tf->notes, 30) }} @endif
                                </p>

                                @if ($tf->status === 'rejected' && $tf->rejection_reason)
                                    <p class="text-[10px] text-rose-600 font-medium mt-0.5">
                                        Alasan tolak: {{ $tf->rejection_reason }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="text-right flex flex-col items-end space-y-1">
                            @if ($tf->status === 'approved')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    Approved
                                </span>
                            @elseif ($tf->status === 'rejected')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                    Rejected
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                    Pending
                                </span>
                            @endif

                            @if ($tf->proof_image_path)
                                <a href="{{ asset('storage/' . $tf->proof_image_path) }}" target="_blank" class="text-[10px] text-brand-600 hover:underline flex items-center space-x-0.5">
                                    <i data-lucide="image" class="w-3 h-3"></i>
                                    <span>Bukti</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Credit Items -->
                @foreach ($todayCredits as $cr)
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-start space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-xs font-semibold text-slate-800">{{ $cr->store_name }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">&bull; Kredit</span>
                                </div>
                                <p class="text-sm font-bold text-slate-900 mt-0.5">
                                    Rp {{ number_format($cr->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-[10px] text-slate-400">
                                    {{ $cr->created_at->format('H:i') }} WIB 
                                    @if ($cr->notes) &bull; {{ Str::limit($cr->notes, 30) }} @endif
                                </p>
                            </div>
                        </div>

                        <div class="text-right flex flex-col items-end space-y-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                Tercatat
                            </span>

                            @if ($cr->invoice_photo_path)
                                <a href="{{ asset('storage/' . $cr->invoice_photo_path) }}" target="_blank" class="text-[10px] text-brand-600 hover:underline flex items-center space-x-0.5">
                                    <i data-lucide="image" class="w-3 h-3"></i>
                                    <span>Faktur</span>
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach

            @endif
        </div>
    </div>

    <!-- Mutasi Transaksi Log Lengkap Hari Ini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i data-lucide="list" class="w-4 h-4 text-slate-600"></i>
                <h3 class="font-bold text-sm text-slate-800">Semua Mutasi Hari Ini</h3>
            </div>
            <a href="{{ route('driver.history') }}" class="text-xs text-brand-600 hover:underline font-medium">
                Lihat Lengkap &rarr;
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($summary['mutations'] as $mutation)
                <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5
                            @if ($mutation['type'] === 'delivery') bg-indigo-50 text-indigo-600
                            @elseif ($mutation['type'] === 'return') bg-rose-50 text-rose-600
                            @elseif ($mutation['type'] === 'transfer') bg-blue-50 text-blue-600
                            @elseif ($mutation['type'] === 'credit') bg-amber-50 text-amber-600
                            @else bg-emerald-50 text-emerald-600 @endif">
                            @if ($mutation['type'] === 'delivery') <i data-lucide="truck" class="w-4 h-4"></i>
                            @elseif ($mutation['type'] === 'return') <i data-lucide="corner-up-left" class="w-4 h-4"></i>
                            @elseif ($mutation['type'] === 'transfer') <i data-lucide="arrow-right-left" class="w-4 h-4"></i>
                            @elseif ($mutation['type'] === 'credit') <i data-lucide="file-text" class="w-4 h-4"></i>
                            @else <i data-lucide="banknote" class="w-4 h-4"></i> @endif
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-800">{{ $mutation['title'] }}</p>
                            <p class="text-[10px] text-slate-400">{{ $mutation['description'] }}</p>
                            @if (isset($mutation['rejection_reason']) && $mutation['rejection_reason'])
                                <p class="text-[10px] text-rose-600 font-medium">Alasan: {{ $mutation['rejection_reason'] }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-xs font-bold 
                            @if ($mutation['flow'] === 'in') text-slate-900
                            @elseif ($mutation['flow'] === 'paid') text-emerald-600
                            @else text-blue-600 @endif">
                            Rp {{ number_format($mutation['amount'], 0, ',', '.') }}
                        </p>
                        <span class="text-[10px] text-slate-400 font-medium">
                            {{ $mutation['status_label'] }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">
                    Belum ada catatan mutasi untuk hari ini.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
