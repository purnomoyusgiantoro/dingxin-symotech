<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Sopir') - Dingxin</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 1rem);
        }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen bg-slate-50 text-slate-900 antialiased">

    <!-- Header Bersih & Jelas (Logo di Tengah, Info di Samping, Logo Profile dengan Menu Keluar) -->
    <header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-xs">
        <div class="max-w-lg mx-auto px-4 h-14 relative flex items-center justify-between">
            
            <!-- Kiri: Spacer Penyeimbang agar Logo tetap presisi di tengah -->
            <div class="w-8.5 h-8.5 shrink-0"></div>

            <!-- Tengah: Logo Resmi Aice / Dingxin (Tepat di Tengah Header) -->
            <a href="{{ route('driver.dashboard') }}" class="absolute left-1/2 -translate-x-1/2 flex items-center justify-center shrink-0 z-10" title="Beranda Sopir">
                <img src="{{ asset('images/dingxin_logo.png') }}" alt="Aice Dingxin" class="h-7 sm:h-8 w-auto object-contain">
            </a>

            <!-- Kanan: Logo Profile (Avatar Premium) & Menu Dropdown saat Diklik -->
            <div class="relative z-50 h-full flex items-center justify-end" id="profileDropdownContainer">
                <button type="button" 
                        id="profileButton"
                        class="relative w-9 h-9 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-xs transition-all active:scale-95 cursor-pointer ring-2 ring-slate-200 hover:ring-slate-900 focus:outline-none"
                        aria-label="Menu Profil"
                        title="Profil Sopir & Keluar">
                    <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <!-- Status aktif online -->
                    <span class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Dropdown Card Profil yang Elegan & Berkelas -->
                <div id="profileDropdown" 
                     class="hidden absolute right-0 top-[calc(100%+8px)] w-72 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-4 z-50 animate-in fade-in zoom-in-95 duration-150">
                    
                    <!-- Header Akun Sopir -->
                    <div class="flex items-start space-x-3 pb-3 border-b border-slate-100">
                        <div class="w-11 h-11 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->driver->driver_code ?? Auth::user()->name, 0, 3)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-bold text-slate-900 truncate">{{ Auth::user()->name }}</h4>
                            <p class="text-[11px] text-slate-500 font-medium truncate mt-0.5">Sopir Distribusi Dingxin</p>
                            <div class="flex items-center space-x-1.5 mt-1.5">
                                <span class="px-2 py-0.5 rounded-md bg-slate-900 text-white text-[10px] font-mono font-bold">
                                    {{ Auth::user()->driver->driver_code ?? Auth::user()->username }}
                                </span>
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px] font-semibold border border-slate-200">
                                    Area {{ Auth::user()->driver->area_code ?? '-' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Armada Kendaraan -->
                    <div class="my-3 p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <x-app-icon name="types.truck" class="w-4 h-4 text-slate-500" />
                            <span class="text-xs text-slate-500 font-medium">Plat Armada:</span>
                        </div>
                        <span class="text-xs font-mono font-bold text-slate-900 bg-white px-2 py-0.5 rounded border border-slate-200">
                            {{ Auth::user()->driver->plate_number ?? 'Belum Diatur' }}
                        </span>
                    </div>

                    <!-- Tombol Keluar Akun -->
                    <form action="{{ route('driver.logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                        @csrf
                        <button type="submit" 
                                class="w-full flex items-center justify-center space-x-2 py-2.5 px-3 bg-rose-50 hover:bg-rose-100 active:bg-rose-200 text-rose-700 rounded-xl font-bold text-xs transition border border-rose-200 shadow-2xs cursor-pointer">
                            <x-app-icon name="nav.logout" class="w-4 h-4 text-rose-600" />
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Backdrop Gelap Halus saat Profil Terbuka -->
            <div id="profileBackdrop" class="hidden fixed inset-0 bg-slate-900/30 backdrop-blur-2xs z-40 transition-opacity"></div>

        </div>
    </header>

    <!-- Konten Utama Halaman -->
    <main class="flex-1 w-full max-w-lg mx-auto px-4 py-4 mb-24">

        <!-- Pesan Berhasil -->
        @if (session('success'))
            <div class="mb-4 p-3.5 rounded-xl bg-white border-2 border-slate-900 text-slate-900 text-sm flex items-start space-x-2.5 shadow-sm">
                <x-app-icon name="status.success" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
                <div class="font-semibold flex-1">{{ session('success') }}</div>
            </div>
        @endif

        <!-- Pesan Peringatan / Error -->
        @if (session('error'))
            <div class="mb-4 p-3.5 rounded-xl bg-white border-2 border-rose-600 text-rose-900 text-sm flex items-start space-x-2.5 shadow-sm">
                <x-app-icon name="status.danger" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
                <div class="font-semibold flex-1">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Pesan Validasi Form -->
        @if ($errors->any())
            <div class="mb-4 p-3.5 rounded-xl bg-white border-2 border-rose-500 text-rose-900 text-sm space-y-1 shadow-sm">
                <div class="font-bold flex items-center space-x-1.5">
                    <x-app-icon name="status.warning" class="w-4 h-4 text-rose-600" />
                    <span>Periksa kembali isian Anda:</span>
                </div>
                <ul class="list-disc list-inside text-rose-800 pl-1 space-y-0.5 font-medium text-xs">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Navigasi Bawah Rapi & Mudah Dipencet (Menggunakan SVG Lokal) -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-slate-200 shadow-md pb-safe">
        <div class="max-w-lg mx-auto px-4 h-16 flex items-center justify-between">
            <!-- Beranda -->
            <a href="{{ route('driver.dashboard') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('driver.dashboard') ? 'text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <x-app-icon name="nav.home" class="w-5 h-5 mb-0.5" />
                <span class="text-xs">Beranda</span>
            </a>

            <!-- Transfer -->
            <a href="{{ route('driver.transfer.create') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('driver.transfer.*') ? 'text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <x-app-icon name="nav.transfer" class="w-5 h-5 mb-0.5" />
                <span class="text-xs">Transfer</span>
            </a>

            <!-- Bon Kredit -->
            <a href="{{ route('driver.credit.create') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('driver.credit.*') ? 'text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <x-app-icon name="nav.credit" class="w-5 h-5 mb-0.5" />
                <span class="text-xs">Bon Kredit</span>
            </a>

            <!-- Riwayat -->
            <a href="{{ route('driver.history') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('driver.history') ? 'text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <x-app-icon name="nav.history" class="w-5 h-5 mb-0.5" />
                <span class="text-xs">Riwayat</span>
            </a>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('profileButton');
            const dropdown = document.getElementById('profileDropdown');
            const backdrop = document.getElementById('profileBackdrop');

            function toggleProfile(show) {
                if (show) {
                    dropdown.classList.remove('hidden');
                    backdrop.classList.remove('hidden');
                } else {
                    dropdown.classList.add('hidden');
                    backdrop.classList.add('hidden');
                }
            }

            if (btn && dropdown && backdrop) {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const isHidden = dropdown.classList.contains('hidden');
                    toggleProfile(isHidden);
                });

                backdrop.addEventListener('click', function () {
                    toggleProfile(false);
                });
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
