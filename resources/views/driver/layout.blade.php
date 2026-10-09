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

    <!-- Header Bersih & Jelas -->
    <header class="sticky top-0 z-30 bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-lg mx-auto px-4 h-15 py-2.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-slate-900 flex items-center justify-center font-bold text-white text-sm shadow-sm shrink-0">
                    DX
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="text-base font-bold text-slate-900 tracking-tight">
                            {{ Auth::user()->driver->driver_code ?? Auth::user()->username }}
                        </span>
                        <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold border border-slate-200">
                            Area {{ Auth::user()->driver->area_code ?? '-' }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 truncate max-w-[180px] sm:max-w-xs">{{ Auth::user()->name }}</p>
                </div>
            </div>

            <!-- Tombol Keluar (Jelas dengan teks) -->
            <form action="{{ route('driver.logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem?');">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-100 text-slate-700 text-xs font-semibold transition" 
                        title="Keluar dari sistem">
                    <x-app-icon name="nav.logout" class="w-4 h-4 text-slate-600" />
                    <span>Keluar</span>
                </button>
            </form>
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

            <!-- + Transfer -->
            <a href="{{ route('driver.transfer.create') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('driver.transfer.*') ? 'text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <x-app-icon name="nav.transfer" class="w-5 h-5 mb-0.5" />
                <span class="text-xs">+ Transfer</span>
            </a>

            <!-- + Kredit -->
            <a href="{{ route('driver.credit.create') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('driver.credit.*') ? 'text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <x-app-icon name="nav.credit" class="w-5 h-5 mb-0.5" />
                <span class="text-xs">+ Bon Kredit</span>
            </a>

            <!-- Riwayat -->
            <a href="{{ route('driver.history') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition {{ request()->routeIs('driver.history') ? 'text-slate-900 font-bold' : 'text-slate-500 hover:text-slate-900 font-medium' }}">
                <x-app-icon name="nav.history" class="w-5 h-5 mb-0.5" />
                <span class="text-xs">Riwayat</span>
            </a>
        </div>
    </nav>

    @stack('scripts')
</body>
</html>
