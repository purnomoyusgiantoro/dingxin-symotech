<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Masuk ke Akun - Dingxin Symotech</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="min-h-full flex items-center justify-center p-4 bg-slate-50 text-slate-900">

    <div class="w-full max-w-sm space-y-6">
        
        <!-- Header Bersih -->
        <div class="text-center space-y-1.5">
            <div class="w-12 h-12 mx-auto rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold text-lg shadow-sm">
                DX
            </div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Dingxin Symotech</h1>
            <p class="text-xs text-slate-500">Sistem Distribusi & Setoran Sopir</p>
        </div>

        <!-- Kartu Login -->
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm space-y-4">
            
            @if (session('success'))
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-medium">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-medium">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <p class="font-medium">{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('driver.login.post') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Input Kode Sopir / Username -->
                <div>
                    <label for="login" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kode Sopir / Username
                    </label>
                    <input type="text" 
                           id="login" 
                           name="login" 
                           value="{{ old('login') }}" 
                           required 
                           autocomplete="username"
                           autofocus
                           placeholder="Contoh: TGL1.2 / sopir" 
                           class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-base focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <!-- Input Kata Sandi -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kata Sandi
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="••••••••" 
                           class="w-full px-3.5 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 text-base focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-slate-900 transition">
                </div>

                <!-- Tombol Masuk -->
                <div class="pt-1">
                    <button type="submit" 
                            class="w-full py-3.5 px-4 bg-slate-900 hover:bg-black active:scale-[0.99] text-white font-bold text-base rounded-xl transition cursor-pointer shadow-sm">
                        Masuk ke Sistem
                    </button>
                </div>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400">
            PT Dingxin Multi Distribusi
        </p>
    </div>

</body>
</html>
