<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Bar Kontrol & Filter Tanggal -->
        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="p-2 rounded-lg bg-primary-50 dark:bg-primary-950 text-primary-600 dark:text-primary-400">
                    <x-filament::icon icon="heroicon-o-calendar-days" class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Pilih Tanggal Operasional</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Menampilkan rekapitulasi setoran armada harian</p>
                </div>
            </div>

            <div class="flex items-center space-x-3">
                <input type="date" 
                       wire:model.live="selectedDate" 
                       class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent transition" />

                <button type="button" 
                        wire:click="$set('selectedDate', '{{ now()->format('Y-m-d') }}')" 
                        class="px-3 py-2 text-xs font-medium rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 transition">
                    Hari Ini
                </button>
            </div>
        </div>

        @php
            $data = $this->settlementData;
            $summary = $data['summary_all'];
            $drivers = $data['drivers'];
        @endphp

        <!-- 3 Kartu Ringkasan Eksekutif (Stats Overview) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total Wajib Setor -->
            <div class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Wajib Setor All Sopir</p>
                    <p class="text-2xl font-black text-gray-900 dark:text-white mt-1">
                        Rp {{ number_format($summary['total_wajib_setor'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Dari {{ $summary['total_drivers'] }} sopir aktif</p>
                </div>
                <div class="p-3 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 rounded-xl">
                    <x-filament::icon icon="heroicon-o-banknotes" class="w-8 h-8" />
                </div>
            </div>

            <!-- Total Sudah Disetor -->
            <div class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Uang Fisik Diterima Kasir</p>
                    <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                        Rp {{ number_format($summary['total_sudah_setor'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Total uang cash fisik masuk</p>
                </div>
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 rounded-xl">
                    <x-filament::icon icon="heroicon-o-check-circle" class="w-8 h-8" />
                </div>
            </div>

            <!-- Total Selisih Setor -->
            <div class="p-5 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Selisih Akhir</p>
                    <p class="text-2xl font-black {{ $summary['total_selisih_setor'] > 0 ? 'text-rose-600 dark:text-rose-400' : ($summary['total_selisih_setor'] < 0 ? 'text-cyan-600 dark:text-cyan-400' : 'text-emerald-600 dark:text-emerald-400') }} mt-1">
                        @if ($summary['total_selisih_setor'] > 0)
                            Kurang: Rp {{ number_format($summary['total_selisih_setor'], 0, ',', '.') }}
                        @elseif ($summary['total_selisih_setor'] < 0)
                            Lebih: Rp {{ number_format(abs($summary['total_selisih_setor']), 0, ',', '.') }}
                        @else
                            Rp 0 (Pas/Lunas)
                        @endif
                    </p>
                    <p class="text-xs text-gray-400 mt-1">Wajib Setor - Uang Diterima</p>
                </div>
                <div class="p-3 {{ $summary['total_selisih_setor'] > 0 ? 'bg-rose-50 dark:bg-rose-950 text-rose-600 dark:text-rose-400' : 'bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400' }} rounded-xl">
                    <x-filament::icon icon="heroicon-o-scale" class="w-8 h-8" />
                </div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Lengkap Seluruh Sopir -->
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Wajib Setor, Sudah Setor & Selisih Per Sopir</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold uppercase tracking-wider">
                            <th class="py-3 px-3">No</th>
                            <th class="py-3 px-3">Kode Sopir</th>
                            <th class="py-3 px-3">Nama Sopir</th>
                            <th class="py-3 px-3 text-right">Bawaan (Rp)</th>
                            <th class="py-3 px-3 text-right">Retur (Rp)</th>
                            <th class="py-3 px-3 text-right">Transfer Confirmed (Rp)</th>
                            <th class="py-3 px-3 text-right">Kredit Toko (Rp)</th>
                            <th class="py-3 px-3 text-right bg-blue-50/50 dark:bg-blue-950/20 font-extrabold text-blue-900 dark:text-blue-300">Wajib Setor (Rp)</th>
                            <th class="py-3 px-3 text-right bg-emerald-50/50 dark:bg-emerald-950/20 font-extrabold text-emerald-900 dark:text-emerald-300">Sudah Setor (Rp)</th>
                            <th class="py-3 px-3 text-right bg-amber-50/50 dark:bg-amber-950/20 font-extrabold text-gray-900 dark:text-white">Selisih Setor (Rp)</th>
                            <th class="py-3 px-3 text-center">Status</th>
                            <th class="py-3 px-3 text-center">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                        @forelse ($drivers as $idx => $d)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                <td class="py-3 px-3 text-gray-400 font-mono">{{ $idx + 1 }}</td>
                                <td class="py-3 px-3 font-mono font-bold text-gray-900 dark:text-white">
                                    <span class="px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600">
                                        {{ $d['driver_code'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 font-medium whitespace-nowrap">{{ $d['driver_name'] }}</td>
                                <td class="py-3 px-3 text-right font-mono">{{ number_format($d['amount_carried'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-right font-mono text-rose-600 dark:text-rose-400">
                                    {{ $d['amount_returned'] > 0 ? '-' . number_format($d['amount_returned'], 0, ',', '.') : '0' }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-blue-600 dark:text-blue-400">
                                    {{ number_format($d['transfer_approved'], 0, ',', '.') }}
                                    @if ($d['transfer_pending'] > 0)
                                        <span class="block text-[10px] text-amber-500 font-sans" title="Menunggu verifikasi kasir">(Pnd: {{ number_format($d['transfer_pending'], 0, ',', '.') }})</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-amber-600 dark:text-amber-400">
                                    {{ number_format($d['amount_credit'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold bg-blue-50/40 dark:bg-blue-950/20 text-blue-900 dark:text-blue-200">
                                    {{ number_format($d['wajib_setor'], 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-bold bg-emerald-50/40 dark:bg-emerald-950/20 text-emerald-900 dark:text-emerald-200">
                                    {{ number_format($d['sudah_setor'], 0, ',', '.') }}
                                    @if ($d['cash_deposits_count'] > 1)
                                        <span class="block text-[10px] text-emerald-600 dark:text-emerald-400 font-sans">({{ $d['cash_deposits_count'] }}x setor)</span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-right font-mono font-extrabold bg-amber-50/40 dark:bg-amber-950/20
                                    {{ $d['selisih_setor'] > 0 ? 'text-rose-600 dark:text-rose-400' : ($d['selisih_setor'] < 0 ? 'text-cyan-600 dark:text-cyan-400' : 'text-emerald-600 dark:text-emerald-400') }}">
                                    @if ($d['selisih_setor'] > 0)
                                        -{{ number_format($d['selisih_setor'], 0, ',', '.') }}
                                    @elseif ($d['selisih_setor'] < 0)
                                        +{{ number_format(abs($d['selisih_setor']), 0, ',', '.') }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    @if ($d['status'] === 'lunas')
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">
                                            Lunas
                                        </span>
                                    @elseif ($d['status'] === 'lebih_setor')
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-900/60 dark:text-cyan-300">
                                            Lebih
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 text-[11px] font-bold rounded-full bg-rose-100 text-rose-800 dark:bg-rose-900/60 dark:text-rose-300">
                                            Kurang
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-3 text-center whitespace-nowrap">
                                    <button type="button" 
                                            wire:click="openDepositModal({{ $d['driver_id'] }}, '{{ addslashes($d['driver_name']) }} ({{ $d['driver_code'] }})')"
                                            class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded bg-primary-600 hover:bg-primary-700 text-white shadow-sm transition">
                                        <x-filament::icon icon="heroicon-m-plus" class="w-3.5 h-3.5 mr-1" />
                                        Input Setor
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="py-8 text-center text-gray-400">Tidak ada sopir aktif yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- BARIS ALL STOR (FOOTER TOTAL AKUMULASI LENGKAP) -->
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-gray-900 border-t-2 border-gray-300 dark:border-gray-600 font-black text-gray-900 dark:text-white text-xs">
                            <td colspan="3" class="py-4 px-3 uppercase tracking-wider font-extrabold text-sm">
                                🏁 BARIS ALL SETOR (TOTAL ARMADA)
                            </td>
                            <td class="py-4 px-3 text-right font-mono text-sm">
                                Rp {{ number_format($summary['total_carried'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-3 text-right font-mono text-sm text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($summary['total_returned'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-3 text-right font-mono text-sm text-blue-600 dark:text-blue-400">
                                Rp {{ number_format($summary['total_transfer_approved'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-3 text-right font-mono text-sm text-amber-600 dark:text-amber-400">
                                Rp {{ number_format($summary['total_credit'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-3 text-right font-mono text-sm bg-blue-100 dark:bg-blue-950 font-black text-blue-950 dark:text-blue-200">
                                Rp {{ number_format($summary['total_wajib_setor'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-3 text-right font-mono text-sm bg-emerald-100 dark:bg-emerald-950 font-black text-emerald-950 dark:text-emerald-200">
                                Rp {{ number_format($summary['total_sudah_setor'], 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-3 text-right font-mono text-sm bg-amber-100 dark:bg-amber-950 font-black
                                {{ $summary['total_selisih_setor'] > 0 ? 'text-rose-700 dark:text-rose-400' : ($summary['total_selisih_setor'] < 0 ? 'text-cyan-700 dark:text-cyan-400' : 'text-emerald-700 dark:text-emerald-400') }}">
                                @if ($summary['total_selisih_setor'] > 0)
                                    -Rp {{ number_format($summary['total_selisih_setor'], 0, ',', '.') }}
                                @elseif ($summary['total_selisih_setor'] < 0)
                                    +Rp {{ number_format(abs($summary['total_selisih_setor']), 0, ',', '.') }}
                                @else
                                    Rp 0 (Pas)
                                @endif
                            </td>
                            <td class="py-4 px-3 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-full 
                                    {{ $summary['status'] === 'lunas' ? 'bg-emerald-200 text-emerald-900' : 'bg-rose-200 text-rose-900' }}">
                                    {{ strtoupper($summary['status']) }}
                                </span>
                            </td>
                            <td class="py-4 px-3 text-center text-gray-400 text-[10px]">
                                Rekap All
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>

    <!-- Modal Input Setoran Tunai Kasir -->
    <x-filament::modal id="deposit-modal" width="md">
        <x-slot name="heading">
            Input Setoran Tunai Kasir
        </x-slot>

        <x-slot name="description">
            Catat penerimaan uang tunai fisik dari sopir armada. Mendukung pencatatan bertahap (multi-setor).
        </x-slot>

        <form wire:submit="saveDeposit" class="space-y-4">
            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500">Sopir:</p>
                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $selectedDriverName }}</p>
                <p class="text-xs text-gray-500 mt-1">Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Nominal Uang Tunai Diterima (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm font-bold">Rp</span>
                    <input type="number" 
                           step="any"
                           wire:model="depositAmount" 
                           placeholder="5000000" 
                           class="w-full pl-10 pr-3 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white font-mono font-bold focus:ring-2 focus:ring-primary-500 focus:border-transparent" 
                           required />
                </div>
                @error('depositAmount') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Catatan Kasir (Opsional)
                </label>
                <input type="text" 
                       wire:model="depositNotes" 
                       placeholder="Contoh: Setoran uang tunai tahap 1" 
                       class="w-full px-3 py-2 text-sm bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" />
            </div>

            <div class="flex items-center justify-end space-x-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                <x-filament::button color="gray" wire:click="$dispatch('close-modal', { id: 'deposit-modal' })">
                    Batal
                </x-filament::button>

                <x-filament::button type="submit" color="success">
                    Simpan Setoran Tunai
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>
</x-filament-panels::page>
