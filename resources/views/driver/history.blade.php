@extends('driver.layout')

@section('title', 'Riwayat Input & Setoran')

@section('content')
<div class="space-y-4">

    <!-- Top Navigation Breadcrumb & Date Picker -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Riwayat Harian Sopir</h2>
                <p class="text-xs text-slate-500">Lihat data setoran & mutasi berdasarkan tanggal</p>
            </div>
            <a href="{{ route('driver.dashboard') }}" class="text-xs text-brand-600 font-semibold hover:underline">
                &larr; Dashboard
            </a>
        </div>

        <form method="GET" action="{{ route('driver.history') }}" class="flex items-center space-x-2 pt-1">
            <input type="date" 
                   name="date" 
                   value="{{ $selectedDate }}" 
                   class="flex-1 px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
            <button type="submit" 
                    class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-semibold shadow-sm transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Summary on Selected Date -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                Ringkasan: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
            </span>
            @php
                $status = $summary['status'];
            @endphp
            @if ($status === 'lunas')
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                    LUNAS
                </span>
            @elseif ($status === 'lebih_setor')
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">
                    LEBIH SETOR
                </span>
            @else
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                    KURANG SETOR
                </span>
            @endif
        </div>

        <div class="grid grid-cols-3 gap-2 text-center pt-1">
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] text-slate-500">Wajib Setor</p>
                <p class="text-xs sm:text-sm font-bold text-slate-800 mt-0.5">
                    Rp {{ number_format($summary['target_cash'], 0, ',', '.') }}
                </p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] text-slate-500">Disetor Kasir</p>
                <p class="text-xs sm:text-sm font-bold text-emerald-700 mt-0.5">
                    Rp {{ number_format($summary['actual_cash'], 0, ',', '.') }}
                </p>
            </div>
            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                <p class="text-[10px] text-slate-500">Selisih</p>
                <p class="text-xs sm:text-sm font-bold font-mono mt-0.5 {{ $summary['difference'] < 0 ? 'text-rose-600' : 'text-slate-800' }}">
                    {{ $summary['difference'] < 0 ? '-' : '' }}Rp {{ number_format(abs($summary['difference']), 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Mutasi Lengkap Hari Terpilih -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-xs sm:text-sm text-slate-800 flex items-center space-x-1.5">
                <i data-lucide="activity" class="w-4 h-4 text-brand-600"></i>
                <span>Mutasi Transaksi ({{ $mutations->count() }})</span>
            </h3>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($mutations as $mutation)
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
                                <p class="text-[10px] text-rose-600 font-medium mt-0.5">Alasan Tolak: {{ $mutation['rejection_reason'] }}</p>
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
                    Tidak ada mutasi pada tanggal ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Riwayat 14 Hari Terakhir -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-xs sm:text-sm text-slate-800 flex items-center space-x-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-slate-600"></i>
                <span>Riwayat Settlement Terakhir</span>
            </h3>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($settlementHistory as $history)
                <a href="{{ route('driver.history', ['date' => $history->date->toDateString()]) }}" 
                   class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition block">
                    <div>
                        <p class="text-xs font-bold text-slate-800">
                            {{ $history->date->translatedFormat('d M Y') }}
                        </p>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            Wajib: Rp {{ number_format($history->target_cash, 0, ',', '.') }} &bull; 
                            Setor: Rp {{ number_format($history->actual_cash, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="text-right">
                        @if ($history->status === 'lunas')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Lunas
                            </span>
                        @elseif ($history->status === 'lebih_setor')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800">
                                Lebih
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                Kurang
                            </span>
                        @endif
                        <p class="text-[10px] text-slate-500 font-mono mt-0.5">
                            {{ $history->difference < 0 ? '-' : '' }}Rp {{ number_format(abs($history->difference), 0, ',', '.') }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="p-6 text-center text-xs text-slate-400">
                    Belum ada riwayat settlement sebelumnya.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
