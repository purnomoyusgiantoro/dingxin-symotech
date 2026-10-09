@extends('driver.layout')

@section('title', 'Catat Transfer Bank')

@section('content')
<div class="space-y-4">

    <!-- Navigasi Atas & Judul Halaman -->
    <div class="flex items-center space-x-3">
        <a href="{{ route('driver.dashboard') }}" 
           class="p-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:text-slate-900 hover:bg-slate-100 transition shadow-sm"
           title="Kembali ke Beranda">
            <x-app-icon name="actions.arrow-left" class="w-5 h-5 text-slate-700" />
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-900">Catat Transfer Toko</h2>
            <p class="text-xs text-slate-500">Kirim laporan bukti transfer untuk diverifikasi Kasir</p>
        </div>
    </div>

    <!-- Form Input Transfer -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">

        <form action="{{ route('driver.transfer.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="transferForm">
            @csrf

            <!-- Tanggal Transaksi (Otomatis) -->
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Tanggal Transaksi</span>
                <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($todayDate)->translatedFormat('d F Y') }}</span>
            </div>

            <!-- Nama Toko -->
            <div>
                <label for="store_name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Nama Toko / Outlet <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <input type="text" 
                           id="store_name" 
                           name="store_name" 
                           value="{{ old('store_name') }}" 
                           required 
                           placeholder="Contoh: Toko Sinar Jaya" 
                           class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 text-base focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>
                @error('store_name')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nominal Transfer -->
            <div>
                <label for="claimed_amount_display" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Nominal Transfer (Rp) <span class="text-rose-600">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-700 font-bold text-base">
                        Rp
                    </div>
                    <input type="text" 
                           id="claimed_amount_display" 
                           required 
                           inputmode="numeric"
                           placeholder="0" 
                           class="w-full pl-12 pr-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 font-mono font-bold text-base focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                    <input type="hidden" id="claimed_amount" name="claimed_amount" value="{{ old('claimed_amount') }}">
                </div>
                @error('claimed_amount')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Bukti Transfer -->
            <div>
                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Foto Bukti Transfer / Resi <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-4 text-center hover:border-slate-900 bg-slate-50 transition cursor-pointer relative" id="dropArea">
                    <input type="file" 
                           id="proof_image" 
                           name="proof_image" 
                           accept="image/*" 
                           capture="environment"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div id="uploadPlaceholder" class="space-y-2 py-2">
                        <div class="w-10 h-10 mx-auto rounded-full bg-slate-200 text-slate-800 flex items-center justify-center">
                            <x-app-icon name="actions.camera" class="w-5 h-5 text-slate-800" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Ketuk untuk Ambil Foto / Pilih Gambar</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format JPG / PNG</p>
                        </div>
                    </div>

                    <!-- Tempat Tinjauan Foto -->
                    <div id="previewContainer" class="hidden">
                        <img id="imagePreview" src="" alt="Bukti Transfer" class="max-h-52 mx-auto rounded-lg shadow-xs object-contain border border-slate-200">
                        <p class="text-xs text-slate-900 font-bold underline mt-2">Ketuk untuk mengganti foto</p>
                    </div>
                </div>

                @error('proof_image')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Catatan Tambahan <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="2" 
                          placeholder="Nomor rekening, bank pengirim, atau keterangan..." 
                          class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3.5 px-4 bg-slate-900 hover:bg-black active:scale-[0.99] text-white font-bold text-sm rounded-xl shadow-sm flex items-center justify-center space-x-2 transition cursor-pointer">
                    <x-app-icon name="actions.send" class="w-4 h-4 text-white" />
                    <span>Kirim Bukti Transfer</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Format Rupiah
    const displayInput = document.getElementById('claimed_amount_display');
    const hiddenInput = document.getElementById('claimed_amount');

    if (hiddenInput.value) {
        displayInput.value = formatRupiah(hiddenInput.value);
    }

    displayInput.addEventListener('input', function (e) {
        let value = this.value.replace(/[^0-9]/g, '');
        hiddenInput.value = value;
        this.value = formatRupiah(value);
    });

    function formatRupiah(angka) {
        if (!angka) return '';
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Preview Foto
    const proofInput = document.getElementById('proof_image');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');

    proofInput.addEventListener('change', function (e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                imagePreview.src = event.target.result;
                uploadPlaceholder.classList.add('hidden');
                previewContainer.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
