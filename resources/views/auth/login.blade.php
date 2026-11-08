<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke CargoTrace | Global Shipment Visibility</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        * { font-family: 'Inter', sans-serif; }
        .glass-card {
            background: rgba(10, 20, 50, 0.72);
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
        .btn-login {
            background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 50%, #1E40AF 100%);
            box-shadow: 0 4px 24px rgba(37,99,235,0.45);
            transition: all 0.2s;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #3B82F6 0%, #2563EB 50%, #1D4ED8 100%);
            box-shadow: 0 6px 32px rgba(59,130,246,0.55);
            transform: translateY(-1px);
        }
        /* Animated glow dots on globe photo */
        @keyframes pulse-glow {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }
        .glow-dot { animation: pulse-glow 2.5s ease-in-out infinite; }
    </style>
</head>
<body class="h-full relative overflow-hidden" style="background:#010A1B;">

    {{-- ===== FULL PAGE BACKGROUND ===== --}}
    <div class="absolute inset-0 z-0">
        <img src="/images/earth-bg.jpg" alt="Global Cargo Network" 
             class="w-full h-full object-cover object-center"
             style="object-position: 55% center;">
        {{-- Dark gradient overlay – left darker for logo, right lighter for card area --}}
        <div class="absolute inset-0" style="background: linear-gradient(100deg, rgba(1,10,27,0.65) 0%, rgba(1,10,27,0.15) 55%, rgba(1,10,27,0.82) 100%);"></div>
        <div class="absolute inset-0" style="background: linear-gradient(to top, rgba(1,10,27,0.8) 0%, transparent 40%, rgba(1,10,27,0.3) 100%);"></div>
    </div>

    {{-- ===== MAIN LAYOUT ===== --}}
    <div class="relative z-10 min-h-screen flex items-center justify-between px-10 md:px-16 lg:px-24 xl:px-32 py-10">

        {{-- LEFT: Branding Panel --}}
        <div class="flex-1 max-w-xl pr-8 hidden md:flex flex-col justify-center">
            {{-- Logo --}}
            <div class="flex items-center gap-4 mb-10">
                {{-- SVG Logo Icon --}}
                <div class="w-16 h-16 flex-shrink-0">
                    <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <!-- Hexagon base -->
                        <polygon points="32,4 56,18 56,46 32,60 8,46 8,18" fill="rgba(0,168,232,0.15)" stroke="#00A8E8" stroke-width="1.5"/>
                        <!-- Package box -->
                        <rect x="20" y="22" width="24" height="20" rx="2" fill="none" stroke="#00A8E8" stroke-width="2"/>
                        <line x1="20" y1="30" x2="44" y2="30" stroke="#00A8E8" stroke-width="2"/>
                        <line x1="32" y1="22" x2="32" y2="30" stroke="#00A8E8" stroke-width="2"/>
                        <!-- Orbit ring -->
                        <ellipse cx="32" cy="32" rx="22" ry="8" fill="none" stroke="rgba(0,168,232,0.5)" stroke-width="1.2" transform="rotate(-15 32 32)"/>
                        <!-- Airplane -->
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

            {{-- Demo credentials - quick fill --}}
            <div class="mt-6">
                <p class="text-xs font-semibold uppercase tracking-widest mb-3" style="color:rgba(130,170,220,0.6);">Akun Demo — Klik untuk Isi Cepat</p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="fillLogin('admin@cargotrace.id','password')"
                        class="px-3 py-1.5 text-xs rounded-full border font-semibold transition-all hover:scale-105"
                        style="background:rgba(0,168,232,0.12);border-color:rgba(0,168,232,0.35);color:#67E8F9;">
                        🛡️ Administrator
                    </button>
                    <button type="button" onclick="fillLogin('operator@cargotrace.id','password')"
                        class="px-3 py-1.5 text-xs rounded-full border font-semibold transition-all hover:scale-105"
                        style="background:rgba(16,185,129,0.12);border-color:rgba(16,185,129,0.35);color:#6EE7B7;">
                        ⚙️ Operator
                    </button>
                    <button type="button" onclick="fillLogin('customer@globalmart.com','password')"
                        class="px-3 py-1.5 text-xs rounded-full border font-semibold transition-all hover:scale-105"
                        style="background:rgba(139,92,246,0.12);border-color:rgba(139,92,246,0.35);color:#C4B5FD;">
                        👤 Customer
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Login Card --}}
        <div class="w-full md:w-auto md:min-w-[400px] md:max-w-[440px]">
            <div class="glass-card rounded-3xl p-8 shadow-2xl">

                {{-- Card Header --}}
                <div class="mb-7">
                    {{-- Mobile logo (only visible on mobile) --}}
                    <div class="flex items-center gap-3 mb-5 md:hidden">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-9 h-9">
                            <polygon points="32,4 56,18 56,46 32,60 8,46 8,18" fill="rgba(0,168,232,0.15)" stroke="#00A8E8" stroke-width="1.5"/>
                            <rect x="20" y="22" width="24" height="20" rx="2" fill="none" stroke="#00A8E8" stroke-width="2"/>
                            <line x1="20" y1="30" x2="44" y2="30" stroke="#00A8E8" stroke-width="2"/>
                            <line x1="32" y1="22" x2="32" y2="30" stroke="#00A8E8" stroke-width="2"/>
                        </svg>
                        <span class="text-xl font-black text-white">Cargo<span style="color:#00A8E8;">Trace</span></span>
                    </div>

                    <h2 class="text-2xl font-bold text-white leading-tight">Masuk ke CargoTrace</h2>
                    <p class="text-sm mt-1.5" style="color:rgba(160,195,240,0.8);">
                        Kelola, pantau, dan lacak pengiriman global<br>dalam satu platform.
                    </p>
                </div>

                {{-- Alerts --}}
                @if(session('status'))
                    <div class="mb-5 p-3 rounded-xl text-xs font-medium flex items-center gap-2"
                         style="background:rgba(16,185,129,0.15);border:1px solid rgba(16,185,129,0.3);color:#6EE7B7;">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        {{ session('status') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-5 p-3 rounded-xl text-xs font-medium flex items-center gap-2"
                         style="background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);color:#FCA5A5;">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                {{-- Form --}}
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Email --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i data-lucide="mail" class="w-4 h-4" style="color:rgba(130,170,220,0.6);"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               required autofocus
                               placeholder="Alamat Email"
                               class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-sm">
                    </div>

                    {{-- Password --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i data-lucide="lock" class="w-4 h-4" style="color:rgba(130,170,220,0.6);"></i>
                        </div>
                        <input type="password" id="password" name="password"
                               required
                               placeholder="Kata Sandi"
                               class="input-field w-full pl-11 pr-12 py-3 rounded-xl text-sm">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-4"
                                style="color:rgba(130,170,220,0.6);">
                            <i data-lucide="eye" class="w-4 h-4" id="eye-icon"></i>
                        </button>
                    </div>

                    {{-- Remember + Forgot --}}
                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="remember" name="remember"
                                   class="w-4 h-4 rounded"
                                   style="accent-color:#3B82F6;">
                            <span class="text-xs" style="color:rgba(160,195,240,0.8);">Ingat Saya</span>
                        </label>
                        <a href="javascript:alert('Gunakan kata sandi demo: password');"
                           class="text-xs font-medium transition-colors"
                           style="color:rgba(160,195,240,0.8);"
                           onmouseover="this.style.color='#67E8F9'"
                           onmouseout="this.style.color='rgba(160,195,240,0.8)'">
                            Lupa kata sandi?
                        </a>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit"
                            class="btn-login w-full py-3 px-4 rounded-xl text-sm font-bold text-white flex items-center justify-center gap-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        Masuk
                    </button>
                </form>

                {{-- Register Link --}}
                <div class="mt-6 text-center">
                    <p class="text-sm" style="color:rgba(130,170,220,0.7);">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}"
                           class="font-semibold transition-colors ml-1"
                           style="color:#00A8E8;"
                           onmouseover="this.style.color='#38BDF8'"
                           onmouseout="this.style.color='#00A8E8'">
                            Daftar Sekarang
                        </a>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        function fillLogin(email, pass) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = pass;
        }

        function togglePassword() {
            const pw = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (pw.type === 'password') {
                pw.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                pw.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
