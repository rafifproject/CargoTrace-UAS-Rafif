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
            
            --hero-bg-img: url('{{ asset('images/hero-bg-dark.jpg') }}');
            --hero-overlay-start: rgba(6, 12, 30, 0.85);
            --hero-overlay-mid: rgba(6, 12, 30, 0.40);
            
            --tentang-bg-img: url('{{ asset('images/tentang-kami-bg-dark-v2.jpg') }}?v={{ time() }}');
            --produk-bg-img: url('{{ asset('images/produk-bg-dark-v2.jpg') }}?v={{ time() }}');
            --alur-bg-img: url('{{ asset('images/alur-sistem-bg-dark.jpg') }}');
            
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
            
            --hero-bg-img: url('{{ asset('images/hero-bg-clean.jpg') }}');
            --hero-overlay-start: rgba(248, 250, 252, 0.88);
            --hero-overlay-mid: rgba(248, 250, 252, 0.38);
            
            --tentang-bg-img: url('{{ asset('images/tentang-kami-bg.jpg') }}');
            --produk-bg-img: url('{{ asset('images/produk-bg-clean-v2.jpg') }}?v={{ time() }}');
            --alur-bg-img: url('{{ asset('images/alur-sistem-bg.jpg') }}');
            
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
    {{-- TOP NAVIGATION BAR (Exact replica of user design mockup)                  --}}
    {{-- ========================================================================= --}}
    <header id="navbar" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 backdrop-blur-xl border-b"
            style="background: var(--nav-bg); border-color: var(--nav-border);">
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
                    <a href="#alur-sistem" class="nav-link px-3.5 py-1.5 rounded-full text-xs lg:text-sm font-medium transition hover:opacity-80"
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
            <a href="#alur-sistem" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Alur Sistem') }}</a>
            <a href="#fitur" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Fitur') }}</a>
            <a href="#kontak" class="block px-4 py-2 rounded-lg text-sm" style="color: var(--nav-link)">{{ __('Kontak') }}</a>
        </div>
    </header>


    {{-- ========================================================================= --}}
    {{-- SECTION 1: HERO / BERANDA (PANORAMIC FULL-WIDTH - EXACTLY LIKE IMAGE 3)    --}}
    {{-- ========================================================================= --}}
    <section id="beranda" class="hero-panoramic relative w-full min-h-[92vh] lg:min-h-[98vh] pt-24 pb-12 flex flex-col justify-between overflow-hidden">
        
        {{-- Soft Diffuse Overlay on the left to guarantee 100% text readability --}}
        <div class="absolute inset-0 pointer-events-none z-10"
             style="background: linear-gradient(to right, var(--hero-overlay-start) 0%, var(--hero-overlay-mid) 40%, transparent 68%);">
        </div>

        {{-- MAIN HERO TEXT CONTENT (Left-Aligned, Exactly matching Image 3) --}}
        <div class="relative z-20 w-full px-6 sm:px-10 lg:px-14 xl:px-20 pt-10 sm:pt-16 lg:pt-24 my-auto">
            <div class="w-full lg:max-w-2xl xl:max-w-3xl flex flex-col items-start text-left">
                
                {{-- Eyebrow with Brand Accent Bar --}}
                <div class="flex items-center gap-3 mb-4">
                    <span class="text-xs uppercase tracking-widest font-extrabold" style="color: var(--brand-cyan)">
                        {{ __('GLOBAL SHIPMENT VISIBILITY & TRACKING PLATFORM') }}
                    </span>
                    <div class="h-0.5 w-12" style="background: linear-gradient(to right, var(--brand-cyan), transparent);"></div>
                </div>

                {{-- Main Headline (2 Lines, Clean, Bold Typography matching Image 2/3) --}}
                <h1 class="font-outfit text-4xl sm:text-5xl lg:text-[3.5rem] xl:text-[4rem] font-extrabold tracking-tight leading-[1.12] mb-6"
                    style="color: var(--text-heading)">
                    {{ __('Ekspor Kopi Spesialti Brasil') }} <br>
                    {{ __('ke') }} <span style="color: var(--brand-cyan)" class="drop-shadow-[0_0_25px_var(--brand-glow)]">{{ __('Pasar Dunia') }}</span>
                </h1>

                {{-- Descriptive Paragraph --}}
                <p class="text-base sm:text-lg leading-relaxed mb-8 max-w-xl font-normal" style="color: var(--text-body)">
                    {{ __('CargoTrace adalah platform terpadu untuk pemesanan, pelacakan, dan manajemen pengiriman kopi spesialti dari Pelabuhan Santos, Brasil secara transparan dan real-time.') }}
                </p>

                {{-- CTA Buttons: Primary Pill + Secondary Play Pill --}}
                <div class="flex flex-wrap items-center gap-4">
                    <a href="{{ route('login') }}" 
                       class="inline-flex items-center gap-2.5 px-7 py-3.5 rounded-full font-bold text-sm transition transform hover:-translate-y-0.5 shadow-lg"
                       style="background: var(--btn-primary-bg); color: var(--btn-primary-text);">
                        <span>{{ __('Pesan Kargo Sekarang') }}</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>

                    <a href="#alur-sistem" 
                       class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full font-medium text-sm transition backdrop-blur-md hover:scale-105"
                       style="background: var(--btn-secondary-bg); border: 1px solid var(--btn-secondary-border); color: var(--btn-secondary-text);">
                        <i data-lucide="play-circle" class="w-5 h-5" style="color: var(--brand-cyan)"></i>
                        <span>{{ __('Lihat Cara Kerja') }}</span>
                    </a>
                </div>

            </div>
        </div>

        {{-- Bottom spacing --}}
        <div class="relative z-20 pb-8 sm:pb-12 lg:pb-16"></div>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 2: TENTANG KAMI (Santos Exporters S.A. Story & Global Hub)       --}}
    {{-- ========================================================================= --}}
    <section id="tentang-kami" class="py-16 sm:py-20 lg:py-24 relative overflow-hidden"
             style="background-image: var(--tentang-bg-img); background-size: cover; background-position: top right; background-repeat: no-repeat;">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- Section Header (Left-aligned, leaving top-right ship illustration unobstructed) --}}
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
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
                
                {{-- Left: Mission, Historical Context & Traceability Card --}}
                <div class="lg:col-span-7">
                    <div class="theme-card rounded-3xl p-6 sm:p-8 flex flex-col justify-between"
                         style="background: var(--card-bg); border: 1px solid var(--card-border);">
                        <div>
                            <div class="flex items-center gap-3.5 mb-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center flex-shrink-0"
                                     style="background: rgba(0, 112, 243, 0.1); border: 1px solid var(--card-border); color: var(--brand-cyan);">
                                    <i data-lucide="layers" class="w-6 h-6"></i>
                                </div>
                                <h3 class="font-outfit text-lg sm:text-xl font-bold leading-snug" style="color: var(--text-heading)">
                                    {{ __('Pusat Ekspor Kopi Global') }} <span style="color: var(--brand-cyan)">{{ __('Terbesar di Dunia') }}</span>
                                </h3>
                            </div>
                            
                            <p class="text-xs sm:text-sm leading-relaxed mb-3.5" style="color: var(--text-body)">
                                {{ __('Pelabuhan Santos (Porto de Santos), yang terletak di negara bagian São Paulo, Brasil, merupakan pintu gerbang ekspor maritim bagi lebih dari 80% kopi Brasil ke pasar dunia. Berdiri di jantung ekosistem logistik maritim ini, Santos Exporters S.A. mengintegrasikan perkebunan kopi terbaik di Minas Gerais, Sul de Minas, dan Mogiana langsung ke kapal kontainer laut.') }}
                            </p>
                            
                            <p class="text-xs sm:text-sm leading-relaxed mb-6" style="color: var(--text-body)">
                                {{ __('Melalui platform CargoTrace, kami menghilangkan ketidakpastian pengiriman laut: setiap kontainer kopi dipantau kelembapannya, dilengkapi sertifikasi Specialty Coffee Association (SCA), dan dapat dilacak pergerakan AIS kapal secara transparan dari dermaga muat hingga tiba di pelabuhan tujuan pembeli.') }}
                            </p>
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

                {{-- Right: 4 Metric Cards (Horizontal Flex Layout matching Mockup) --}}
                <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    
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

        {{-- Flowing Ocean Wave at bottom of Tentang Kami --}}
        <div class="w-full overflow-hidden leading-none pt-12 sm:pt-16 pointer-events-none">
            <svg viewBox="0 0 1440 70" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-8 sm:h-12" preserveAspectRatio="none">
                <path d="M0,35 C320,65 640,5 960,40 C1200,60 1360,25 1440,35 L1440,70 L0,70 Z" fill="var(--wave-2)" opacity="0.3"/>
                <path d="M0,45 C280,15 600,65 920,30 C1180,50 1340,20 1440,45 L1440,70 L0,70 Z" fill="var(--wave-1)" opacity="0.6"/>
            </svg>
        </div>
    </section>

    {{-- ========================================================================= --}}
    {{-- SECTION 3: PRODUK KOPI SPESIALTI BRASIL (6 Authentic Database Varieties) --}}
    {{-- ========================================================================= --}}
    <section id="produk" class="py-8 sm:py-12 lg:py-14 relative overflow-hidden flex flex-col justify-between"
             style="background-image: var(--produk-bg-img); background-size: cover; background-position: center top; background-repeat: no-repeat;">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            
            {{-- Section Header (Left-aligned, leaving top-right ship illustration unobstructed) --}}
            <div class="w-full lg:max-w-xl xl:max-w-2xl text-left mb-6 sm:mb-8">
                <div class="flex items-center gap-2.5 mb-2.5">
                    <div class="h-1 w-8 rounded-full" style="background: var(--brand-cyan)"></div>
                    <span class="text-xs sm:text-sm font-bold tracking-wider uppercase" style="color: var(--text-heading)">
                        {{ __('KATALOG KOMODITAS EKSPOR') }}
                    </span>
                </div>
                
                <h2 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight leading-[1.15] mb-3 sm:mb-4"
                    style="color: var(--text-heading)">
                    {{ __('Pilihan Kopi Spesialti') }} <span style="color: var(--brand-cyan)">{{ __('Brasil Terbaik') }}</span>
                </h2>
                
                <p class="text-xs sm:text-sm lg:text-base leading-relaxed font-normal" style="color: var(--text-body)">
                    {{ __('Dipanen langsung dari perkebunan dataran tinggi terbaik Brasil, disortir dengan ketat sesuai standar Specialty Coffee Association (SCA) sebelum diberangkatkan dari Pelabuhan Santos.') }}
                </p>
            </div>

            {{-- 6 Coffee Varieties Grid (3 Columns on Desktop, 2 on Tablet, 1 on Mobile) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3.5 sm:gap-4 lg:gap-5 pb-4">
                
                {{-- Product 1: Brazil Santos Bourbon Specialty --}}
                <div class="theme-card rounded-3xl overflow-hidden flex flex-row items-stretch border transition hover:-translate-y-1 hover:shadow-xl"
                     style="background: var(--card-bg); border-color: var(--card-border);">
                    <div class="w-24 sm:w-28 md:w-32 flex-shrink-0 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/products/coffee-1.png') }}" alt="Kopi Brazil Santos Bourbon Specialty" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 p-3.5 sm:p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight shadow-sm"
                                      style="background: rgba(245, 158, 11, 0.15); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.3);">
                                    SCA Score 86.5
                                </span>
                                <span class="text-[11px] flex items-center gap-1 font-medium" style="color: var(--text-muted)">
                                    <i data-lucide="mountain" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                    1,200 m dpl
                                </span>
                            </div>
                            <h3 class="font-outfit text-sm sm:text-base font-extrabold leading-tight mb-1" style="color: var(--text-heading)">
                                Kopi Brazil Santos Bourbon Specialty
                            </h3>
                            <p class="text-[11px] font-medium mb-2" style="color: var(--text-muted)">
                                Sul de Minas, Brasil • Natural Sun-Dried Process
                            </p>
                            <div class="flex items-start gap-1.5 text-[11px] leading-snug mb-3" style="color: var(--text-body)">
                                <i data-lucide="coffee" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-[#0070F3]"></i>
                                <span>Karamel, Cokelat Susu, Hazelnut, Body Lembut</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t" style="border-color: var(--nav-border)">
                            <div class="flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                                <i data-lucide="package" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                <span>Kemasan: 60kg GrainPro</span>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition hover:scale-105 shadow-sm"
                               style="background: #0070F3; color: #FFFFFF;">
                                <span>{{ __('Pesan Kargo') }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Product 2: Brazil Santos Yellow Catuai Natural --}}
                <div class="theme-card rounded-3xl overflow-hidden flex flex-row items-stretch border transition hover:-translate-y-1 hover:shadow-xl"
                     style="background: var(--card-bg); border-color: var(--card-border);">
                    <div class="w-24 sm:w-28 md:w-32 flex-shrink-0 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/products/coffee-2.png') }}" alt="Brazil Santos Yellow Catuai Natural" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 p-3.5 sm:p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight shadow-sm"
                                      style="background: rgba(0, 112, 243, 0.15); color: #0070F3; border: 1px solid rgba(0, 112, 243, 0.3);">
                                    SCA Score 87.5 • Micro-lot
                                </span>
                                <span class="text-[11px] flex items-center gap-1 font-medium" style="color: var(--text-muted)">
                                    <i data-lucide="mountain" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                    1,250 m dpl
                                </span>
                            </div>
                            <h3 class="font-outfit text-sm sm:text-base font-extrabold leading-tight mb-1" style="color: var(--text-heading)">
                                Brazil Santos Yellow Catuai Natural
                            </h3>
                            <p class="text-[11px] font-medium mb-2" style="color: var(--text-muted)">
                                Carmo de Minas, Brasil • Anaerobic Natural
                            </p>
                            <div class="flex items-start gap-1.5 text-[11px] leading-snug mb-3" style="color: var(--text-body)">
                                <i data-lucide="coffee" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-[#0070F3]"></i>
                                <span>Madu Liar, Markisa, Bunga Tropis, Gula Tebu</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t" style="border-color: var(--nav-border)">
                            <div class="flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                                <i data-lucide="package" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                <span>Kemasan: 30kg Vakum Box</span>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition hover:scale-105 shadow-sm"
                               style="background: #0070F3; color: #FFFFFF;">
                                <span>{{ __('Pesan Kargo') }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Product 3: Brazil Santos Cerrado Mineiro --}}
                <div class="theme-card rounded-3xl overflow-hidden flex flex-row items-stretch border transition hover:-translate-y-1 hover:shadow-xl"
                     style="background: var(--card-bg); border-color: var(--card-border);">
                    <div class="w-24 sm:w-28 md:w-32 flex-shrink-0 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/products/coffee-3.png') }}" alt="Brazil Santos Cerrado Mineiro" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 p-3.5 sm:p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight shadow-sm"
                                      style="background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3);">
                                    SCA Score 85.0 • D.O. Protected
                                </span>
                                <span class="text-[11px] flex items-center gap-1 font-medium" style="color: var(--text-muted)">
                                    <i data-lucide="mountain" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                    1,050 m dpl
                                </span>
                            </div>
                            <h3 class="font-outfit text-sm sm:text-base font-extrabold leading-tight mb-1" style="color: var(--text-heading)">
                                Brazil Santos Cerrado Mineiro
                            </h3>
                            <p class="text-[11px] font-medium mb-2" style="color: var(--text-muted)">
                                Patrocinio, Cerrado Mineiro • Pulped Natural
                            </p>
                            <div class="flex items-start gap-1.5 text-[11px] leading-snug mb-3" style="color: var(--text-body)">
                                <i data-lucide="coffee" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-[#0070F3]"></i>
                                <span>Kakao Hitam, Kacang Almond, Body Tebal, Low Acid</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t" style="border-color: var(--nav-border)">
                            <div class="flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                                <i data-lucide="package" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                <span>Kemasan: Kontainer 20ft / FCL</span>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition hover:scale-105 shadow-sm"
                               style="background: #0070F3; color: #FFFFFF;">
                                <span>{{ __('Pesan Kargo') }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Product 4: Santos Strictly Soft Fine Cup (SSFC) --}}
                <div class="theme-card rounded-3xl overflow-hidden flex flex-row items-stretch border transition hover:-translate-y-1 hover:shadow-xl"
                     style="background: var(--card-bg); border-color: var(--card-border);">
                    <div class="w-24 sm:w-28 md:w-32 flex-shrink-0 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/products/coffee-4.png') }}" alt="Santos Strictly Soft Fine Cup (SSFC)" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 p-3.5 sm:p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight shadow-sm"
                                      style="background: rgba(139, 92, 246, 0.15); color: #8B5CF6; border: 1px solid rgba(139, 92, 246, 0.3);">
                                    SCA Score 84.0 • NY 2/3 SSFC
                                </span>
                                <span class="text-[11px] flex items-center gap-1 font-medium" style="color: var(--text-muted)">
                                    <i data-lucide="mountain" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                    1,000 m dpl
                                </span>
                            </div>
                            <h3 class="font-outfit text-sm sm:text-base font-extrabold leading-tight mb-1" style="color: var(--text-heading)">
                                Santos Strictly Soft Fine Cup (SSFC)
                            </h3>
                            <p class="text-[11px] font-medium mb-2" style="color: var(--text-muted)">
                                Mogiana Paulista • Natural Sun-Dried
                            </p>
                            <div class="flex items-start gap-1.5 text-[11px] leading-snug mb-3" style="color: var(--text-body)">
                                <i data-lucide="coffee" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-[#0070F3]"></i>
                                <span>Bersih & Seimbang, Kacang Manis, Halus</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t" style="border-color: var(--nav-border)">
                            <div class="flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                                <i data-lucide="package" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                <span>Kemasan: Karung Goni 60kg</span>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition hover:scale-105 shadow-sm"
                               style="background: #0070F3; color: #FFFFFF;">
                                <span>{{ __('Pesan Kargo') }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Product 5: Santos Decaf Swiss Water Process --}}
                <div class="theme-card rounded-3xl overflow-hidden flex flex-row items-stretch border transition hover:-translate-y-1 hover:shadow-xl"
                     style="background: var(--card-bg); border-color: var(--card-border);">
                    <div class="w-24 sm:w-28 md:w-32 flex-shrink-0 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/products/coffee-5.png') }}" alt="Santos Decaf Swiss Water Process" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 p-3.5 sm:p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight shadow-sm"
                                      style="background: rgba(2, 132, 199, 0.15); color: #0284C7; border: 1px solid rgba(2, 132, 199, 0.3);">
                                    SCA Score 83.5 • 99.9% Bebas Kafein
                                </span>
                                <span class="text-[11px] flex items-center gap-1 font-medium" style="color: var(--text-muted)">
                                    <i data-lucide="mountain" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                    1,100 m dpl
                                </span>
                            </div>
                            <h3 class="font-outfit text-sm sm:text-base font-extrabold leading-tight mb-1" style="color: var(--text-heading)">
                                Santos Decaf Swiss Water Process
                            </h3>
                            <p class="text-[11px] font-medium mb-2" style="color: var(--text-muted)">
                                Minas Gerais • 100% Chemical-Free Decaf
                            </p>
                            <div class="flex items-start gap-1.5 text-[11px] leading-snug mb-3" style="color: var(--text-body)">
                                <i data-lucide="coffee" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-[#0070F3]"></i>
                                <span>Tanpa Kimia, Kakao Lembut, Walnut</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t" style="border-color: var(--nav-border)">
                            <div class="flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                                <i data-lucide="package" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                <span>Kemasan: Multi-Barrier 60kg</span>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition hover:scale-105 shadow-sm"
                               style="background: #0070F3; color: #FFFFFF;">
                                <span>{{ __('Pesan Kargo') }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Product 6: Santos Espresso Master Blend --}}
                <div class="theme-card rounded-3xl overflow-hidden flex flex-row items-stretch border transition hover:-translate-y-1 hover:shadow-xl"
                     style="background: var(--card-bg); border-color: var(--card-border);">
                    <div class="w-24 sm:w-28 md:w-32 flex-shrink-0 relative overflow-hidden flex items-center justify-center">
                        <img src="{{ asset('images/products/coffee-6.png') }}" alt="Santos Espresso Master Blend" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 p-3.5 sm:p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between gap-1 mb-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-tight shadow-sm"
                                      style="background: rgba(239, 68, 68, 0.15); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.3);">
                                    SCA Score 85.5 • Master Blend
                                </span>
                                <span class="text-[11px] flex items-center gap-1 font-medium" style="color: var(--text-muted)">
                                    <i data-lucide="mountain" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                    1,150 m dpl
                                </span>
                            </div>
                            <h3 class="font-outfit text-sm sm:text-base font-extrabold leading-tight mb-1" style="color: var(--text-heading)">
                                Santos Espresso Master Blend
                            </h3>
                            <p class="text-[11px] font-medium mb-2" style="color: var(--text-muted)">
                                Santos Select Blend • Full City Roast Ready
                            </p>
                            <div class="flex items-start gap-1.5 text-[11px] leading-snug mb-3" style="color: var(--text-body)">
                                <i data-lucide="coffee" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5 text-[#0070F3]"></i>
                                <span>Crema Tebal, Dark Cocoa, Molase</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-2 border-t" style="border-color: var(--nav-border)">
                            <div class="flex items-center gap-1.5 text-[11px]" style="color: var(--text-muted)">
                                <i data-lucide="package" class="w-3.5 h-3.5 text-[#0070F3]"></i>
                                <span>Kemasan: Karung Foil 60kg</span>
                            </div>
                            <a href="{{ route('login') }}" 
                               class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold transition hover:scale-105 shadow-sm"
                               style="background: #0070F3; color: #FFFFFF;">
                                <span>{{ __('Pesan Kargo') }}</span>
                                <i data-lucide="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 4: ALUR SISTEM (5-Stage Swimlane Flowmap Showcase)                --}}
    {{-- ========================================================================= --}}
    <section id="alur-sistem" class="relative w-full overflow-hidden" style="background: var(--bg-alt);">
        <div class="w-full max-w-[1500px] mx-auto px-2 sm:px-4 lg:px-6 py-6 lg:py-10">
            <div class="w-full overflow-x-auto pb-4 custom-scrollbar">
                <div class="relative w-full min-w-[1000px] aspect-[1024/576] rounded-2xl lg:rounded-3xl overflow-hidden shadow-2xl border border-slate-200/60 dark:border-cyan-500/20"
                     style="container-type: inline-size;">
                    
                    {{-- HD Background Image --}}
                    <img id="flowmapImage"
                         src="{{ asset('images/alur-sistem-bg-clean-hd.jpg') }}?v={{ time() }}" 
                         alt="Alur Kerja Pengiriman Kargo (5 Tahap Utama) - CargoTrace" 
                         class="absolute inset-0 w-full h-full object-cover select-none pointer-events-none">

                    {{-- Vector HD Typography Overlay (Razor-Sharp typography aligned with user background) --}}
                    <div class="absolute inset-0 w-full h-full pointer-events-none select-none font-sans">
                        
                        {{-- 1. Main Header Title (Top Left Sky) --}}
                        <div class="absolute" style="left: 4.5%; top: 8.0%; width: 44%;">
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[0.82cqw] font-bold tracking-wider uppercase mb-1.5 shadow-sm alur-badge border border-current/20">
                                <i data-lucide="activity" class="w-[1.1cqw] h-[1.1cqw]"></i>
                                <span>{{ __('FLOWMAP SISTEM TERINTEGRASI') }}</span>
                            </div>
                            <h2 class="font-outfit text-[2.2cqw] font-extrabold tracking-tight leading-tight alur-title mb-1">
                                {{ __('Alur Kerja Pengiriman Kargo') }}
                            </h2>
                            <div class="font-outfit text-[1.4cqw] font-bold alur-subtitle mb-1.5">
                                {{ __('(5 Tahap Utama)') }}
                            </div>
                            <p class="text-[0.92cqw] leading-relaxed alur-body max-w-[95%]">
                                {{ __('Diagram alur swimlane yang menghubungkan peran Customer (Buyer), Logistics Operator, Sistem Mesin Otomatis, dan Administrator secara harmonis.') }}
                            </p>
                        </div>

                        {{-- 2. The 5 Steps on the 3D Wavy Path (Next to Circles) --}}
                        {{-- Step 1 --}}
                        <div class="absolute flex items-center justify-start pl-1" style="left: 13.5%; top: 38.6%; width: 10.5%; height: 4.5%;">
                            <span class="text-[0.78cqw] font-extrabold alur-title leading-tight tracking-tight">
                                {{ __('Pemesanan Kargo') }}
                            </span>
                        </div>
                        {{-- Step 2 --}}
                        <div class="absolute flex items-center justify-start pl-1" style="left: 30.2%; top: 36.0%; width: 12.0%; height: 4.5%;">
                            <span class="text-[0.74cqw] font-extrabold alur-title leading-tight tracking-tight">
                                {{ __('Validasi & Pengurusan Ekspedisi') }}
                            </span>
                        </div>
                        {{-- Step 3 --}}
                        <div class="absolute flex items-center justify-start pl-1" style="left: 50.2%; top: 38.6%; width: 10.5%; height: 4.5%;">
                            <span class="text-[0.78cqw] font-extrabold alur-title leading-tight tracking-tight">
                                {{ __('Pelacakan Perjalanan') }}
                            </span>
                        </div>
                        {{-- Step 4 --}}
                        <div class="absolute flex items-center justify-start pl-1" style="left: 68.2%; top: 41.0%; width: 12.0%; height: 4.5%;">
                            <span class="text-[0.72cqw] font-extrabold alur-title leading-tight tracking-tight">
                                {{ __('Deteksi Keterlambatan & Exception') }}
                            </span>
                        </div>
                        {{-- Step 5 --}}
                        <div class="absolute flex items-center justify-start pl-1" style="left: 86.2%; top: 33.6%; width: 10.5%; height: 4.5%;">
                            <span class="text-[0.78cqw] font-extrabold alur-title leading-tight tracking-tight">
                                {{ __('Penyelesaian & Analitik') }}
                            </span>
                        </div>

                        {{-- 3. Bottom-Left Card: Tujuan Flowmap --}}
                        <div class="absolute flex items-center justify-center" style="left: 4.7%; top: 62.0%; width: 16.1%; height: 4.0%;">
                            <span class="text-white text-[0.85cqw] font-bold tracking-wide">{{ __('Tujuan Flowmap') }}</span>
                        </div>
                        <div class="absolute flex items-center justify-center px-2 text-center" style="left: 4.0%; top: 67.5%; width: 17.5%; height: 13.0%;">
                            <p class="text-[0.76cqw] leading-relaxed alur-body font-medium">
                                {{ __('Menggambarkan alur kerja sistem CargoTrace secara menyeluruh, mulai dari pemesanan hingga analitik eksekusi, dengan keterlibatan multi hak akses.') }}
                            </p>
                        </div>

                        {{-- 4. Bottom-Center Card: Aktor / Hak Akses (4 Columns) --}}
                        <div class="absolute flex items-center justify-center" style="left: 27.2%; top: 63.0%; width: 16.0%; height: 4.0%;">
                            <span class="text-white text-[0.85cqw] font-bold tracking-wide">{{ __('Aktor / Hak Akses') }}</span>
                        </div>
                        {{-- Actor 1: Customer --}}
                        <div class="absolute flex flex-col justify-center" style="left: 31.0%; top: 68.5%; width: 9.8%; height: 11.5%;">
                            <div class="text-[0.78cqw] font-extrabold alur-title leading-tight mb-1">{{ __('Customer (Buyer)') }}</div>
                            <div class="text-[0.66cqw] alur-body leading-snug">{{ __('Memesan kargo, melihat status, terima notifikasi.') }}</div>
                        </div>
                        {{-- Actor 2: Logistics Operator --}}
                        <div class="absolute flex flex-col justify-center" style="left: 44.5%; top: 68.5%; width: 9.8%; height: 11.5%;">
                            <div class="text-[0.78cqw] font-extrabold alur-title leading-tight mb-1">{{ __('Logistics Operator') }}</div>
                            <div class="text-[0.66cqw] alur-body leading-snug">{{ __('Mengelola ekspedisi, dokumen, dan tracking.') }}</div>
                        </div>
                        {{-- Actor 3: Sistem Otomatis --}}
                        <div class="absolute flex flex-col justify-center" style="left: 57.2%; top: 68.5%; width: 9.8%; height: 11.5%;">
                            <div class="text-[0.78cqw] font-extrabold alur-title leading-tight mb-1">{{ __('Sistem (Otomatis)') }}</div>
                            <div class="text-[0.66cqw] alur-body leading-snug">{{ __('Memproses data, melacak pengiriman, notifikasi.') }}</div>
                        </div>
                        {{-- Actor 4: Administrator --}}
                        <div class="absolute flex flex-col justify-center" style="left: 71.0%; top: 68.5%; width: 9.8%; height: 11.5%;">
                            <div class="text-[0.78cqw] font-extrabold alur-title leading-tight mb-1">{{ __('Administrator') }}</div>
                            <div class="text-[0.66cqw] alur-body leading-snug">{{ __('Memantau kinerja, sistem, validasi data, & akses.') }}</div>
                        </div>

                        {{-- 5. Bottom-Right Card: Legenda Simbol --}}
                        <div class="absolute flex items-center justify-center" style="left: 82.2%; top: 62.0%; width: 14.5%; height: 4.0%;">
                            <span class="text-white text-[0.85cqw] font-bold tracking-wide">{{ __('Legenda Simbol') }}</span>
                        </div>
                        <div class="absolute flex items-center" style="left: 87.2%; top: 67.2%; width: 9.8%; height: 2.6%;">
                            <span class="text-[0.72cqw] font-semibold alur-title">{{ __('Mulai / Selesai') }}</span>
                        </div>
                        <div class="absolute flex items-center" style="left: 87.2%; top: 70.4%; width: 9.8%; height: 2.6%;">
                            <span class="text-[0.72cqw] font-semibold alur-title">{{ __('Proses / Aktivitas') }}</span>
                        </div>
                        <div class="absolute flex items-center" style="left: 87.2%; top: 73.6%; width: 9.8%; height: 2.6%;">
                            <span class="text-[0.72cqw] font-semibold alur-title">{{ __('Keputusan') }}</span>
                        </div>
                        <div class="absolute flex items-center" style="left: 87.2%; top: 76.8%; width: 9.8%; height: 2.6%;">
                            <span class="text-[0.72cqw] font-semibold alur-title">{{ __('Data / Dokumen') }}</span>
                        </div>
                        <div class="absolute flex items-center" style="left: 87.2%; top: 80.0%; width: 9.8%; height: 2.6%;">
                            <span class="text-[0.72cqw] font-semibold alur-title">{{ __('Alur Proses') }}</span>
                        </div>

                        {{-- 6. Bottom Ribbon: Hasil / Output Sistem --}}
                        <div class="absolute flex items-center pl-14" style="left: 3.0%; top: 86.8%; width: 17.5%; height: 10.0%;">
                            <span class="text-[0.90cqw] font-extrabold text-white tracking-wide leading-tight">{{ __('Hasil / Output Sistem') }}</span>
                        </div>
                        {{-- Box 1 --}}
                        <div class="absolute flex items-center" style="left: 28.5%; top: 87.5%; width: 10.5%; height: 8.5%;">
                            <span class="text-[0.74cqw] font-semibold alur-title leading-snug">{{ __('Order kargo tercatat & tervalidasi') }}</span>
                        </div>
                        {{-- Box 2 --}}
                        <div class="absolute flex items-center" style="left: 42.5%; top: 87.5%; width: 10.5%; height: 8.5%;">
                            <span class="text-[0.74cqw] font-semibold alur-title leading-snug">{{ __('Rute & pelacakan real-time di peta') }}</span>
                        </div>
                        {{-- Box 3 --}}
                        <div class="absolute flex items-center" style="left: 56.5%; top: 87.5%; width: 10.5%; height: 8.5%;">
                            <span class="text-[0.74cqw] font-semibold alur-title leading-snug">{{ __('Notifikasi otomatis jika ada kendala') }}</span>
                        </div>
                        {{-- Box 4 --}}
                        <div class="absolute flex items-center" style="left: 71.0%; top: 87.5%; width: 10.5%; height: 8.5%;">
                            <span class="text-[0.74cqw] font-semibold alur-title leading-snug">{{ __('Status pengiriman hingga Delivered') }}</span>
                        </div>
                        {{-- Box 5 --}}
                        <div class="absolute flex items-center" style="left: 85.5%; top: 87.5%; width: 10.5%; height: 8.5%;">
                            <span class="text-[0.74cqw] font-semibold alur-title leading-snug">{{ __('Data analitik bisnis untuk keputusan') }}</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 5: FITUR UNGGULAN PLATFORM                                        --}}
    {{-- ========================================================================= --}}
    <section id="fitur" class="py-20 lg:py-28 relative" style="background: var(--bg-page)">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase mb-3 shadow-sm"
                     style="background: var(--brand-glow); color: var(--brand-cyan); border: 1px solid var(--card-border)">
                    <i data-lucide="cpu" class="w-3.5 h-3.5"></i>
                    <span>{{ __('ARSITEKTUR & FITUR UNGGULAN') }}</span>
                </div>
                <h2 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4"
                    style="color: var(--text-heading)">
                    {{ __('Dibangun untuk Kompleksitas Logistik Kopi Maritim') }}
                </h2>
                <p class="text-base sm:text-lg leading-relaxed font-normal" style="color: var(--text-body)">
                    {{ __('Integrasi fitur canggih yang memberikan keunggulan operasional, keandalan analitik bisnis, dan transparansi penuh bagi seluruh pemangku kepentingan.') }}
                </p>
            </div>

            {{-- 4 Core Architectural Features Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                {{-- Feature 1: Live Maritime Geolocation Tracking --}}
                <div class="theme-card rounded-3xl p-8 relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 shadow-md"
                         style="background: var(--brand-glow); color: var(--brand-cyan); border: 1px solid var(--card-border)">
                        <i data-lucide="compass" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-outfit text-2xl font-bold mb-3" style="color: var(--text-heading)">
                        Pelacakan Geolokasi Maritim Interaktif (Leaflet)
                    </h3>
                    <p class="text-sm sm:text-base leading-relaxed mb-6" style="color: var(--text-body)">
                        Peta maritim digital berskala global yang memproyeksikan lintasan pelayaran kapal kontainer kopi dari Dermaga Santos ke berbagai pelabuhan tujuan dunia, lengkap dengan interpolasi koordinat GPS dan pembaruan checkpoint otomatis.
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm" style="color: var(--text-body)">
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Visualisasi rute Great Circle pelayaran samudra Atlantik & Pasifik</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Estimasi waktu kedatangan (ETA) adaptif berdasarkan kecepatan kapal laut</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Riwayat checkpoint pelabuhan transit yang tercatat secara kronologis</span>
                        </li>
                    </ul>
                </div>

                {{-- Feature 2: Automated Delay & Exception Engine --}}
                <div class="theme-card rounded-3xl p-8 relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 shadow-md"
                         style="background: rgba(244, 63, 94, 0.15); color: #F43F5E; border: 1px solid rgba(244, 63, 94, 0.3)">
                        <i data-lucide="alert-triangle" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-outfit text-2xl font-bold mb-3" style="color: var(--text-heading)">
                        Deteksi Keterlambatan & Exception Otomatis
                    </h3>
                    <p class="text-sm sm:text-base leading-relaxed mb-6" style="color: var(--text-body)">
                        Engine cerdas yang secara deterministik membandingkan estimasi kedatangan dengan ambang batas (delay threshold). Jika badai samudra atau kendala kepabeanan terdeteksi, status dialihkan ke EXCEPTION dan alert langsung dikirim.
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm" style="color: var(--text-body)">
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-rose-500"></i>
                            <span>Klasifikasi otomatis kendala: Cuaca Buruk, Masalah Dokumen, atau Kerusakan Mesin</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-rose-500"></i>
                            <span>Notifikasi push seketika kepada pembeli kargo dan operator logistik</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-rose-500"></i>
                            <span>Pencatatan tindakan resolusi kendala dan pemulihan jadwal kontainer</span>
                        </li>
                    </ul>
                </div>

                {{-- Feature 3: Strict RBAC & Anti-IDOR Security --}}
                <div class="theme-card rounded-3xl p-8 relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 shadow-md"
                         style="background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3)">
                        <i data-lucide="shield-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-outfit text-2xl font-bold mb-3" style="color: var(--text-heading)">
                        Keamanan Berlapis (RBAC & Anti-IDOR)
                    </h3>
                    <p class="text-sm sm:text-base leading-relaxed mb-6" style="color: var(--text-body)">
                        Pemisahan hak akses yang ketat antara 3 peran: Administrator, Logistics Operator, dan Customer. Dilindungi Policy Laravel anti-IDOR yang memastikan buyer hanya dapat mengakses data kargo miliknya sendiri.
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm" style="color: var(--text-body)">
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Audit logging abadi yang mencatat IP, User Agent, dan stempel waktu aktivitas</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Proteksi manipulasi resi atau pergantian status kargo di luar kewenangan</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-500"></i>
                            <span>Enkripsi kata sandi mutakhir dan proteksi brute-force login</span>
                        </li>
                    </ul>
                </div>

                {{-- Feature 4: Executive Business Intelligence & Analytics --}}
                <div class="theme-card rounded-3xl p-8 relative overflow-hidden">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 shadow-md"
                         style="background: rgba(245, 158, 11, 0.15); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.3)">
                        <i data-lucide="bar-chart-3" class="w-7 h-7"></i>
                    </div>
                    <h3 class="font-outfit text-2xl font-bold mb-3" style="color: var(--text-heading)">
                        Dasbor Analitik Bisnis & Telemetri Kargo
                    </h3>
                    <p class="text-sm sm:text-base leading-relaxed mb-6" style="color: var(--text-body)">
                        Pusat telemetri bisnis komprehensif bagi Administrator: menghitung total omzet (Gross Merchandise Value USD), grafik negara pengimpor terbesar, komoditas kopi terlaris, dan rasio pemenuhan SLA ketepatan waktu.
                    </p>
                    <ul class="space-y-2.5 text-xs sm:text-sm" style="color: var(--text-body)">
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-amber-500"></i>
                            <span>Grafik interaktif Chart.js pendapatan bulanan dan tren rute pelabuhan</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-amber-500"></i>
                            <span>Peringkat 5 negara pembeli kopi Santos terbanyak secara real-time</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <i data-lucide="check" class="w-4 h-4 text-amber-500"></i>
                            <span>Ekspor data pelacakan dan analitik untuk keperluan audit eksternal</span>
                        </li>
                    </ul>
                </div>

            </div>

        </div>
    </section>


    {{-- ========================================================================= --}}
    {{-- SECTION 6: KONTAK & QUICK TRACK (Santos HQ & Direct Inquiry)              --}}
    {{-- ========================================================================= --}}
    <section id="kontak" class="py-20 lg:py-28 relative border-t"
             style="background: var(--bg-alt); border-color: var(--nav-border)">
        <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Section Header --}}
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-bold tracking-wider uppercase mb-3 shadow-sm"
                     style="background: var(--brand-glow); color: var(--brand-cyan); border: 1px solid var(--card-border)">
                    <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                    <span>{{ __('HUBUNGI KAMI & LACAK CEPAT') }}</span>
                </div>
                <h2 class="font-outfit text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-4"
                    style="color: var(--text-heading)">
                    {{ __('Siap Memulai Pengiriman Kopi Spesialti Anda?') }}
                </h2>
                <p class="text-base sm:text-lg leading-relaxed font-normal" style="color: var(--text-body)">
                    {{ __('Hubungi perwakilan Santos Exporters S.A. di Pelabuhan Santos atau lacak langsung nomor resi kontainer Anda secara instan.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
                
                {{-- LEFT COLUMN: Quick Resi Search & Port Headquarters Info --}}
                <div class="lg:col-span-5 space-y-6">
                    
                    {{-- Quick Resi Tracking Card --}}
                    <div class="theme-card rounded-3xl p-6 sm:p-7 relative overflow-hidden" style="border-color: var(--brand-cyan)">
                        <div class="flex items-center gap-2.5 mb-2">
                            <i data-lucide="search" class="w-5 h-5" style="color: var(--brand-cyan)"></i>
                            <h3 class="font-outfit text-lg font-bold" style="color: var(--text-heading)">Lacak Status Kargo Cepat</h3>
                        </div>
                        <p class="text-xs mb-4" style="color: var(--text-muted)">
                            Punya nomor resi pengiriman? Masukkan kode tracking (misal: <code class="font-mono font-bold" style="color: var(--brand-cyan)">CT-2026-001</code>) untuk cek status langsung:
                        </p>
                        
                        <form id="quickTrackForm" onsubmit="handleQuickTrack(event)" class="space-y-3">
                            <div class="relative">
                                <input type="text" id="quickTrackInput" placeholder="Masukkan nomor resi (CT-2026-001)..." 
                                       value="CT-2026-001"
                                       class="w-full px-4 py-3 rounded-xl text-sm focus:outline-none transition"
                                       style="background: rgba(255,255,255,0.06); border: 1px solid var(--nav-border); color: var(--text-heading)">
                            </div>
                            <button type="submit" class="w-full py-3 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 shadow-md hover:scale-[1.02]"
                                    style="background: var(--btn-primary-bg); color: var(--btn-primary-text)">
                                <i data-lucide="navigation" class="w-4 h-4"></i>
                                <span>{{ __('Lacak Sekarang') }}</span>
                            </button>
                        </form>

                        {{-- Quick Search Result Preview Demo --}}
                        <div id="quickTrackResult" class="mt-4 p-4 rounded-xl text-xs space-y-2"
                             style="background: rgba(255,255,255,0.04); border: 1px solid var(--nav-border)">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold" style="color: var(--brand-cyan)">CT-2026-001</span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-500 font-semibold text-[10px]">DELIVERED</span>
                            </div>
                            <div class="font-medium" style="color: var(--text-heading)">Kopi Brazil Santos Bourbon Specialty</div>
                            <div class="flex items-center justify-between text-[11px] pt-1 border-t" style="border-color: var(--nav-border); color: var(--text-muted)">
                                <span>Santos, Brasil &rarr; Tanjung Priok, ID</span>
                                <span class="text-emerald-500 font-semibold">Tiba di Roastery</span>
                            </div>
                        </div>
                    </div>

                    {{-- Port Headquarters Details --}}
                    <div class="theme-card rounded-3xl p-6 sm:p-7 space-y-4">
                        <h4 class="font-outfit text-base font-bold flex items-center gap-2" style="color: var(--text-heading)">
                            <i data-lucide="map-pin" class="w-4 h-4" style="color: var(--brand-cyan)"></i>
                            <span>Kantor Operasional & Dermaga Santos</span>
                        </h4>
                        <div class="space-y-3 text-xs sm:text-sm" style="color: var(--text-body)">
                            <div>
                                <span class="block text-[11px] uppercase font-bold" style="color: var(--text-muted)">Alamat Terminal:</span>
                                <span>Santos Exporters S.A., Terminal Portuário de Santos, Cais do Saboó, Santos - SP, 11010-140, Brasil</span>
                            </div>
                            <div>
                                <span class="block text-[11px] uppercase font-bold" style="color: var(--text-muted)">Perwakilan Asia Pasifik (Indonesia):</span>
                                <span>Kawasan Logistik Maritim Pelabuhan Tanjung Priok, Jakarta Utara, 14310</span>
                            </div>
                            <div>
                                <span class="block text-[11px] uppercase font-bold" style="color: var(--text-muted)">Kontak Email:</span>
                                <span style="color: var(--brand-cyan)">ops@cargotrace.com / export@santosexporters.br</span>
                            </div>
                            <div>
                                <span class="block text-[11px] uppercase font-bold" style="color: var(--text-muted)">Layanan Telepon Operasional (24/7):</span>
                                <span>+55 (13) 3219-4800 / +62 (21) 5088-7200</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- RIGHT COLUMN: Pre-Order & Inquiry Form --}}
                <div class="lg:col-span-7">
                    <div class="theme-card rounded-3xl p-7 lg:p-9 relative">
                        <div class="mb-6">
                            <h3 class="font-outfit text-2xl font-bold mb-2" style="color: var(--text-heading)">
                                Formulir Konsultasi & Pemesanan Kargo Kopi
                            </h3>
                            <p class="text-xs sm:text-sm" style="color: var(--text-body)">
                                Isi formulir di bawah ini untuk mendapatkan kuotasi resmi pengiriman kontainer kopi FCL/LCL dari Pelabuhan Santos.
                            </p>
                        </div>

                        <form id="inquiryForm" onsubmit="handleInquirySubmit(event)" class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-body)">Nama Lengkap *</label>
                                    <input type="text" required placeholder="Contoh: Budi Pratama" 
                                           class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition"
                                           style="background: rgba(255,255,255,0.06); border: 1px solid var(--nav-border); color: var(--text-heading)">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-body)">Nama Perusahaan / Roastery *</label>
                                    <input type="text" required placeholder="Contoh: Nusantara Specialty Roasters" 
                                           class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition"
                                           style="background: rgba(255,255,255,0.06); border: 1px solid var(--nav-border); color: var(--text-heading)">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-body)">Email Bisnis *</label>
                                    <input type="email" required placeholder="budi@roastery.id" 
                                           class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition"
                                           style="background: rgba(255,255,255,0.06); border: 1px solid var(--nav-border); color: var(--text-heading)">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-body)">Pelabuhan Tujuan *</label>
                                    <select class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition"
                                            style="background: rgba(255,255,255,0.06); border: 1px solid var(--nav-border); color: var(--text-heading)">
                                        <option value="Tanjung Priok, Indonesia">Port of Tanjung Priok (Jakarta, ID)</option>
                                        <option value="Tanjung Perak, Indonesia">Port of Tanjung Perak (Surabaya, ID)</option>
                                        <option value="Rotterdam, Netherlands">Port of Rotterdam (Belanda)</option>
                                        <option value="Busan, South Korea">Port of Busan (Korea Selatan)</option>
                                        <option value="Tokyo Haneda, Japan">Haneda / Tokyo Port (Jepang)</option>
                                        <option value="London Heathrow, UK">London Heathrow Airport (UK)</option>
                                        <option value="New York JFK, USA">JFK International Port (USA)</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-body)">Pilihan Varietas Kopi Brasil *</label>
                                <select class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition"
                                        style="background: rgba(255,255,255,0.06); border: 1px solid var(--nav-border); color: var(--text-heading)">
                                    <option value="Bourbon Specialty">Kopi Brazil Santos Bourbon Specialty (SCA 86.5)</option>
                                    <option value="Yellow Catuai">Brazil Santos Yellow Catuai Natural Micro-lot (SCA 87.5)</option>
                                    <option value="Cerrado Mineiro">Brazil Santos Cerrado Mineiro Single-Origin (SCA 85.0)</option>
                                    <option value="Strictly Soft">Brazil Santos Strictly Soft Fine Cup SSFC (SCA 84.0)</option>
                                    <option value="Decaf Swiss Water">Brazil Santos Decaf Swiss Water Process (SCA 83.5)</option>
                                    <option value="Espresso Master Blend">Brazil Santos Espresso Roast Master Blend (SCA 85.5)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold mb-1.5" style="color: var(--text-body)">Volume Muatan & Catatan Khusus</label>
                                <textarea rows="3" placeholder="Contoh: Permintaan 1 Kontainer FCL 20ft (sekitar 300 karung 60kg). Jadwal keberangkatan yang diinginkan awal bulan depan." 
                                          class="w-full px-4 py-2.5 rounded-xl text-sm focus:outline-none transition"
                                          style="background: rgba(255,255,255,0.06); border: 1px solid var(--nav-border); color: var(--text-heading)"></textarea>
                            </div>

                            <button type="submit" 
                                    class="w-full py-3.5 rounded-xl font-bold text-sm transition flex items-center justify-center gap-2 shadow-lg hover:scale-[1.01]"
                                    style="background: var(--btn-primary-bg); color: var(--btn-primary-text)">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                <span>{{ __('Kirim Permintaan Kuotasi Ekspor') }}</span>
                            </button>
                        </form>

                        {{-- Submission Success Notification --}}
                        <div id="inquirySuccessToast" class="hidden mt-4 p-4 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-500 text-xs flex items-center gap-3">
                            <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                            <span>{{ __('Permintaan kuotasi berhasil dikirim! Tim operasional Santos Exporters S.A. akan segera menghubungi Anda.') }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>


    {{-- ========================================================================= --}}
    {{-- FOOTER                                                                    --}}
    {{-- ========================================================================= --}}
    <footer class="border-t pt-16 pb-12 relative text-sm"
            style="background: var(--bg-page); border-color: var(--nav-border); color: var(--text-muted)">
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
                        <li><a href="#alur-sistem" class="hover:underline transition">Alur Sistem & Flowmap</a></li>
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

        // Active Navigation Highlighter on Scroll
        window.addEventListener('scroll', () => {
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
