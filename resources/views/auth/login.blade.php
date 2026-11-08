<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk ke CargoTrace | Global Shipment Visibility & Tracking Platform</title>
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        
        /* Modern deep space background with fallback */
        .bg-space-network {
            background-color: #020817;
            background-image: url('{{ asset('images/earth-bg.jpg') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Glassmorphism Card exactly matching the UAS mockup */
        .glass-card {
            background: rgba(11, 23, 50, 0.65);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(0, 180, 255, 0.22);
            box-shadow: 0 0 45px -5px rgba(0, 168, 232, 0.18), 0 25px 60px -15px rgba(0, 0, 0, 0.7);
        }

        /* Form Inputs */
        .input-dark {
            background: rgba(15, 28, 60, 0.75);
            border: 1px solid rgba(100, 160, 240, 0.25);
            color: #ffffff;
            transition: all 0.2s ease-in-out;
        }
        .input-dark::placeholder {
            color: rgba(160, 195, 240, 0.5);
        }
        .input-dark:focus {
            outline: none;
            border-color: #00B4D8;
            box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.2);
            background: rgba(18, 35, 75, 0.85);
        }

        /* Primary Action Button (Bright Cyan-Blue Pill) */
        .btn-cyan-glow {
            background: linear-gradient(135deg, #00B4D8 0%, #0077B6 50%, #023E8A 100%);
            box-shadow: 0 4px 20px rgba(0, 180, 216, 0.4);
            transition: all 0.2s ease-in-out;
        }
        .btn-cyan-glow:hover {
            background: linear-gradient(135deg, #00C7EC 0%, #0088D4 50%, #0056B3 100%);
            box-shadow: 0 6px 28px rgba(0, 199, 236, 0.55);
            transform: translateY(-1px);
        }
    </style>
</head>
<body class="h-full bg-space-network relative overflow-x-hidden min-h-screen flex items-center justify-center">

    {{-- Gradient Atmospheric Overlays --}}
    <div class="fixed inset-0 pointer-events-none z-0" 
         style="background: radial-gradient(circle at 75% 50%, rgba(2, 8, 23, 0.3) 0%, rgba(2, 8, 23, 0.8) 100%);">
    </div>

    {{-- Main Container --}}
    <div class="relative z-10 w-full max-w-7xl mx-auto px-6 sm:px-10 lg:px-16 py-12 flex flex-col lg:flex-row items-center justify-between gap-12 lg:gap-16">

        {{-- LEFT COLUMN: Glowing 3D Logo & Brand Presentation --}}
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-start text-left">
            
            <div class="flex items-center gap-5 mb-3">
                {{-- Isometric 3D Cargo Box with Orbit & Airplanes (Official Logo Mockup) --}}
                <div class="w-20 h-20 sm:w-24 sm:h-24 flex-shrink-0 drop-shadow-[0_0_25px_rgba(0,194,255,0.6)]">
                    <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                        <defs>
                            <linearGradient id="cubeTop" x1="60" y1="20" x2="60" y2="55" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#38BDF8"/>
                                <stop offset="100%" stop-color="#0284C7"/>
                            </linearGradient>
                            <linearGradient id="cubeLeft" x1="25" y1="40" x2="60" y2="95" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#0369A1"/>
                                <stop offset="100%" stop-color="#075985"/>
                            </linearGradient>
                            <linearGradient id="cubeRight" x1="60" y1="40" x2="95" y2="95" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#0284C7"/>
                                <stop offset="100%" stop-color="#0369A1"/>
                            </linearGradient>
                            <linearGradient id="orbitGlow" x1="10" y1="85" x2="110" y2="35" gradientUnits="userSpaceOnUse">
                                <stop offset="0%" stop-color="#00F0FF" stop-opacity="0.9"/>
                                <stop offset="70%" stop-color="#00A8E8" stop-opacity="0.9"/>
                                <stop offset="100%" stop-color="#38BDF8" stop-opacity="0"/>
                            </linearGradient>
                        </defs>

                        <!-- Isometric 3D Cube -->
                        <!-- Top Face -->
                        <polygon points="60,24 88,40 60,56 32,40" fill="url(#cubeTop)" stroke="#38BDF8" stroke-width="2.5" stroke-linejoin="round"/>
                        <!-- Left Face -->
                        <polygon points="32,40 60,56 60,88 32,72" fill="url(#cubeLeft)" stroke="#0284C7" stroke-width="2.5" stroke-linejoin="round"/>
                        <!-- Right Face -->
                        <polygon points="60,56 88,40 88,72 60,88" fill="url(#cubeRight)" stroke="#38BDF8" stroke-width="2.5" stroke-linejoin="round"/>
                        <!-- Internal Box Seams -->
                        <line x1="60" y1="56" x2="60" y2="88" stroke="#38BDF8" stroke-width="2"/>

                        <!-- Orbital Curved Swoosh Ring -->
                        <path d="M 16,84 C 18,98 52,106 82,90 C 98,82 106,68 102,52 C 100,44 94,38 88,34" 
                              fill="none" stroke="url(#orbitGlow)" stroke-width="4.5" stroke-linecap="round"/>

                        <!-- Airplane 1 (Mid-flight on upper orbit) -->
                        <g transform="translate(90, 48) rotate(-42)">
                            <path d="M0,0 L12,-4 L10,0 L14,3 L10,3 L8,6 L6,6 L7,2 L2,2 Z" fill="#00F0FF" stroke="#FFFFFF" stroke-width="0.8"/>
                        </g>

                        <!-- Airplane 2 (Following jet) -->
                        <g transform="translate(100, 36) rotate(-42) scale(0.85)">
                            <path d="M0,0 L12,-4 L10,0 L14,3 L10,3 L8,6 L6,6 L7,2 L2,2 Z" fill="#38BDF8" stroke="#FFFFFF" stroke-width="0.8"/>
                        </g>
                    </svg>
                </div>

                {{-- CargoTrace Wordmark --}}
                <div class="font-outfit">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-none">
                        Cargo<span class="text-[#00C2FF]">Trace</span>
                    </h1>
                </div>
            </div>

            {{-- Subtitles --}}
            <div class="mt-2 space-y-1">
                <p class="text-sm sm:text-base font-medium text-slate-200 tracking-wide">
                    Global Shipment Visibility &
                </p>
                <p class="text-sm sm:text-base font-medium text-slate-200 tracking-wide">
                    Tracking Platform
                </p>
            </div>

            {{-- Demo Accounts Quick-Fill Badge (For UAS Evaluation) --}}
            <div class="mt-8 pt-6 border-t border-slate-700/50 w-full max-w-md">
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 mb-2.5">
                    Akun Demo — Klik untuk Isi Cepat:
                </p>
                <div class="flex flex-wrap gap-2">
                    <button type="button" onclick="fillLogin('admin@cargotrace.id','password')"
                        class="px-3 py-1.5 text-xs rounded-xl border border-cyan-500/30 bg-cyan-950/40 text-cyan-300 font-medium hover:bg-cyan-900/60 transition-all flex items-center gap-1.5">
                        <i data-lucide="shield" class="w-3.5 h-3.5"></i> Administrator
                    </button>
                    <button type="button" onclick="fillLogin('operator@cargotrace.id','password')"
                        class="px-3 py-1.5 text-xs rounded-xl border border-emerald-500/30 bg-emerald-950/40 text-emerald-300 font-medium hover:bg-emerald-900/60 transition-all flex items-center gap-1.5">
                        <i data-lucide="settings" class="w-3.5 h-3.5"></i> Operator
                    </button>
                    <button type="button" onclick="fillLogin('customer@globalmart.com','password')"
                        class="px-3 py-1.5 text-xs rounded-xl border border-indigo-500/30 bg-indigo-950/40 text-indigo-300 font-medium hover:bg-indigo-900/60 transition-all flex items-center gap-1.5">
                        <i data-lucide="user" class="w-3.5 h-3.5"></i> Customer
                    </button>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Glassmorphism Login Card --}}
        <div class="w-full lg:w-auto lg:min-w-[420px] max-w-md">
            <div class="glass-card rounded-3xl p-8 sm:p-9 shadow-2xl relative">

                {{-- Language Selector Pill (Top Right) --}}
                <div class="flex justify-end mb-4">
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full border border-slate-600/60 bg-slate-800/80 text-xs text-white cursor-pointer hover:border-slate-500 transition-colors">
                        <span class="text-sm">🇮🇩</span>
                        <span class="font-semibold text-slate-200">ID</span>
                        <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                    </div>
                </div>

                {{-- Card Header --}}
                <div class="mb-7">
                    <h2 class="text-2xl sm:text-[26px] font-bold text-white tracking-tight leading-tight">
                        Masuk ke CargoTrace
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300/90 mt-2 leading-relaxed">
                        Kelola, pantau, dan lacak pengiriman global dalam satu platform.
                    </p>
                </div>

                {{-- Flash Message / Error Alerts --}}
                @if(session('status'))
                    <div class="mb-4 p-3 rounded-xl text-xs font-medium flex items-center gap-2 bg-emerald-950/60 border border-emerald-500/40 text-emerald-300">
                        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 rounded-xl text-xs font-medium flex items-center gap-2 bg-rose-950/60 border border-rose-500/40 text-rose-300">
                        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                {{-- Form Login --}}
                <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf

                    {{-- Email Input --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                               placeholder="Alamat Email"
                               class="input-dark w-full pl-11 pr-4 py-3 rounded-xl text-xs sm:text-sm">
                    </div>

                    {{-- Password Input with Show/Hide Toggle --}}
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                               placeholder="Kata Sandi"
                               class="input-dark w-full pl-11 pr-11 py-3 rounded-xl text-xs sm:text-sm">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-200">
                            <i data-lucide="eye" class="w-4 h-4" id="eye-icon"></i>
                        </button>
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-cyan-500 focus:ring-cyan-500">
                            <span class="text-xs text-slate-300">Ingat Saya</span>
                        </label>
                        <a href="javascript:alert('Gunakan password demo: password');" class="text-xs text-slate-300 hover:text-cyan-300 transition-colors">
                            Lupa kata sandi?
                        </a>
                    </div>

                    {{-- Submit Button (Rounded Pill Gradient) --}}
                    <button type="submit"
                            class="btn-cyan-glow w-full mt-3 py-3 px-5 rounded-2xl text-xs sm:text-sm font-semibold text-white flex items-center justify-center gap-2">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Masuk</span>
                    </button>
                </form>

                {{-- Register Link --}}
                <div class="mt-7 text-center">
                    <p class="text-xs text-slate-300">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}" class="font-semibold text-[#00C2FF] hover:text-cyan-300 transition-colors ml-1">
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
