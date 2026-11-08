<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Customer | CargoTrace - Global Shipment Visibility</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(10, 20, 50, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(100, 160, 255, 0.18);
        }
        .input-field {
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(100, 160, 255, 0.22);
            color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field::placeholder { color: rgba(180, 200, 240, 0.4); }
        .input-field:focus {
            outline: none;
            border-color: #3B82F6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.18);
        }
        .btn-register {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 50%, #1E40AF 100%);
            box-shadow: 0 4px 24px rgba(37,99,235,0.45);
            transition: all 0.2s;
        }
        .btn-register:hover {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 50%, #1D4ED8 100%);
            box-shadow: 0 6px 32px rgba(59,130,246,0.55);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="h-full relative overflow-y-auto" style="background:#010A1B;">

    {{-- ===== FULL PAGE BACKGROUND ===== --}}
    <div class="fixed inset-0 z-0">
        <img src="/images/earth-bg.jpg" alt="Global Cargo Network" 
             class="w-full h-full object-cover object-center"
             style="object-position: 55% center;">
        <div class="absolute inset-0" style="background: linear-gradient(100deg, rgba(1,10,27,0.7) 0%, rgba(1,10,27,0.2) 55%, rgba(1,10,27,0.85) 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(1,10,27,0.85) 0%, transparent 40%, rgba(1,10,27,0.3) 100%);"></div>
    </div>

    {{-- ===== MAIN LAYOUT ===== --}}
    <div class="relative z-10 min-h-screen flex items-center justify-between px-8 md:px-16 lg:px-24 xl:px-32 py-10">

        {{-- LEFT: Branding Panel --}}
        <div class="flex-1 max-w-xl pr-8 hidden md:flex flex-col justify-center">
            {{-- Logo --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 flex-shrink-0">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <polygon points="32,4 56,18 56,46 32,60 8,46 8,18" fill="rgba(0,168,232,0.15)" stroke="#00A8E8" stroke-width="1.5"/>
                        <rect x="20" y="22" width="24" height="20" rx="2" fill="none" stroke="#00A8E8" stroke-width="2"/>
                        <line x1="20" y1="30" x2="44" y2="30" stroke="#00A8E8" stroke-width="2"/>
                        <line x1="32" y1="22" x2="32" y2="30" stroke="#00A8E8" stroke-width="2"/>
                        <ellipse cx="32" cy="32" rx="22" ry="8" fill="none" stroke="rgba(0,168,232,0.5)" stroke-width="1.2" transform="rotate(-15 32 32)"/>
                        <path d="M44 20 L50 14 M44 20 L48 24 M44 20 L40 22" stroke="#00A8E8" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-4xl font-black tracking-tight leading-none" style="color:#fff;">
                        Cargo<span style="color:#00A8E8;">Trace</span>
                    </h1>
                    <p class="text-sm font-medium mt-1" style="color:rgba(180,220,255,0.75);">Global Shipment Visibility &</p>
                    <p class="text-sm font-medium" style="color:rgba(180,220,255,0.75);">Tracking Platform</p>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-2xl lg:text-3xl font-extrabold text-white leading-tight">
                    Pantau Pengiriman Kargo Anda Secara Real-Time
                </h2>
                <p class="text-xs text-slate-300 leading-relaxed max-w-md">
                    Daftar akun Customer untuk mendapatkan akses visibilitas pengiriman internasional, pelacakan rute peta satelit, riwayat timeline kontainer, dan notifikasi status langsung ke perangkat Anda.
                </p>
            </div>

            <div class="mt-8 pt-6 border-t border-slate-700/60 flex items-center gap-2 text-cyan-400 text-xs font-semibold">
                <i data-lucide="shield-check" class="w-4 h-4"></i>
                <span>Registrasi Publik Resmi: Portal Akses Customer</span>
            </div>
        </div>

        {{-- RIGHT: Register Card (Mockup 02) --}}
        <div class="w-full md:w-auto md:min-w-[420px] md:max-w-[460px]">
            <div class="glass-card rounded-3xl p-8 shadow-2xl">

                {{-- Header --}}
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4 md:hidden">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8">
                            <polygon points="32,4 56,18 56,46 32,60 8,46 8,18" fill="rgba(0,168,232,0.15)" stroke="#00A8E8" stroke-width="1.5"/>
                            <rect x="20" y="22" width="24" height="20" rx="2" fill="none" stroke="#00A8E8" stroke-width="2"/>
                            <line x1="20" y1="30" x2="44" y2="30" stroke="#00A8E8" stroke-width="2"/>
                            <line x1="32" y1="22" x2="32" y2="30" stroke="#00A8E8" stroke-width="2"/>
                        </svg>
                        <span class="text-xl font-black text-white">Cargo<span style="color:#00A8E8;">Trace</span></span>
                    </div>

                    <h2 class="text-2xl font-bold text-white leading-tight">Buat Akun Customer</h2>
                    <p class="text-xs mt-1" style="color:rgba(160,195,240,0.8);">
                        Buat akun untuk memantau pengiriman Anda.
                    </p>
                </div>

                {{-- Errors --}}
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl text-xs font-medium flex items-center gap-2"
                         style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#FCA5A5;">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
                    @csrf

                    {{-- Nama Lengkap --}}
                    <div>
                        <label for="name" class="block text-xs font-medium text-slate-300 mb-1">Nama Lengkap <span class="text-rose-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <i data-lucide="user" class="w-4 h-4" style="color:rgba(130,170,220,0.6);"></i>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                                   placeholder="Masukkan nama lengkap"
                                   class="input-field w-full pl-10 pr-4 py-2.5 rounded-xl text-xs">
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-xs font-medium text-slate-300 mb-1">Alamat Email <span class="text-rose-400">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <i data-lucide="mail" class="w-4 h-4" style="color:rgba(130,170,220,0.6);"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                   placeholder="nama@perusahaan.com"
                                   class="input-field w-full pl-10 pr-4 py-2.5 rounded-xl text-xs">
                        </div>
                    </div>

                    {{-- Perusahaan (Opsional) --}}
                    <div>
                        <label for="company_name" class="block text-xs font-medium text-slate-300 mb-1">Perusahaan / Organisasi (Opsional)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                                <i data-lucide="building" class="w-4 h-4" style="color:rgba(130,170,220,0.6);"></i>
                            </div>
                            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}"
                                   placeholder="PT IndoNusa Logistics"
                                   class="input-field w-full pl-10 pr-4 py-2.5 rounded-xl text-xs">
                        </div>
                    </div>

                    {{-- Kata Sandi & Konfirmasi --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="password" class="block text-xs font-medium text-slate-300 mb-1">Kata Sandi <span class="text-rose-400">*</span></label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                       placeholder="••••••••"
                                       class="input-field w-full px-3.5 py-2.5 rounded-xl text-xs">
                            </div>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-medium text-slate-300 mb-1">Ulangi Sandi <span class="text-rose-400">*</span></label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required
                                       placeholder="••••••••"
                                       class="input-field w-full px-3.5 py-2.5 rounded-xl text-xs">
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="btn-register w-full mt-2 py-3 px-4 rounded-xl text-xs font-bold text-white flex items-center justify-center gap-2">
                        <i data-lucide="user-plus" class="w-4 h-4"></i>
                        Daftar
                    </button>
                </form>

                {{-- Login Link --}}
                <div class="mt-5 text-center">
                    <p class="text-xs" style="color:rgba(130,170,220,0.7);">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                           class="font-semibold transition-colors ml-1"
                           style="color:#00A8E8;"
                           onmouseover="this.style.color='#38BDF8'"
                           onmouseout="this.style.color='#00A8E8'">
                            Masuk sekarang
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>
</html>
