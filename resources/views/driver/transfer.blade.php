@extends('driver.layout')

@section('title', 'Catat Transfer Bank')

@section('content')
<div class="space-y-3.5">

    <!-- Header Judul & Tanggal -->
    <div class="flex items-center justify-between">
        <h2 class="text-base font-bold text-slate-900">Catat Setoran</h2>
        <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-2xs">
            {{ \Carbon\Carbon::parse($todayDate)->translatedFormat('d M Y') }}
        </div>
    </div>

    <!-- Segmented Control Terpadu (Transfer vs Bon) -->
    <div class="grid grid-cols-2 bg-slate-100 p-1 rounded-xl border border-slate-200">
        <a href="{{ route('driver.transfer.create') }}" 
           class="flex items-center justify-center space-x-1.5 py-2 px-3 rounded-lg text-xs transition bg-white text-slate-900 font-bold shadow-2xs">
            <x-app-icon name="types.bank" class="w-4 h-4 text-blue-600" />
            <span>Transfer Toko</span>
        </a>
        <a href="{{ route('driver.credit.create') }}" 
           class="flex items-center justify-center space-x-1.5 py-2 px-3 rounded-lg text-xs transition text-slate-500 hover:text-slate-900 font-medium">
            <x-app-icon name="types.receipt" class="w-4 h-4 text-slate-500" />
            <span>Bon / Kredit</span>
        </a>
    </div>

    <!-- Card Formulir Transfer -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs">
        <form action="{{ route('driver.transfer.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="transferForm">
            @csrf

            <!-- Nama Toko -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="store_name" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        NAMA TOKO / OUTLET
                    </label>
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">Wajib</span>
                </div>
                <input type="text" 
                       id="store_name" 
                       name="store_name" 
                       value="{{ old('store_name') }}" 
                       required 
                       placeholder="Contoh: Toko Sinar Jaya" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition">
                @error('store_name')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nominal Transfer + Quick Chips -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="claimed_amount_display" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        NOMINAL TRANSFER
                    </label>
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">Wajib</span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-700 font-bold text-sm">
                        Rp
                    </div>
                    <input type="text" 
                           id="claimed_amount_display" 
                           required 
                           inputmode="numeric"
                           placeholder="0" 
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-mono font-bold text-base focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition">
                    <input type="hidden" id="claimed_amount" name="claimed_amount" value="{{ old('claimed_amount') }}">
                </div>

                <!-- Quick Nominal Chips -->
                <div class="flex items-center space-x-2 mt-2 overflow-x-auto pb-0.5">
                    <button type="button" onclick="setNominal(500000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        500 Rb
                    </button>
                    <button type="button" onclick="setNominal(1000000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        1 Jt
                    </button>
                    <button type="button" onclick="setNominal(2500000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        2,5 Jt
                    </button>
                    <button type="button" onclick="setNominal(5000000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        5 Jt
                    </button>
                </div>

                @error('claimed_amount')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Bukti Transfer -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        BUKTI TRANSFER / RESI
                    </label>
                    <span class="text-[10px] font-medium text-slate-400">Opsional</span>
                </div>
                
                <div class="border-2 border-dashed border-slate-300 rounded-xl p-3.5 text-center hover:border-slate-900 bg-slate-50 transition cursor-pointer relative" id="dropArea">
                    <input type="file" 
                           id="proof_image" 
                           name="proof_image" 
                           accept="image/*" 
                           capture="environment"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div id="uploadPlaceholder" class="flex items-center space-x-3 text-left py-1">
                        <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center shrink-0 shadow-2xs">
                            <x-app-icon name="actions.camera" class="w-5 h-5 text-slate-700" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Ambil Foto / Pilih Gambar</p>
                            <p class="text-[11px] text-slate-500 mt-0.5">Format JPG, PNG (Maks 5MB)</p>
                        </div>
                    </div>

                    <!-- Tempat Tinjauan Foto -->
                    <div id="previewContainer" class="hidden py-1 text-center">
                        <img id="imagePreview" src="" alt="Bukti Transfer" class="max-h-48 mx-auto rounded-lg shadow-xs object-contain border border-slate-200">
                        <p class="text-xs text-slate-900 font-bold underline mt-2">Ketuk untuk mengganti foto</p>
                    </div>
                </div>

                @error('proof_image')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan / Bank Pengirim -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="notes" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        CATATAN / BANK PENGIRIM
                    </label>
                    <span class="text-[10px] font-medium text-slate-400">Opsional</span>
                </div>
                <textarea id="notes" 
                          name="notes" 
                          rows="2"
                          placeholder="Cth: BCA a.n. Toko Sinar Jaya" 
                          class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition resize-none">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Kirim -->
            <div class="pt-1">
                <button type="submit" 
                        class="w-full py-3 px-4 bg-slate-900 hover:bg-black active:scale-[0.99] text-white font-bold text-sm rounded-xl shadow-sm flex items-center justify-center space-x-2 transition cursor-pointer">
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

    function setNominal(amount) {
        hiddenInput.value = amount;
        displayInput.value = formatRupiah(amount);
    }

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
