<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Dingxin Symotech Distribution</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 sm:p-6 bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-100">

    <div class="w-full max-w-md space-y-6">
        
        <!-- Header Branding -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-brand-600 to-cyan-500 shadow-xl shadow-brand-500/20 text-white font-extrabold text-2xl tracking-tight">
                DX
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">Dingxin Symotech Portal</h1>
            <p class="text-sm text-slate-400">Portal Distribusi Sopir & Operasional Setoran</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700/80 rounded-2xl p-6 sm:p-8 shadow-2xl space-y-6">
            
            @if (session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-300 text-xs flex items-center space-x-2">
                    <i data-lucide="check-circle" class="w-4 h-4 shrink-0 text-emerald-400"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs flex items-center space-x-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0 text-rose-400"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('warning'))
                <div class="p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-300 text-xs flex items-center space-x-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0 text-amber-400"></i>
                    <span>{{ session('warning') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-xs space-y-1">
                    <div class="font-semibold flex items-center space-x-1.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                        <span>Autentikasi Gagal:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-rose-200">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('driver.login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input Kode Sopir / Username -->
                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Username / Kode Sopir
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user-check" class="w-5 h-5"></i>
                        </div>
                        <input type="text" 
                               id="login" 
                               name="login" 
                               value="{{ old('login') }}" 
                               required 
                               autocomplete="username"
                               autofocus
                               placeholder="TGL1.2 / kasir / admin1 / gm" 
                               class="w-full pl-11 pr-4 py-3 bg-slate-900/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                    </div>
                </div>

                <!-- Input Password -->
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-5 h-5"></i>
                        </div>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required 
                               autocomplete="current-password"
                               placeholder="••••••••" 
                               class="w-full pl-11 pr-11 py-3 bg-slate-900/90 border border-slate-700 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                        <button type="button" 
                                onclick="togglePasswordVisibility()" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-white transition">
                            <i id="eye-icon" data-lucide="eye" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 text-slate-300 cursor-pointer">
                        <input type="checkbox" name="remember" checked class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-brand-600 focus:ring-brand-500 focus:ring-offset-slate-800">
                        <span>Ingat saya di perangkat ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" 
                        class="w-full py-3.5 px-4 bg-gradient-to-r from-brand-600 to-blue-600 hover:from-brand-500 hover:to-blue-500 active:scale-[0.99] text-white font-semibold text-sm rounded-xl shadow-lg shadow-brand-600/30 flex items-center justify-center space-x-2 transition cursor-pointer">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Akun</span>
                </button>
            </form>

            <!-- Quick Demo Credentials Helper -->
            <div class="border-t border-slate-700/60 pt-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Isi Cepat Akun Demo:</span>
                    <span class="text-[11px] text-slate-500">Pass: <code class="text-slate-300">password</code></span>
                </div>
                <div class="flex flex-wrap gap-1.5 text-xs">
                    <button type="button" onclick="fillAccount('TGL1.2', 'password')" class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-[11px] font-medium transition cursor-pointer">
                        🚚 Sopir TGL1.2
                    </button>
                    <button type="button" onclick="fillAccount('BRS1.2', 'password')" class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-[11px] font-medium transition cursor-pointer">
                        🚚 Sopir BRS1.2
                    </button>
                    <button type="button" onclick="fillAccount('kasir', 'password')" class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-[11px] font-medium transition cursor-pointer">
                        💵 Kasir
                    </button>
                    <button type="button" onclick="fillAccount('admin1', 'password')" class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-[11px] font-medium transition cursor-pointer">
                        📦 Admin 1
                    </button>
                    <button type="button" onclick="fillAccount('gm', 'password')" class="px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 text-[11px] font-medium transition cursor-pointer">
                        👑 GM
                    </button>
                </div>
            </div>

            <!-- Link to Filament Panel -->
            <div class="border-t border-slate-700/60 pt-3 text-center">
                <a href="/admin/login" class="inline-flex items-center space-x-1.5 text-xs text-brand-400 hover:text-brand-300 font-medium transition">
                    <span>Akses Panel Admin / Kasir / GM Langsung</span>
                    <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </div>

        <p class="text-center text-[11px] text-slate-500">
            &copy; {{ date('Y') }} PT Dingxin Multi Distribusi. Hak cipta dilindungi.
        </p>
    </div>

    <script>
        lucide.createIcons();

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }

        function fillAccount(username, password) {
            document.getElementById('login').value = username;
            document.getElementById('password').value = password;
        }
    </script>
</body>
</html>
