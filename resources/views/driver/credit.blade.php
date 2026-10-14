@extends('driver.layout')

@section('title', 'Catat Bon Kredit')

@section('content')
<div class="space-y-3.5">

    <!-- Header Judul & Tanggal -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-base font-bold text-slate-900">Catat Setoran</h2>
            <p class="text-xs text-slate-500">Catat penyerahan barang dengan bon/tempo toko</p>
        </div>
        <div class="text-xs font-semibold text-slate-600 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-2xs">
            {{ \Carbon\Carbon::parse($todayDate)->translatedFormat('d M Y') }}
        </div>
    </div>

    <!-- Segmented Control Terpadu (Transfer vs Bon) -->
    <div class="grid grid-cols-2 bg-slate-100 p-1 rounded-xl border border-slate-200">
        <a href="{{ route('driver.transfer.create') }}" 
           class="flex items-center justify-center space-x-1.5 py-2 px-3 rounded-lg text-xs transition text-slate-500 hover:text-slate-900 font-medium">
            <x-app-icon name="types.bank" class="w-4 h-4 text-slate-500" />
            <span>Transfer Toko</span>
        </a>
        <a href="{{ route('driver.credit.create') }}" 
           class="flex items-center justify-center space-x-1.5 py-2 px-3 rounded-lg text-xs transition bg-white text-slate-900 font-bold shadow-2xs">
            <x-app-icon name="types.receipt" class="w-4 h-4 text-amber-600" />
            <span>Bon / Kredit</span>
        </a>
    </div>

    <!-- Card Formulir Bon / Kredit -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-4 sm:p-5 shadow-xs">
        <form action="{{ route('driver.credit.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4" id="creditForm">
            @csrf

            <!-- Nama Toko -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="store_name" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        NAMA TOKO / PELANGGAN
                    </label>
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">Wajib</span>
                </div>
                <input type="text" 
                       id="store_name" 
                       name="store_name" 
                       value="{{ old('store_name') }}" 
                       required 
                       placeholder="Contoh: Toko Berkah Abadi" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition">
                @error('store_name')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nominal Bon Kredit + Quick Chips -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="amount_display" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        NOMINAL BON / KREDIT
                    </label>
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">Wajib</span>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-700 font-bold text-sm">
                        Rp
                    </div>
                    <input type="text" 
                           id="amount_display" 
                           required 
                           inputmode="numeric"
                           placeholder="0" 
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 font-mono font-bold text-base focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition">
                    <input type="hidden" id="amount" name="amount" value="{{ old('amount') }}">
                </div>

                <!-- Quick Nominal Chips -->
                <div class="flex items-center space-x-2 mt-2 overflow-x-auto pb-0.5">
                    <button type="button" onclick="setNominalCredit(500000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        500 Rb
                    </button>
                    <button type="button" onclick="setNominalCredit(1250000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        1,25 Jt
                    </button>
                    <button type="button" onclick="setNominalCredit(2000000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        2 Jt
                    </button>
                    <button type="button" onclick="setNominalCredit(5000000)" class="text-xs font-semibold py-1 px-2.5 rounded-full bg-slate-100 hover:bg-amber-50 hover:text-amber-700 hover:border-amber-200 border border-slate-200 text-slate-700 transition shrink-0 cursor-pointer">
                        5 Jt
                    </button>
                </div>

                @error('amount')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Foto Faktur Fisik (Terintegrasi Warning Box) -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        FOTO NOTA FISIK BERTANDA TANGAN
                    </label>
                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">Wajib Lampirkan</span>
                </div>
                
                <div class="border-2 border-dashed border-amber-300 rounded-xl p-3.5 text-center hover:border-amber-500 bg-amber-50/60 transition cursor-pointer relative" id="dropArea">
                    <input type="file" 
                           id="invoice_photo" 
                           name="invoice_photo" 
                           accept="image/*" 
                           capture="environment"
                           required
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                    <div id="uploadPlaceholder" class="flex items-center space-x-3 text-left py-1">
                        <div class="w-10 h-10 rounded-xl bg-white border border-amber-300 text-amber-700 flex items-center justify-center shrink-0 shadow-2xs">
                            <x-app-icon name="actions.camera" class="w-5 h-5 text-amber-700" />
                        </div>
                        <div>
                            <p class="text-xs font-bold text-amber-900">Ambil Foto Nota Fisik & Cap Toko</p>
                            <p class="text-[11px] text-amber-700 mt-0.5">Wajib ada tanda tangan & total tagihan jelas</p>
                        </div>
                    </div>

                    <!-- Tempat Tinjauan Foto -->
                    <div id="previewContainer" class="hidden py-1 text-center">
                        <img id="imagePreview" src="" alt="Foto Faktur" class="max-h-48 mx-auto rounded-lg shadow-xs object-contain border border-amber-200">
                        <p class="text-xs text-amber-900 font-bold underline mt-2">Ketuk untuk mengambil ulang foto</p>
                    </div>
                </div>

                @error('invoice_photo')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor Nota / Catatan -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="notes" class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">
                        NOMOR NOTA / CATATAN
                    </label>
                    <span class="text-[10px] font-medium text-slate-400">Opsional</span>
                </div>
                <textarea id="notes" 
                          name="notes" 
                          rows="2"
                          placeholder="Cth: INV-882 • Tempo 7 Hari" 
                          class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-slate-900 focus:bg-white transition resize-none">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-1">
                <button type="submit" 
                        class="w-full py-3 px-4 bg-slate-900 hover:bg-black active:scale-[0.99] text-white font-bold text-sm rounded-xl shadow-sm flex items-center justify-center space-x-2 transition cursor-pointer">
                    <x-app-icon name="actions.check-square" class="w-4 h-4 text-white" />
                    <span>Simpan Faktur Bon</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const displayInput = document.getElementById('amount_display');
    const hiddenInput = document.getElementById('amount');

    if (hiddenInput.value) {
        displayInput.value = formatRupiah(hiddenInput.value);
    }

    displayInput.addEventListener('input', function (e) {
        let value = this.value.replace(/[^0-9]/g, '');
        hiddenInput.value = value;
        this.value = formatRupiah(value);
    });

    function setNominalCredit(amount) {
        hiddenInput.value = amount;
        displayInput.value = formatRupiah(amount);
    }

    function formatRupiah(angka) {
        if (!angka) return '';
        return angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    // Preview Foto
    const photoInput = document.getElementById('invoice_photo');
    const uploadPlaceholder = document.getElementById('uploadPlaceholder');
    const previewContainer = document.getElementById('previewContainer');
    const imagePreview = document.getElementById('imagePreview');

    photoInput.addEventListener('change', function (e) {
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
