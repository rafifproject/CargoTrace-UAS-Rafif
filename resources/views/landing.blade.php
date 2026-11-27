<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CargoTrace — {{ __('Ekspor Kopi Spesialti Brasil ke Pasar Dunia') }} | Santos Exporters S.A.</title>
    
    <!-- Favicon: CargoTrace Official 3D Box & Orbit Logo -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
    
    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Outfit:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS & Lucide Icons -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        
        /* =====================================================================
           THEME VARIABLES: DARK MODE (Default) & CLEAN LIGHT MODE
           ===================================================================== */
        :root {
            --bg-page: #060C1E;
            --bg-alt: #08122B;
            --text-heading: #FFFFFF;
            --text-body: #CBD5E1;
            --text-muted: #94A3B8;
            --brand-cyan: #00C2FF;
            --brand-glow: rgba(0, 194, 255, 0.5);
            
            --nav-bg: rgba(6, 12, 30, 0.75);
            --nav-border: rgba(255, 255, 255, 0.12);
            --nav-link: #CBD5E1;
            --nav-active-bg: #00C2FF;
            --nav-active-text: #060C1E;
            
            --hero-bg-img: url('{{ asset('images/hero-bg-dark-v2.jpg') }}?v={{ filemtime(public_path('images/hero-bg-dark-v2.jpg')) }}');
            --hero-overlay-start: rgba(4, 14, 34, 0.76);
            --hero-overlay-mid: rgba(4, 14, 34, 0.36);
            
            --tentang-bg-img: url('{{ asset('images/tentang-kami-bg-dark-v2.jpg') }}?v={{ time() }}');
            --produk-bg-img: url('{{ asset('images/produk-bg-dark-v2.jpg') }}?v={{ time() }}');
            --alur-bg-img: url('{{ asset('images/alur-sistem-bg-dark.jpg') }}');
            --kontak-bg-img: url('{{ asset('images/kontak-port-santos-bg.jpg') }}');
            --kontak-bg-color: #001128;
            --kontak-border-color: rgba(6, 182, 212, 0.15);
            --kontak-heading-color: #FFFFFF;
            --kontak-body-color: #CBD5E1;
            --kontak-muted-color: #94A3B8;
            --kontak-icon-bg: rgba(6, 26, 68, 0.85);
            --kontak-icon-border: rgba(0, 194, 255, 0.4);
            --kontak-icon-color: #00C2FF;
            --kontak-mask-gradient: linear-gradient(to right, #001128 0%, rgba(0, 17, 40, 0.85) 45%, transparent 100%);
            
            --card-bg: rgba(11, 23, 50, 0.75);
            --card-border: rgba(0, 194, 255, 0.22);
            --card-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.6);
            --card-hover-border: rgba(0, 194, 255, 0.5);
            
            --btn-primary-bg: #00C2FF;
            --btn-primary-text: #060C1E;
            --btn-secondary-bg: rgba(255, 255, 255, 0.12);
            --btn-secondary-border: rgba(255, 255, 255, 0.3);
            --btn-secondary-text: #FFFFFF;
            
            --wave-1: #00C2FF;
            --wave-2: #0284C7;
        }

        /* Clean / Light Mode (Exact matches to user provided Clean Background & Mockup) */
        html.theme-clean {
            --bg-page: #F8FAFC;
            --bg-alt: #FFFFFF;
            --text-heading: #0A1938;
            --text-body: #334155;
            --text-muted: #64748B;
            --brand-cyan: #0070F3;
            --brand-glow: rgba(0, 112, 243, 0.3);
            
            --nav-bg: rgba(255, 255, 255, 0.88);
            --nav-border: rgba(226, 232, 240, 0.9);
            --nav-link: #334155;
            --nav-active-bg: #0070F3;
            --nav-active-text: #FFFFFF;
            
            --hero-bg-img: url('{{ asset('images/hero-bg-clean-v2.jpg') }}?v={{ filemtime(public_path('images/hero-bg-clean-v2.jpg')) }}');
            --hero-overlay-start: rgba(255, 255, 255, 0.86);
            --hero-overlay-mid: rgba(255, 255, 255, 0.42);
            
            --tentang-bg-img: url('{{ asset('images/tentang-kami-bg.jpg') }}');
            --produk-bg-img: url('{{ asset('images/produk-bg-clean-v2.jpg') }}?v={{ time() }}');
            --alur-bg-img: url('{{ asset('images/alur-sistem-bg.jpg') }}');
            --kontak-bg-img: url('{{ asset('images/kontak-port-santos-bg-clean.jpg') }}');
            --kontak-bg-color: #F8FAFC;
            --kontak-border-color: rgba(226, 232, 240, 0.9);
            --kontak-heading-color: #0A1938;
            --kontak-body-color: #334155;
            --kontak-muted-color: #64748B;
            --kontak-icon-bg: rgba(240, 249, 255, 0.95);
            --kontak-icon-border: rgba(0, 112, 243, 0.3);
            --kontak-icon-color: #0070F3;
            --kontak-mask-gradient: linear-gradient(to right, #F8FAFC 0%, rgba(248, 250, 252, 0.65) 26%, transparent 48%);
            
            --card-bg: rgba(255, 255, 255, 0.92);
            --card-border: rgba(203, 213, 225, 0.85);
            --card-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08);
            --card-hover-border: rgba(0, 112, 243, 0.4);
            
            --btn-primary-bg: #0070F3;
            --btn-primary-text: #FFFFFF;
            --btn-secondary-bg: rgba(255, 255, 255, 0.9);
            --btn-secondary-border: #CBD5E1;
            --btn-secondary-text: #0A1938;
            
            --wave-1: #0070F3;
            --wave-2: #38BDF8;
        }

        body {
            background-color: var(--bg-page);
            color: var(--text-body);
            overflow-x: hidden;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Dynamic Glassmorphism Cards */
        .theme-card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--card-border);
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .theme-card:hover {
            transform: translateY(-4px);
            border-color: var(--card-hover-border);
            box-shadow: 0 16px 36px -10px var(--brand-glow);
        }

        /* Hero Container with Dynamic Background Image */
        .hero-panoramic {
            background-image: var(--hero-bg-img);
            background-size: cover;
            background-position: center bottom;
            background-repeat: no-repeat;
            transition: background-image 0.5s ease;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--bg-page); }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--brand-cyan); }

        /* Hide scrollbars for clean mobile touch swipe */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Floating 3D Micro-animation */
        @keyframes floatSlow {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }
        .animate-float {
            animation: floatSlow 4s ease-in-out infinite;
        }

        /* Hero Staggered Entrance & Live Route Animations */
        @keyframes heroFadeUp {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes dashFlow {
            to { stroke-dashoffset: -28; }
        }
        .hero-anim-1 { animation: heroFadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .hero-anim-2 { animation: heroFadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.1s both; }
        .hero-anim-3 { animation: heroFadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.2s both; }
        .hero-anim-4 { animation: heroFadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.3s both; }
        .hero-anim-5 { animation: heroFadeUp 0.65s cubic-bezier(0.22, 1, 0.36, 1) 0.4s both; }
        .route-dash-anim {
            stroke-dasharray: 5 4;
            animation: dashFlow 1.6s linear infinite;
        }

        /* Floating Transparent Navbar (No Rectangle at Top) */
        #navbar {
            background: transparent;
            border-color: transparent;
        }
        #navbar.is-scrolled {
            background: var(--nav-bg);
            border-color: var(--nav-border);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        /* Hero Tracking Glass Card */
        .hero-track-card {
            background: rgba(8, 25, 58, 0.52);
            border: 1px solid rgba(56, 189, 248, 0.28);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        html.theme-clean .hero-track-card {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow: 0 12px 32px -8px rgba(15, 23, 42, 0.12);
        }

        /* Alur Sistem Vector Typography */
        .theme-clean .alur-title { color: #0A1938; }
        .theme-clean .alur-subtitle { color: #0070F3; }
        .theme-clean .alur-body { color: #334155; }
        .theme-clean .alur-badge { background: rgba(0, 112, 243, 0.12); color: #0070F3; border-color: rgba(0, 112, 243, 0.25); }

        html:not(.theme-clean) .alur-title { color: #0A1938; }
        html:not(.theme-clean) .alur-subtitle { color: #0070F3; }
        html:not(.theme-clean) .alur-body { color: #334155; }
        html:not(.theme-clean) .alur-badge { background: rgba(0, 112, 243, 0.12); color: #0070F3; border-color: rgba(0, 112, 243, 0.25); }
    </style>
</head>
<body class="min-h-screen flex flex-col relative selection:bg-[#00C2FF] selection:text-[#060C1E]">

    {{-- ========================================================================= --}}
    {{-- TOP NAVIGATION BAR (Floating Transparent - No Rectangle)                  --}}
    {{-- ========================================================================= --}}
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 border-b border-transparent">
        <div class="w-full px-4 sm:px-8 lg:px-14 xl:px-20">
            <div class="flex items-center justify-between h-20">
                
                {{-- Brand Lockup: Logo 3D Box + Wordmark + Tagline --}}
                <a href="#beranda" class="flex items-center gap-3.5 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 flex-shrink-0 drop-shadow-[0_0_15px_rgba(0,194,255,0.6)] group-hover:scale-105 transition">
                        <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            <defs>
                                <linearGradient id="navCubeTop" x1="60" y1="20" x2="60" y2="55" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#38BDF8"/>
                                    <stop offset="100%" stop-color="#0284C7"/>
                                </linearGradient>
                                <linearGradient id="navCubeLeft" x1="25" y1="40" x2="60" y2="95" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#0369A1"/>
                                    <stop offset="100%" stop-color="#075985"/>
                                </linearGradient>
                                <linearGradient id="navCubeRight" x1="60" y1="40" x2="95" y2="95" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#0284C7"/>
                                    <stop offset="100%" stop-color="#0369A1"/>
                                </linearGradient>
                                <linearGradient id="navOrbitGlow" x1="10" y1="85" x2="110" y2="35" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#00F0FF" stop-opacity="0.9"/>
                                    <stop offset="70%" stop-color="#00A8E8" stop-opacity="0.9"/>
                                    <stop offset="100%" stop-color="#38BDF8" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <polygon points="60,24 88,40 60,56 32,40" fill="url(#navCubeTop)" stroke="#38BDF8" stroke-width="2.5" stroke-linejoin="round"/>
                            <polygon points="32,40 60,56 60,88 32,72" fill="url(#navCubeLeft)" stroke="#0284C7" stroke-width="2.5" stroke-linejoin="round"/>
                            <polygon points="60,56 88,40 88,72 60,88" fill="url(#navCubeRight)" stroke="#38BDF8" stroke-width="2.5" stroke-linejoin="round"/>
                            <line x1="60" y1="56" x2="60" y2="88" stroke="#38BDF8" stroke-width="2"/>
                            <path d="M 16,84 C 18,98 52,106 82,90 C 98,82 106,68 102,52 C 100,44 94,38 88,34" fill="none" stroke="url(#navOrbitGlow)" stroke-width="4.5" stroke-linecap="round"/>
                            <g transform="translate(90, 48) rotate(-42)">
                                <path d="M0,0 L12,-4 L10,0 L14,3 L10,3 L8,6 L6,6 L7,2 L2,2 Z" fill="#00F0FF" stroke="#FFFFFF" stroke-width="0.8"/>
                            </g>
                        </svg>
                    </div>

                    <div class="flex flex-col justify-center">
                        <span class="font-outfit text-xl sm:text-2xl font-extrabold tracking-tight leading-none" style="color: var(--text-heading)">
                            Cargo<span style="color: var(--brand-cyan)">Trace</span>
                        </span>
                        <span class="hidden sm:inline-block text-[10px] sm:text-[11px] font-normal tracking-wide mt-1" style="color: var(--text-muted)">
                            Global Shipment Visibility & Tracking Platform
                        </span>
                    </div>
                </a>

                {{-- Center Navigation Links (6 Menu Items) --}}
                <nav class="hidden md:flex items-center gap-1.5 lg:gap-2">
                    <a href="#beranda" class="nav-link px-4 py-1.5 rounded-full text-xs lg:text-sm font-semibold transition"
                       style="background: var(--nav-active-bg); color: var(--nav-active-text);">
                        {{ __('Beranda') }}
                    </a>
                    <a href="#tentang-kami" class="nav-link px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-medium transition hover:opacity-80"
                       style="color: var(--nav-link)">
                        {{ __('Tentang Kami') }}
                    </a>
                    <a href="#produk" class="nav-link px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-medium transition hover:opacity-80"
                       style="color: var(--nav-link)">
                        {{ __('Produk') }}
                    </a>
                    <a href="#landingpage" class="nav-link px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-medium transition hover:opacity-80"
                       style="color: var(--nav-link)">
                        {{ __('Alur Sistem') }}
                    </a>
                    <a href="#fitur" class="nav-link px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-medium transition hover:opacity-80"
                       style="color: var(--nav-link)">
                        {{ __('Fitur') }}
                    </a>
                    <a href="#kontak" class="nav-link px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-medium transition hover:opacity-80"
                       style="color: var(--nav-link)">
                        {{ __('Kontak') }}
                    </a>
                </nav>

                {{-- Right Controls: Theme Switcher (Clean/Dark) + Language + Masuk / Login --}}
                <div class="flex items-center gap-2.5 sm:gap-3">
                    
                    {{-- Clean / Dark Theme Switcher Button --}}
                    <button id="themeToggleBtn" onclick="toggleTheme()" type="button" 
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold transition shadow-sm"
                            style="border-color: var(--nav-border); color: var(--text-heading); background: rgba(255,255,255,0.08);"
                            title="Beralih Tema (Clean / Dark Mode)">
                        <span id="themeToggleIcon" class="text-sm">☀️</span>
                        <span id="themeToggleText" class="hidden sm:inline">Clean Mode</span>
                    </button>

                    {{-- Language Toggle (ID / EN) --}}
                    <div class="flex items-center border rounded-full p-0.5 text-xs" style="border-color: var(--nav-border); background: rgba(255,255,255,0.04);">
                        <a href="{{ route('locale.switch', 'id') }}" 
                           class="px-2 py-0.5 rounded-full transition {{ app()->getLocale() == 'id' ? 'font-bold shadow' : 'opacity-60 hover:opacity-100' }}"
                           style="{{ app()->getLocale() == 'id' ? 'background: var(--brand-cyan); color: var(--btn-primary-text);' : 'color: var(--text-body);' }}">
                            ID
                        </a>
                        <a href="{{ route('locale.switch', 'en') }}" 
                           class="px-2 py-0.5 rounded-full transition {{ app()->getLocale() == 'en' ? 'font-bold shadow' : 'opacity-60 hover:opacity-100' }}"
                           style="{{ app()->getLocale() == 'en' ? 'background: var(--brand-cyan); color: var(--btn-primary-text);' : 'color: var(--text-body);' }}">
                            EN
                        </a>
                    </div>

                    {{-- Masuk / Login Button --}}
                    @auth
                        @php
                            $currentUser = auth()->user();
                            $targetDashboard = $currentUser->isOperator() 
                                ? route('operator.dashboard') 
                                : ($currentUser->isCustomer() ? route('customer.dashboard') : route('admin.dashboard'));
                            $roleLabel = $currentUser->isOperator() ? 'Operator' : ($currentUser->isCustomer() ? 'Customer' : 'Admin');
                        @endphp
                        <a href="{{ $targetDashboard }}" 
                           class="flex items-center gap-2 px-4 sm:px-5 py-2 rounded-full border transition font-semibold text-xs sm:text-sm shadow-md"
                           style="border-color: var(--brand-cyan); background: var(--brand-cyan); color: var(--btn-primary-text);">
                            <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                            <span>{{ __('Dasbor') }} ({{ $roleLabel }})</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="flex items-center gap-2 px-4 sm:px-5 py-2 rounded-full border transition font-semibold text-xs sm:text-sm shadow-sm hover:scale-105"
                           style="border-color: var(--brand-cyan); color: var(--brand-cyan); background: rgba(255,255,255,0.06);">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span>{{ __('Masuk / Login') }}</span>
                        </a>
                    @endauth

                    {{-- Mobile Hamburger --}}
                    <button id="mobileMenuBtn" type="button" class="md:hidden p-2 rounded-lg focus:outline-none" style="color: var(--text-heading)">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Drawer Menu --}}
        <div id="mobileMenu" class="hidden md:hidden border-t px-4 py-4 space-y-2 backdrop-blur-2xl"
             style="background: var(--nav-bg); border-color: var(--nav-border);">
            <a href="#beranda" class="block px-4 py-2 rounded-lg text-sm font-semibold" style="background: var(--nav-active-bg); color: var(--nav-active-text);">{{ __('Beranda') }}</a>
            <a href="#tentang-kami" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Tentang Kami') }}</a>
            <a href="#produk" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Produk') }}</a>
            <a href="#landingpage" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Alur Sistem') }}</a>
            <a href="#fitur" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Fitur') }}</a>
            <a href="#kontak" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Kontak') }}</a>
        </div>
    </header>


    {{-- ========================================================================= --}}
    {{-- SECTION 1: HERO / BERANDA (Interactive Tracking Card + Panoramic Port)    --}}
    {{-- ========================================================================= --}}
    <section id="beranda" class="hero-panoramic relative w-full min-h-[92vh] lg:min-h-[98vh] pt-24 pb-12 flex flex-col justify-between overflow-hidden">
        
        {{-- Soft Diffuse Overlay on the left to guarantee 100% text readability --}}
        <div class="absolute inset-0 pointer-events-none z-10"
             style="background: linear-gradient(to right, var(--hero-overlay-start) 0%, var(--hero-overlay-mid) 44%, transparent 72%);">
        </div>

        {{-- MAIN HERO CONTENT (Left-Aligned, matching Dark & Clean design references) --}}
        <div class="relative z-20 w-full px-6 sm:px-10 lg:px-14 xl:px-20 pt-6 sm:pt-10 lg:pt-14 my-auto">
            <div class="w-full lg:max-w-2xl xl:max-w-[660px] flex flex-col items-start text-left">
                
                {{-- Interactive Floating Glass Card: Real-time Shipment Tracking --}}
                <a href="#landingpage"
                   class="hero-anim-1 hero-track-card group relative w-full max-w-[440px] rounded-2xl p-4 sm:p-5 mb-7 flex items-center justify-between gap-4 overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                    <div class="relative z-10 flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                                  style="background: rgba(0, 194, 255, 0.14); color: var(--brand-cyan);">
                                <i data-lucide="ship" class="w-4 h-4"></i>
                            </span>
                            <span class="font-outfit text-sm sm:text-[15px] font-bold tracking-tight" style="color: var(--text-heading)">
                                {{ __('Real-time Shipment Tracking') }}
                            </span>
                        </div>
                        <p class="text-xs leading-relaxed" style="color: var(--text-body)">
                            {{ __('Pantau posisi kapal, status kargo, dan estimasi kedatangan secara real-time dalam satu platform terpadu.') }}
                        </p>
                    </div>

                    {{-- Right Mini Live Route Map + Arrow --}}
                    <div class="relative z-10 flex flex-col items-end justify-between self-stretch flex-shrink-0 w-24">
                        <span class="w-6 h-6 rounded-full flex items-center justify-center transition-transform duration-300 group-hover:translate-x-0.5 group-hover:-translate-y-0.5"
                              style="color: var(--text-heading);">
                            <i data-lucide="arrow-up-right" class="w-4 h-4"></i>
                        </span>
                        <svg viewBox="0 0 110 56" class="w-24 h-12 overflow-visible" fill="none">
                            <path d="M10 44 C 34 40, 48 18, 86 24" stroke="var(--brand-cyan)" stroke-opacity="0.35" stroke-width="2" stroke-linecap="round"/>
                            <path d="M10 44 C 34 40, 48 18, 86 24" stroke="var(--brand-cyan)" stroke-width="2.2" stroke-linecap="round" class="route-dash-anim"/>
                            <circle cx="10" cy="44" r="3.5" fill="var(--brand-cyan)" fill-opacity="0.85"/>
                            <circle cx="86" cy="24" r="7" fill="var(--brand-cyan)" fill-opacity="0.22" class="animate-ping"/>
                            <circle cx="86" cy="24" r="4.5" fill="var(--brand-cyan)"/>
                        </svg>
                    </div>
                </a>

                {{-- Eyebrow with Brand Accent Bar --}}
                <div class="hero-anim-2 flex items-center gap-3 mb-3.5">
                    <span class="text-[11px] sm:text-xs uppercase tracking-[0.16em] font-extrabold" style="color: var(--brand-cyan)">
                        {{ __('GLOBAL SHIPMENT VISIBILITY & TRACKING PLATFORM') }}
                    </span>
                    <div class="h-[1.5px] w-10 sm:w-14" style="background: linear-gradient(to right, var(--brand-cyan), transparent);"></div>
                </div>

                {{-- Main Headline --}}
                <h1 class="hero-anim-3 font-outfit text-4xl sm:text-5xl lg:text-[3.4rem] xl:text-[3.85rem] font-extrabold tracking-tight leading-[1.1] mb-5"
                    style="color: var(--text-heading)">
                    {{ __('Ekspor Kopi Spesialti') }} <br>
                    {{ __('Brasil ke') }}
                    <span class="italic font-extrabold drop-shadow-[0_0_24px_var(--brand-glow)]" style="color: var(--brand-cyan)">
                        {{ __('Pasar Dunia') }}
                    </span>
                </h1>

                {{-- Descriptive Paragraph --}}
                <p class="hero-anim-4 text-sm sm:text-base lg:text-[17px] leading-relaxed mb-7 max-w-lg font-normal" style="color: var(--text-body)">
                    {{ __('CargoTrace adalah platform terpadu untuk pemesanan, pelacakan, dan manajemen pengiriman kopi spesialti dari Brasil secara transparan dan real-time.') }}
                </p>

                {{-- CTA Buttons --}}
                <div class="hero-anim-5 flex flex-wrap items-center gap-3.5">
                    <a href="{{ route('login') }}" 
                       class="group inline-flex items-center gap-2.5 px-6 sm:px-7 py-3 sm:py-3.5 rounded-full font-bold text-sm transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-xl shadow-lg"
                       style="background: var(--btn-primary-bg); color: var(--btn-primary-text);">
                        <i data-lucide="package" class="w-4 h-4"></i>
                        <span>{{ __('Pesan Kargo Sekarang') }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 transition-transform duration-300 group-hover:translate-x-1"></i>
                    </a>

                    <a href="#landingpage" 
                       class="inline-flex items-center gap-2 px-6 py-3 sm:py-3.5 rounded-full font-semibold text-sm transition-all duration-300 backdrop-blur-md hover:scale-105"
                       style="background: var(--btn-secondary-bg); border: 1px solid var(--btn-secondary-border); color: var(--btn-secondary-text);">
                        <i data-lucide="play-circle" class="w-4 h-4" style="color: var(--brand-cyan)"></i>
                        <span>{{ __('Lihat Cara Kerja') }}</span>
                    </a>
                </div>

            </div>
        </div>

        {{-- Bottom spacing with smooth gradient blend into Section 2 --}}
        <div class="relative z-20 pb-8 sm:pb-12"></div>
        <div class="absolute bottom-0 left-0 right-0 h-24 pointer-events-none z-10"
             style="background: linear-gradient(to bottom, transparent 0%, var(--bg-alt) 100%);"></div>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 2: TENTANG KAMI (Santos Exporters S.A. Story & Global Hub)       --}}
    {{-- ========================================================================= --}}
    <section id="tentang-kami" class="py-14 sm:py-18 lg:py-24 relative overflow-hidden transition-colors duration-300"
             style="background: var(--bg-alt);">

        {{-- Ambient Oceanic Glows --}}
        <div class="absolute -top-24 right-1/4 w-96 h-96 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-10 w-96 h-96 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- Section Header --}}
            <div class="w-full lg:max-w-xl xl:max-w-2xl text-left mb-10 sm:mb-12">
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="h-1 w-8 rounded-full" style="background: var(--brand-cyan)"></div>
                    <span class="text-xs sm:text-sm font-bold tracking-wider uppercase" style="color: var(--text-heading)">
                        {{ __('Tentang Kami') }}
                    </span>
                </div>
                
                <h2 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-[1.15] mb-4"
                    style="color: var(--text-heading)">
                    {{ __('Rantai Pasok Kopi Spesialti') }} <br>
                    {{ __('Berkelas Dunia dari') }} <br>
                    <span style="color: var(--brand-cyan)">{{ __('Pelabuhan Santos') }}</span>
                </h2>
                
                <p class="text-sm sm:text-base leading-relaxed font-normal" style="color: var(--text-body)">
                    {{ __('Menghubungkan perkebunan kopi terbaik Brasil dengan roastery spesialti global di Asia, Eropa, dan Amerika melalui pelabuhan maritim terbesar di Amerika Latin.') }}
                </p>
            </div>

            {{-- 2 Columns: Story & Mission (Left) + 4 Metric Cards (Right) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-stretch">
                
                {{-- Left: Mission, Historical Context & Traceability Card --}}
                <div class="lg:col-span-7 flex">
                    <div class="theme-card rounded-3xl p-6 sm:p-8 flex flex-col justify-between gap-6 w-full h-full"
                         style="background: var(--card-bg); border: 1px solid var(--card-border);">
                        <div>
                            <div class="flex items-center gap-3.5 mb-5">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                                     style="background: rgba(0, 112, 243, 0.1); border: 1px solid var(--card-border); color: var(--brand-cyan);">
                                    <i data-lucide="layers" class="w-6 h-6"></i>
                                </div>
                                <h3 class="font-outfit text-lg sm:text-xl font-bold leading-snug" style="color: var(--text-heading)">
                                    {{ __('Pusat Ekspor Kopi Global') }} <span style="color: var(--brand-cyan)">{{ __('Terbesar di Dunia') }}</span>
                                </h3>
                            </div>
                            
                            <p class="text-sm leading-relaxed mb-4" style="color: var(--text-body)">
                                {{ __('Pelabuhan Santos (Porto de Santos), yang terletak di negara bagian São Paulo, Brasil, merupakan pintu gerbang ekspor maritim bagi lebih dari 80% kopi Brasil ke pasar dunia. Berdiri di jantung ekosistem logistik maritim ini, Santos Exporters S.A. mengintegrasikan perkebunan kopi terbaik di Minas Gerais, Sul de Minas, dan Mogiana langsung ke kapal kontainer laut.') }}
                            </p>
                            
                            <p class="text-sm leading-relaxed mb-5" style="color: var(--text-body)">
                                {{ __('Melalui platform CargoTrace, kami menghilangkan ketidakpastian pengiriman laut: setiap kontainer kopi dipantau kelembapannya, dilengkapi sertifikasi Specialty Coffee Association (SCA), dan dapat dilacak pergerakan AIS kapal secara transparan dari dermaga muat hingga tiba di pelabuhan tujuan pembeli.') }}
                            </p>

                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold"
                                      style="background: rgba(0, 112, 243, 0.08); border: 1px solid var(--card-border); color: var(--brand-cyan);">
                                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                    {{ __('Sertifikasi Ekspor COO & Phytosanitary') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-semibold"
                                      style="background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.25); color: #10B981;">
                                    <i data-lucide="radio" class="w-3.5 h-3.5"></i>
                                    {{ __('Pemantauan AIS & IoT Kelembapan Real-Time') }}
                                </span>
                            </div>
                        </div>

                        {{-- 3 Value Pillars at the bottom of the card --}}
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-5 border-t" style="border-color: var(--nav-border)">
                            
                            {{-- Pillar 1: Single-Origin --}}
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center flex-shrink-0 text-emerald-500 mt-0.5">
                                    <i data-lucide="check" class="w-4 h-4 stroke-[2.5]"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs sm:text-sm leading-tight" style="color: var(--text-heading)">Single-Origin</h4>
                                    <p class="text-[11px] mt-0.5 leading-snug" style="color: var(--text-muted)">{{ __('Terlacak hingga kebun asal & sertifikat COO') }}</p>
                                </div>
                            </div>

                            {{-- Pillar 2: GrainPro Liner --}}
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5"
                                     style="background: rgba(0, 112, 243, 0.12); border: 1px solid var(--card-border); color: var(--brand-cyan);">
                                    <i data-lucide="package" class="w-4 h-4 stroke-[2.5]"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs sm:text-sm leading-tight" style="color: var(--text-heading)">GrainPro Liner</h4>
                                    <p class="text-[11px] mt-0.5 leading-snug" style="color: var(--text-muted)">{{ __('Kontainer terlindung menjaga kelembapan 11%') }}</p>
                                </div>
                            </div>

                            {{-- Pillar 3: SCA 84+ Cupping --}}
                            <div class="flex items-start gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-amber-500/15 border border-amber-500/30 flex items-center justify-center flex-shrink-0 text-amber-500 mt-0.5">
                                    <i data-lucide="award" class="w-4 h-4 stroke-[2.5]"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs sm:text-sm leading-tight" style="color: var(--text-heading)">SCA 84+ Cupping</h4>
                                    <p class="text-[11px] mt-0.5 leading-snug" style="color: var(--text-muted)">{{ __('Uji cupping terstandar Q-Grader bersertifikasi') }}</p>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Right: Santos Port Visual Showcase Card + 4 Metric Cards --}}
                <div class="lg:col-span-5 flex flex-col gap-4 h-full">
                    
                    {{-- Santos Port Brazil Visual Showcase Card --}}
                    <div class="theme-card relative rounded-2xl overflow-hidden shadow-xl border group flex-shrink-0"
                         style="background: var(--card-bg); border-color: var(--card-border);">
                        <img src="{{ asset('images/tentang-kami-bg.jpg') }}" alt="Porto de Santos Brazil" 
                             class="w-full h-40 sm:h-44 object-cover object-top group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-[var(--card-bg)] via-transparent to-transparent"></div>
                        <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-600/90 text-white shadow-sm backdrop-blur-sm uppercase tracking-wider">
                                Porto de Santos • Brazil
                            </span>
                            <span class="text-xs font-semibold text-cyan-200 drop-shadow">Pintu Ekspor 80% Kopi Dunia</span>
                        </div>
                    </div>

                    {{-- 4 Metric Cards (2x2 Grid) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    
                    {{-- Metric 1: 40+ Global Ports --}}
                    <div class="theme-card rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden"
                         style="background: var(--card-bg); border: 1px solid var(--card-border);">
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-sm"
                             style="background: rgba(2, 132, 199, 0.15); color: #0284C7;">
                            <i data-lucide="globe" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <div class="font-outfit text-2xl sm:text-3xl font-extrabold leading-none mb-1" style="color: var(--text-heading)">
                                40+
                            </div>
                            <div class="text-xs sm:text-sm font-bold" style="color: var(--brand-cyan)">{{ __('Pelabuhan & Hub Global') }}</div>
                            <p class="text-[11px] mt-0.5 leading-tight" style="color: var(--text-muted)">Santos, Priok, Rotterdam, Busan, Haneda, JFK</p>
                        </div>
                    </div>

                    {{-- Metric 2: $6.4M+ GMV Cargo Value --}}
                    <div class="theme-card rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden"
                         style="background: var(--card-bg); border: 1px solid var(--card-border);">
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-sm"
                             style="background: rgba(16, 185, 129, 0.15); color: #10B981;">
                            <i data-lucide="layers" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <div class="font-outfit text-2xl sm:text-3xl font-extrabold leading-none mb-1" style="color: var(--text-heading)">
                                $6.4M+
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-emerald-500">{{ __('Total Nilai Kargo (GMV)') }}</div>
                            <p class="text-[11px] mt-0.5 leading-tight" style="color: var(--text-muted)">{{ __('Ekspor komoditas bernilai tinggi terproteksi') }}</p>
                        </div>
                    </div>

                    {{-- Metric 3: 88.6% On-Time SLA --}}
                    <div class="theme-card rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden"
                         style="background: var(--card-bg); border: 1px solid var(--card-border);">
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-sm"
                             style="background: rgba(37, 99, 235, 0.15); color: #2563EB;">
                            <i data-lucide="trending-up" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <div class="font-outfit text-2xl sm:text-3xl font-extrabold leading-none mb-1" style="color: var(--text-heading)">
                                88.6%
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-blue-500">{{ __('Ketepatan Waktu (SLA)') }}</div>
                            <p class="text-[11px] mt-0.5 leading-tight" style="color: var(--text-muted)">{{ __('Deteksi otomatis deviasi rute dan cuaca') }}</p>
                        </div>
                    </div>

                    {{-- Metric 4: 6 Certified Varieties --}}
                    <div class="theme-card rounded-2xl p-5 flex items-center gap-4 relative overflow-hidden"
                         style="background: var(--card-bg); border: 1px solid var(--card-border);">
                        <div class="w-12 h-12 rounded-2xl flex-shrink-0 flex items-center justify-center shadow-sm"
                             style="background: rgba(245, 158, 11, 0.15); color: #F59E0B;">
                            <i data-lucide="coffee" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <div class="font-outfit text-2xl sm:text-3xl font-extrabold leading-none mb-1" style="color: var(--text-heading)">
                                6
                            </div>
                            <div class="text-xs sm:text-sm font-bold text-amber-500">{{ __('Varietas Kopi Spesialti') }}</div>
                            <p class="text-[11px] mt-0.5 leading-tight" style="color: var(--text-muted)">Bourbon, Catuai, Cerrado, SSFC & Blend</p>
                        </div>
                    </div>

                    </div>
                </div>

            </div>

        </div>

        {{-- Flowing Ocean Wave at bottom of Tentang Kami transitioning into Produk --}}
        <div class="w-full overflow-hidden leading-none pt-12 sm:pt-16 pointer-events-none">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-8 sm:h-12" preserveAspectRatio="none">
                <path d="M0,25 C320,55 640,10 960,35 C1200,55 1360,20 1440,25 L1440,60 L0,60 Z" fill="var(--brand-cyan)" opacity="0.25"/>
                <path d="M0,35 C280,15 600,55 920,25 C1180,45 1340,15 1440,35 L1440,60 L0,60 Z" fill="var(--bg-page)"/>
            </svg>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- SECTION 3: PRODUK KOPI SPESIALTI BRASIL (Interactive Full-Bleed Showcase) --}}
    {{-- ========================================================================= --}}
    <section id="produk" class="relative min-h-[680px] lg:min-h-[760px] flex flex-col justify-between overflow-hidden text-white">

        {{-- Dynamic Full-Bleed Background Layer --}}
        <div class="absolute inset-0 z-0 overflow-hidden bg-[#040C1E]">
            <img id="produk-bg-img"
                 src="{{ asset('images/products/coffee-1.png') }}?v={{ filemtime(public_path('images/products/coffee-1.png')) }}"
                 alt="Background Produk Kopi"
                 class="w-full h-full object-cover object-center transition-all duration-700 ease-out">
            {{-- Subtle Left Readability Scrim (keeps product photo sharp & vivid) --}}
            <div class="absolute inset-0"
                 style="background: linear-gradient(100deg, rgba(4, 12, 28, 0.72) 0%, rgba(4, 12, 28, 0.36) 44%, rgba(4, 12, 28, 0.06) 76%, transparent 100%);"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#040C1E]/55 via-transparent to-[#040C1E]/25"></div>
        </div>

        {{-- Main Content Area --}}
        <div class="relative z-10 w-full max-w-[1400px] mx-auto px-6 sm:px-10 lg:px-14 pt-16 sm:pt-20 pb-8 my-auto">
            
            {{-- Top Eyebrow --}}
            <div class="flex items-center gap-2.5 mb-8">
                <div class="h-1 w-8 rounded-full bg-cyan-400"></div>
                <span class="text-xs sm:text-sm font-bold tracking-widest uppercase text-cyan-300">
                    {{ __('KATALOG KOMODITAS EKSPOR & PROFIL RASA') }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                
                {{-- Left Column: Active Product Spotlight & Tasting Notes --}}
                <div class="lg:col-span-5 flex flex-col items-start text-left" id="produk-spotlight-panel">
                    <div class="h-[3px] w-9 rounded-full bg-amber-400 mb-3 transition-all duration-300"></div>

                    <p id="produk-active-origin" class="text-xs sm:text-sm font-semibold tracking-wider uppercase text-cyan-300 mb-2">
                        Sul de Minas, Brasil • Natural Sun-Dried Process
                    </p>

                    <h2 id="produk-active-title" class="font-outfit text-3xl sm:text-4xl lg:text-[2.75rem] font-black tracking-tight leading-[1.08] uppercase text-white mb-4">
                        Kopi Brazil Santos Bourbon Specialty
                    </h2>

                    <p id="produk-active-desc" class="text-sm leading-relaxed text-slate-200/90 mb-5 max-w-lg">
                        Varian unggulan dari dataran tinggi Sul de Minas yang dijemur alami di bawah sinar matahari, menghasilkan karakter manis karamel dengan body lembut yang ideal untuk filter maupun espresso.
                    </p>

                    {{-- Flavor / Tasting Notes Box --}}
                    <div class="w-full max-w-lg rounded-2xl p-4 mb-6 border border-white/15 bg-white/10 backdrop-blur-md">
                        <div class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-wider text-amber-300 mb-1.5">
                            <i data-lucide="coffee" class="w-3.5 h-3.5"></i>
                            <span>{{ __('Profil Rasa (Tasting Notes)') }}</span>
                        </div>
                        <div id="produk-active-flavor" class="text-sm sm:text-base font-bold text-white mb-3">
                            Karamel, Cokelat Susu, Hazelnut, Body Lembut
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[11px] font-semibold">
                            <span id="produk-active-sca" class="px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-400/30">
                                SCA Score 86.5
                            </span>
                            <span id="produk-active-elevation" class="px-2.5 py-1 rounded-full bg-cyan-500/20 text-cyan-200 border border-cyan-400/30 flex items-center gap-1">
                                <i data-lucide="mountain" class="w-3 h-3"></i> 1,200 m dpl
                            </span>
                            <span id="produk-active-packaging" class="px-2.5 py-1 rounded-full bg-white/10 text-slate-200 border border-white/20 flex items-center gap-1">
                                <i data-lucide="package" class="w-3 h-3"></i> 60kg GrainPro
                            </span>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3">
                        <span class="w-11 h-11 rounded-full bg-amber-500 text-slate-950 flex items-center justify-center shadow-lg shadow-amber-500/30 flex-shrink-0">
                            <i data-lucide="award" class="w-5 h-5"></i>
                        </span>
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2.5 px-6 py-3 rounded-full border border-white/40 bg-white/10 hover:bg-cyan-500 hover:border-cyan-400 hover:text-slate-950 text-white text-xs sm:text-sm font-bold uppercase tracking-wider backdrop-blur-md transition-all duration-300">
                            <span>{{ __('Pesan Kargo Varian Ini') }}</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>

                {{-- Right Column: Interactive Portrait Product Cards Strip --}}
                <div class="lg:col-span-7 flex flex-col gap-6">
                    <div id="produk-cards-track" class="flex items-center gap-3.5 sm:gap-4 overflow-x-auto pb-4 pt-2 px-1 no-scrollbar scroll-smooth">
                        
                        {{-- Card 0 --}}
                        <button type="button" onclick="selectProductSlide(0)" id="produk-card-0"
                                class="produk-slide-card group relative shrink-0 w-[165px] sm:w-[185px] h-[255px] sm:h-[285px] rounded-2xl overflow-hidden text-left transition-all duration-500 border-2 border-cyan-400 shadow-2xl scale-[1.03]">
                            <img src="{{ asset('images/products/coffee-1.png') }}?v={{ filemtime(public_path('images/products/coffee-1.png')) }}" alt="Bourbon Specialty" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/90 text-slate-950">SCA 86.5</div>
                            <div class="absolute bottom-3.5 left-3.5 right-3.5">
                                <div class="text-[10px] text-cyan-300 font-semibold uppercase tracking-wider truncate">Sul de Minas</div>
                                <div class="font-outfit text-sm font-extrabold text-white leading-tight mt-0.5">Bourbon Specialty</div>
                            </div>
                        </button>

                        {{-- Card 1 --}}
                        <button type="button" onclick="selectProductSlide(1)" id="produk-card-1"
                                class="produk-slide-card group relative shrink-0 w-[165px] sm:w-[185px] h-[255px] sm:h-[285px] rounded-2xl overflow-hidden text-left transition-all duration-500 border border-white/20 opacity-80 hover:opacity-100 hover:-translate-y-1.5">
                            <img src="{{ asset('images/products/coffee-2.png') }}?v={{ filemtime(public_path('images/products/coffee-2.png')) }}" alt="Yellow Catuai" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/90 text-white">SCA 87.5</div>
                            <div class="absolute bottom-3.5 left-3.5 right-3.5">
                                <div class="text-[10px] text-cyan-300 font-semibold uppercase tracking-wider truncate">Carmo de Minas</div>
                                <div class="font-outfit text-sm font-extrabold text-white leading-tight mt-0.5">Yellow Catuai Natural</div>
                            </div>
                        </button>

                        {{-- Card 2 --}}
                        <button type="button" onclick="selectProductSlide(2)" id="produk-card-2"
                                class="produk-slide-card group relative shrink-0 w-[165px] sm:w-[185px] h-[255px] sm:h-[285px] rounded-2xl overflow-hidden text-left transition-all duration-500 border border-white/20 opacity-80 hover:opacity-100 hover:-translate-y-1.5">
                            <img src="{{ asset('images/products/coffee-3.png') }}?v={{ filemtime(public_path('images/products/coffee-3.png')) }}" alt="Cerrado Mineiro" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/90 text-slate-950">SCA 85.0</div>
                            <div class="absolute bottom-3.5 left-3.5 right-3.5">
                                <div class="text-[10px] text-cyan-300 font-semibold uppercase tracking-wider truncate">Cerrado Mineiro</div>
                                <div class="font-outfit text-sm font-extrabold text-white leading-tight mt-0.5">Cerrado Pulped Natural</div>
                            </div>
                        </button>

                        {{-- Card 3 --}}
                        <button type="button" onclick="selectProductSlide(3)" id="produk-card-3"
                                class="produk-slide-card group relative shrink-0 w-[165px] sm:w-[185px] h-[255px] sm:h-[285px] rounded-2xl overflow-hidden text-left transition-all duration-500 border border-white/20 opacity-80 hover:opacity-100 hover:-translate-y-1.5">
                            <img src="{{ asset('images/products/coffee-4.png') }}?v={{ filemtime(public_path('images/products/coffee-4.png')) }}" alt="Santos SSFC" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold bg-violet-500/90 text-white">SCA 84.0</div>
                            <div class="absolute bottom-3.5 left-3.5 right-3.5">
                                <div class="text-[10px] text-cyan-300 font-semibold uppercase tracking-wider truncate">Mogiana Paulista</div>
                                <div class="font-outfit text-sm font-extrabold text-white leading-tight mt-0.5">Santos Fine Cup (SSFC)</div>
                            </div>
                        </button>

                        {{-- Card 4 --}}
                        <button type="button" onclick="selectProductSlide(4)" id="produk-card-4"
                                class="produk-slide-card group relative shrink-0 w-[165px] sm:w-[185px] h-[255px] sm:h-[285px] rounded-2xl overflow-hidden text-left transition-all duration-500 border border-white/20 opacity-80 hover:opacity-100 hover:-translate-y-1.5">
                            <img src="{{ asset('images/products/coffee-5.png') }}?v={{ filemtime(public_path('images/products/coffee-5.png')) }}" alt="Santos Decaf" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-500/90 text-slate-950">SCA 83.5</div>
                            <div class="absolute bottom-3.5 left-3.5 right-3.5">
                                <div class="text-[10px] text-cyan-300 font-semibold uppercase tracking-wider truncate">Minas Gerais</div>
                                <div class="font-outfit text-sm font-extrabold text-white leading-tight mt-0.5">Decaf Swiss Water</div>
                            </div>
                        </button>

                        {{-- Card 5 --}}
                        <button type="button" onclick="selectProductSlide(5)" id="produk-card-5"
                                class="produk-slide-card group relative shrink-0 w-[165px] sm:w-[185px] h-[255px] sm:h-[285px] rounded-2xl overflow-hidden text-left transition-all duration-500 border border-white/20 opacity-80 hover:opacity-100 hover:-translate-y-1.5">
                            <img src="{{ asset('images/products/coffee-6.png') }}?v={{ filemtime(public_path('images/products/coffee-6.png')) }}" alt="Espresso Master Blend" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/25 to-transparent"></div>
                            <div class="absolute top-3 left-3 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/90 text-white">SCA 85.5</div>
                            <div class="absolute bottom-3.5 left-3.5 right-3.5">
                                <div class="text-[10px] text-cyan-300 font-semibold uppercase tracking-wider truncate">Santos Select</div>
                                <div class="font-outfit text-sm font-extrabold text-white leading-tight mt-0.5">Espresso Master Blend</div>
                            </div>
                        </button>

                    </div>

                    {{-- Bottom Controls: Arrows + Progress Bar + Slide Counter --}}
                    <div class="flex items-center gap-4 sm:gap-6 px-1">
                        <div class="flex items-center gap-2.5 flex-shrink-0">
                            <button type="button" onclick="stepProductSlide(-1)" aria-label="Produk Sebelumnya"
                                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-full border border-white/35 bg-white/5 hover:bg-white/20 text-white flex items-center justify-center backdrop-blur-md transition-all hover:scale-105 active:scale-95">
                                <i data-lucide="chevron-left" class="w-5 h-5"></i>
                            </button>
                            <button type="button" onclick="stepProductSlide(1)" aria-label="Produk Berikutnya"
                                    class="w-10 h-10 sm:w-11 sm:h-11 rounded-full border border-white/35 bg-white/5 hover:bg-white/20 text-white flex items-center justify-center backdrop-blur-md transition-all hover:scale-105 active:scale-95">
                                <i data-lucide="chevron-right" class="w-5 h-5"></i>
                            </button>
                        </div>

                        <div class="flex-1 h-[2px] bg-white/25 rounded-full overflow-hidden">
                            <div id="produk-progress-bar" class="h-full bg-amber-400 transition-all duration-500" style="width: 16.66%;"></div>
                        </div>

                        <div id="produk-slide-counter" class="font-outfit text-3xl sm:text-4xl font-black tracking-tighter text-white/95 flex-shrink-0">
                            01
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Flowing Ocean Wave at bottom of Produk transitioning into Alur Sistem --}}
        <div class="relative z-10 w-full overflow-hidden leading-none pt-6 pointer-events-none">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-8 sm:h-12" preserveAspectRatio="none">
                <path d="M0,25 C320,55 640,10 960,35 C1200,55 1360,20 1440,25 L1440,60 L0,60 Z" fill="var(--brand-cyan)" opacity="0.2"/>
                <path d="M0,35 C280,15 600,55 920,25 C1180,45 1340,15 1440,35 L1440,60 L0,60 Z" fill="var(--bg-alt)"/>
            </svg>
        </div>

        {{-- Interactive Product Spotlight Script --}}
        <script>
            const productCatalog = [
                {
                    image: "{{ asset('images/products/coffee-1.png') }}?v={{ filemtime(public_path('images/products/coffee-1.png')) }}",
                    origin: "Sul de Minas, Brasil • Natural Sun-Dried Process",
                    title: "Kopi Brazil Santos Bourbon Specialty",
                    desc: "Varian unggulan dari dataran tinggi Sul de Minas yang dijemur alami di bawah sinar matahari, menghasilkan karakter manis karamel dengan body lembut yang ideal untuk filter maupun espresso.",
                    flavor: "Karamel, Cokelat Susu, Hazelnut, Body Lembut",
                    sca: "SCA Score 86.5",
                    elevation: "1,200 m dpl",
                    packaging: "Kemasan: 60kg GrainPro"
                },
                {
                    image: "{{ asset('images/products/coffee-2.png') }}?v={{ filemtime(public_path('images/products/coffee-2.png')) }}",
                    origin: "Carmo de Minas, Brasil • Anaerobic Natural",
                    title: "Brazil Santos Yellow Catuai Natural",
                    desc: "Ceri kuning pilihan yang difermentasi secara anaerobik terkontrol, menghadirkan ledakan aroma buah tropis, madu liar, dan rasa manis gula tebu yang kompleks.",
                    flavor: "Madu Liar, Markisa, Bunga Tropis, Gula Tebu",
                    sca: "SCA Score 87.5 • Micro-lot",
                    elevation: "1,250 m dpl",
                    packaging: "Kemasan: 30kg Vakum Box"
                },
                {
                    image: "{{ asset('images/products/coffee-3.png') }}?v={{ filemtime(public_path('images/products/coffee-3.png')) }}",
                    origin: "Patrocinio, Cerrado Mineiro • Pulped Natural",
                    title: "Brazil Santos Cerrado Mineiro",
                    desc: "Kopi bersertifikat Indikasi Geografis (D.O.) dari sabana Cerrado Mineiro dengan proses pulped natural, menghasilkan keasaman rendah, body tebal, dan aftertaste kakao hitam.",
                    flavor: "Kakao Hitam, Kacang Almond, Body Tebal, Low Acid",
                    sca: "SCA Score 85.0 • D.O. Protected",
                    elevation: "1,050 m dpl",
                    packaging: "Kemasan: Kontainer 20ft / FCL"
                },
                {
                    image: "{{ asset('images/products/coffee-4.png') }}?v={{ filemtime(public_path('images/products/coffee-4.png')) }}",
                    origin: "Mogiana Paulista • Natural Sun-Dried",
                    title: "Santos Strictly Soft Fine Cup (SSFC)",
                    desc: "Klasifikasi ekspor premium NY 2/3 Strictly Soft Fine Cup dari wilayah Mogiana dengan profil cangkir yang sangat bersih, seimbang, dan manis kacang panggang.",
                    flavor: "Bersih & Seimbang, Kacang Manis, Halus",
                    sca: "SCA Score 84.0 • NY 2/3 SSFC",
                    elevation: "1,000 m dpl",
                    packaging: "Kemasan: Karung Goni 60kg"
                },
                {
                    image: "{{ asset('images/products/coffee-5.png') }}?v={{ filemtime(public_path('images/products/coffee-5.png')) }}",
                    origin: "Minas Gerais • 100% Chemical-Free Decaf",
                    title: "Santos Decaf Swiss Water Process",
                    desc: "Diproses secara alami menggunakan metode Swiss Water tanpa pelarut kimia untuk mengekstrak 99.9% kafein sambil tetap mempertahankan cita rasa kakao lembut dan walnut.",
                    flavor: "Tanpa Kimia, Kakao Lembut, Walnut",
                    sca: "SCA Score 83.5 • 99.9% Bebas Kafein",
                    elevation: "1,100 m dpl",
                    packaging: "Kemasan: Multi-Barrier 60kg"
                },
                {
                    image: "{{ asset('images/products/coffee-6.png') }}?v={{ filemtime(public_path('images/products/coffee-6.png')) }}",
                    origin: "Santos Select Blend • Full City Roast Ready",
                    title: "Santos Espresso Master Blend",
                    desc: "Racikan khusus ekspor yang dirancang untuk profil Full City Roast, menghasilkan crema tebal keemasan dengan karakter dark cocoa dan manis molase yang kuat.",
                    flavor: "Crema Tebal, Dark Cocoa, Molase",
                    sca: "SCA Score 85.5 • Master Blend",
                    elevation: "1,150 m dpl",
                    packaging: "Kemasan: Karung Foil 60kg"
                }
            ];

            let activeProductIdx = 0;

            function selectProductSlide(idx) {
                const item = productCatalog[idx];
                if (!item) return;
                activeProductIdx = idx;

                // 1. Animate Background Image Crossfade + Zoom
                const bg = document.getElementById('produk-bg-img');
                if (bg) {
                    bg.style.opacity = '0.35';
                    bg.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        bg.src = item.image;
                        bg.style.opacity = '1';
                        bg.style.transform = 'scale(1.03)';
                    }, 180);
                }

                // 2. Animate Spotlight Text Panel
                const panel = document.getElementById('produk-spotlight-panel');
                if (panel) {
                    panel.style.opacity = '0';
                    panel.style.transform = 'translateY(10px)';
                    panel.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                    setTimeout(() => {
                        document.getElementById('produk-active-origin').textContent = item.origin;
                        document.getElementById('produk-active-title').textContent = item.title;
                        document.getElementById('produk-active-desc').textContent = item.desc;
                        document.getElementById('produk-active-flavor').textContent = item.flavor;
                        document.getElementById('produk-active-sca').textContent = item.sca;
                        document.getElementById('produk-active-elevation').innerHTML = `<i data-lucide="mountain" class="w-3 h-3"></i> ${item.elevation}`;
                        document.getElementById('produk-active-packaging').innerHTML = `<i data-lucide="package" class="w-3 h-3"></i> ${item.packaging}`;
                        if (window.lucide) lucide.createIcons();
                        panel.style.opacity = '1';
                        panel.style.transform = 'translateY(0)';
                    }, 160);
                }

                // 3. Highlight Active Card & Scroll Into View
                for (let i = 0; i < productCatalog.length; i++) {
                    const card = document.getElementById('produk-card-' + i);
                    if (!card) continue;
                    if (i === idx) {
                        card.classList.remove('border', 'border-white/20', 'opacity-80');
                        card.classList.add('border-2', 'border-cyan-400', 'shadow-2xl', 'scale-[1.03]', 'opacity-100');
                        card.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });
                    } else {
                        card.classList.remove('border-2', 'border-cyan-400', 'shadow-2xl', 'scale-[1.03]', 'opacity-100');
                        card.classList.add('border', 'border-white/20', 'opacity-80');
                    }
                }

                // 4. Update Progress Bar & Counter
                const progress = document.getElementById('produk-progress-bar');
                if (progress) {
                    progress.style.width = (((idx + 1) / productCatalog.length) * 100) + '%';
                }
                const counter = document.getElementById('produk-slide-counter');
                if (counter) {
                    counter.textContent = String(idx + 1).padStart(2, '0');
                }
            }

            function stepProductSlide(delta) {
                const next = (activeProductIdx + delta + productCatalog.length) % productCatalog.length;
                selectProductSlide(next);
            }
        </script>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 4: ALUR SISTEM (Interactive 5-Stage Swimlane Flowmap Showcase)   --}}
    {{-- ========================================================================= --}}
    <section id="landingpage" class="py-16 lg:py-24 relative overflow-hidden transition-colors duration-300" style="background: var(--bg-alt);">
        
        {{-- Subtle Ambient Glows --}}
        <div class="absolute -top-32 left-1/4 w-96 h-96 rounded-full bg-cyan-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 right-1/4 w-96 h-96 rounded-full bg-blue-600/10 blur-3xl pointer-events-none"></div>

        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- 1. Top Section Header & Port Santos Hero Showcase --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center mb-12 lg:mb-16">
                
                {{-- Header Typography --}}
                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-3 shadow-sm border transition-all duration-300"
                         style="background: var(--brand-glow); color: var(--brand-cyan); border-color: var(--card-border);">
                        <i data-lucide="git-merge" class="w-4 h-4 animate-pulse"></i>
                        <span>{{ __('FLOWMAP SISTEM TERINTEGRASI') }}</span>
                    </div>

                    <h2 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-tight mb-2"
                        style="color: var(--text-heading)">
                        {{ __('Alur Kerja Pengiriman Kargo') }}
                        <span class="block bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-400 bg-clip-text text-transparent">
                            {{ __('(5 Tahap Utama)') }}
                        </span>
                    </h2>

                    <p class="text-base sm:text-lg leading-relaxed font-normal max-w-2xl mt-3" style="color: var(--text-body)">
                        {{ __('Diagram alur swimlane yang menghubungkan peran Customer (Buyer), Logistics Operator, Sistem Mesin Otomatis, dan Administrator secara harmonis dalam satu ekosistem logistik maritim.') }}
                    </p>

                    <div class="flex flex-wrap items-center gap-3 mt-5">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> End-to-End SLA Guaranteed
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400 border border-blue-500/20">
                            <i data-lucide="satellite" class="w-3.5 h-3.5"></i> Live AIS Satelit
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20">
                            <i data-lucide="cpu" class="w-3.5 h-3.5"></i> Multi-Role Collaboration
                        </span>
                    </div>
                </div>

                {{-- Santos Port & Ship Interactive Visual Banner --}}
                <div class="lg:col-span-5">
                    <div class="relative rounded-2xl overflow-hidden shadow-2xl border transition-all duration-500 hover:shadow-cyan-500/20 hover:scale-[1.01] group"
                         style="background: var(--card-bg); border-color: var(--card-border);">
                        <img src="{{ asset('images/alur/port-santos-banner.png') }}" 
                             alt="Pelabuhan Santos & Pengiriman Global Kopi CargoTrace" 
                             class="w-full h-48 sm:h-56 object-cover object-center group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent flex items-end p-4">
                            <div class="flex items-center justify-between w-full text-white">
                                <div>
                                    <div class="text-xs font-semibold tracking-wider text-cyan-300 uppercase flex items-center gap-1.5">
                                        <i data-lucide="anchor" class="w-3.5 h-3.5"></i> Port of Santos, Brazil
                                    </div>
                                    <div class="text-sm font-bold">Rute Utama Ekspor Kopi Global</div>
                                </div>
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-cyan-500/80 text-white shadow-sm backdrop-blur-sm">
                                    LIVE TERMINAL
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


            {{-- 2. The 5 Main Stages (Interactive 3D Coverflow Perspective Carousel) --}}
            <div class="mb-14">

                {{-- Quick Stepper Pills (Desktop & Mobile) --}}
                <div class="flex items-center justify-center gap-1.5 sm:gap-2 mb-5 p-1.5 rounded-2xl border bg-slate-500/5 backdrop-blur-sm shadow-sm max-w-2xl mx-auto" style="border-color: var(--card-border);">
                    <button type="button" onclick="selectFlowStep(1)" class="flex-1 py-1.5 px-2 text-center rounded-xl text-[11px] sm:text-xs font-bold transition-all text-slate-500 hover:text-blue-500" id="mob-step-btn-1">1. Pesan</button>
                    <button type="button" onclick="selectFlowStep(2)" class="flex-1 py-1.5 px-2 text-center rounded-xl text-[11px] sm:text-xs font-bold transition-all text-slate-500 hover:text-amber-500" id="mob-step-btn-2">2. Validasi</button>
                    <button type="button" onclick="selectFlowStep(3)" class="flex-1 py-1.5 px-2 text-center rounded-xl text-[11px] sm:text-xs font-bold transition-all text-cyan-600 bg-cyan-500/15 shadow-sm" id="mob-step-btn-3">3. Tracking</button>
                    <button type="button" onclick="selectFlowStep(4)" class="flex-1 py-1.5 px-2 text-center rounded-xl text-[11px] sm:text-xs font-bold transition-all text-slate-500 hover:text-rose-500" id="mob-step-btn-4">4. Alert</button>
                    <button type="button" onclick="selectFlowStep(5)" class="flex-1 py-1.5 px-2 text-center rounded-xl text-[11px] sm:text-xs font-bold transition-all text-slate-500 hover:text-purple-500" id="mob-step-btn-5">5. Selesai</button>
                </div>

                {{-- 3D Coverflow Stage --}}
                <div class="relative w-full h-[380px] sm:h-[410px] flex items-center justify-center overflow-hidden select-none" id="flow-3d-wrapper">
                    
                    {{-- Prev Arrow Button --}}
                    <button type="button" onclick="stepCarousel(-1)" aria-label="Tahap Sebelumnya"
                            class="absolute left-1 sm:left-4 z-40 w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center border backdrop-blur-md shadow-lg transition-all hover:scale-110 active:scale-95"
                            style="background: var(--card-bg); border-color: var(--card-border); color: var(--text-heading);">
                        <i data-lucide="chevron-left" class="w-5 h-5"></i>
                    </button>

                    {{-- 3D Perspective Container --}}
                    <div id="flow-steps-container"
                         class="relative w-full h-full"
                         style="perspective: 1300px; transform-style: preserve-3d;">
                        
                        {{-- STEP 1: Pemesanan Kargo --}}
                        <div class="flow-3d-card theme-card rounded-3xl p-5 flex flex-col justify-between cursor-pointer group overflow-hidden border backdrop-blur-xl"
                             style="position: absolute; left: 50%; top: 50%; width: min(295px, 76vw); height: 355px; background: var(--card-bg); border-color: var(--card-border); transform-origin: center center; backface-visibility: hidden; transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.45s ease, filter 0.45s ease, box-shadow 0.45s ease, border-color 0.45s ease;"
                             onclick="selectFlowStep(1)" id="step-card-1">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-blue-600 to-blue-400"></div>
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="w-8 h-8 rounded-full bg-blue-600 text-white font-black text-sm flex items-center justify-center shadow-lg shadow-blue-500/30">
                                        1
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/15 text-blue-500 border border-blue-500/25">
                                        Customer
                                    </span>
                                </div>
                                <h3 class="font-outfit text-base font-extrabold leading-snug group-hover:text-blue-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Pemesanan Kargo') }}
                                </h3>
                                <p class="text-xs leading-relaxed mt-1.5" style="color: var(--text-muted)">
                                    {{ __('Input pesanan kargo kopi, tonase, spesifikasi bean & penentuan kontainer ekspor.') }}
                                </p>
                            </div>

                            <div class="mt-3 rounded-2xl overflow-hidden bg-gradient-to-b from-blue-500/5 to-cyan-500/10 p-2.5 flex items-center justify-center border border-blue-500/15">
                                <img src="{{ asset('images/alur/step-1-pedestal.png') }}" 
                                     alt="Step 1 Pemesanan Kargo" 
                                     class="w-full h-28 object-contain group-hover:scale-105 transition-transform duration-500 animate-float pointer-events-none">
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200/40 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-blue-500">
                                <span>PO &amp; Verifikasi</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>

                        {{-- STEP 2: Validasi & Pengurusan Ekspedisi --}}
                        <div class="flow-3d-card theme-card rounded-3xl p-5 flex flex-col justify-between cursor-pointer group overflow-hidden border backdrop-blur-xl"
                             style="position: absolute; left: 50%; top: 50%; width: min(295px, 76vw); height: 355px; background: var(--card-bg); border-color: var(--card-border); transform-origin: center center; backface-visibility: hidden; transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.45s ease, filter 0.45s ease, box-shadow 0.45s ease, border-color 0.45s ease;"
                             onclick="selectFlowStep(2)" id="step-card-2">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-amber-500 to-amber-300"></div>
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="w-8 h-8 rounded-full bg-amber-500 text-white font-black text-sm flex items-center justify-center shadow-lg shadow-amber-500/30">
                                        2
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-500 border border-amber-500/25">
                                        Operator
                                    </span>
                                </div>
                                <h3 class="font-outfit text-base font-extrabold leading-snug group-hover:text-amber-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Validasi & Ekspedisi') }}
                                </h3>
                                <p class="text-xs leading-relaxed mt-1.5" style="color: var(--text-muted)">
                                    {{ __('Pemeriksaan kelayakan kontainer, karantina, bea cukai pelabuhan, & manifest muatan.') }}
                                </p>
                            </div>

                            <div class="mt-3 rounded-2xl overflow-hidden bg-gradient-to-b from-amber-500/5 to-amber-500/10 p-2.5 flex items-center justify-center border border-amber-500/15">
                                <img src="{{ asset('images/alur/step-2-pedestal.png') }}" 
                                     alt="Step 2 Validasi Ekspedisi" 
                                     class="w-full h-28 object-contain group-hover:scale-105 transition-transform duration-500 animate-float [animation-delay:0.8s] pointer-events-none">
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200/40 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-amber-500">
                                <span>Stuffing Pelabuhan</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>

                        {{-- STEP 3: Pelacakan Perjalanan --}}
                        <div class="flow-3d-card theme-card rounded-3xl p-5 flex flex-col justify-between cursor-pointer group overflow-hidden border backdrop-blur-xl"
                             style="position: absolute; left: 50%; top: 50%; width: min(295px, 76vw); height: 355px; background: var(--card-bg); border-color: var(--card-border); transform-origin: center center; backface-visibility: hidden; transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.45s ease, filter 0.45s ease, box-shadow 0.45s ease, border-color 0.45s ease;"
                             onclick="selectFlowStep(3)" id="step-card-3">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-cyan-500 to-teal-400"></div>
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="w-8 h-8 rounded-full bg-cyan-500 text-white font-black text-sm flex items-center justify-center shadow-lg shadow-cyan-500/30">
                                        3
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/15 text-cyan-500 border border-cyan-500/25">
                                        Sistem IoT
                                    </span>
                                </div>
                                <h3 class="font-outfit text-base font-extrabold leading-snug group-hover:text-cyan-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Pelacakan Perjalanan') }}
                                </h3>
                                <p class="text-xs leading-relaxed mt-1.5" style="color: var(--text-muted)">
                                    {{ __('Telemetri satelit AIS real-time, koordinat lintang/bujur kapal, suhu kontainer, & ETA live.') }}
                                </p>
                            </div>

                            <div class="mt-3 rounded-2xl overflow-hidden bg-gradient-to-b from-cyan-500/5 to-cyan-500/10 p-2.5 flex items-center justify-center border border-cyan-500/15">
                                <img src="{{ asset('images/alur/step-3-pedestal.png') }}" 
                                     alt="Step 3 Pelacakan Perjalanan" 
                                     class="w-full h-28 object-contain group-hover:scale-105 transition-transform duration-500 animate-float [animation-delay:1.6s] pointer-events-none">
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200/40 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-cyan-500">
                                <span>Satelit AIS Live</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>

                        {{-- STEP 4: Deteksi Keterlambatan & Exception --}}
                        <div class="flow-3d-card theme-card rounded-3xl p-5 flex flex-col justify-between cursor-pointer group overflow-hidden border backdrop-blur-xl"
                             style="position: absolute; left: 50%; top: 50%; width: min(295px, 76vw); height: 355px; background: var(--card-bg); border-color: var(--card-border); transform-origin: center center; backface-visibility: hidden; transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.45s ease, filter 0.45s ease, box-shadow 0.45s ease, border-color 0.45s ease;"
                             onclick="selectFlowStep(4)" id="step-card-4">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-rose-500 to-orange-400"></div>
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="w-8 h-8 rounded-full bg-rose-500 text-white font-black text-sm flex items-center justify-center shadow-lg shadow-rose-500/30">
                                        4
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-500 border border-rose-500/25">
                                        AI Warning
                                    </span>
                                </div>
                                <h3 class="font-outfit text-base font-extrabold leading-snug group-hover:text-rose-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Deteksi Exception') }}
                                </h3>
                                <p class="text-xs leading-relaxed mt-1.5" style="color: var(--text-muted)">
                                    {{ __('Peringatan dini cuaca badai, potensi delay rute maritim, & auto-notifikasi multi-kanal.') }}
                                </p>
                            </div>

                            <div class="mt-3 rounded-2xl overflow-hidden bg-gradient-to-b from-rose-500/5 to-rose-500/10 p-2.5 flex items-center justify-center border border-rose-500/15">
                                <img src="{{ asset('images/alur/step-4-pedestal.png') }}" 
                                     alt="Step 4 Deteksi Exception" 
                                     class="w-full h-28 object-contain group-hover:scale-105 transition-transform duration-500 animate-float [animation-delay:2.4s] pointer-events-none">
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200/40 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-rose-500">
                                <span>Mitigasi Anomali</span>
                                <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover:translate-x-1 transition-transform"></i>
                            </div>
                        </div>

                        {{-- STEP 5: Penyelesaian & Analitik --}}
                        <div class="flow-3d-card theme-card rounded-3xl p-5 flex flex-col justify-between cursor-pointer group overflow-hidden border backdrop-blur-xl"
                             style="position: absolute; left: 50%; top: 50%; width: min(295px, 76vw); height: 355px; background: var(--card-bg); border-color: var(--card-border); transform-origin: center center; backface-visibility: hidden; transition: transform 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.45s ease, filter 0.45s ease, box-shadow 0.45s ease, border-color 0.45s ease;"
                             onclick="selectFlowStep(5)" id="step-card-5">
                            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-purple-600 to-indigo-500"></div>
                            <div>
                                <div class="flex items-center justify-between mb-2.5">
                                    <span class="w-8 h-8 rounded-full bg-purple-600 text-white font-black text-sm flex items-center justify-center shadow-lg shadow-purple-500/30">
                                        5
                                    </span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-500/15 text-purple-500 border border-purple-500/25">
                                        Admin
                                    </span>
                                </div>
                                <h3 class="font-outfit text-base font-extrabold leading-snug group-hover:text-purple-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Penyelesaian & Analitik') }}
                                </h3>
                                <p class="text-xs leading-relaxed mt-1.5" style="color: var(--text-muted)">
                                    {{ __('Konfirmasi kargo tiba di tujuan (Delivered), POD digital, & rekap evaluasi analitik bisnis.') }}
                                </p>
                            </div>

                            <div class="mt-3 rounded-2xl overflow-hidden bg-gradient-to-b from-purple-500/5 to-purple-500/10 p-2.5 flex items-center justify-center border border-purple-500/15">
                                <img src="{{ asset('images/alur/step-5-pedestal.png') }}" 
                                     alt="Step 5 Penyelesaian & Analitik" 
                                     class="w-full h-28 object-contain group-hover:scale-105 transition-transform duration-500 animate-float [animation-delay:3.2s] pointer-events-none">
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-200/40 dark:border-slate-800 flex items-center justify-between text-xs font-semibold text-purple-500">
                                <span>SLA Delivered &amp; BI</span>
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                            </div>
                        </div>

                    </div>

                    {{-- Next Arrow Button --}}
                    <button type="button" onclick="stepCarousel(1)" aria-label="Tahap Berikutnya"
                            class="absolute right-1 sm:right-4 z-40 w-10 h-10 sm:w-11 sm:h-11 rounded-full flex items-center justify-center border backdrop-blur-md shadow-lg transition-all hover:scale-110 active:scale-95"
                            style="background: var(--card-bg); border-color: var(--card-border); color: var(--text-heading);">
                        <i data-lucide="chevron-right" class="w-5 h-5"></i>
                    </button>

                </div>
            </div>


            {{-- 3. The Structured Information Cards (Tujuan Sistem & Aktor Hak Akses) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-8">
                
                {{-- Card 1: Tujuan Alur Kerja (4 Cols) --}}
                <div class="lg:col-span-4 theme-card rounded-2xl p-6 flex flex-col justify-between border hover:border-cyan-500/40 transition-all duration-300">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-600 text-white mb-4 shadow-md shadow-blue-500/20">
                            <i data-lucide="target" class="w-3.5 h-3.5"></i>
                            <span>{{ __('Tujuan Alur Kerja') }}</span>
                        </div>

                        <h4 class="font-outfit text-base sm:text-lg font-extrabold mb-2" style="color: var(--text-heading)">
                            {{ __('Transparansi Logistik Kargo Kopi') }}
                        </h4>

                        <p class="text-xs sm:text-sm leading-relaxed" style="color: var(--text-body)">
                            {{ __('Menggambarkan alur kerja sistem CargoTrace secara menyeluruh, mulai dari pemesanan hingga analitik eksekusi, dengan keterlibatan multi hak akses.') }}
                        </p>
                    </div>

                    <div class="space-y-2 mt-5 pt-4 border-t border-slate-200/40 dark:border-slate-800 text-xs font-medium" style="color: var(--text-body)">
                        <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Akurasi Tracking Satelit 99.8%</span>
                        </div>
                        <div class="flex items-center gap-2 text-blue-600 dark:text-blue-400">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Zero Bottleneck Dokumen Pabean</span>
                        </div>
                        <div class="flex items-center gap-2 text-cyan-600 dark:text-cyan-400">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            <span>Notifikasi Real-Time Multi-Aktor</span>
                        </div>
                    </div>
                </div>

                {{-- Card 2: Aktor / Hak Akses (8 Cols - 4 Actor Grid) --}}
                <div class="lg:col-span-8 theme-card rounded-2xl p-6 border hover:border-cyan-500/40 transition-all duration-300">
                    <div class="flex items-center justify-between mb-5">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-blue-600 text-white shadow-md shadow-blue-500/20">
                            <i data-lucide="users" class="w-3.5 h-3.5"></i>
                            <span>{{ __('Aktor / Hak Akses') }}</span>
                        </div>
                        <span class="text-xs font-semibold text-cyan-600 dark:text-cyan-400">4 Hak Akses Terintegrasi</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        
                        {{-- Actor 1: Customer (Buyer) --}}
                        <div class="p-3.5 rounded-xl border transition-all duration-300 hover:scale-[1.02] flex items-start gap-3 cursor-pointer"
                             id="actor-card-1"
                             onclick="selectActorStep(1)"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border-2 border-blue-500/40 shadow-sm bg-blue-100 dark:bg-blue-950 flex items-center justify-center">
                                <img src="{{ asset('images/alur/avatar-customer.png') }}" alt="Customer" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold leading-tight mb-0.5" style="color: var(--text-heading)">
                                    {{ __('Customer (Buyer)') }}
                                </div>
                                <p class="text-[11px] leading-snug" style="color: var(--text-muted)">
                                    {{ __('Memesan kargo, melihat status posisi live, dan terima notifikasi kedatangan.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Actor 2: Logistics Operator --}}
                        <div class="p-3.5 rounded-xl border transition-all duration-300 hover:scale-[1.02] flex items-start gap-3 cursor-pointer"
                             id="actor-card-2"
                             onclick="selectActorStep(2)"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border-2 border-amber-500/40 shadow-sm bg-amber-100 dark:bg-amber-950 flex items-center justify-center">
                                <img src="{{ asset('images/alur/avatar-operator.png') }}" alt="Operator" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold leading-tight mb-0.5" style="color: var(--text-heading)">
                                    {{ __('Logistics Operator') }}
                                </div>
                                <p class="text-[11px] leading-snug" style="color: var(--text-muted)">
                                    {{ __('Mengelola ekspedisi, dokumen pabean pelabuhan, dan pemantauan kargo.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Actor 3: Sistem Otomatis --}}
                        <div class="p-3.5 rounded-xl border transition-all duration-300 hover:scale-[1.02] flex items-start gap-3 cursor-pointer"
                             id="actor-card-3"
                             onclick="selectActorStep(3)"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border-2 border-cyan-500/40 shadow-sm bg-cyan-100 dark:bg-cyan-950 flex items-center justify-center">
                                <img src="{{ asset('images/alur/avatar-system.png') }}" alt="Sistem Otomatis" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold leading-tight mb-0.5" style="color: var(--text-heading)">
                                    {{ __('Sistem (Otomatis)') }}
                                </div>
                                <p class="text-[11px] leading-snug" style="color: var(--text-muted)">
                                    {{ __('Memproses telemetri IoT, melacak pergerakan kapal, & kirim alert kendala.') }}
                                </p>
                            </div>
                        </div>

                        {{-- Actor 4: Administrator --}}
                        <div class="p-3.5 rounded-xl border transition-all duration-300 hover:scale-[1.02] flex items-start gap-3 cursor-pointer"
                             id="actor-card-4"
                             onclick="selectActorStep(4)"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <div class="w-10 h-10 rounded-full overflow-hidden shrink-0 border-2 border-purple-500/40 shadow-sm bg-purple-100 dark:bg-purple-950 flex items-center justify-center">
                                <img src="{{ asset('images/alur/avatar-admin.png') }}" alt="Administrator" class="w-full h-full object-cover">
                            </div>
                            <div class="min-w-0">
                                <div class="text-xs font-extrabold leading-tight mb-0.5" style="color: var(--text-heading)">
                                    {{ __('Administrator') }}
                                </div>
                                <p class="text-[11px] leading-snug" style="color: var(--text-muted)">
                                    {{ __('Memantau kinerja sistem, audit validasi data, dan manajemen otorisasi akses.') }}
                                </p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>


            {{-- 4. Bottom Ribbon Bar: Hasil / Output Sistem --}}
            <div class="theme-card rounded-2xl p-3 sm:p-4 border shadow-xl transition-all duration-300 hover:shadow-cyan-500/10"
                 style="background: var(--card-bg); border-color: var(--card-border);">
                
                <div class="flex flex-col lg:flex-row items-stretch lg:items-center gap-3">
                    
                    {{-- Left Badge Header --}}
                    <div class="px-5 py-3 rounded-xl bg-gradient-to-r from-blue-700 via-blue-600 to-cyan-600 text-white flex items-center justify-between lg:justify-center gap-3 shrink-0 shadow-lg shadow-blue-600/30">
                        <div class="flex items-center gap-2.5">
                            <span class="p-1 rounded-full bg-white/20">
                                <i data-lucide="target" class="w-4 h-4 text-white"></i>
                            </span>
                            <span class="font-outfit text-sm font-extrabold uppercase tracking-wide">
                                {{ __('Hasil / Output Sistem') }}
                            </span>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-cyan-200"></i>
                    </div>

                    {{-- 5 Output Cards Grid --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2.5 grow">
                        
                        {{-- Output 1 --}}
                        <div class="p-2.5 rounded-xl border flex items-center gap-2.5 hover:bg-blue-500/5 transition-all hover:border-blue-400/40"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <img src="{{ asset('images/alur/icon-box-order.png') }}" alt="Order Kargo" class="w-8 h-8 object-contain shrink-0 drop-shadow-sm">
                            <span class="text-xs font-semibold leading-snug" style="color: var(--text-heading)">
                                {{ __('Order kargo tercatat & tervalidasi') }}
                            </span>
                        </div>

                        {{-- Output 2 --}}
                        <div class="p-2.5 rounded-xl border flex items-center gap-2.5 hover:bg-cyan-500/5 transition-all hover:border-cyan-400/40"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <img src="{{ asset('images/alur/icon-ship-tracking.png') }}" alt="Pelacakan Kapal" class="w-8 h-8 object-contain shrink-0 drop-shadow-sm">
                            <span class="text-xs font-semibold leading-snug" style="color: var(--text-heading)">
                                {{ __('Rute & pelacakan real-time di peta') }}
                            </span>
                        </div>

                        {{-- Output 3 --}}
                        <div class="p-2.5 rounded-xl border flex items-center gap-2.5 hover:bg-amber-500/5 transition-all hover:border-amber-400/40"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <img src="{{ asset('images/alur/icon-bell-notification.png') }}" alt="Notifikasi Kendala" class="w-8 h-8 object-contain shrink-0 drop-shadow-sm">
                            <span class="text-xs font-semibold leading-snug" style="color: var(--text-heading)">
                                {{ __('Notifikasi otomatis jika ada kendala') }}
                            </span>
                        </div>

                        {{-- Output 4 --}}
                        <div class="p-2.5 rounded-xl border flex items-center gap-2.5 hover:bg-emerald-500/5 transition-all hover:border-emerald-400/40"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <img src="{{ asset('images/alur/icon-barchart-status.png') }}" alt="Status Delivered" class="w-8 h-8 object-contain shrink-0 drop-shadow-sm">
                            <span class="text-xs font-semibold leading-snug" style="color: var(--text-heading)">
                                {{ __('Status pengiriman hingga Delivered') }}
                            </span>
                        </div>

                        {{-- Output 5 --}}
                        <div class="p-2.5 rounded-xl border flex items-center gap-2.5 hover:bg-purple-500/5 transition-all hover:border-purple-400/40"
                             style="background: var(--bg-page); border-color: var(--card-border);">
                            <img src="{{ asset('images/alur/icon-database-bi.png') }}" alt="Analitik Bisnis" class="w-8 h-8 object-contain shrink-0 drop-shadow-sm">
                            <span class="text-xs font-semibold leading-snug" style="color: var(--text-heading)">
                                {{ __('Data analitik bisnis untuk keputusan') }}
                            </span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Client-side 3D Coverflow & Stakeholder Sync Script --}}
        <script>
            let currentFlowStep = 3;

            function updateMobileStepperUi(stepNumber) {
                const colorClasses = {
                    1: 'text-blue-500 bg-blue-500/15 shadow-sm',
                    2: 'text-amber-500 bg-amber-500/15 shadow-sm',
                    3: 'text-cyan-500 bg-cyan-500/15 shadow-sm',
                    4: 'text-rose-500 bg-rose-500/15 shadow-sm',
                    5: 'text-purple-500 bg-purple-500/15 shadow-sm'
                };
                for (let i = 1; i <= 5; i++) {
                    const btn = document.getElementById('mob-step-btn-' + i);
                    if (btn) {
                        if (i === stepNumber) {
                            btn.className = `flex-1 py-1.5 px-2 text-center rounded-xl text-[11px] sm:text-xs font-bold transition-all ${colorClasses[i]}`;
                        } else {
                            btn.className = 'flex-1 py-1.5 px-2 text-center rounded-xl text-[11px] sm:text-xs font-bold transition-all text-slate-500 hover:text-cyan-500';
                        }
                    }
                }
            }

            function render3DCarousel(activeStep) {
                const vw = window.innerWidth;
                const spread1 = vw >= 1024 ? 210 : (vw >= 640 ? 150 : 72);
                const spread2 = vw >= 1024 ? 385 : (vw >= 640 ? 265 : 120);

                const activeGlows = {
                    1: '0 24px 55px -12px rgba(37, 99, 235, 0.45)',
                    2: '0 24px 55px -12px rgba(245, 158, 11, 0.45)',
                    3: '0 24px 55px -12px rgba(6, 182, 212, 0.45)',
                    4: '0 24px 55px -12px rgba(244, 63, 94, 0.45)',
                    5: '0 24px 55px -12px rgba(147, 51, 234, 0.45)'
                };
                const activeBorders = {
                    1: 'rgba(59, 130, 246, 0.75)',
                    2: 'rgba(245, 158, 11, 0.75)',
                    3: 'rgba(6, 182, 212, 0.8)',
                    4: 'rgba(244, 63, 94, 0.75)',
                    5: 'rgba(168, 85, 247, 0.75)'
                };

                for (let i = 1; i <= 5; i++) {
                    const card = document.getElementById('step-card-' + i);
                    if (!card) continue;

                    // Circular offset in range [-2, -1, 0, 1, 2]
                    let diff = ((i - activeStep + 7) % 5) - 2;
                    const absDiff = Math.abs(diff);

                    if (diff === 0) {
                        card.style.transform = 'translate(-50%, -50%) translateZ(0px) scale(1) rotateY(0deg)';
                        card.style.zIndex = '30';
                        card.style.opacity = '1';
                        card.style.filter = 'brightness(1)';
                        card.style.boxShadow = activeGlows[i];
                        card.style.borderColor = activeBorders[i];
                    } else if (absDiff === 1) {
                        const tx = diff * spread1;
                        const ry = diff * -14;
                        card.style.transform = `translate(calc(-50% + ${tx}px), -50%) translateZ(-110px) scale(0.86) rotateY(${ry}deg)`;
                        card.style.zIndex = '20';
                        card.style.opacity = '0.9';
                        card.style.filter = 'brightness(0.82)';
                        card.style.boxShadow = '0 16px 35px -10px rgba(0, 0, 0, 0.45)';
                        card.style.borderColor = 'var(--card-border)';
                    } else {
                        const tx = (diff > 0 ? 1 : -1) * spread2;
                        const ry = (diff > 0 ? 1 : -1) * -22;
                        card.style.transform = `translate(calc(-50% + ${tx}px), -50%) translateZ(-220px) scale(0.72) rotateY(${ry}deg)`;
                        card.style.zIndex = '10';
                        card.style.opacity = '0.68';
                        card.style.filter = 'brightness(0.65)';
                        card.style.boxShadow = '0 10px 25px -10px rgba(0, 0, 0, 0.4)';
                        card.style.borderColor = 'var(--card-border)';
                    }
                }
            }

            function selectFlowStep(stepNumber) {
                currentFlowStep = stepNumber;
                updateMobileStepperUi(stepNumber);
                render3DCarousel(stepNumber);

                // Highlight corresponding stakeholder / actor card
                const actorMapping = {
                    1: 1, // Customer
                    2: 2, // Operator
                    3: 3, // Sistem IoT
                    4: 3, // Sistem AI Warning
                    5: 4  // Admin
                };

                const targetActor = actorMapping[stepNumber];
                for (let a = 1; a <= 4; a++) {
                    const actorCard = document.getElementById('actor-card-' + a);
                    if (actorCard) {
                        if (a === targetActor) {
                            actorCard.classList.add('ring-2', 'ring-cyan-500', 'scale-[1.03]', 'shadow-lg');
                        } else {
                            actorCard.classList.remove('ring-2', 'ring-cyan-500', 'scale-[1.03]', 'shadow-lg');
                        }
                    }
                }
            }

            function stepCarousel(delta) {
                const next = ((currentFlowStep - 1 + delta + 5) % 5) + 1;
                selectFlowStep(next);
            }

            function selectActorStep(actorId) {
                if (actorId === 1) selectFlowStep(1);
                else if (actorId === 2) selectFlowStep(2);
                else if (actorId === 3) selectFlowStep(currentFlowStep === 3 ? 4 : 3);
                else if (actorId === 4) selectFlowStep(5);
            }

            function jumpToStep(stepNumber) {
                selectFlowStep(stepNumber);
            }

            document.addEventListener('DOMContentLoaded', () => {
                selectFlowStep(3);

                window.addEventListener('resize', () => {
                    render3DCarousel(currentFlowStep);
                });

                // Touch Swipe Support on 3D Wrapper
                const wrapper = document.getElementById('flow-3d-wrapper');
                if (wrapper) {
                    let touchStartX = 0;
                    wrapper.addEventListener('touchstart', (e) => {
                        touchStartX = e.changedTouches[0].screenX;
                    }, { passive: true });
                    wrapper.addEventListener('touchend', (e) => {
                        const dx = e.changedTouches[0].screenX - touchStartX;
                        if (Math.abs(dx) > 40) {
                            stepCarousel(dx < 0 ? 1 : -1);
                        }
                    }, { passive: true });
                }
            });
        </script>

        {{-- Flowing Ocean Wave at bottom of Alur Sistem transitioning into Fitur --}}
        <div class="w-full overflow-hidden leading-none pt-10 sm:pt-14 pointer-events-none">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-8 sm:h-12" preserveAspectRatio="none">
                <path d="M0,25 C320,55 640,10 960,35 C1200,55 1360,20 1440,25 L1440,60 L0,60 Z" fill="var(--brand-cyan)" opacity="0.2"/>
                <path d="M0,35 C280,15 600,55 920,25 C1180,45 1340,15 1440,35 L1440,60 L0,60 Z" fill="var(--bg-page)"/>
            </svg>
        </div>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 5: FITUR UNGGULAN PLATFORM                                        --}}
    {{-- ========================================================================= --}}
    <section id="fitur" class="py-16 sm:py-20 lg:py-24 relative overflow-hidden transition-colors duration-300" style="background: var(--bg-page)">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- Section Header: 2 Columns (Text on Left, 3D Telemetry Laptop Artwork on Right) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center mb-12 sm:mb-16">
                
                <div class="lg:col-span-7 text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-4 shadow-sm bg-blue-500/10 text-blue-600 dark:text-cyan-400 border border-blue-500/20">
                        <i data-lucide="package" class="w-3.5 h-3.5"></i>
                        <span>{{ __('Arsitektur & Fitur Unggulan') }}</span>
                    </div>
                    <h2 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4 leading-tight"
                        style="color: var(--text-heading)">
                        {{ __('Dibangun untuk Kompleksitas Logistik Kopi Maritim') }}
                    </h2>
                    <p class="text-sm sm:text-base lg:text-lg leading-relaxed font-normal" style="color: var(--text-muted)">
                        {{ __('Integrasi fitur canggih yang memberikan keunggulan operasional, keandalan analitik bisnis, dan transparansi penuh bagi seluruh pemangku kepentingan.') }}
                    </p>
                </div>

                <div class="lg:col-span-5 flex justify-center lg:justify-end">
                    <div class="relative w-full max-w-sm sm:max-w-md">
                        {{-- Atmosphere Glow --}}
                        <div class="absolute inset-0 bg-gradient-to-tr from-cyan-500/20 to-blue-600/20 rounded-full blur-3xl pointer-events-none -z-10"></div>
                        <img src="{{ asset('images/alur/laptop-tracking-containers.png') }}" 
                             alt="Telemetri Global Kargo & Kontainer" 
                             class="w-full h-auto object-contain animate-float drop-shadow-2xl [animation-duration:6s]">
                    </div>
                </div>

            </div>

            {{-- 4 Core Architectural Features Grid (2x2 Grid with 3D Pedestal on the Left of each Card) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                
                {{-- Feature 1: Live Maritime Geolocation Tracking --}}
                <div class="theme-card rounded-3xl p-6 sm:p-7 border transition-all duration-300 hover:shadow-2xl hover:border-blue-500/40 relative overflow-hidden group">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 sm:gap-6">
                        
                        {{-- 3D Pedestal Asset on Left --}}
                        <div class="w-full sm:w-44 md:w-48 shrink-0 flex items-center justify-center">
                            <img src="{{ asset('images/alur/step-3-pedestal.png') }}" 
                                 alt="Pelacakan Geolokasi Maritim" 
                                 class="w-36 sm:w-44 h-auto object-contain group-hover:scale-105 transition-transform duration-500 animate-float drop-shadow-lg">
                        </div>

                        {{-- Text Content on Right --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2.5">
                                <span class="w-9 h-9 rounded-xl bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-outfit text-base sm:text-lg font-bold leading-snug group-hover:text-blue-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Pelacakan Geolokasi Maritim Interaktif (Leaflet)') }}
                                </h3>
                            </div>

                            <p class="text-xs sm:text-sm leading-relaxed mb-4" style="color: var(--text-muted)">
                                {{ __('Peta maritim digital berskala global yang memproyeksikan lintasan pelayaran kapal kontainer kopi dari Dermaga Santos ke berbagai pelabuhan tujuan dunia.') }}
                            </p>

                            <ul class="space-y-2 text-xs sm:text-sm font-medium" style="color: var(--text-body)">
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Visualisasi rute Great Circle Atlantik & Pasifik') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Estimasi waktu kedatangan (ETA) adaptif') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Riwayat checkpoint pelabuhan yang tercatat kronologis') }}</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                {{-- Feature 2: Automated Delay & Exception Engine --}}
                <div class="theme-card rounded-3xl p-6 sm:p-7 border transition-all duration-300 hover:shadow-2xl hover:border-rose-500/40 relative overflow-hidden group">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 sm:gap-6">
                        
                        {{-- 3D Pedestal Asset on Left --}}
                        <div class="w-full sm:w-44 md:w-48 shrink-0 flex items-center justify-center">
                            <img src="{{ asset('images/alur/step-4-pedestal.png') }}" 
                                 alt="Deteksi Keterlambatan" 
                                 class="w-36 sm:w-44 h-auto object-contain group-hover:scale-105 transition-transform duration-500 animate-float [animation-delay:1.5s] drop-shadow-lg">
                        </div>

                        {{-- Text Content on Right --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2.5">
                                <span class="w-9 h-9 rounded-xl bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-outfit text-base sm:text-lg font-bold leading-snug group-hover:text-rose-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Deteksi Keterlambatan & Exception Otomatis') }}
                                </h3>
                            </div>

                            <p class="text-xs sm:text-sm leading-relaxed mb-4" style="color: var(--text-muted)">
                                {{ __('Engine cerdas yang secara deterministik membandingkan estimasi kedatangan dengan ambang batas (delay threshold).') }}
                            </p>

                            <ul class="space-y-2 text-xs sm:text-sm font-medium" style="color: var(--text-body)">
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Klasifikasi otomatis kendala (cuaca, dokumen, mesin)') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Notifikasi cepat ke buyer dan operator logistik') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Pencatatan tindakan resolusi dan pemulihan jadwal') }}</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                {{-- Feature 3: Strict RBAC & Anti-IDOR Security --}}
                <div class="theme-card rounded-3xl p-6 sm:p-7 border transition-all duration-300 hover:shadow-2xl hover:border-emerald-500/40 relative overflow-hidden group">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 sm:gap-6">
                        
                        {{-- 3D Pedestal Asset on Left --}}
                        <div class="w-full sm:w-44 md:w-48 shrink-0 flex items-center justify-center">
                            <img src="{{ asset('images/alur/pedestal-security-shield.png') }}" 
                                 alt="Keamanan Berlapis RBAC" 
                                 class="w-36 sm:w-44 h-auto object-contain group-hover:scale-105 transition-transform duration-500 animate-float [animation-delay:2.5s] drop-shadow-lg">
                        </div>

                        {{-- Text Content on Right --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2.5">
                                <span class="w-9 h-9 rounded-xl bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-outfit text-base sm:text-lg font-bold leading-snug group-hover:text-emerald-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Keamanan Berlapis (RBAC & Anti-IDOR)') }}
                                </h3>
                            </div>

                            <p class="text-xs sm:text-sm leading-relaxed mb-4" style="color: var(--text-muted)">
                                {{ __('Pemisahan hak akses yang ketat antara 3 peran: Administrator, Logistics Operator, dan Customer, dengan perlindungan policy tingkat lanjut.') }}
                            </p>

                            <ul class="space-y-2 text-xs sm:text-sm font-medium" style="color: var(--text-body)">
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Audit logging aktivitas (IP, User Agent, dan waktu)') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Proteksi manipulasi rute/status di luar kewenangan') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Enkripsi kata sandi mutakhir dan protokol brute-force') }}</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

                {{-- Feature 4: Executive Business Intelligence & Analytics --}}
                <div class="theme-card rounded-3xl p-6 sm:p-7 border transition-all duration-300 hover:shadow-2xl hover:border-amber-500/40 relative overflow-hidden group">
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 sm:gap-6">
                        
                        {{-- 3D Pedestal Asset on Left --}}
                        <div class="w-full sm:w-44 md:w-48 shrink-0 flex items-center justify-center">
                            <img src="{{ asset('images/alur/step-5-pedestal.png') }}" 
                                 alt="Dasbor Analitik Bisnis" 
                                 class="w-36 sm:w-44 h-auto object-contain group-hover:scale-105 transition-transform duration-500 animate-float [animation-delay:3.5s] drop-shadow-lg">
                        </div>

                        {{-- Text Content on Right --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2.5">
                                <span class="w-9 h-9 rounded-xl bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/20 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-outfit text-base sm:text-lg font-bold leading-snug group-hover:text-amber-500 transition-colors" style="color: var(--text-heading)">
                                    {{ __('Dasbor Analitik Bisnis & Telemetri Kargo') }}
                                </h3>
                            </div>

                            <p class="text-xs sm:text-sm leading-relaxed mb-4" style="color: var(--text-muted)">
                                {{ __('Pusat telemetri bisnis komprehensif bagi Administrator untuk memantau kinerja ekspor kopi secara menyeluruh.') }}
                            </p>

                            <ul class="space-y-2 text-xs sm:text-sm font-medium" style="color: var(--text-body)">
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Grafik interaktif pendapatan bulanan dan tren rute') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Peringkat negara pembeli terbanyak secara real-time') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                                    <span>{{ __('Ekspor data pelacakan dan analitik untuk audit eksternal') }}</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>

            </div>

        </div>

        {{-- Flowing Ocean Wave at bottom of Fitur transitioning into Kontak --}}
        <div class="w-full overflow-hidden leading-none pt-10 sm:pt-14 pointer-events-none">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-8 sm:h-12" preserveAspectRatio="none">
                <path d="M0,25 C320,55 640,10 960,35 C1200,55 1360,20 1440,25 L1440,60 L0,60 Z" fill="var(--brand-cyan)" opacity="0.2"/>
                <path d="M0,35 C280,15 600,55 920,25 C1180,45 1340,15 1440,35 L1440,60 L0,60 Z" fill="var(--kontak-bg-color)"/>
            </svg>
        </div>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 6: KONTAK & QUICK TRACK (Santos HQ & Direct Contact)              --}}
    {{-- ========================================================================= --}}
    <section id="kontak" class="py-16 sm:py-20 lg:py-28 relative overflow-hidden transition-colors duration-300 min-h-[620px] flex items-center"
             style="background: var(--kontak-bg-color); border-color: var(--kontak-border-color);">

        {{-- Desktop Panoramic Background Layer with Port of Santos Ship --}}
        <div class="hidden lg:block absolute inset-0 w-full h-full pointer-events-none select-none transition-all duration-500"
             style="background-image: var(--kontak-bg-img); background-size: cover; background-position: center right; background-repeat: no-repeat; filter: contrast(1.06) saturate(1.12);">
            {{-- Smooth Gradient Mask on the left to keep text ultra-readable --}}
            <div class="absolute inset-0 transition-all duration-500" style="background: var(--kontak-mask-gradient);"></div>
        </div>

        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- Mobile Adaptive Visual Banner --}}
            <div class="lg:hidden w-full mb-8">
                <div class="relative rounded-2xl overflow-hidden shadow-2xl border" style="border-color: var(--kontak-border-color);">
                    <img id="kontakMobileBanner" src="{{ asset('images/kontak-port-santos-bg.jpg') }}" alt="Pelabuhan Santos & Pengiriman Global CargoTrace" class="w-full h-48 sm:h-64 object-cover object-right">
                    <div class="absolute inset-0 bg-gradient-to-t from-[var(--kontak-bg-color)] via-transparent to-transparent"></div>
                    <div class="absolute bottom-3 left-4 right-4 flex items-center justify-between text-white">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-blue-600/90 text-white shadow-sm backdrop-blur-sm uppercase tracking-wider">
                            Port of Santos • Brazil
                        </span>
                        <span class="text-xs font-semibold text-cyan-200 drop-shadow">Hub Ekspor Maritim Global</span>
                    </div>
                </div>
            </div>

            {{-- Main Content: Left-Aligned (Leaving the Ship & Crane on the right completely visible) --}}
            <div class="w-full lg:max-w-xl xl:max-w-2xl text-left">
                
                {{-- Pill Badge --}}
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-5 shadow-sm backdrop-blur-md"
                     style="background: var(--brand-glow); color: var(--brand-cyan); border: 1px solid var(--kontak-icon-border);">
                    <i data-lucide="mail" class="w-3.5 h-3.5" style="color: var(--brand-cyan);"></i>
                    <span>{{ __('HUBUNGI KAMI & LACAK CEPAT') }}</span>
                </div>

                {{-- Heading --}}
                <h2 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4 leading-tight"
                    style="color: var(--kontak-heading-color);">
                    {{ __('Siap Memulai Pengiriman') }}<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 to-blue-500">
                        {{ __('Kopi Spesialti Anda?') }}
                    </span>
                </h2>

                {{-- Subtitle --}}
                <p class="text-sm sm:text-base leading-relaxed mb-8 max-w-lg" style="color: var(--kontak-muted-color);">
                    {{ __('Hubungi perwakilan Santos Exporters S.A. di Pelabuhan Santos atau lacak langsung nomor resi kontainer Anda secara instan.') }}
                </p>

                {{-- 4 Contact Info Points with Glowing Neon Circles --}}
                <div class="space-y-4 max-w-xl">
                    
                    {{-- Item 1: Kantor Operasional & Dermaga Santos --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 shadow-lg group-hover:scale-105 transition-all"
                             style="background: var(--kontak-icon-bg); border: 1px solid var(--kontak-icon-border); color: var(--kontak-icon-color);">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0 pt-0.5">
                            <div class="text-[11px] font-extrabold uppercase tracking-wider mb-0.5" style="color: var(--kontak-heading-color);">
                                {{ __('KANTOR OPERASIONAL & DERMAGA SANTOS') }}
                            </div>
                            <div class="text-xs sm:text-sm leading-snug" style="color: var(--kontak-body-color);">
                                Santos Exporters S.A., Terminal Portuário de Santos, Cais do Saboó, Santos - SP, 11010-140, Brasil
                            </div>
                        </div>
                    </div>

                    {{-- Item 2: Perwakilan Asia Pasifik (Indonesia) --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 shadow-lg group-hover:scale-105 transition-all"
                             style="background: var(--kontak-icon-bg); border: 1px solid var(--kontak-icon-border); color: var(--kontak-icon-color);">
                            <i data-lucide="network" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0 pt-0.5">
                            <div class="text-[11px] font-extrabold uppercase tracking-wider mb-0.5" style="color: var(--kontak-heading-color);">
                                {{ __('PERWAKILAN ASIA PASIFIK (INDONESIA)') }}
                            </div>
                            <div class="text-xs sm:text-sm leading-snug" style="color: var(--kontak-body-color);">
                                Kawasan Logistik Maritim Pelabuhan Tanjung Priok, Jakarta Utara, 14310
                            </div>
                        </div>
                    </div>

                    {{-- Item 3: Kontak Email --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 shadow-lg group-hover:scale-105 transition-all"
                             style="background: var(--kontak-icon-bg); border: 1px solid var(--kontak-icon-border); color: var(--kontak-icon-color);">
                            <i data-lucide="mail" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0 pt-0.5">
                            <div class="text-[11px] font-extrabold uppercase tracking-wider mb-0.5" style="color: var(--kontak-heading-color);">
                                {{ __('KONTAK EMAIL') }}
                            </div>
                            <div class="text-xs sm:text-sm font-medium" style="color: var(--brand-cyan);">
                                ops@cargotrace.com / export@santosexporters.br
                            </div>
                        </div>
                    </div>

                    {{-- Item 4: Layanan Telepon Operasional (24/7) --}}
                    <div class="flex items-start gap-3.5 group">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shrink-0 shadow-lg group-hover:scale-105 transition-all"
                             style="background: var(--kontak-icon-bg); border: 1px solid var(--kontak-icon-border); color: var(--kontak-icon-color);">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0 pt-0.5">
                            <div class="text-[11px] font-extrabold uppercase tracking-wider mb-0.5" style="color: var(--kontak-heading-color);">
                                {{ __('LAYANAN TELEPON OPERASIONAL (24/7)') }}
                            </div>
                            <div class="text-xs sm:text-sm font-semibold" style="color: var(--kontak-body-color);">
                                +55 (13) 3219-4800 / +62 (21) 5088-7200
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- Flowing Ocean Wave at bottom of Kontak transitioning into Footer --}}
        <div class="w-full overflow-hidden leading-none pt-10 sm:pt-14 pointer-events-none">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-8 sm:h-12" preserveAspectRatio="none">
                <path d="M0,25 C320,55 640,10 960,35 C1200,55 1360,20 1440,25 L1440,60 L0,60 Z" fill="var(--brand-cyan)" opacity="0.2"/>
                <path d="M0,35 C280,15 600,55 920,25 C1180,45 1340,15 1440,35 L1440,60 L0,60 Z" fill="var(--bg-page)"/>
            </svg>
        </div>
    </section>


    {{-- ========================================================================= --}}
    {{-- FOOTER                                                                    --}}
    {{-- ========================================================================= --}}
    <footer class="pt-16 pb-12 relative text-sm"
            style="background: var(--bg-page); color: var(--text-muted)">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b" style="border-color: var(--nav-border)">
                
                {{-- Col 1 & 2: Brand Lockup --}}
                <div class="lg:col-span-2 space-y-4">
                    <a href="#beranda" class="flex items-center gap-3">
                        <div class="w-9 h-9 flex-shrink-0">
                            <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                                <polygon points="60,24 88,40 60,56 32,40" fill="#38BDF8"/>
                                <polygon points="32,40 60,56 60,88 32,72" fill="#0369A1"/>
                                <polygon points="60,56 88,40 88,72 60,88" fill="#0284C7"/>
                                <path d="M 16,84 C 18,98 52,106 82,90 C 98,82 106,68 102,52" fill="none" stroke="#00C2FF" stroke-width="4"/>
                            </svg>
                        </div>
                        <span class="font-outfit text-2xl font-extrabold tracking-tight" style="color: var(--text-heading)">
                            Cargo<span style="color: var(--brand-cyan)">Trace</span>
                        </span>
                    </a>
                    <p class="text-xs sm:text-sm leading-relaxed max-w-sm" style="color: var(--text-body)">
                        Global Shipment Visibility & Tracking Platform untuk rantai pasok ekspor kopi spesialti Brasil dari Pelabuhan Santos ke pasar roastery dunia secara transparan dan real-time.
                    </p>
                    <div class="text-xs" style="color: var(--text-muted)">
                        Santos Exporters S.A. &bull; Terminal Portuário de Santos, Brasil
                    </div>
                </div>

                {{-- Col 3: Navigasi Cepat --}}
                <div class="space-y-3">
                    <h5 class="font-bold text-sm tracking-wider uppercase font-outfit" style="color: var(--text-heading)">Navigasi</h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="#beranda" class="hover:underline transition">Beranda</a></li>
                        <li><a href="#tentang-kami" class="hover:underline transition">Tentang Kami</a></li>
                        <li><a href="#produk" class="hover:underline transition">Katalog Produk Kopi</a></li>
                        <li><a href="#landingpage" class="hover:underline transition">Alur Sistem & Flowmap</a></li>
                        <li><a href="#fitur" class="hover:underline transition">Fitur Unggulan</a></li>
                        <li><a href="#kontak" class="hover:underline transition">Kontak & Hub Santos</a></li>
                    </ul>
                </div>

                {{-- Col 4: Akses Akun Sistem --}}
                <div class="space-y-3">
                    <h5 class="font-bold text-sm tracking-wider uppercase font-outfit" style="color: var(--text-heading)">Akses Sistem</h5>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('login') }}" class="hover:underline transition">Portal Masuk (Login)</a></li>
                        <li><a href="{{ route('login') }}" class="hover:underline transition">Dasbor Administrator</a></li>
                        <li><a href="{{ route('login') }}" class="hover:underline transition">Portal Operator Pelabuhan</a></li>
                        <li><a href="{{ route('login') }}" class="hover:underline transition">Area Pelanggan (Customer)</a></li>
                        <li><a href="#kontak" class="hover:underline transition">Lacak Resi Cepat</a></li>
                    </ul>
                </div>

                {{-- Col 5: Akademik & Sertifikasi --}}
                <div class="space-y-3">
                    <h5 class="font-bold text-sm tracking-wider uppercase font-outfit" style="color: var(--text-heading)">Akademik & Lisensi</h5>
                    <div class="p-3.5 rounded-xl border text-xs space-y-1.5" style="background: rgba(255,255,255,0.04); border-color: var(--nav-border)">
                        <div class="font-bold" style="color: var(--text-heading)">Final Project UAS</div>
                        <div style="color: var(--text-body)">Pemrograman Web Berbasis Framework</div>
                        <div class="text-[11px]" style="color: var(--text-muted)">Teknik Informatika &mdash; Universitas Sriwijaya (UNSRI)</div>
                    </div>
                </div>

            </div>

            {{-- Copyright & Bottom Note --}}
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs" style="color: var(--text-muted)">
                <div>
                    &copy; {{ date('Y') }} CargoTrace & Santos Exporters S.A. Seluruh hak cipta dilindungi undang-undang.
                </div>
                <div class="flex items-center gap-4">
                    <span>Pelabuhan Santos, Brasil</span>
                    <span>&bull;</span>
                    <span>SCA Certified Arabica</span>
                    <span>&bull;</span>
                    <span>AIS Live Marine Geolocation</span>
                </div>
            </div>

        </div>
    </footer>


    {{-- ========================================================================= --}}
    {{-- LIGHTBOX MODAL FOR FLOWMAP ZOOM                                           --}}
    {{-- ========================================================================= --}}
    <div id="flowmapModal" class="fixed inset-0 z-50 hidden bg-black/90 backdrop-blur-md flex items-center justify-center p-4 sm:p-6" onclick="closeFlowmapModal()">
        <div class="relative max-w-6xl w-full max-h-[92vh] flex flex-col rounded-3xl border overflow-hidden shadow-2xl" 
             style="background: var(--bg-alt); border-color: var(--brand-cyan)" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between px-6 py-4 border-b" style="background: var(--bg-page); border-color: var(--nav-border)">
                <div class="flex items-center gap-3">
                    <i data-lucide="git-branch" class="w-5 h-5" style="color: var(--brand-cyan)"></i>
                    <h3 class="font-outfit text-base font-bold" style="color: var(--text-heading)">
                        Flowmap Alur Sistem CargoTrace &mdash; Ekspor Kopi Spesialti B2B (Santos &rarr; Pasar Dunia)
                    </h3>
                </div>
                <button onclick="closeFlowmapModal()" class="p-1 rounded-lg focus:outline-none hover:opacity-80" style="color: var(--text-muted)">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            <div class="p-4 overflow-auto max-h-[calc(92vh-70px)] flex justify-center" style="background: var(--bg-page)">
                <img id="flowmapModalImage" src="{{ asset('images/alur-sistem-3d-v2.jpg') }}?v={{ time() }}" alt="Flowmap Lengkap CargoTrace" class="max-w-full h-auto rounded-xl shadow-2xl">
            </div>
        </div>
    </div>


    {{-- ========================================================================= --}}
    {{-- JAVASCRIPT LOGIC                                                          --}}
    {{-- ========================================================================= --}}
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // -------------------------------------------------------------
        // Theme Switcher: Dark Mode vs Clean / Light Mode
        // -------------------------------------------------------------
        function applyTheme(theme) {
            const root = document.documentElement;
            const icon = document.getElementById('themeToggleIcon');
            const label = document.getElementById('themeToggleText');

            if (theme === 'clean') {
                root.classList.add('theme-clean');
                if (icon) icon.textContent = '🌙';
                if (label) label.textContent = 'Dark Mode';
            } else {
                root.classList.remove('theme-clean');
                if (icon) icon.textContent = '☀️';
                if (label) label.textContent = 'Clean Mode';
            }

            // Sync Alur Sistem Flowmap graphic with theme
            const flowmapImg = document.getElementById('flowmapImage');
            const flowmapModalImg = document.getElementById('flowmapModalImage');
            const flowmapSrc = "{{ asset('images/alur-sistem-bg-clean-hd.jpg') }}?v={{ time() }}";
            if (flowmapImg) flowmapImg.src = flowmapSrc;
            if (flowmapModalImg) flowmapModalImg.src = flowmapSrc;

            // Sync Kontak mobile banner with theme
            const kontakMobileBanner = document.getElementById('kontakMobileBanner');
            if (kontakMobileBanner) {
                kontakMobileBanner.src = (theme === 'clean')
                    ? "{{ asset('images/kontak-port-santos-bg-clean.jpg') }}"
                    : "{{ asset('images/kontak-port-santos-bg.jpg') }}";
            }
            localStorage.setItem('cargotrace_landing_theme', theme);
            lucide.createIcons();
        }

        function toggleTheme() {
            const isClean = document.documentElement.classList.contains('theme-clean');
            applyTheme(isClean ? 'dark' : 'clean');
        }

        // Initialize Theme from URL query or localStorage or Default to Dark
        (function() {
            const urlParams = new URLSearchParams(window.location.search);
            const queryTheme = urlParams.get('theme');
            const savedTheme = queryTheme || localStorage.getItem('cargotrace_landing_theme') || 'dark';
            applyTheme(savedTheme);
        })();

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobileMenuBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }

        // Flowmap Lightbox Modal Functions
        function openFlowmapModal() {
            const modal = document.getElementById('flowmapModal');
            if (modal) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeFlowmapModal() {
            const modal = document.getElementById('flowmapModal');
            if (modal) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Close on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeFlowmapModal();
        });

        // Quick Track Interactive Handler
        function handleQuickTrack(e) {
            e.preventDefault();
            const resi = document.getElementById('quickTrackInput').value.trim();
            if (!resi) return;
            window.location.href = "{{ route('login') }}?tracking=" + encodeURIComponent(resi);
        }

        // Inquiry Form Interactive Handler
        function handleInquirySubmit(e) {
            e.preventDefault();
            const toast = document.getElementById('inquirySuccessToast');
            if (toast) {
                toast.classList.remove('hidden');
                e.target.reset();
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 8000);
            }
        }

        // Active Navigation Highlighter & Floating Navbar on Scroll
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (navbar) {
                if (window.scrollY > 30) {
                    navbar.classList.add('is-scrolled');
                } else {
                    navbar.classList.remove('is-scrolled');
                }
            }

            const sections = document.querySelectorAll('section[id]');
            const scrollY = window.pageYOffset;
            const navLinks = document.querySelectorAll('.nav-link');

            sections.forEach(current => {
                const sectionHeight = current.offsetHeight;
                const sectionTop = current.offsetTop - 120;
                const sectionId = current.getAttribute('id');

                if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    navLinks.forEach(link => {
                        if (link.getAttribute('href') === '#' + sectionId) {
                            link.style.background = 'var(--nav-active-bg)';
                            link.style.color = 'var(--nav-active-text)';
                            link.classList.add('font-bold');
                        } else {
                            link.style.background = 'transparent';
                            link.style.color = 'var(--nav-link)';
                            link.classList.remove('font-bold');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
