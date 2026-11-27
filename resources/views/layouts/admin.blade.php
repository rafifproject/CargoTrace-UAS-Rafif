<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Dasbor Administrator')) | CargoTrace - Global Shipment Visibility</title>

    <!-- Favicon: CargoTrace Official 3D Box & Orbit Logo -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Google Fonts: Inter & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@600;700;800;900&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN (Ensures instant zero-config styling with dark mode support) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        navy: {
                            800: '#0B132B',
                            900: '#070C1E',
                            950: '#030712',
                        },
                        brand: {
                            blue: '#034078',
                            deep: '#001F54',
                            cyan: '#00A8E8',
                            light: '#EBF4F6',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Leaflet.js CSS & JS (Reliable Multi-CDN Fallback) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" onerror="this.onerror=null;this.href='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css';"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js';"></script>

    <!-- Chart.js (Multi-CDN Fallback) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js" onerror="this.onerror=null;this.src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js';"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Theme Initialization Script (Avoids Flash of Unstyled Theme) -->
    <script>
        if (localStorage.getItem('cargotrace_theme') === 'dark' || (!('cargotrace_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Custom scrollbar for clean enterprise look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.3); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.5); }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800 dark:bg-[#070C1E] dark:text-slate-100 transition-colors duration-200 antialiased flex flex-col">

    <div class="flex h-[100dvh] overflow-hidden relative">
        <!-- Mobile Sidebar Backdrop -->
        <div id="portal-sidebar-backdrop" onclick="closePortalSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

        <!-- ==================== SIDEBAR ==================== -->
        <aside id="portal-sidebar" class="fixed inset-y-0 left-0 lg:static w-64 bg-[#0A1128] text-slate-300 flex flex-col flex-shrink-0 z-50 lg:z-30 border-r border-slate-800 select-none transform -translate-x-full lg:translate-x-0 transition-transform duration-200 ease-out">
            <!-- Sidebar Header / Unified Brand & Organization Card -->
            <div class="p-3.5 border-b border-slate-800/80 bg-gradient-to-b from-[#0A1128] to-[#0d1633]">
                <!-- Main Brand Row -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 flex-shrink-0 drop-shadow-[0_0_12px_rgba(0,194,255,0.4)]">
                        <svg viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
                            <defs>
                                <linearGradient id="sbTopAdmin" x1="60" y1="20" x2="60" y2="55" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#38BDF8"/>
                                    <stop offset="100%" stop-color="#0284C7"/>
                                </linearGradient>
                                <linearGradient id="sbLeftAdmin" x1="25" y1="40" x2="60" y2="95" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#0369A1"/>
                                    <stop offset="100%" stop-color="#075985"/>
                                </linearGradient>
                                <linearGradient id="sbRightAdmin" x1="60" y1="40" x2="95" y2="95" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#0284C7"/>
                                    <stop offset="100%" stop-color="#0369A1"/>
                                </linearGradient>
                                <linearGradient id="sbOrbitAdmin" x1="10" y1="85" x2="110" y2="35" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#00F0FF" stop-opacity="0.95"/>
                                    <stop offset="70%" stop-color="#00A8E8" stop-opacity="0.95"/>
                                    <stop offset="100%" stop-color="#38BDF8" stop-opacity="0"/>
                                </linearGradient>
                            </defs>
                            <polygon points="60,24 88,40 60,56 32,40" fill="url(#sbTopAdmin)" stroke="#38BDF8" stroke-width="2.5" stroke-linejoin="round"/>
                            <polygon points="32,40 60,56 60,88 32,72" fill="url(#sbLeftAdmin)" stroke="#0284C7" stroke-width="2.5" stroke-linejoin="round"/>
                            <polygon points="60,56 88,40 88,72 60,88" fill="url(#sbRightAdmin)" stroke="#38BDF8" stroke-width="2.5" stroke-linejoin="round"/>
                            <line x1="60" y1="56" x2="60" y2="88" stroke="#38BDF8" stroke-width="2"/>
                            <path d="M 16,84 C 18,98 52,106 82,90 C 98,82 106,68 102,52 C 100,44 94,38 88,34" 
                                  fill="none" stroke="url(#sbOrbitAdmin)" stroke-width="4.5" stroke-linecap="round"/>
                            <g transform="translate(90, 48) rotate(-42)">
                                <path d="M0,0 L12,-4 L10,0 L14,3 L10,3 L8,6 L6,6 L7,2 L2,2 Z" fill="#00F0FF" stroke="#FFFFFF" stroke-width="0.8"/>
                            </g>
                            <g transform="translate(100, 36) rotate(-42) scale(0.85)">
                                <path d="M0,0 L12,-4 L10,0 L14,3 L10,3 L8,6 L6,6 L7,2 L2,2 Z" fill="#38BDF8" stroke="#FFFFFF" stroke-width="0.8"/>
                            </g>
                        </svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between">
                            <h1 class="font-extrabold text-white text-[17px] tracking-tight leading-none" style="font-family: 'Outfit', sans-serif;">
                                Cargo<span class="text-[#00C2FF]">Trace</span>
                            </h1>
                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-400 border border-cyan-500/25">
                                <span class="w-1 h-1 rounded-full bg-cyan-400 animate-pulse"></span>
                                ADMIN
                            </span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium mt-1 truncate">Global Freight Visibility</p>
                    </div>
                </div>

                <!-- Enterprise Organization Card (Clean, Full-Width, Aesthetic) -->
                <div class="mt-3 p-2 rounded-lg bg-slate-900/60 border border-slate-800/80 flex items-center justify-between hover:border-slate-700/60 transition-colors">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="w-6 h-6 rounded-md bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-xs flex-shrink-0">
                            ☕
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-[11px] font-bold text-slate-200 truncate leading-tight">Santos Exporters S.A.</div>
                            <div class="text-[9px] text-amber-400/90 font-medium truncate flex items-center gap-1 mt-0.5">
                                <span class="w-1 h-1 rounded-full bg-emerald-400"></span>
                                <span>Santos Hub &bull; Brazil</span>
                            </div>
                        </div>
                    </div>
                    <span class="text-[9px] font-semibold text-slate-500 bg-slate-800/80 px-1.5 py-0.5 rounded">HQ</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6 text-sm">
                <!-- Group 1: Dasbor -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ __('Dasbor Utama') }}</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30 font-semibold' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('admin.dashboard') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Dasbor Administrator') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: Pengiriman -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ __('Pengiriman Global') }}</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.shipments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.shipments.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="package" class="w-4 h-4 {{ request()->routeIs('admin.shipments.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Semua Pengiriman') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.tracking.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.tracking.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="git-commit" class="w-4 h-4 {{ request()->routeIs('admin.tracking.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Riwayat Pelacakan') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.shipments.map') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.shipments.map') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="globe-2" class="w-4 h-4 {{ request()->routeIs('admin.shipments.map') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Peta Pengiriman') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.exceptions.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.exceptions.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 {{ request()->routeIs('admin.exceptions.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                    <span>{{ __('Pengecualian') }}</span>
                                </div>
                                @php $openExceptions = \App\Models\ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING'])->count(); @endphp
                                @if($openExceptions > 0)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">{{ $openExceptions }}</span>
                                @endif
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: Sumber Daya -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ __('Sumber Daya') }}</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.carriers.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.carriers.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="truck" class="w-4 h-4 {{ request()->routeIs('admin.carriers.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Manajemen Ekspedisi') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.locations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.locations.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="map-pin" class="w-4 h-4 {{ request()->routeIs('admin.locations.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Manajemen Lokasi') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.users.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="users" class="w-4 h-4 {{ request()->routeIs('admin.users.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Manajemen Pengguna') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.analytics.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="bar-chart-3" class="w-4 h-4 {{ request()->routeIs('admin.analytics.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Analitik Operasional') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 4: Intelijen Global -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ __('Intelijen Global') }}</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.intelligence.weather') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.intelligence.weather') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="cloud-sun" class="w-4 h-4 {{ request()->routeIs('admin.intelligence.weather') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Monitoring Cuaca') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.intelligence.currency') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.intelligence.currency') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="coins" class="w-4 h-4 {{ request()->routeIs('admin.intelligence.currency') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Kurs & Nilai Tukar') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.intelligence.economy') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.intelligence.economy') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="trending-up" class="w-4 h-4 {{ request()->routeIs('admin.intelligence.economy') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Indikator Ekonomi') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.intelligence.news') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.intelligence.news') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="newspaper" class="w-4 h-4 {{ request()->routeIs('admin.intelligence.news') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Berita Global') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.intelligence.ports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.intelligence.ports') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="anchor" class="w-4 h-4 {{ request()->routeIs('admin.intelligence.ports') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Direktori Pelabuhan') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.intelligence.airports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.intelligence.airports') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="plane" class="w-4 h-4 {{ request()->routeIs('admin.intelligence.airports') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Bandara Kargo Global') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.intelligence.comparison') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.intelligence.comparison') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="scale" class="w-4 h-4 {{ request()->routeIs('admin.intelligence.comparison') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Perbandingan Negara') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 5: Administrasi -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">{{ __('Administrasi') }}</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.notifications.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="bell" class="w-4 h-4 {{ request()->routeIs('admin.notifications.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                    <span>{{ __('Pusat Notifikasi') }}</span>
                                </div>
                                @php $unreadNotifs = \App\Models\Notification::where('is_read', false)->count(); @endphp
                                @if($unreadNotifs > 0)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">{{ $unreadNotifs }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.audit-logs.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="file-text" class="w-4 h-4 {{ request()->routeIs('admin.audit-logs.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Log Audit') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('admin.settings.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="settings" class="w-4 h-4 {{ request()->routeIs('admin.settings.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>{{ __('Pengaturan Sistem') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-3 border-t border-slate-800/80 bg-slate-900/40">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 transition-colors border border-rose-500/20">
                        <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                        <span>{{ __('Keluar dari Sesi') }}</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ==================== MAIN CONTENT AREA ==================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white dark:bg-[#0A1128] border-b border-slate-200 dark:border-slate-800 px-3 sm:px-6 flex items-center justify-between gap-2 sm:gap-4 z-20 flex-shrink-0 transition-colors">
                <!-- Mobile Menu Button & Search Bar -->
                <div class="flex items-center gap-2 sm:gap-3 flex-1 min-w-0 max-w-md">
                    <button type="button" onclick="togglePortalSidebar()" class="lg:hidden p-2 rounded-lg text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:text-cyan-500 transition-colors shrink-0" aria-label="Buka Menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <form method="GET" action="{{ route('admin.shipments.index') }}" class="relative w-full min-w-0">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari No. Resi, Pelanggan, Ekspedisi, atau Rute...') }}" class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-100 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-cyan-500 placeholder-slate-400 transition-colors">
                    </form>
                </div>

                <!-- Right Actions: Live Clock, Language Toggle, Theme Toggle, Notification Bell, User Menu -->
                <div class="flex items-center gap-1.5 sm:gap-3 shrink-0">
                    <!-- Live Global Clock -->
                    <div class="hidden md:flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-900/80 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-cyan-500"></i>
                        <span id="global-clock" class="font-mono font-medium">UTC --:-- | WIB --:--</span>
                    </div>

                    <!-- Quick Language Switcher Pill (ID / EN) -->
                    <a href="{{ route('locale.switch', app()->getLocale() === 'en' ? 'id' : 'en') }}"
                       class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-bold transition-all border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-900 text-slate-700 dark:text-slate-200 hover:border-cyan-500/40 hover:text-cyan-500"
                       title="{{ app()->getLocale() === 'en' ? 'Ganti ke Bahasa Indonesia' : 'Switch to English' }}">
                        <span>{{ app()->getLocale() === 'en' ? '🇬🇧 EN' : '🇮🇩 ID' }}</span>
                    </a>

                    <!-- Single Global Dark/Light Toggle Button -->
                    <button id="theme-toggle" type="button" onclick="toggleGlobalTheme()" class="p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 transition-colors" title="{{ __('Beralih Tema (Terang / Gelap)') }}">
                        <i data-lucide="sun" class="w-4 h-4 hidden dark:block text-amber-400"></i>
                        <i data-lucide="moon" class="w-4 h-4 block dark:hidden text-slate-600"></i>
                    </button>

                    <!-- Notifications Dropdown / Shortcut -->
                    <a href="{{ route('admin.notifications.index') }}" class="relative p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 transition-colors" title="{{ __('Pusat Notifikasi') }}">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        @if($unreadNotifs > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $unreadNotifs }}
                            </span>
                        @endif
                    </a>

                    <!-- User Profile Dropdown -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-800">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-cyan-600 to-blue-700 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            {{ substr(auth()->user()->name ?? 'Admin', 0, 2) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-semibold text-slate-800 dark:text-white leading-tight">{{ auth()->user()->name ?? 'Admin' }}</div>
                            <div class="text-[10px] text-cyan-600 dark:text-cyan-400 font-medium">{{ __('Administrator') }}</div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page Body -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden p-3 sm:p-5 lg:p-6 bg-slate-50 dark:bg-[#070C1E] transition-colors">
                <!-- Session Alerts -->
                @if(session('status') || session('success'))
                    <div class="mb-5 flex items-center gap-3 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 dark:bg-emerald-950/40 dark:border-emerald-800/60 dark:text-emerald-300 text-sm">
                        <i data-lucide="check-circle-2" class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400"></i>
                        <p class="font-medium">{{ session('status') ?? session('success') }}</p>
                    </div>
                @endif

                @if(session('error') || $errors->any())
                    <div class="mb-5 flex items-start gap-3 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 dark:bg-rose-950/40 dark:border-rose-800/60 dark:text-rose-300 text-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 text-rose-600 dark:text-rose-400 mt-0.5"></i>
                        <div>
                            <p class="font-semibold">{{ session('error') ?? 'Terjadi kesalahan pada data yang dimasukkan:' }}</p>
                            @if($errors->any())
                                <ul class="list-disc list-inside mt-1 text-xs space-y-0.5">
                                    @foreach($errors->all() as $err)
                                        <li>{{ $err }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Global Scripts -->
    <script>
        // Initialize Lucide Icons
        lucide.createIcons();

        // Single Global Dark/Light Toggle
        function toggleGlobalTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('cargotrace_theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('cargotrace_theme', 'dark');
            }
            lucide.createIcons();
            // Dispatch event for charts/maps to re-render themes
            window.dispatchEvent(new Event('themechange'));
        }

        // Live UTC and WIB Clock
        function updateGlobalClock() {
            const now = new Date();
            const utcHours = String(now.getUTCHours()).padStart(2, '0');
            const utcMinutes = String(now.getUTCMinutes()).padStart(2, '0');
            const utcSeconds = String(now.getUTCSeconds()).padStart(2, '0');

            // WIB is UTC+7
            const wibDate = new Date(now.getTime() + (7 * 3600 * 1000));
            const wibHours = String(wibDate.getUTCHours()).padStart(2, '0');
            const wibMinutes = String(wibDate.getUTCMinutes()).padStart(2, '0');

            const clockEl = document.getElementById('global-clock');
            if (clockEl) {
                clockEl.textContent = `UTC ${utcHours}:${utcMinutes}:${utcSeconds} | WIB ${wibHours}:${wibMinutes}`;
            }
        }
        setInterval(updateGlobalClock, 1000);
        updateGlobalClock();

        // Mobile Sidebar Drawer Controls
        function togglePortalSidebar() {
            const sidebar = document.getElementById('portal-sidebar');
            const backdrop = document.getElementById('portal-sidebar-backdrop');
            if (!sidebar || !backdrop) return;
            const isClosed = sidebar.classList.contains('-translate-x-full');
            if (isClosed) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                closePortalSidebar();
            }
        }
        function closePortalSidebar() {
            const sidebar = document.getElementById('portal-sidebar');
            const backdrop = document.getElementById('portal-sidebar-backdrop');
            if (sidebar) sidebar.classList.add('-translate-x-full');
            if (backdrop) backdrop.classList.add('hidden');
        }
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closePortalSidebar(); });
        window.addEventListener('resize', () => { if (window.innerWidth >= 1024) closePortalSidebar(); });
    </script>
    @stack('scripts')
</body>
</html>

