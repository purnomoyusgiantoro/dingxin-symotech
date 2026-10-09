<x-filament-panels::page>
    <style>
        /* Styling Khusus Rekapitulasi Setoran Harian */
        .settlement-overview-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1.25rem;
        }
        @media (max-width: 960px) {
            .settlement-overview-grid {
                grid-template-columns: repeat(1, minmax(0, 1fr));
            }
        }
        .settlement-stat-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 125px;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .settlement-stat-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07);
        }
        .stat-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .stat-card-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            white-space: nowrap;
        }
        .stat-card-icon {
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .stat-card-value {
            font-size: 1.35rem;
            font-weight: 800;
            line-height: 1.25;
            white-space: nowrap;
            letter-spacing: -0.01em;
            display: flex;
            align-items: baseline;
            gap: 0.4rem;
        }
        .stat-card-subtitle {
            font-size: 0.75rem;
            color: #94a3b8;
            margin-top: 0.4rem;
            white-space: nowrap;
        }

        /* Container dan Scrollbar Tabel Rekapitulasi */
        .settlement-table-wrapper {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            overflow: hidden;
            margin-top: 1.5rem;
        }
        .settlement-table-header {
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
            background: #ffffff;
        }
        .settlement-scroll-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }
        .settlement-scroll-container::-webkit-scrollbar {
            height: 8px;
        }
        .settlement-scroll-container::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 4px;
        }
        .settlement-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .settlement-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Tabel Rekapitulasi Sopir */
        .settlement-table {
            width: 100%;
            min-width: 1260px;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.8rem;
        }
        .settlement-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.725rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 0.85rem 1rem !important;
            white-space: nowrap !important;
            border-bottom: 1px solid #e2e8f0;
        }
        .settlement-table td {
            padding: 0.85rem 1rem !important;
            white-space: nowrap !important;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .settlement-table tbody tr:hover td {
            background-color: #f8fafc;
        }
        .settlement-table tfoot td {
            background-color: #f8fafc;
            border-top: 2px solid #cbd5e1;
            padding: 0.95rem 1rem !important;
            white-space: nowrap !important;
        }

        /* Dark Mode */
        :is(.dark) .settlement-stat-card {
            background: #1e293b;
            border-color: #334155;
        }
        :is(.dark) .stat-card-title {
            color: #94a3b8;
        }
        :is(.dark) .stat-card-subtitle {
            color: #64748b;
        }
        :is(.dark) .settlement-table-wrapper {
            background: #1e293b;
            border-color: #334155;
        }
        :is(.dark) .settlement-table-header {
            background: #1e293b;
            border-bottom-color: #334155;
        }
        :is(.dark) .settlement-table th {
            background: #0f172a;
            color: #cbd5e1;
            border-bottom-color: #334155;
        }
        :is(.dark) .settlement-table td {
            border-bottom-color: #334155;
            color: #e2e8f0;
        }
        :is(.dark) .settlement-table tbody tr:hover td {
            background-color: #334155;
        }
        :is(.dark) .settlement-table tfoot td {
            background-color: #0f172a;
            border-top-color: #475569;
            color: #f8fafc;
        }
    </style>

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

                @if(in_array(auth()->user()?->role, ['gm', 'cashier']))
                    <button type="button" 
                            wire:click="exportExcel" 
                            class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition">
                        <x-filament::icon icon="heroicon-m-arrow-down-tray" class="w-4 h-4" />
                        Export Excel
                    </button>
                @endif
            </div>
        </div>

        @php
            $data = $this->settlementData;
            $summary = $data['summary_all'];
            $drivers = $data['drivers'];
        @endphp

        <!-- 3 Kartu Ringkasan Eksekutif (Stats Overview) -->
        <div class="settlement-overview-grid">
            <!-- 1. Total Wajib Setor -->
            <div class="settlement-stat-card">
                <div class="stat-card-top">
                    <span class="stat-card-title">TOTAL WAJIB SETOR</span>
                    <div class="stat-card-icon" style="background: #eff6ff; color: #2563eb;">
                        <x-filament::icon icon="heroicon-o-banknotes" class="w-5 h-5" />
                    </div>
                </div>
                <div>
                    <div class="stat-card-value font-mono" style="color: #0f172a;">
                        Rp {{ number_format($summary['total_wajib_setor'], 0, ',', '.') }}
                    </div>
                    <div class="stat-card-subtitle">
                        Dari {{ $summary['total_drivers'] }} sopir aktif
                    </div>
                </div>
            </div>

            <!-- 2. Total Uang Fisik Diterima -->
            <div class="settlement-stat-card">
                <div class="stat-card-top">
                    <span class="stat-card-title">UANG FISIK DITERIMA</span>
                    <div class="stat-card-icon" style="background: #ecfdf5; color: #059669;">
                        <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5" />
                    </div>
                </div>
                <div>
                    <div class="stat-card-value font-mono" style="color: #059669;">
                        Rp {{ number_format($summary['total_sudah_setor'], 0, ',', '.') }}
                    </div>
                    <div class="stat-card-subtitle">
                        Total uang cash fisik masuk
                    </div>
                </div>
            </div>

            <!-- 3. Total Selisih Akhir -->
            <div class="settlement-stat-card">
                <div class="stat-card-top">
                    <span class="stat-card-title">SELISIH SETOR AKHIR</span>
                    <div class="stat-card-icon" style="background: {{ $summary['total_selisih_setor'] > 0 ? '#fff1f2' : '#ecfdf5' }}; color: {{ $summary['total_selisih_setor'] > 0 ? '#e11d48' : '#059669' }};">
                        <x-filament::icon icon="heroicon-o-scale" class="w-5 h-5" />
                    </div>
                </div>
                <div>
                    <div class="stat-card-value font-mono">
                        @if ($summary['total_selisih_setor'] > 0)
                            <span style="font-size: 0.725rem; font-weight: 800; background: #ffe4e6; color: #e11d48; padding: 0.15rem 0.5rem; border-radius: 0.375rem; letter-spacing: 0.05em; font-family: ui-sans-serif, sans-serif;">
                                KURANG
                            </span>
                            <span style="color: #e11d48;">
                                Rp {{ number_format($summary['total_selisih_setor'], 0, ',', '.') }}
                            </span>
                        @elseif ($summary['total_selisih_setor'] < 0)
                            <span style="font-size: 0.725rem; font-weight: 800; background: #cffafe; color: #0891b2; padding: 0.15rem 0.5rem; border-radius: 0.375rem; letter-spacing: 0.05em; font-family: ui-sans-serif, sans-serif;">
                                LEBIH
                            </span>
                            <span style="color: #0891b2;">
                                Rp {{ number_format(abs($summary['total_selisih_setor']), 0, ',', '.') }}
                            </span>
                        @else
                            <span style="font-size: 0.725rem; font-weight: 800; background: #d1fae5; color: #059669; padding: 0.15rem 0.5rem; border-radius: 0.375rem; letter-spacing: 0.05em; font-family: ui-sans-serif, sans-serif;">
                                PAS / LUNAS
                            </span>
                            <span style="color: #059669;">
                                Rp 0
                            </span>
                        @endif
                    </div>
                    <div class="stat-card-subtitle">
                        Wajib Setor - Uang Diterima
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Rekapitulasi Lengkap Seluruh Sopir -->
        <div class="settlement-table-wrapper">
            <div class="settlement-table-header">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Daftar Wajib Setor, Sudah Setor & Selisih Per Sopir</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y') }}</p>
                </div>
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 text-xs font-medium border border-gray-200 dark:border-gray-700">
                    <x-filament::icon icon="heroicon-m-arrows-right-left" class="w-4 h-4 text-gray-500" />
                    <span>Geser tabel untuk kolom Status & Aksi</span>
                </div>
            </div>

            <div class="settlement-scroll-container">
                <table class="settlement-table">
                    <thead>
                        <tr>
                            <th style="width: 45px; text-align: center;">No</th>
                            <th style="width: 90px; text-align: center;">Kode</th>
                            <th style="min-width: 140px; text-align: left;">Nama Sopir</th>
                            <th style="min-width: 110px; text-align: right;">Bawaan (Rp)</th>
                            <th style="min-width: 100px; text-align: right;">Retur (Rp)</th>
                            <th style="min-width: 125px; text-align: right;">Transfer Conf.</th>
                            <th style="min-width: 110px; text-align: right;">Kredit (Rp)</th>
                            <th style="min-width: 125px; text-align: right; background: #eff6ff; color: #1e3a8a;">Wajib Setor</th>
                            <th style="min-width: 125px; text-align: right; background: #ecfdf5; color: #065f46;">Sudah Setor</th>
                            <th style="min-width: 115px; text-align: right; background: #fffbeb; color: #78350f;">Selisih</th>
                            <th style="min-width: 100px; text-align: center;">Status</th>
                            <th style="min-width: 130px; text-align: center;">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($drivers as $idx => $d)
                            <tr>
                                <td style="text-align: center; color: #94a3b8; font-family: ui-monospace, monospace;">{{ $idx + 1 }}</td>
                                <td style="text-align: center;">
                                    <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 0.375rem; background: #f1f5f9; border: 1px solid #e2e8f0; font-family: ui-monospace, monospace; font-weight: 700; color: #0f172a; font-size: 0.75rem;">
                                        {{ $d['driver_code'] }}
                                    </span>
                                </td>
                                <td style="text-align: left; font-weight: 600; color: #1e293b;">{{ $d['driver_name'] }}</td>
                                <td style="text-align: right; font-family: ui-monospace, monospace; color: #334155;">{{ number_format($d['amount_carried'], 0, ',', '.') }}</td>
                                <td style="text-align: right; font-family: ui-monospace, monospace; color: #e11d48;">
                                    {{ $d['amount_returned'] > 0 ? '-' . number_format($d['amount_returned'], 0, ',', '.') : '0' }}
                                </td>
                                <td style="text-align: right; font-family: ui-monospace, monospace; color: #2563eb;">
                                    <div style="font-weight: 600;">{{ number_format($d['transfer_approved'], 0, ',', '.') }}</div>
                                    @if ($d['transfer_pending'] > 0)
                                        <div style="font-size: 0.65rem; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; padding: 1px 5px; border-radius: 4px; display: inline-block; margin-top: 2px; white-space: nowrap; font-family: ui-sans-serif, sans-serif;" title="Menunggu verifikasi kasir">
                                            + Pnd: {{ number_format($d['transfer_pending'], 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right; font-family: ui-monospace, monospace; color: #d97706;">
                                    {{ number_format($d['amount_credit'], 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 700; background-color: rgba(239, 246, 255, 0.6); color: #1e3a8a;">
                                    {{ number_format($d['wajib_setor'], 0, ',', '.') }}
                                </td>
                                <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 700; background-color: rgba(236, 253, 245, 0.6); color: #065f46;">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.35rem;">
                                        <span>{{ number_format($d['sudah_setor'], 0, ',', '.') }}</span>
                                        @if ($d['cash_deposits_count'] > 0)
                                            <button type="button" 
                                                    wire:click="openHistoryModal({{ $d['driver_id'] }}, '{{ addslashes($d['driver_name']) }} ({{ $d['driver_code'] }})')"
                                                    style="display: inline-flex; align-items: center; padding: 2px 4px; border-radius: 4px; background: #d1fae5; color: #047857; border: none; cursor: pointer;"
                                                    title="Lihat Rincian Setoran Tunai">
                                                <x-filament::icon icon="heroicon-m-eye" class="w-3.5 h-3.5" />
                                            </button>
                                        @endif
                                    </div>
                                    @if ($d['cash_deposits_count'] > 1)
                                        <div style="font-size: 0.65rem; color: #059669; font-family: ui-sans-serif, sans-serif; cursor: pointer; margin-top: 2px;"
                                             wire:click="openHistoryModal({{ $d['driver_id'] }}, '{{ addslashes($d['driver_name']) }} ({{ $d['driver_code'] }})')">
                                            ({{ $d['cash_deposits_count'] }}x setor)
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 800; background-color: rgba(254, 243, 199, 0.4); color: {{ $d['selisih_setor'] > 0 ? '#e11d48' : ($d['selisih_setor'] < 0 ? '#0891b2' : '#059669') }};">
                                    @if ($d['selisih_setor'] > 0)
                                        -{{ number_format($d['selisih_setor'], 0, ',', '.') }}
                                    @elseif ($d['selisih_setor'] < 0)
                                        +{{ number_format(abs($d['selisih_setor']), 0, ',', '.') }}
                                    @else
                                        0
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if ($d['status'] === 'lunas')
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                                            Lunas
                                        </span>
                                    @elseif ($d['status'] === 'lebih_setor')
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; background: #cffafe; color: #155e75; border: 1px solid #a5f3fc;">
                                            Lebih
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; padding: 0.2rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 700; background: #ffe4e6; color: #9f1239; border: 1px solid #fecdd3;">
                                            Kurang
                                        </span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" 
                                            wire:click="openDepositModal({{ $d['driver_id'] }}, '{{ addslashes($d['driver_name']) }} ({{ $d['driver_code'] }})')"
                                            style="display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem; padding: 0.35rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700; background: #2563eb; color: #ffffff; border: none; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: background 0.15s ease;"
                                            onmouseover="this.style.background='#1d4ed8'"
                                            onmouseout="this.style.background='#2563eb'">
                                        <x-filament::icon icon="heroicon-m-plus" class="w-3.5 h-3.5" />
                                        Input Setor
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" style="padding: 2rem; text-align: center; color: #94a3b8;">Tidak ada sopir aktif yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>

                    <!-- BARIS ALL SETOR (FOOTER TOTAL AKUMULASI LENGKAP) -->
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; color: #334155;">
                                🏁 BARIS ALL SETOR (TOTAL ARMADA)
                            </td>
                            <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 800; color: #1e293b;">
                                Rp {{ number_format($summary['total_carried'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 800; color: #e11d48;">
                                Rp {{ number_format($summary['total_returned'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 800; color: #2563eb;">
                                Rp {{ number_format($summary['total_transfer_approved'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 800; color: #d97706;">
                                Rp {{ number_format($summary['total_credit'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 900; background: #eff6ff; color: #1e3a8a;">
                                Rp {{ number_format($summary['total_wajib_setor'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 900; background: #ecfdf5; color: #065f46;">
                                Rp {{ number_format($summary['total_sudah_setor'], 0, ',', '.') }}
                            </td>
                            <td style="text-align: right; font-family: ui-monospace, monospace; font-weight: 900; background: #fffbeb; color: {{ $summary['total_selisih_setor'] > 0 ? '#be123c' : ($summary['total_selisih_setor'] < 0 ? '#0e7490' : '#047857') }};">
                                @if ($summary['total_selisih_setor'] > 0)
                                    -Rp {{ number_format($summary['total_selisih_setor'], 0, ',', '.') }}
                                @elseif ($summary['total_selisih_setor'] < 0)
                                    +Rp {{ number_format(abs($summary['total_selisih_setor']), 0, ',', '.') }}
                                @else
                                    Rp 0 (Pas)
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span style="display: inline-block; padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.725rem; font-weight: 800; background: {{ $summary['status'] === 'lunas' ? '#d1fae5' : '#ffe4e6' }}; color: {{ $summary['status'] === 'lunas' ? '#065f46' : '#9f1239' }};">
                                    {{ strtoupper($summary['status']) }}
                                </span>
                            </td>
                            <td style="text-align: center; color: #94a3b8; font-size: 0.7rem;">
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
                <div class="flex rounded-lg shadow-sm border border-gray-300 dark:border-gray-700 overflow-hidden focus-within:ring-2 focus-within:ring-primary-500">
                    <span class="inline-flex items-center px-3.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold border-r border-gray-300 dark:border-gray-700 text-sm select-none">
                        Rp
                    </span>
                    <input type="number" 
                           step="any"
                           wire:model="depositAmount" 
                           placeholder="5000000" 
                           class="flex-1 px-3 py-2 text-sm bg-white dark:bg-gray-900 border-0 text-gray-900 dark:text-white font-mono font-bold focus:ring-0 focus:outline-none" 
                           required />
                </div>
                @if ($depositAmount > 0)
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mt-1">
                        Terbaca: Rp {{ number_format((float)$depositAmount, 0, ',', '.') }}
                    </p>
                @endif
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

    <!-- Modal Riwayat Multi-Setoran Tunai Sopir -->
    <x-filament::modal id="history-modal" width="2xl">
        <x-slot name="heading">
            Rincian Penerimaan Setoran Tunai Kasir
        </x-slot>

        <x-slot name="description">
            Riwayat uang tunai fisik yang telah diterima kasir untuk armada: <strong class="text-gray-900 dark:text-white">{{ $historyDriverName }}</strong> pada tanggal {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}.
        </x-slot>

        <div class="space-y-4">
            @php
                $deposits = $this->driverHistoryDeposits;
            @endphp

            @if ($deposits->isEmpty())
                <div class="p-6 text-center text-xs text-gray-400 border border-dashed border-gray-200 dark:border-gray-700 rounded-xl">
                    Belum ada catatan setoran uang tunai untuk sopir ini pada tanggal yang dipilih.
                </div>
            @else
                <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-900/60 border-b border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-bold uppercase">
                                <th class="py-2.5 px-3">Tahap</th>
                                <th class="py-2.5 px-3 text-right">Nominal (Rp)</th>
                                <th class="py-2.5 px-3">Kasir Penerima</th>
                                <th class="py-2.5 px-3">Catatan</th>
                                <th class="py-2.5 px-3">Waktu Input</th>
                                @if(auth()->user()?->role === 'gm')
                                    <th class="py-2.5 px-3 text-center">Aksi GM</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($deposits as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="py-2.5 px-3 font-semibold text-gray-900 dark:text-white">
                                        Tahap {{ $item->deposit_phase }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-mono font-bold text-emerald-600 dark:text-emerald-400">
                                        Rp {{ number_format($item->amount_received, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-700 dark:text-gray-300">
                                        {{ $item->cashier?->name ?? 'Kasir' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-500 dark:text-gray-400">
                                        {{ $item->notes ?? '-' }}
                                    </td>
                                    <td class="py-2.5 px-3 text-gray-500 dark:text-gray-400 font-mono">
                                        {{ $item->created_at->format('H:i') }} WIB
                                    </td>
                                    @if(auth()->user()?->role === 'gm')
                                        <td class="py-2.5 px-3 text-center">
                                            <button type="button" 
                                                    wire:confirm="Yakin ingin membatalkan/menghapus setoran Tahap {{ $item->deposit_phase }} sebesar Rp {{ number_format($item->amount_received, 0, ',', '.') }}?"
                                                    wire:click="deleteDeposit({{ $item->id }})"
                                                    class="px-2 py-0.5 text-[10px] font-bold rounded bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 transition">
                                                Hapus
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 dark:bg-gray-900 font-bold border-t border-gray-200 dark:border-gray-700">
                                <td class="py-2 px-3 text-gray-900 dark:text-white">Total Disetor</td>
                                <td class="py-2 px-3 text-right font-mono font-extrabold text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($deposits->sum('amount_received'), 0, ',', '.') }}
                                </td>
                                <td colspan="{{ auth()->user()?->role === 'gm' ? 4 : 3 }}"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif

            <div class="flex items-center justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
                <x-filament::button color="gray" wire:click="$dispatch('close-modal', { id: 'history-modal' })">
                    Tutup
                </x-filament::button>
            </div>
        </div>
    </x-filament::modal>
</x-filament-panels::page>
