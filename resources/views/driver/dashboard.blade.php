@extends('driver.layout')

@section('title', 'Beranda Sopir')

@section('content')
<div class="space-y-4">

    <!-- Kartu Identitas Sopir & Tanggal (Bersih & Elegan) -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
        <div class="space-y-0.5">
            <div class="flex items-center space-x-2">
                <span class="text-lg font-bold text-slate-900 tracking-tight">{{ $driver->user->name }}</span>
                <span class="text-xs px-2 py-0.5 rounded-md bg-slate-900 text-white font-mono font-bold">
                    {{ $driver->driver_code }}
                </span>
            </div>
            <p class="text-xs text-slate-500 font-medium">
                {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                Plat: {{ $driver->plate_number ?? '-' }}
            </span>
        </div>
    </div>

    <!-- Kartu Utama: Sisa Uang Yang Harus Disetor -->
    @php
        $diff = $summary['difference'];
        $status = $summary['status'];
        $isLunas = $status === 'lunas';
        $isLebih = $status === 'lebih_setor';
        $isKurang = $status === 'kurang_setor';
    @endphp

    <div class="rounded-2xl p-4 sm:p-5 bg-slate-900 text-white shadow-sm space-y-3.5">
        <!-- Status Header -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                Status Setoran Hari Ini
            </span>
            <div>
                @if ($isLunas)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-500 text-white">
                        LUNAS / PAS
                    </span>
                @elseif ($isLebih)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-500 text-white">
                        LEBIH SETOR
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-400 text-slate-950">
                        KURANG SETOR
                    </span>
                @endif
            </div>
        </div>

        <!-- Nominal Wajib Setor / Sisa -->
        <div>
            <p class="text-xs text-slate-400 font-medium">Sisa Wajib Disetor ke Kasir</p>
            <div class="text-2xl sm:text-3xl font-bold tracking-tight text-white font-mono mt-0.5">
                @if ($diff < 0)
                    Lebih Rp {{ number_format(abs($diff), 0, ',', '.') }}
                @elseif ($diff > 0)
                    Rp {{ number_format($diff, 0, ',', '.') }}
                @else
                    Rp 0 (Lunas)
                @endif
            </div>
        </div>

        <!-- Rincian Wajib vs Sudah Disetor -->
        <div class="grid grid-cols-2 gap-2.5 pt-1 border-t border-slate-800">
            <div class="bg-slate-800/80 rounded-xl p-2.5">
                <p class="text-[11px] text-slate-400 font-medium">Total Wajib Setor</p>
                <p class="text-sm sm:text-base font-bold font-mono text-white mt-0.5">
                    Rp {{ number_format($summary['target_cash'], 0, ',', '.') }}
                </p>
            </div>
            <div class="bg-slate-800/80 rounded-xl p-2.5">
                <p class="text-[11px] text-slate-400 font-medium">Sudah Disetor Fisik</p>
                <p class="text-sm sm:text-base font-bold font-mono text-white mt-0.5">
                    Rp {{ number_format($summary['actual_cash'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Dua Tombol Aksi Utama (Proporsional, Mudah Dipencet, Icon Lokal SVG) -->
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('driver.transfer.create') }}" 
           class="flex items-center justify-center space-x-2 py-3.5 px-3 bg-slate-900 hover:bg-black active:scale-[0.99] text-white font-bold text-sm rounded-xl shadow-sm transition">
            <x-app-icon name="actions.plus-circle" class="w-4 h-4 shrink-0 text-white" />
            <span>+ Catat Transfer</span>
        </a>
        <a href="{{ route('driver.credit.create') }}" 
           class="flex items-center justify-center space-x-2 py-3.5 px-3 bg-white hover:bg-slate-100 active:scale-[0.99] text-slate-900 font-bold text-sm rounded-xl shadow-sm transition border-2 border-slate-900">
            <x-app-icon name="actions.file-plus" class="w-4 h-4 shrink-0 text-slate-900" />
            <span>+ Catat Bon</span>
        </a>
    </div>

    <!-- Ringkasan Angka Muatan & Penjualan (4 Kotak Rapi) -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
            <h3 class="font-bold text-sm text-slate-900">Rincian Operasional Hari Ini</h3>
            <span class="text-xs text-slate-500 font-medium font-mono">
                Net Bawaan: Rp {{ number_format($summary['net_carried'], 0, ',', '.') }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2.5">
            <!-- Barang Dibawa -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Barang Dibawa</p>
                <p class="text-sm sm:text-base font-bold text-slate-900 font-mono mt-0.5">
                    Rp {{ number_format($summary['amount_carried'], 0, ',', '.') }}
                </p>
            </div>

            <!-- Barang Retur -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Barang Kembali / Retur</p>
                <p class="text-sm sm:text-base font-bold text-slate-900 font-mono mt-0.5">
                    Rp {{ number_format($summary['amount_returned'], 0, ',', '.') }}
                </p>
            </div>

            <!-- Transfer Toko Disetujui -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Transfer Toko (Sah)</p>
                <p class="text-sm sm:text-base font-bold text-slate-900 font-mono mt-0.5">
                    Rp {{ number_format($summary['transfer_approved'], 0, ',', '.') }}
                </p>
            </div>

            <!-- Nota Bon Kredit -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200">
                <p class="text-xs text-slate-500 font-medium">Nota Bon / Kredit</p>
                <p class="text-sm sm:text-base font-bold text-slate-900 font-mono mt-0.5">
                    Rp {{ number_format($summary['amount_credit'], 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Catatan Transaksi yang Diinput Hari Ini -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-900">Catatan Yang Sudah Anda Masukkan</h3>
            <span class="text-xs text-slate-500 font-semibold bg-slate-100 px-2 py-0.5 rounded-md">
                {{ $todayTransfers->count() + $todayCredits->count() }} Data
            </span>
        </div>

        @if ($todayTransfers->isEmpty() && $todayCredits->isEmpty())
            <div class="p-6 text-center text-slate-400 space-y-1">
                <x-app-icon name="types.inbox" class="w-8 h-8 mx-auto text-slate-300 mb-1" />
                <p class="text-xs text-slate-500 font-medium">Belum ada catatan transfer atau bon hari ini.</p>
                <p class="text-[11px] text-slate-400">Gunakan tombol di atas untuk mencatat pembayaran toko.</p>
            </div>
        @else
            <div class="divide-y divide-slate-100">
                <!-- Daftar Transfer -->
                @foreach ($todayTransfers as $tf)
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="min-w-0 pr-2">
                            <div class="flex items-center space-x-1.5">
                                <span class="text-xs font-bold text-slate-900 truncate">{{ $tf->store_name }}</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Transfer &bull; {{ $tf->created_at->format('H:i') }} WIB
                            </p>
                            @if ($tf->status === 'rejected' && $tf->rejection_reason)
                                <p class="text-xs text-rose-600 font-medium mt-0.5">Alasan tolak: {{ $tf->rejection_reason }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-slate-900 font-mono">
                                Rp {{ number_format($tf->claimed_amount, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center justify-end space-x-1.5 mt-1">
                                @if ($tf->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        Disetujui
                                    </span>
                                @elseif ($tf->status === 'rejected')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                        Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                        Menunggu Kasir
                                    </span>
                                @endif

                                @if ($tf->proof_image_path)
                                    <a href="{{ asset('storage/' . $tf->proof_image_path) }}" target="_blank" class="p-1 text-slate-600 hover:text-slate-900" title="Lihat Foto Bukti">
                                        <x-app-icon name="actions.eye" class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Daftar Bon / Kredit -->
                @foreach ($todayCredits as $cr)
                    <div class="p-3.5 flex items-center justify-between hover:bg-slate-50 transition">
                        <div class="min-w-0 pr-2">
                            <span class="text-xs font-bold text-slate-900 truncate block">{{ $cr->store_name }}</span>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Bon / Kredit &bull; {{ $cr->created_at->format('H:i') }} WIB
                            </p>
                            @if ($cr->notes)
                                <p class="text-xs text-slate-400 truncate mt-0.5">{{ $cr->notes }}</p>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            <p class="text-sm font-bold text-slate-900 font-mono">
                                Rp {{ number_format($cr->amount, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center justify-end space-x-1.5 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-800">
                                    Tercatat
                                </span>
                                @if ($cr->invoice_photo_path)
                                    <a href="{{ asset('storage/' . $cr->invoice_photo_path) }}" target="_blank" class="p-1 text-slate-600 hover:text-slate-900" title="Lihat Foto Faktur">
                                        <x-app-icon name="actions.eye" class="w-4 h-4" />
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
