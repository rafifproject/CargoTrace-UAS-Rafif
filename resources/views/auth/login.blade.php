<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke Akun | CargoTrace - Global Shipment Visibility</title>
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-[#050C1A] text-slate-100 flex items-center justify-center p-4 relative overflow-hidden">

    <!-- Earth Background with Orbit Glow Overlay -->
    <div class="absolute inset-0 z-0">
        <img src="/images/earth-bg.jpg" alt="Earth from Space" class="w-full h-full object-cover object-center opacity-70">
        <div class="absolute inset-0 bg-gradient-to-t from-[#050C1A] via-transparent to-[#050C1A]/80"></div>
        <div class="absolute inset-0 bg-[#0A1128]/40 backdrop-blur-[2px]"></div>
    </div>

    <!-- Login Container Card -->
    <div class="relative z-10 w-full max-w-4xl grid md:grid-cols-2 bg-slate-900/60 backdrop-blur-xl border border-slate-700/60 rounded-3xl shadow-2xl overflow-hidden">
        
        <!-- Left Branding Panel -->
        <div class="p-8 md:p-12 flex flex-col justify-between border-b md:border-b-0 md:border-r border-slate-800/80 bg-gradient-to-br from-slate-900/80 to-transparent">
            <div>
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-6">
                    <img src="/images/logo.png" alt="CargoTrace Logo" class="h-10 w-auto object-contain">
                    <div>
                        <h1 class="text-xl font-bold tracking-tight text-white">CargoTrace</h1>
                        <p class="text-[11px] text-cyan-400 font-semibold tracking-wider uppercase">Global Visibility</p>
                    </div>
                </div>

                <div class="space-y-3 mt-8">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-white leading-tight">
                        Platform Visibilitas & Pelacakan Pengiriman Global
                    </h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Sistem pemantauan rute kargo internasional, pelabuhan, status cuaca, peringatan keterlambatan, dan intelijen risiko logistik berskala dunia.
                    </p>
                </div>
            </div>

            <!-- Demo Credential Autofill Shortcut for UAS Presentation -->
            <div class="mt-8 pt-6 border-t border-slate-800/80">
                <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Akun Demo (Klik untuk Isi Cepat):</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="fillLogin('admin@cargotrace.id', 'password')" class="px-2.5 py-1 text-xs rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 transition-colors">
                        Admin
                    </button>
                    <button type="button" onclick="fillLogin('operator@cargotrace.id', 'password')" class="px-2.5 py-1 text-xs rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 transition-colors">
                        Operator
                    </button>
                    <button type="button" onclick="fillLogin('customer@globalmart.com', 'password')" class="px-2.5 py-1 text-xs rounded-lg bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 transition-colors">
                        Customer
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="p-8 md:p-12 flex flex-col justify-center bg-slate-900/40">
            <div class="mb-6">
                <h3 class="text-xl font-bold text-white">Masuk ke Akun</h3>
                <p class="text-xs text-slate-400 mt-1">Silakan masukkan email dan kata sandi Anda.</p>
            </div>

            @if(session('status'))
                <div class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 text-xs">
                    {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300 mb-1.5">Alamat Email</label>
                    <div class="relative">
                        <i data-lucide="mail" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@perusahaan.com" class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-medium text-slate-300">Kata Sandi</label>
                        <a href="javascript:alert('Gunakan kata sandi demo: password');" class="text-[11px] text-cyan-400 hover:text-cyan-300 transition-colors">Lupa kata sandi?</a>
                    </div>
                    <div class="relative">
                        <i data-lucide="lock" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="password" id="password" name="password" required placeholder="••••••••" class="w-full pl-10 pr-4 py-2.5 text-xs bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-transparent transition-all">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-cyan-500 focus:ring-cyan-500 focus:ring-offset-slate-900">
                    <label for="remember" class="ml-2 text-xs text-slate-300">Ingat saya pada perangkat ini</label>
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 shadow-lg shadow-cyan-600/30 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="log-in" class="w-4 h-4"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-xs text-slate-400">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold transition-colors">Daftar sekarang</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function fillLogin(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }
    </script>
</body>
</html>

