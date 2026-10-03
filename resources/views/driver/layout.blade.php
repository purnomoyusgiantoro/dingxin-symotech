<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Sopir') - Dingxin Distribution</title>

    <!-- Tailwind CSS CDN with typography and forms -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#1e3a8a',
                            900: '#0f172a',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', '-apple-system', 'Segoe UI', 'Roboto', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        /* Mobile safe-area padding for modern devices */
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom, 1rem);
        }
    </style>
    @stack('styles')
</head>
<body class="flex flex-col min-h-screen text-slate-800 antialiased selection:bg-brand-500 selection:text-white">

    <!-- Top App Bar (Sticky Mobile Header) -->
    <header class="sticky top-0 z-30 bg-slate-900 text-white shadow-md border-b border-slate-800">
        <div class="max-w-lg mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-600 to-blue-400 flex items-center justify-center font-black text-white text-base shadow">
                    DX
                </div>
                <div>
                    <h1 class="text-xs font-semibold text-slate-400 uppercase tracking-wider leading-tight">Dingxin Portal</h1>
                    <div class="flex items-center space-x-1.5">
                        <span class="text-sm font-bold text-white tracking-wide">
                            {{ Auth::user()->driver->driver_code ?? Auth::user()->username }}
                        </span>
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            Aktif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Profile & Area -->
            <div class="flex items-center space-x-2">
                <div class="text-right hidden sm:block">
                    <div class="text-xs font-semibold text-white">{{ Auth::user()->name }}</div>
                    <div class="text-[10px] text-slate-400">Area: {{ Auth::user()->driver->area_code ?? '-' }}</div>
                </div>

                <!-- Logout Trigger -->
                <form action="{{ route('driver.logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?');">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition" title="Logout">
                        <i data-lucide="log-out" class="w-5 h-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <!-- Main Container (Limited to mobile-friendly width) -->
    <main class="flex-1 w-full max-w-lg mx-auto px-4 py-4 mb-20">

        <!-- Notification Alerts -->
        @if (session('success'))
            <div class="mb-4 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start space-x-2.5 shadow-sm animate-fade-in">
                <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                <div class="font-medium flex-1">{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-start space-x-2.5 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                <div class="font-medium flex-1">{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm space-y-1 shadow-sm">
                <div class="font-semibold flex items-center space-x-1.5 text-rose-700">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    <span>Terdapat kesalahan input:</span>
                </div>
                <ul class="list-disc list-inside text-rose-600 pl-1 space-y-0.5">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Bottom Navigation Bar (Mobile Native App Style) -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 shadow-lg pb-safe">
        <div class="max-w-lg mx-auto px-6 h-16 flex items-center justify-between">
            <!-- Beranda / Dashboard -->
            <a href="{{ route('driver.dashboard') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('driver.dashboard') ? 'text-brand-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <i data-lucide="home" class="w-5 h-5 mb-1 transition-transform group-active:scale-90"></i>
                <span class="text-[11px]">Beranda</span>
            </a>

            <!-- Input Transfer -->
            <a href="{{ route('driver.transfer.create') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('driver.transfer.*') ? 'text-brand-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <i data-lucide="arrow-up-right" class="w-5 h-5 mb-1 transition-transform group-active:scale-90"></i>
                <span class="text-[11px]">+ Transfer</span>
            </a>

            <!-- Input Kredit -->
            <a href="{{ route('driver.credit.create') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('driver.credit.*') ? 'text-brand-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <i data-lucide="file-text" class="w-5 h-5 mb-1 transition-transform group-active:scale-90"></i>
                <span class="text-[11px]">+ Kredit</span>
            </a>

            <!-- Riwayat -->
            <a href="{{ route('driver.history') }}" 
               class="flex flex-col items-center justify-center flex-1 py-1 transition group {{ request()->routeIs('driver.history') ? 'text-brand-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <i data-lucide="history" class="w-5 h-5 mb-1 transition-transform group-active:scale-90"></i>
                <span class="text-[11px]">Riwayat</span>
            </a>
        </div>
    </nav>

    <!-- Init Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
