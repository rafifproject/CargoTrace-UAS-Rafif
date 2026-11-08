<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dasbor Operasional') | CargoTrace - Logistics Operator</title>

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
        <!-- ==================== SIDEBAR ==================== -->
        <aside class="w-64 bg-[#0A1128] text-slate-300 flex flex-col flex-shrink-0 z-30 border-r border-slate-800 select-none">
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center px-5 border-b border-slate-800/80 gap-3">
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 flex-shrink-0">
                    <polygon points="20,2 36,11 36,29 20,38 4,29 4,11" fill="rgba(0,168,232,0.15)" stroke="#00A8E8" stroke-width="1.2"/>
                    <rect x="12" y="14" width="16" height="13" rx="1.5" fill="none" stroke="#00A8E8" stroke-width="1.6"/>
                    <line x1="12" y1="19" x2="28" y2="19" stroke="#00A8E8" stroke-width="1.6"/>
                    <line x1="20" y1="14" x2="20" y2="19" stroke="#00A8E8" stroke-width="1.6"/>
                    <ellipse cx="20" cy="20" rx="14" ry="5" fill="none" stroke="rgba(0,168,232,0.4)" stroke-width="0.9" transform="rotate(-15 20 20)"/>
                    <path d="M28 12 L32 8 M28 12 L31 15" stroke="#00A8E8" stroke-width="1.2" stroke-linecap="round"/>
                </svg>
                <div>
                    <h1 class="font-bold text-white text-base tracking-tight leading-tight">CargoTrace</h1>
                    <p class="text-[10px] text-cyan-400 font-medium tracking-wide uppercase">Operations Hub</p>
                </div>
            </div>

            <!-- Role Badge -->
            <div class="px-5 py-3 border-b border-slate-800/60 bg-slate-900/40 flex items-center justify-between">
                <span class="text-xs text-slate-400 font-medium">Akses Portal:</span>
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Logistics Operator
                </span>
            </div>

            <!-- Sidebar Navigation Menu (Section 4 Format) -->
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-5 text-sm">
                <!-- Group 1: BERANDA -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Beranda</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('operator.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.dashboard') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30 font-semibold' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="layout-dashboard" class="w-4 h-4 {{ request()->routeIs('operator.dashboard') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Dasbor Operasional</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 2: PENGELOLAAN PENGIRIMAN -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Pengelolaan Pengiriman</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('operator.shipments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.shipments.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="package" class="w-4 h-4 {{ request()->routeIs('operator.shipments.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Semua Pengiriman</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.shipments.create') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.shipments.create') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="package-plus" class="w-4 h-4 {{ request()->routeIs('operator.shipments.create') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Buat Pengiriman</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 3: PEMANTAUAN -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Pemantauan</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('operator.tracking.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.tracking.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="git-commit" class="w-4 h-4 {{ request()->routeIs('operator.tracking.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Riwayat Pelacakan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.shipments.map') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.shipments.map') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="globe-2" class="w-4 h-4 {{ request()->routeIs('operator.shipments.map') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Peta Pengiriman</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.exceptions.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.exceptions.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="alert-triangle" class="w-4 h-4 {{ request()->routeIs('operator.exceptions.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                    <span>Pengecualian</span>
                                </div>
                                @php $openExceptions = \App\Models\ShipmentException::whereIn('status', ['OPEN', 'INVESTIGATING'])->count(); @endphp
                                @if($openExceptions > 0)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">{{ $openExceptions }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.intelligence.weather') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.intelligence.weather') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="cloud-sun" class="w-4 h-4 {{ request()->routeIs('operator.intelligence.weather') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Pemantauan Cuaca</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 4: INTELIJEN GLOBAL -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Intelijen Global</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('operator.intelligence.economy') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.intelligence.economy') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="trending-up" class="w-4 h-4 {{ request()->routeIs('operator.intelligence.economy') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Indikator Ekonomi</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.intelligence.currency') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.intelligence.currency') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="coins" class="w-4 h-4 {{ request()->routeIs('operator.intelligence.currency') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Kurs Mata Uang</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.intelligence.news') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.intelligence.news') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="newspaper" class="w-4 h-4 {{ request()->routeIs('operator.intelligence.news') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Berita Logistik Global</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.intelligence.ports') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.intelligence.ports') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="anchor" class="w-4 h-4 {{ request()->routeIs('operator.intelligence.ports') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Direktori Pelabuhan</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.intelligence.comparison') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.intelligence.comparison') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="scale" class="w-4 h-4 {{ request()->routeIs('operator.intelligence.comparison') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Perbandingan Negara</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.intelligence.risk') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.intelligence.risk') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="shield-alert" class="w-4 h-4 {{ request()->routeIs('operator.intelligence.risk') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                <span>Intelijen Risiko</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Group 5: INFORMASI -->
                <div>
                    <div class="px-3 mb-2 text-[11px] font-semibold tracking-wider text-slate-400 uppercase">Informasi</div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('operator.notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.notifications.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="bell" class="w-4 h-4 {{ request()->routeIs('operator.notifications.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
                                    <span>Pusat Notifikasi</span>
                                </div>
                                @php $unreadNotifs = \App\Models\Notification::where('is_read', false)->count(); @endphp
                                @if($unreadNotifs > 0)
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">{{ $unreadNotifs }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('operator.profile.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg font-medium transition-colors {{ request()->routeIs('operator.profile.index') ? 'bg-cyan-600/20 text-cyan-300 border border-cyan-500/30' : 'hover:bg-slate-800/60 hover:text-white text-slate-300' }}">
                                <i data-lucide="user-circle" class="w-4 h-4 {{ request()->routeIs('operator.profile.index') ? 'text-cyan-400' : 'text-slate-400' }}"></i>
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
                        <span>Keluar dari Sesi</span>
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
                    <form method="GET" action="{{ route('operator.shipments.index') }}" class="relative w-full">
                        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" placeholder="Cari Resi Kargo, Pelanggan, atau Rute..." class="w-full pl-10 pr-4 py-1.5 text-xs bg-slate-100 dark:bg-slate-900/60 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 rounded-lg focus:outline-none focus:ring-1 focus:ring-cyan-500 placeholder-slate-400 transition-colors">
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
                    <a href="{{ route('operator.notifications.index') }}" class="relative p-2 rounded-lg text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 transition-colors" title="Pusat Notifikasi">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                        @if($unreadNotifs > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-rose-500 text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $unreadNotifs }}
                            </span>
                        @endif
                    </a>

                    <!-- User Profile Dropdown / Profile Link -->
                    <a href="{{ route('operator.profile.index') }}" class="flex items-center gap-3 pl-3 border-l border-slate-200 dark:border-slate-800 hover:opacity-90 transition-opacity">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-600 to-cyan-700 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                            {{ substr(auth()->user()->name ?? 'Operator', 0, 2) }}
                        </div>
                        <div class="hidden sm:block text-left">
                            <div class="text-xs font-semibold text-slate-800 dark:text-white leading-tight">{{ auth()->user()->name ?? 'Operator' }}</div>
                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium">Logistics Operator</div>
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

