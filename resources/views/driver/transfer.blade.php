@extends('driver.layout')

@section('title', 'Input Transfer Pembayaran')

@section('content')
<div class="space-y-4">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center space-x-2">
        <a href="{{ route('driver.dashboard') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
        </a>
        <div>
            <h2 class="text-base font-bold text-slate-900">Input Bukti Transfer</h2>
            <p class="text-xs text-slate-500">Ajukan verifikasi pembayaran transfer toko ke Kasir</p>
        </div>
    </div>

    <!-- Transfer Input Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-sm space-y-5">

        <form action="{{ route('driver.transfer.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="transferForm">
            @csrf

            <!-- Tanggal (Readonly display) -->
            <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 flex items-center justify-between text-xs">
                <span class="text-slate-500 font-medium">Tanggal Transaksi</span>
                <span class="font-bold text-slate-800">{{ \Carbon\Carbon::parse($todayDate)->translatedFormat('d F Y') }}</span>
            </div>

            <!-- Nama Toko -->
            <div>
                <label for="store_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Toko / Outlet <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="store" class="w-4 h-4"></i>
                    </div>
                    <input type="text" 
                           id="store_name" 
                           name="store_name" 
                           value="{{ old('store_name') }}" 
                           required 
                           placeholder="Contoh: Toko Berkah Jaya" 
                           class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition">
                </div>
                @error('store_name')
                    <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nominal Transfer -->
            <div>
                <label for="claimed_amount" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nominal Transfer (Rp) <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500 font-bold text-sm">
                        Rp
                    </div>
                    <input type="text" 
                           id="claimed_amount_display" 
                           required 
                           inputmode="numeric"
                           placeholder="0" 
                           class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-mono font-bold text-base focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition">
                    <input type="hidden" id="claimed_amount" name="claimed_amount" value="{{ old('claimed_amount') }}">
                </div>
                <p id="amount_words" class="text-[11px] text-slate-400 mt-1 italic"></p>
                @error('claimed_amount')
                    <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Bukti Transfer (Opsional tapi direkomendasikan) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Foto Bukti Transfer / Resi <span class="text-slate-400 font-normal lowercase">(opsional)</span>
                </label>
                
                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-4 text-center hover:border-brand-500 bg-slate-50/60 transition cursor-pointer relative" id="dropArea">
                    <input type="file" 
                           id="proof_image" 
                           name="proof_image" 
                           accept="image/*" 
                           capture="environment"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div id="uploadPlaceholder" class="space-y-2 py-2">
                        <div class="w-12 h-12 mx-auto rounded-full bg-blue-50 text-brand-600 flex items-center justify-center">
                            <i data-lucide="camera" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-700">Ambil Foto / Pilih Gambar Resi</p>
                            <p class="text-[11px] text-slate-400">JPG, PNG, WEBP hingga 5MB</p>
                        </div>
                    </div>

                    <!-- Image Preview Container -->
                    <div id="previewContainer" class="hidden">
                        <img id="imagePreview" src="" alt="Preview Bukti" class="max-h-56 mx-auto rounded-xl shadow-md object-contain border border-slate-200">
                        <p class="text-[11px] text-brand-600 font-medium mt-2">Ketuk untuk mengganti foto</p>
                    </div>
                </div>

                @error('proof_image')
                    <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Tambahan -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan Tambahan
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="2" 
                          placeholder="Nomor rekening, bank pengirim, atau keterangan transfer..." 
                          class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:bg-white transition">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-[11px] text-rose-600 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full py-3.5 px-4 bg-brand-600 hover:bg-brand-700 active:scale-[0.99] text-white font-bold text-sm rounded-xl shadow-lg shadow-brand-600/30 flex items-center justify-center space-x-2 transition cursor-pointer">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Ajukan Verifikasi Transfer</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
    // Format Rupiah Input Helper
    const displayInput = document.getElementById('claimed_amount_display');
    const hiddenInput = document.getElementById('claimed_amount');

    // Pre-populate if old value exists
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

    // Image Preview Helper
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
