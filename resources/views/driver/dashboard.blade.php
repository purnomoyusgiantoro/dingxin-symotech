@extends('driver.layout')

@section('title', 'Beranda Sopir')

@section('content')
<div class="space-y-3.5">

    @php
        $diff = $summary['difference'];
        $status = $summary['status'];
        $isLunas = $status === 'lunas';
        $isLebih = $status === 'lebih_setor';
        $isKurang = $status === 'kurang_setor';

        $targetCash = $summary['target_cash'] > 0 ? $summary['target_cash'] : 1;
        $actualCash = max(0, $summary['actual_cash']);
        $progressPct = min(100, max(0, round(($actualCash / $targetCash) * 100)));
    @endphp

    <!-- 1. Hero Settlement Card (Clean Fintech Dark Gradient) -->
    <div class="relative overflow-hidden rounded-2xl p-4 sm:p-5 bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-md space-y-3.5">
        <!-- Radial Glow Decoration -->
        <div class="absolute -top-10 -right-10 w-36 h-36 bg-blue-500/15 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Status Header & Tanggal Operasional -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-800/80">
            <div class="flex items-center space-x-1.5 text-slate-300">
                <x-app-icon name="types.calendar" class="w-4 h-4 text-slate-400 shrink-0" />
                <span class="text-xs font-semibold">
                    {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </span>
            </div>
            <div>
                @if ($isLunas)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                        LUNAS / PAS
                    </span>
                @elseif ($isLebih)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">
                        LEBIH SETOR
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        KURANG SETOR
                    </span>
                @endif
            </div>
        </div>

        <!-- Nominal Wajib Setor / Sisa -->
        <div>
            <p class="text-xs text-slate-400 font-medium">Sisa Wajib Setor Tunai ke Kasir</p>
            <div class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white font-mono mt-0.5">
                @if ($diff < 0)
                    Lebih Rp {{ number_format(abs($diff), 0, ',', '.') }}
                @elseif ($diff > 0)
                    Rp {{ number_format($diff, 0, ',', '.') }}
                @else
                    Rp 0 (Lunas)
                @endif
            </div>
        </div>

        <!-- Progress Bar Pelunasan -->
        <div class="space-y-1">
            <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $progressPct }}%;"></div>
            </div>
        </div>

        <!-- Rincian Wajib vs Sudah Disetor -->
        <div class="grid grid-cols-2 gap-3 pt-2.5 border-t border-slate-800/80">
            <div>
                <p class="text-[11px] text-slate-400 font-medium">Total Wajib Setor</p>
                <p class="text-sm sm:text-base font-bold font-mono text-slate-100 mt-0.5">
                    Rp {{ number_format($summary['target_cash'], 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-[11px] text-slate-400 font-medium">Sudah Disetor Fisik</p>
                <p class="text-sm sm:text-base font-bold font-mono text-slate-100 mt-0.5">
                    Rp {{ number_format($summary['actual_cash'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- 2. Rincian Operasional Bergaya Struk (Receipt List) -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/90 shadow-xs space-y-2.5">
        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
            <h3 class="font-bold text-sm text-slate-900">Rincian Operasional</h3>
            <span class="text-xs text-slate-600 font-semibold font-mono bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200/60">
                Net Bawaan: Rp {{ number_format($summary['net_carried'], 0, ',', '.') }}
            </span>
        </div>

        <div class="space-y-2 text-xs divide-y divide-dashed divide-slate-100">
            <div class="flex items-center justify-between pt-1">
                <span class="flex items-center space-x-2 text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                    <span>Barang Dibawa</span>
                </span>
                <span class="font-mono font-bold text-slate-900">
                    Rp {{ number_format($summary['amount_carried'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex items-center justify-between pt-2">
                <span class="flex items-center space-x-2 text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-rose-500 shrink-0"></span>
                    <span>Barang Kembali / Retur</span>
                </span>
                <span class="font-mono font-bold text-rose-600">
                    - Rp {{ number_format($summary['amount_returned'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex items-center justify-between pt-2">
                <span class="flex items-center space-x-2 text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>Transfer Toko (Sah)</span>
                </span>
                <span class="font-mono font-bold text-slate-900">
                    Rp {{ number_format($summary['transfer_approved'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex items-center justify-between pt-2">
                <span class="flex items-center space-x-2 text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-amber-500 shrink-0"></span>
                    <span>Nota Bon / Kredit</span>
                </span>
                <span class="font-mono font-bold text-slate-900">
                    Rp {{ number_format($summary['amount_credit'], 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- 3. Catatan Transaksi Hari Ini -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900">Catatan Hari Ini</h3>
            <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-2.5 py-0.5 rounded-full">
                {{ $todayTransfers->count() + $todayCredits->count() }} Data
            </span>
        </div>

        @if ($todayTransfers->isEmpty() && $todayCredits->isEmpty())
            <!-- Empty State Ringkas & Kontekstual -->
            <div class="p-6 text-center space-y-2">
                <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                    <x-app-icon name="types.inbox" class="w-5 h-5 text-slate-400" />
                </div>
                <div class="text-xs font-bold text-slate-700">Belum ada catatan hari ini</div>
                <p class="text-[11px] text-slate-500 max-w-xs mx-auto">Catat bukti transfer atau nota bon toko dari tab Catat Setoran.</p>
                <div class="grid grid-cols-2 gap-2 pt-2 max-w-xs mx-auto">
                    <a href="{{ route('driver.transfer.create') }}" class="py-2 px-3 bg-slate-900 text-white rounded-xl text-xs font-bold shadow-2xs hover:bg-black transition text-center">
                        + Catat Transfer
                    </a>
                    <a href="{{ route('driver.credit.create') }}" class="py-2 px-3 bg-slate-100 text-slate-900 border border-slate-200 rounded-xl text-xs font-bold hover:bg-slate-200 transition text-center">
                        + Catat Bon
                    </a>
                </div>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                <!-- Daftar Transfer -->
                @foreach ($todayTransfers as $tf)
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center space-x-3 min-w-0 pr-2">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                                <x-app-icon name="types.bank" class="w-4 h-4 text-blue-600" />
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 truncate block">{{ $tf->store_name }}</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Transfer BCA &bull; {{ $tf->created_at->format('H:i') }} WIB
                                </p>
                                @if ($tf->status === 'rejected' && $tf->rejection_reason)
                                    <p class="text-[11px] text-rose-600 font-medium mt-0.5">Alasan tolak: {{ $tf->rejection_reason }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs sm:text-sm font-bold text-slate-900 font-mono">
                                Rp {{ number_format($tf->claimed_amount, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center justify-end space-x-1.5 mt-1">
                                @if ($tf->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Disetujui
                                    </span>
                                @elseif ($tf->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Menunggu Kasir
                                    </span>
                                @endif

                                @if ($tf->proof_image_path)
                                    <a href="{{ asset('storage/' . $tf->proof_image_path) }}" target="_blank" class="p-1 text-slate-500 hover:text-slate-900" title="Lihat Foto Bukti">
                                        <x-app-icon name="actions.eye" class="w-3.5 h-3.5" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Daftar Bon / Kredit -->
                @foreach ($todayCredits as $cr)
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="flex items-center space-x-3 min-w-0 pr-2">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0 border border-amber-100">
                                <x-app-icon name="types.receipt" class="w-4 h-4 text-amber-600" />
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-slate-900 truncate block">{{ $cr->store_name }}</span>
                                <p class="text-[11px] text-slate-500 mt-0.5">
                                    Bon / Kredit &bull; {{ $cr->created_at->format('H:i') }} WIB
                                </p>
                                @if ($cr->notes)
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ $cr->notes }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-xs sm:text-sm font-bold text-slate-900 font-mono">
                                Rp {{ number_format($cr->amount, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center justify-end space-x-1.5 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    Tercatat
                                </span>
                                @if ($cr->invoice_photo_path)
                                    <a href="{{ asset('storage/' . $cr->invoice_photo_path) }}" target="_blank" class="p-1 text-slate-500 hover:text-slate-900" title="Lihat Foto Faktur">
                                        <x-app-icon name="actions.eye" class="w-3.5 h-3.5" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
