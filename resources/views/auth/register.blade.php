<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Baru | CargoTrace - Global Shipment Visibility</title>
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

    <!-- Register Container Card -->
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
                        Bergabung dengan Jaringan Logistik Global
                    </h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Dapatkan akses visibilitas pengiriman internasional, estimasi waktu tiba (ETA) akurat, dan pelacakan kontainer multi-moda dalam satu platform terpadu.
                    </p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-800/80 text-xs text-slate-400">
                <div class="flex items-center gap-2 text-cyan-400 font-medium">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>Registrasi Publik Resmi: Akses Customer</span>
                </div>
                <p class="text-[11px] text-slate-500 mt-1">Akun Administrator & Operator dibuat melalui otorisasi internal sistem.</p>
            </div>
        </div>

        <!-- Right Form Panel -->
        <div class="p-8 md:p-12 flex flex-col justify-center bg-slate-900/40">
            <div class="mb-5">
                <h3 class="text-xl font-bold text-white">Buat Akun Pelanggan</h3>
                <p class="text-xs text-slate-400 mt-1">Lengkapi informasi berikut untuk memulai.</p>
            </div>

            @if($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-300 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required placeholder="Contoh: Andi Pratama" class="w-full px-3.5 py-2 text-xs bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                </div>

                <div>
                    <label for="email" class="block text-xs font-medium text-slate-300 mb-1">Alamat Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="nama@perusahaan.com" class="w-full px-3.5 py-2 text-xs bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                </div>

                <div>
                    <label for="company_name" class="block text-xs font-medium text-slate-300 mb-1">Perusahaan / Organisasi (Opsional)</label>
                    <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" placeholder="PT Sumber Kargo Makmur" class="w-full px-3.5 py-2 text-xs bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="password" class="block text-xs font-medium text-slate-300 mb-1">Kata Sandi</label>
                        <input type="password" id="password" name="password" required placeholder="Min. 8 karakter" class="w-full px-3.5 py-2 text-xs bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-medium text-slate-300 mb-1">Ulangi Sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi kata sandi" class="w-full px-3.5 py-2 text-xs bg-slate-800/80 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 transition-all">
                    </div>
                </div>

                <button type="submit" class="w-full mt-2 py-2.5 px-4 rounded-xl text-xs font-semibold text-white bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 focus:outline-none focus:ring-2 focus:ring-cyan-500 shadow-lg shadow-cyan-600/30 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Daftar Akun Baru</span>
                </button>
            </form>

            <div class="mt-4 text-center">
                <p class="text-xs text-slate-400">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="text-cyan-400 hover:text-cyan-300 font-semibold transition-colors">Masuk di sini</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>

