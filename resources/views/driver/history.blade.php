@extends('driver.layout')

@section('title', 'Riwayat Setoran')

@section('content')
<div class="space-y-3.5">

    <!-- Header & Filter Bar Ringkas -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 shadow-xs space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Riwayat Harian Sopir</h2>
                <p class="text-xs text-slate-500">Pilih tanggal untuk melihat rincian setoran</p>
            </div>
            <a href="{{ route('driver.dashboard') }}" class="text-xs text-slate-600 font-bold hover:text-slate-900 transition flex items-center space-x-1">
                <span>&larr;</span>
                <span>Beranda</span>
            </a>
        </div>

        <!-- Filter Pills & Compact Date Picker -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-2 pt-1 border-t border-slate-100">
            <div class="flex items-center space-x-1.5 p-1 bg-slate-100 rounded-xl">
                <button type="button" 
                        onclick="filterHistory('all', this)" 
                        class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold bg-slate-900 text-white shadow-2xs transition">
                    Semua
                </button>
                <button type="button" 
                        onclick="filterHistory('kurang_setor', this)" 
                        class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-200 transition">
                    Kurang
                </button>
                <button type="button" 
                        onclick="filterHistory('lunas', this)" 
                        class="filter-pill px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-200 transition">
                    Lunas
                </button>
            </div>

            <form method="GET" action="{{ route('driver.history') }}" class="flex items-center space-x-1.5">
                <input type="date" 
                       name="date" 
                       value="{{ $selectedDate }}" 
                       onchange="this.form.submit()"
                       class="px-2.5 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-900">
                <button type="submit" 
                        class="px-3 py-1.5 bg-slate-900 hover:bg-black text-white rounded-xl text-xs font-bold shadow-2xs transition flex items-center space-x-1 cursor-pointer">
                    <x-app-icon name="actions.search" class="w-3.5 h-3.5 text-white" />
                    <span>Cari</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Tanggal Terpilih (Akordion Utama Aktif) -->
    @php
        $status = $summary['status'];
        $diff = $summary['difference'];
        $statusClass = $status === 'lunas' ? 'lunas' : ($status === 'lebih_setor' ? 'lebih_setor' : 'kurang_setor');
    @endphp

    <div class="history-card bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden transition" data-status="{{ $statusClass }}">
        <!-- Accordion Header -->
        <div class="p-4 flex items-center justify-between cursor-pointer select-none bg-slate-50/50 hover:bg-slate-50 transition" onclick="toggleAccordion('detail-selected')">
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}
                    </h3>
                    <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">Aktif</span>
                </div>
                <p class="text-xs text-slate-500 font-mono mt-0.5">
                    Wajib: Rp {{ number_format($summary['target_cash'], 0, ',', '.') }} &bull; Setor: Rp {{ number_format($summary['actual_cash'], 0, ',', '.') }}
                </p>
            </div>

            <div class="flex items-center space-x-3 text-right">
                <div>
                    @if ($status === 'lunas')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                            Lunas
                        </span>
                        <p class="text-xs font-mono font-bold text-slate-500 mt-0.5">Rp 0</p>
                    @elseif ($status === 'lebih_setor')
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                            Lebih
                        </span>
                        <p class="text-xs font-mono font-bold text-blue-600 mt-0.5">+Rp {{ number_format(abs($diff), 0, ',', '.') }}</p>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                            Kurang
                        </span>
                        <p class="text-xs font-mono font-bold text-rose-600 mt-0.5">-Rp {{ number_format(abs($diff), 0, ',', '.') }}</p>
                    @endif
                </div>
                <svg id="chevron-detail-selected" class="w-4 h-4 text-slate-400 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>

        <!-- Accordion Body Detail -->
        <div id="detail-selected" class="p-4 border-t border-slate-100 bg-white space-y-3">
            <!-- 3 Metric Box Ringkas -->
            <div class="grid grid-cols-3 gap-2 text-center">
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/80">
                    <p class="text-[10px] text-slate-500 font-medium">Wajib Setor</p>
                    <p class="text-xs font-bold text-slate-900 font-mono mt-0.5">
                        Rp {{ number_format($summary['target_cash'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/80">
                    <p class="text-[10px] text-slate-500 font-medium">Disetor Kasir</p>
                    <p class="text-xs font-bold text-slate-900 font-mono mt-0.5">
                        Rp {{ number_format($summary['actual_cash'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-2 rounded-xl bg-slate-50 border border-slate-200/80">
                    <p class="text-[10px] text-slate-500 font-medium">Sisa / Selisih</p>
                    <p class="text-xs font-bold font-mono mt-0.5 {{ $diff > 0 ? 'text-rose-600' : ($diff < 0 ? 'text-blue-600' : 'text-emerald-600') }}">
                        {{ $diff < 0 ? '+' : ($diff > 0 ? '-' : '') }}Rp {{ number_format(abs($diff), 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Daftar Mutasi & Transaksi -->
            <div class="space-y-1.5 pt-1">
                <div class="flex items-center justify-between pb-1">
                    <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        Rincian Transaksi ({{ $mutations->count() }})
                    </span>
                </div>

                <div class="divide-y divide-slate-100 rounded-xl border border-slate-100 overflow-hidden">
                    @forelse ($mutations as $mutation)
                        <div class="p-2.5 flex items-center justify-between hover:bg-slate-50 transition bg-white">
                            <div class="flex items-center space-x-2.5 min-w-0 pr-2">
                                <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                                    @if ($mutation['type'] === 'delivery') <x-app-icon name="types.truck" class="w-3.5 h-3.5" />
                                    @elseif ($mutation['type'] === 'return') <x-app-icon name="types.return" class="w-3.5 h-3.5" />
                                    @elseif ($mutation['type'] === 'transfer') <x-app-icon name="types.bank" class="w-3.5 h-3.5 text-blue-600" />
                                    @elseif ($mutation['type'] === 'credit') <x-app-icon name="types.receipt" class="w-3.5 h-3.5 text-amber-600" />
                                    @else <x-app-icon name="types.cash" class="w-3.5 h-3.5 text-emerald-600" /> @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-slate-900 truncate">{{ $mutation['title'] }}</p>
                                    <p class="text-[10px] text-slate-500 truncate">{{ $mutation['description'] }}</p>
                                    @if (isset($mutation['rejection_reason']) && $mutation['rejection_reason'])
                                        <p class="text-[10px] text-rose-600 font-medium">Alasan: {{ $mutation['rejection_reason'] }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <p class="text-xs font-bold text-slate-900 font-mono">
                                    Rp {{ number_format($mutation['amount'], 0, ',', '.') }}
                                </p>
                                <span class="inline-block text-[10px] text-slate-600 font-semibold bg-slate-100 px-1.5 py-0.2 rounded mt-0.5">
                                    {{ $mutation['status_label'] }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-xs text-slate-400">
                            Tidak ada catatan transaksi pada tanggal ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Hari Sebelumnya (14 Hari Terakhir) -->
    <div class="space-y-2 pt-2">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center space-x-1.5">
                <x-app-icon name="types.calendar" class="w-3.5 h-3.5 text-slate-600" />
                <span>Riwayat Hari Sebelumnya</span>
            </h3>
            <span class="text-[11px] text-slate-400 font-medium">{{ $settlementHistory->count() }} Hari Tercatat</span>
        </div>

        <div class="space-y-2">
            @forelse ($settlementHistory as $history)
                @php
                    $hStatus = $history->status;
                    $hDiff = $history->difference;
                    $hClass = $hStatus === 'lunas' ? 'lunas' : ($hStatus === 'lebih_setor' ? 'lebih_setor' : 'kurang_setor');
                    $hId = 'history-' . $history->id;
                @endphp
                <div class="history-card bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden transition" data-status="{{ $hClass }}">
                    <div class="p-3.5 flex items-center justify-between cursor-pointer select-none hover:bg-slate-50 transition" onclick="toggleAccordion('{{ $hId }}')">
                        <div>
                            <p class="text-xs font-bold text-slate-900">
                                {{ $history->date->translatedFormat('l, d M Y') }}
                            </p>
                            <p class="text-[11px] text-slate-500 font-mono mt-0.5">
                                Wajib: Rp {{ number_format($history->target_cash, 0, ',', '.') }} &bull; Setor: Rp {{ number_format($history->actual_cash, 0, ',', '.') }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-3 text-right">
                            <div>
                                @if ($hStatus === 'lunas')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Lunas
                                    </span>
                                    <p class="text-xs font-mono font-bold text-slate-500 mt-0.5">Rp 0</p>
                                @elseif ($hStatus === 'lebih_setor')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-800">
                                        Lebih
                                    </span>
                                    <p class="text-xs font-mono font-bold text-blue-600 mt-0.5">+Rp {{ number_format(abs($hDiff), 0, ',', '.') }}</p>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">
                                        Kurang
                                    </span>
                                    <p class="text-xs font-mono font-bold text-rose-600 mt-0.5">-Rp {{ number_format(abs($hDiff), 0, ',', '.') }}</p>
                                @endif
                            </div>
                            <svg id="chevron-{{ $hId }}" class="w-4 h-4 text-slate-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Expandable Sub-Detail -->
                    <div id="{{ $hId }}" class="hidden p-3.5 border-t border-slate-100 bg-slate-50/70">
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="p-2 rounded-lg bg-white border border-slate-200/80">
                                <p class="text-[10px] text-slate-400 font-medium">Wajib Setor</p>
                                <p class="text-xs font-bold text-slate-900 font-mono mt-0.5">
                                    Rp {{ number_format($history->target_cash, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-white border border-slate-200/80">
                                <p class="text-[10px] text-slate-400 font-medium">Disetor Kasir</p>
                                <p class="text-xs font-bold text-slate-900 font-mono mt-0.5">
                                    Rp {{ number_format($history->actual_cash, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="p-2 rounded-lg bg-white border border-slate-200/80">
                                <p class="text-[10px] text-slate-400 font-medium">Selisih</p>
                                <p class="text-xs font-bold font-mono mt-0.5 {{ $hDiff > 0 ? 'text-rose-600' : ($hDiff < 0 ? 'text-blue-600' : 'text-emerald-600') }}">
                                    {{ $hDiff < 0 ? '+' : ($hDiff > 0 ? '-' : '') }}Rp {{ number_format(abs($hDiff), 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 text-right">
                            <a href="{{ route('driver.history', ['date' => $history->date->toDateString()]) }}" class="inline-flex items-center space-x-1 text-xs font-bold text-blue-600 hover:text-blue-800">
                                <span>Lihat Rincian Lengkap Hari Ini</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                    Belum ada riwayat hari sebelumnya.
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleAccordion(id) {
        const el = document.getElementById(id);
        const chevron = document.getElementById('chevron-' + id);
        if (!el) return;
        el.classList.toggle('hidden');
        if (chevron) {
            chevron.classList.toggle('rotate-180');
        }
    }

    function filterHistory(status, btn) {
        document.querySelectorAll('.filter-pill').forEach(b => {
            b.classList.remove('bg-slate-900', 'text-white', 'shadow-2xs');
            b.classList.add('text-slate-600', 'hover:bg-slate-200');
        });
        btn.classList.add('bg-slate-900', 'text-white', 'shadow-2xs');
        btn.classList.remove('text-slate-600', 'hover:bg-slate-200');

        document.querySelectorAll('.history-card').forEach(card => {
            if (status === 'all' || card.dataset.status === status) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }
</script>
@endpush
