<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pelacakan Kargo') | CargoTrace - Customer Portal</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
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

    <!-- Leaflet.js CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Theme Initialization Script -->
    <script>
        if (localStorage.getItem('cargotrace_theme') === 'dark' || (!('cargotrace_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(100, 116, 139, 0.3); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(100, 116, 139, 0.5); }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-slate-50 text-slate-800 dark:bg-[#070C1E] dark:text-slate-100 transition-colors duration-200 antialiased flex flex-col">

    <div class="flex h-screen overflow-hidden">
        <!-- ==================== SIDEBAR CUSTOMER ==================== -->
        <aside class="w-64 bg-[#0A1128] text-slate-300 flex flex-col flex-shrink-0 z-30 border-r border-slate-800 select-none">
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center px-5 border-b border-slate-800/80 gap-3">
                <img src="/images/logo.png" alt="CargoTrace Logo" class="h-8 w-auto object-contain">
                <div>
                    <h1 class="font-bold text-white text-base tracking-tight leading-tight">CargoTrace</h1>
                    <p class="text-[10px] text-cyan-400 font-medium tracking-wide uppercase">Customer Portal</p>
                </div>
            </div>

            <!-- Role Badge -->
            <div class="px-5 py-3 border-b border-slate-800/60 bg-slate-900/40 flex items-center justify-between">
                <span class="text-xs text-slate-400 font-medium">Akses Akun:</span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                    Verified Customer
                </span>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5 text-sm">
                <!-- Group 1: BERANDA -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Beranda</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.dashboard') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30 font-semibold' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('customer.dashboard') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Beranda</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: PENGIRIMAN SAYA -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Pengiriman Saya</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('customer.shipments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.shipments.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="package" class="w-4 h-4 {{ request()->routeIs('customer.shipments.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Pengiriman Saya</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.tracking.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.tracking.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="git-commit" class="w-4 h-4 {{ request()->routeIs('customer.tracking.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Riwayat Pelacakan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.shipments.map') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.shipments.map') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="globe-2" class="w-4 h-4 {{ request()->routeIs('customer.shipments.map') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Peta Pengiriman</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: STATUS & KENDALA -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Kendala & Informasi</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('customer.exceptions.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.exceptions.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 {{ request()->routeIs('customer.exceptions.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                    <span>Pengecualian</span>
                                </div>
                                @php
                                    $myShipmentIds = auth()->user()->shipments()->pluck('id');
                                    $custOpenExceptions = \App\Models\ShipmentException::whereIn('shipment_id', $myShipmentIds)
                                        ->whereIn('status', ['OPEN', 'INVESTIGATING'])
                                        ->count();
                                @endphp
                                @if($custOpenExceptions > 0)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">{{ $custOpenExceptions }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.intelligence.weather') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.intelligence.weather') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="cloud-sun" class="w-4 h-4 {{ request()->routeIs('customer.intelligence.weather') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Pemantauan Cuaca</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.intelligence.news') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.intelligence.news') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="newspaper" class="w-4 h-4 {{ request()->routeIs('customer.intelligence.news') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Berita Logistik</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.intelligence.comparison') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.intelligence.comparison') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="scale" class="w-4 h-4 {{ request()->routeIs('customer.intelligence.comparison') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Perbandingan Negara</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 4: AKUN SAYA -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Akun Saya</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('customer.notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.notifications.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="bell" class="w-4 h-4 {{ request()->routeIs('customer.notifications.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                    <span>Pusat Notifikasi</span>
                                </div>
                                @php
                                    $custUnreadNotifs = \App\Models\Notification::where(function($q) use ($myShipmentIds) {
                                        $q->where('user_id', auth()->id())->orWhereIn('shipment_id', $myShipmentIds);
                                    })->where('is_read', false)->count();
                                @endphp
                                @if($custUnreadNotifs > 0)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">{{ $custUnreadNotifs }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('customer.profile.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('customer.profile.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="user-circle" class="w-4 h-4 {{ request()->routeIs('customer.profile.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Profil Akun</span>
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
                        <span>Keluar dari Akun</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ==================== MAIN CONTENT AREA ==================== -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-16 bg-white dark:bg-[#0A1128] border-b border-slate-200 dark:border-slate-800 px-6 flex items-center justify-between z-20 flex-shrink-0 transition-colors">
                <!-- Search Bar -->
                <div class="flex items-center gap-3 flex-1 max-w-md">
                    <form method="GET" action="{{ route('customer.shipments.index') }}" class="relative w-full">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" placeholder="Lacak nomor resi pengiriman Anda..." class="w-full pl-10 pr-4 py-1.5 text-xs bg-slate-100 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-cyan-500 placeholder-slate-400 transition-colors">
                    </form>
                </div>

                <!-- Right Actions: Live Clock, Theme Toggle, Notification Bell, User Menu -->
                <div class="flex items-center gap-4">
                    <!-- Live Global Clock -->
                    <div class="hidden md:flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-900/80 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-800">
                        <i data-lucide="clock" class="w-3.5 h-3.5 text-cyan-500"></i>
                        <span id="global-clock" class="font-mono font-medium">UTC --:-- | WIB --:--</span>
                    </div>

                    <!-- Single Global Dark/Light Toggle Button -->
                    <button id="theme-toggle" type="button" onclick="toggleGlobalTheme()" class="p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 transition-colors" title="Beralih Tema (Terang / Gelap)">
                        <i data-lucide="sun" class="w-4 h-4 hidden dark:block text-amber-400"></i>
                        <i data-lucide="moon" class="w-4 h-4 block dark:hidden text-slate-600"></i>
                    </button>

                    <!-- Notifications Dropdown / Shortcut -->
                    <a href="{{ route('customer.notifications.index') }}" class="relative p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 transition-colors" title="Pusat Notifikasi">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        @if($custUnreadNotifs > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-cyan-500 text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $custUnreadNotifs }}
                            </span>
                        @endif
                    </a>

                    <!-- User Profile Dropdown / Profile Link -->
                    <a href="{{ route('customer.profile.index') }}" class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-800 hover:opacity-90 transition-opacity">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-cyan-600 to-blue-700 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            {{ substr(auth()->user()->name ?? 'Customer', 0, 2) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-semibold text-slate-800 dark:text-white leading-tight">{{ auth()->user()->company_name ?: auth()->user()->name }}</div>
                            <div class="text-[10px] text-cyan-600 dark:text-cyan-400 font-medium">Customer Portal</div>
                        </div>
                    </a>
                </div>
            </header>

            <!-- Main Page Body -->
            <main class="flex-1 overflow-y-auto p-6 bg-slate-50 dark:bg-[#070C1E] transition-colors">
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
                            <p class="font-semibold">{{ session('error') ?? 'Terjadi kendala pada input Anda:' }}</p>
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
        lucide.createIcons();

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
            window.dispatchEvent(new Event('themechange'));
        }

        function updateGlobalClock() {
            const now = new Date();
            const utcHours = String(now.getUTCHours()).padStart(2, '0');
            const utcMinutes = String(now.getUTCMinutes()).padStart(2, '0');
            const utcSeconds = String(now.getUTCSeconds()).padStart(2, '0');

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
    </script>
    @stack('scripts')
</body>
</html>

