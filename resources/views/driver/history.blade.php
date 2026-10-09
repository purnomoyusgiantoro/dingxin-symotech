@extends('driver.layout')

@section('title', 'Riwayat Setoran')

@section('content')
<div class="space-y-4">

    <!-- Pilih Tanggal Riwayat -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Riwayat Harian Sopir</h2>
                <p class="text-xs text-slate-500">Pilih tanggal untuk melihat rincian setoran</p>
            </div>
            <a href="{{ route('driver.dashboard') }}" class="text-xs text-slate-700 font-bold hover:text-slate-900 transition">
                &larr; Beranda
            </a>
        </div>

        <form method="GET" action="{{ route('driver.history') }}" class="flex items-center space-x-2 pt-0.5">
            <input type="date" 
                   name="date" 
                   value="{{ $selectedDate }}" 
                   class="flex-1 px-3 py-2.5 bg-white border border-slate-300 rounded-xl text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
            <button type="submit" 
                    class="px-5 py-2.5 bg-slate-900 hover:bg-black text-white rounded-xl text-sm font-bold shadow-sm transition inline-flex items-center space-x-1.5">
                <x-app-icon name="actions.search" class="w-4 h-4 text-white" />
                <span>Cari</span>
            </button>
        </form>
    </div>

    <!-- Ringkasan Pada Tanggal Terpilih -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">
                Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
            </span>
            @php
                $status = $summary['status'];
            @endphp
            @if ($status === 'lunas')
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    LUNAS
                </span>
            @elseif ($status === 'lebih_setor')
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                    LEBIH SETOR
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                    KURANG SETOR
                </span>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-2 text-center pt-0.5">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Wajib Setor</p>
                <p class="text-xs sm:text-sm font-bold text-slate-900 mt-1 font-mono">
                    Rp {{ number_format($summary['target_cash'], 0, ',', '.') }}
                </p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Disetor Kasir</p>
                <p class="text-xs sm:text-sm font-bold text-slate-900 mt-1 font-mono">
                    Rp {{ number_format($summary['actual_cash'], 0, ',', '.') }}
                </p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Sisa / Selisih</p>
                <p class="text-xs sm:text-sm font-bold font-mono mt-1 text-slate-900">
                    {{ $summary['difference'] < 0 ? '-' : '' }}Rp {{ number_format(abs($summary['difference']), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Mutasi & Transaksi Tanggal Terpilih -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900 flex items-center space-x-1.5">
                <x-app-icon name="types.calendar" class="w-4 h-4 text-slate-900" />
                <span>Daftar Transaksi ({{ $mutations->count() }})</span>
            </h3>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($mutations as $mutation)
                <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                    <div class="flex items-start space-x-3 min-w-0 pr-2">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-900 flex items-center justify-center shrink-0 mt-0.5">
                            @if ($mutation['type'] === 'delivery') <x-app-icon name="types.truck" class="w-4 h-4" />
                            @elseif ($mutation['type'] === 'return') <x-app-icon name="types.return" class="w-4 h-4" />
                            @elseif ($mutation['type'] === 'transfer') <x-app-icon name="types.bank" class="w-4 h-4" />
                            @elseif ($mutation['type'] === 'credit') <x-app-icon name="types.receipt" class="w-4 h-4" />
                            @else <x-app-icon name="types.cash" class="w-4 h-4" /> @endif
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold text-slate-900 truncate">{{ $mutation['title'] }}</p>
                            <p class="text-[11px] text-slate-500 truncate">{{ $mutation['description'] }}</p>
                            @if (isset($mutation['rejection_reason']) && $mutation['rejection_reason'])
                                <p class="text-xs text-rose-600 font-medium mt-0.5">Alasan: {{ $mutation['rejection_reason'] }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <p class="text-xs sm:text-sm font-bold text-slate-900 font-mono">
                            Rp {{ number_format($mutation['amount'], 0, ',', '.') }}
                        </p>
                        <span class="inline-block text-[11px] text-slate-600 font-semibold bg-slate-100 px-2 py-0.5 rounded mt-0.5">
                            {{ $mutation['status_label'] }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">
                    Tidak ada catatan transaksi pada tanggal ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Riwayat 14 Hari Terakhir -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900 flex items-center space-x-1.5">
                <x-app-icon name="types.calendar" class="w-4 h-4 text-slate-900" />
                <span>Riwayat Hari Sebelumnya</span>
            </h3>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($settlementHistory as $history)
                <a href="{{ route('driver.history', ['date' => $history->date->toDateString()]) }}" 
                   class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition block">
                    <div>
                        <p class="text-xs font-bold text-slate-900">
                            {{ $history->date->translatedFormat('d M Y') }}
                        </p>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Wajib: Rp {{ number_format($history->target_cash, 0, ',', '.') }} &bull; 
                            Setor: Rp {{ number_format($history->actual_cash, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="text-right">
                        @if ($history->status === 'lunas')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                Lunas
                            </span>
                        @elseif ($history->status === 'lebih_setor')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800">
                                Lebih
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                Kurang
                            </span>
                        @endif
                        <p class="text-xs text-slate-600 font-mono mt-0.5 font-bold">
                            {{ $history->difference < 0 ? '-' : '' }}Rp {{ number_format(abs($history->difference), 0, ',', '.') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">
                    Belum ada riwayat sebelumnya.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
