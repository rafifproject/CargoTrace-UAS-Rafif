@extends('layouts.admin')

@section('title', 'Dasbor Administrator')

@push('styles')
<style>
    .map-mode-btn {
        background: #fff;
        color: #64748b;
        border-color: #cbd5e1;
        transition: background .2s, color .2s, border-color .2s;
    }
    .dark .map-mode-btn {
        background: #0f172a;
        color: #94a3b8;
        border-color: #334155;
    }
    #dashboard-map { z-index: 1; }
    #country-list { scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent; }
    #country-list::-webkit-scrollbar { width: 4px; }
    #country-list::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .country-item:hover { background: rgba(14,165,233,.07); }
    .leaflet-popup-content b { color: #0e6ba8; }
    .leaflet-popup-content-wrapper {
        border-radius: 14px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        padding: 4px;
    }

    /* Custom Leaflet Weather Pill Badges (Ref: media_1794280421603_3a4f8e13.jpg) */
    .weather-pill-marker {
        background: transparent !important;
        border: none !important;
    }
    .weather-pill-box {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 2px solid #ffffff;
        border-radius: 9px;
        padding: 2px 6px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 11px;
        line-height: 1.1;
        min-width: 48px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.35);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
        user-select: none;
    }
    .weather-pill-box:hover {
        transform: scale(1.18);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.45);
        z-index: 99999 !important;
    }
    .weather-pill-blue {
        background: linear-gradient(135deg, #1E40AF, #2563EB);
    }
    .weather-pill-green {
        background: linear-gradient(135deg, #047857, #059669);
    }
    .weather-pill-orange {
        background: linear-gradient(135deg, #C2410C, #EA580C);
    }

    /* Realistic Vehicle Markers for Cargo Tracker (No Circles - Pure Real Vehicle Visuals) */
    .cargo-real-vehicle-marker {
        background: transparent !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
    }
    .cargo-real-vehicle-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.5));
    }
    .cargo-real-vehicle-marker:hover .cargo-real-vehicle-wrapper {
        filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 10px rgba(56, 189, 248, 0.8));
        transform: scale(1.22) !important;
        z-index: 99999 !important;
    }
    .cargo-vehicle-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        padding: 2px 7px;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 6px;
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 10px;
        line-height: 1.2;
        white-space: nowrap;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        pointer-events: none;
    }
    .cargo-tag-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Dasbor Administrator</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Platform Visibilitas & Manajemen Pelacakan Pengiriman Global</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                Sistem Online & Terhubung
            </span>
            <a href="{{ route('admin.shipments.index') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 transition-colors shadow-sm">
                Lihat Semua Pengiriman
            </a>
        </div>
    </div>

    <!-- 5 KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- 1. Total Shipments -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pengiriman</p>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($totalShipments) }}</h3>
                <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-medium mt-0.5 block">Seluruh Kargo Aktif & Arsip</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-900">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 2. In Transit -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dalam Proses</p>
                <h3 class="text-2xl font-extrabold text-blue-600 dark:text-cyan-400 mt-1">{{ number_format($inTransit) }}</h3>
                <span class="text-[10px] text-slate-400 font-medium mt-0.5 block">Sedang Bergerak</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center border border-cyan-100 dark:border-cyan-900">
                <i data-lucide="navigation" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 3. Delivered -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terkirim</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($delivered) }}</h3>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-500 font-medium mt-0.5 block">Selesai di Tujuan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-900">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 4. Delayed -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terlambat</p>
                <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($delayed) }}</h3>
                <span class="text-[10px] text-amber-600 dark:text-amber-500 font-medium mt-0.5 block">&gt; Batas Waktu ETA</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-900">
                <i data-lucide="clock-alert" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 5. Open Exceptions -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between col-span-2 md:col-span-1">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Pengecualian</p>
                <h3 class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ number_format($openExceptions) }}</h3>
                <span class="text-[10px] text-rose-600 dark:text-rose-400 font-medium mt-0.5 block">Membutuhkan Tindakan</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-900">
                <i data-lucide="alert-octagon" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row: Donut Chart Status & Line Chart 30 Days Trend -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Breakdown Donut Chart -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pengiriman by Status</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Komposisi tahapan kargo aktif dan selesai</p>
            </div>
            <div class="relative my-4 flex items-center justify-center h-48">
                <canvas id="statusChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                    <span class="text-slate-600 dark:text-slate-300">Dalam Proses: <strong class="text-slate-900 dark:text-white">{{ $statusCounts['IN_TRANSIT'] }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-slate-600 dark:text-slate-300">Terkirim: <strong class="text-slate-900 dark:text-white">{{ $statusCounts['DELIVERED'] }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-slate-600 dark:text-slate-300">Dipesan: <strong class="text-slate-900 dark:text-white">{{ $statusCounts['BOOKED'] }}</strong></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                    <span class="text-slate-600 dark:text-slate-300">Dijemput: <strong class="text-slate-900 dark:text-white">{{ $statusCounts['PICKED_UP'] }}</strong></span>
                </div>
            </div>
        </div>

        <!-- 30-Day Volume Trend Line Chart -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tren Pengiriman (30 Hari Terakhir)</h2>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Volume kargo yang dikelola jaringan global CargoTrace</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                    Bulan Berjalan
                </span>
            </div>
            <div class="my-4 h-56">
                <canvas id="trendChart"></canvas>
            </div>
            <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 pt-3 border-t border-slate-100 dark:border-slate-800/80">
                <span>Rata-rata Harian: <strong>~{{ round($totalShipments / 30, 1) }} kargo/hari</strong></span>
                <span class="text-emerald-600 dark:text-emerald-400 flex items-center gap-1 font-medium">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                    Pertumbuhan stabil
                </span>
            </div>
        </div>
    </div>

    <!-- ===== PETA INTERAKTIF 3 MODE ===== -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-800/80 gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="globe" class="w-4 h-4 text-cyan-500"></i>
                    <span>Peta Global Interaktif CargoTrace</span>
                </h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan real-time kargo aktif, sebaran pelabuhan, dan kondisi cuaca maritim global</p>
            </div>
            <!-- Filter Mode Tabs -->
            <div class="flex items-center gap-2 flex-wrap">
                <button onclick="switchMapMode('cargo')" id="btn-cargo"
                    class="map-mode-btn active px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all duration-200 flex items-center gap-1.5">
                    🧭 Pemantau CargoTrace
                </button>
                <button onclick="switchMapMode('route')" id="btn-route"
                    class="map-mode-btn px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all duration-200 flex items-center gap-1.5">
                    📍 Route Tracker
                </button>
                <button onclick="switchMapMode('weather')" id="btn-weather"
                    class="map-mode-btn px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all duration-200 flex items-center gap-1.5">
                    ⛅ Cuaca Langsung
                </button>
                <button onclick="switchMapMode('ports')" id="btn-ports"
                    class="map-mode-btn px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all duration-200 flex items-center gap-1.5">
                    ⚓ Sebaran Pelabuhan
                </button>
                <button onclick="resetMapView()" id="btn-reset"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg border border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-200 flex items-center gap-1.5">
                    🔄 Reset
                </button>
            </div>
        </div>

        <!-- Map Body: 2-column layout -->
        <div class="flex flex-col lg:flex-row" style="min-height:520px">
            <!-- Left: Map + Status Banner -->
            <div class="flex-1 relative flex flex-col">
                <!-- Active Mode Banner -->
                <div id="map-mode-banner" class="px-4 py-2 text-xs font-semibold text-white flex items-center gap-2 transition-all duration-300" style="background:linear-gradient(90deg,#0A1128,#0e3a6e);">
                    <span id="banner-icon">🧭</span>
                    <span id="banner-text">Mode: Pemantau CargoTrace — Kargo Aktif & Jalur Pengiriman</span>
                </div>
                <!-- Route Tracker Quick Selector Bar (Visible only in Route Tracker mode) -->
                <div id="route-selector-bar" class="hidden px-4 py-2 bg-sky-950 text-xs text-white flex items-center gap-2 border-b border-sky-800/60 overflow-x-auto">
                    <span class="text-[11px] font-bold text-sky-300 flex-shrink-0 flex items-center gap-1">
                        📍 Simulasi Rute:
                    </span>
                    <button onclick="focusGlobalRoute(0)" class="px-2.5 py-1 rounded-md bg-sky-800/80 hover:bg-sky-700 text-[11px] font-semibold whitespace-nowrap transition-colors flex items-center gap-1">
                        🌏 Trans-Pacific (Shanghai ➔ LA)
                    </button>
                    <button onclick="focusGlobalRoute(1)" class="px-2.5 py-1 rounded-md bg-sky-800/80 hover:bg-sky-700 text-[11px] font-semibold whitespace-nowrap transition-colors flex items-center gap-1">
                        🇪🇺 Asia-Europe (Singapore ➔ Rotterdam)
                    </button>
                    <button onclick="focusGlobalRoute(2)" class="px-2.5 py-1 rounded-md bg-sky-800/80 hover:bg-sky-700 text-[11px] font-semibold whitespace-nowrap transition-colors flex items-center gap-1">
                        🇦🇺 Nusantara-Oceania (Jakarta ➔ Sydney)
                    </button>
                    <button onclick="focusGlobalRoute(3)" class="px-2.5 py-1 rounded-md bg-sky-800/80 hover:bg-sky-700 text-[11px] font-semibold whitespace-nowrap transition-colors flex items-center gap-1">
                        🌊 Trans-Atlantic (Rotterdam ➔ New York)
                    </button>
                </div>
                <!-- Leaflet Map -->
                <div id="dashboard-map" class="flex-1 z-10" style="min-height:460px"></div>
                <!-- Legend -->
                <div id="map-legend" class="flex items-center gap-4 px-4 py-2 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-800/60 text-[11px] text-slate-500 dark:text-slate-400 flex-wrap">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Asal</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>Tujuan</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-cyan-600 inline-flex items-center justify-center text-[9px] text-white">🚢</span>Kapal</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-sky-500 inline-flex items-center justify-center text-[9px] text-white">✈️</span>Pesawat</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-emerald-600 inline-flex items-center justify-center text-[9px] text-white">🚛</span>Truk</span>
                    <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 bg-cyan-500 inline-block"></span>Jalur Ditempuh</span>
                    <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 border-b border-dashed border-cyan-400 inline-block"></span>Estimasi Rute</span>
                </div>
            </div>

            <!-- Right: Search Country Panel -->
            <div class="w-full lg:w-72 border-t lg:border-t-0 lg:border-l border-slate-100 dark:border-slate-800 flex flex-col">
                <div class="px-4 pt-4 pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400"></i>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">Cari Negara</span>
                    </div>
                    <input type="text" id="country-search-input" oninput="filterCountriesList()"
                        placeholder="Ketik nama negara..."
                        class="w-full px-3 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                </div>
                <!-- Country Selected Card -->
                <div id="selected-country-card" class="hidden mx-3 mt-3 p-3 rounded-xl bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800 text-xs shadow-xs">
                    <div class="flex items-center gap-2.5 mb-1.5">
                        <img id="selected-flag-img" src="" alt="" class="w-7 h-5 object-cover rounded shadow-xs border border-slate-200 dark:border-slate-700">
                        <span id="selected-name" class="font-bold text-slate-800 dark:text-white text-sm"></span>
                    </div>
                    <div class="text-slate-600 dark:text-slate-400">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Hub Utama:</span>
                        <span id="selected-hub" class="font-medium text-cyan-700 dark:text-cyan-400"></span>
                    </div>
                </div>
                <!-- Scrollable Country List -->
                <div class="flex-1 overflow-y-auto" style="max-height:400px">
                    <ul id="country-list" class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($countriesList as $country)
                        @php $isoLower = strtolower(str_replace('_EAST', '', $country['code'])); @endphp
                        <li class="country-item px-4 py-2.5 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors"
                            data-name="{{ $country['name'] }}"
                            data-code="{{ $country['code'] }}"
                            data-lat="{{ $country['lat'] }}"
                            data-lng="{{ $country['lng'] }}"
                            data-flag="{{ $country['flag'] }}"
                            data-iso="{{ $isoLower }}"
                            data-hub="{{ $country['hub'] }}"
                            onclick="focusCountry(this)">
                            <div class="w-6 h-4 rounded overflow-hidden shadow-xs border border-slate-200 dark:border-slate-700 flex-shrink-0 bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                <img src="https://flagcdn.com/w40/{{ $isoLower }}.png"
                                     alt="{{ $country['name'] }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='inline'">
                                <span class="text-xs hidden">{{ $country['flag'] }}</span>
                            </div>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200 truncate">{{ $country['name'] }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Recent Activities & Carrier Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Shipment Activities -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="activity" class="w-4 h-4 text-cyan-500"></i>
                    <span>Aktivitas Pengiriman Terbaru</span>
                </h2>
                <a href="{{ route('admin.tracking.index') }}" class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline font-medium">Lihat Semua</a>
            </div>
            <div class="mt-4 space-y-3">
                @forelse($recentActivities as $event)
                    <div class="flex items-start gap-3 p-2.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-cyan-500/10 text-cyan-500 border border-cyan-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="git-commit" class="w-4 h-4"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <a href="{{ route('admin.shipments.show', $event->shipment_id) }}" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                                    {{ $event->shipment->shipment_number ?? 'CT-SHIP' }}
                                </a>
                                <span class="text-[10px] text-slate-400">{{ $event->event_time->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs text-slate-700 dark:text-slate-300 font-medium truncate">{{ $event->description }}</p>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>
                                {{ $event->location->name ?? ($event->location_name_custom ?? 'Checkpoint') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada aktivitas tracking tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Carrier Overview -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80">
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="truck" class="w-4 h-4 text-cyan-500"></i>
                    <span>Ekspedisi Mitra Global</span>
                </h2>
                <a href="{{ route('admin.carriers.index') }}" class="text-xs text-cyan-600 dark:text-cyan-400 hover:underline font-medium">Kelola Ekspedisi</a>
            </div>
            <div class="mt-4 space-y-3">
                @foreach($carriers as $carrier)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-100 dark:border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-lg bg-blue-600 text-white font-extrabold text-xs flex items-center justify-center tracking-wider">
                                {{ $carrier->code }}
                            </span>
                            <div>
                                <h3 class="text-xs font-bold text-slate-900 dark:text-white">{{ $carrier->name }}</h3>
                                <p class="text-[10px] text-slate-400">{{ $carrier->country }} &bull; {{ $carrier->contact_email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-extrabold text-slate-900 dark:text-white">{{ $carrier->shipments_count }}</span>
                            <span class="block text-[10px] text-slate-400">Pengiriman</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Donut Chart - Shipment by Status
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Dalam Proses', 'Terkirim', 'Dipesan', 'Dijemput', 'Dibuat'],
                datasets: [{
                    data: [
                        {{ $statusCounts['IN_TRANSIT'] }},
                        {{ $statusCounts['DELIVERED'] }},
                        {{ $statusCounts['BOOKED'] }},
                        {{ $statusCounts['PICKED_UP'] }},
                        {{ $statusCounts['CREATED'] }}
                    ],
                    backgroundColor: ['#00A8E8', '#10B981', '#3B82F6', '#6366F1', '#94A3B8'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });

        // 2. Line Chart - 30 Days Trend
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendDates) !!},
                datasets: [{
                    label: 'Volume Pengiriman',
                    data: {!! json_encode($trendValues) !!},
                    borderColor: '#00A8E8',
                    backgroundColor: 'rgba(0, 168, 232, 0.1)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    },
                    y: {
                        grid: { color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: { font: { size: 10 }, precision: 0 }
                    }
                }
            }
        });

        // 3. Leaflet 3-Mode Interactive Global Map
        // ─────────────────────────────────────────
        const map = L.map('dashboard-map', {
            center: [15, 20],
            zoom: 2,
            minZoom: 2,
            maxZoom: 20
        });

        // 1. Google Maps Real-World Standard (Nama Jalan, Nama Toko, POI Bisnis, Gedung, Restoran, SPBU)
        const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        });

        // 2. Google Hybrid Satellite (Citra Satelit + Label Jalan & Toko)
        const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps Satelit'
        });

        // 3. OpenStreetMap Standar
        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        // Pasang Google Maps (Jalan + Toko/POI) sebagai peta dasar utama
        googleStreets.addTo(map);

        // Layer Switcher Control di sudut kanan atas peta
        const baseMaps = {
            "🗺️ Google Maps (Jalan & Toko/POI)": googleStreets,
            "🛰️ Satelit Hybrid": googleHybrid,
            "🌍 OpenStreetMap": osm
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        // Data from PHP controller
        const shipments    = {!! json_encode($mapShipments) !!};
        const portsData    = {!! json_encode($globalPorts) !!};
        const weatherData  = {!! json_encode($weatherHubs) !!};
        const countriesData = {!! json_encode($countriesList) !!};

        // Guaranteed Absolute Route URLs direct from Laravel Blade (No 404s!)
        const WEATHER_PAGE_URL = "{{ route('admin.intelligence.weather') }}";
        const PORTS_PAGE_URL   = "{{ route('admin.intelligence.ports') }}";
        const SHIPMENT_BASE_URL = "{{ url('admin/shipments') }}";

        // Route URLs helper - 100% Safe across localhost, subfolders, and virtual hosts
        function getAppUrl(path) {
            const cleanPath = path.startsWith('/') ? path : '/' + path;
            const pathname = window.location.pathname;
            const publicIdx = pathname.indexOf('/public');
            let basePath = '';
            if (publicIdx !== -1) {
                basePath = pathname.substring(0, publicIdx + 7);
            }
            return window.location.origin + basePath + cleanPath;
        }

        // Global Route Tracker Preset Highway Routes
        const globalRoutes = [
            {
                id: 'ROUTE-TP-01',
                name: 'Trans-Pacific Express Corridor',
                vessel: 'MV Pacific Pioneer (IMO 9812401)',
                carrier: 'COSCO / Evergreen Marine',
                origin: { name: 'Port of Shanghai (Yangshan)', country: 'China', iso: 'cn', lat: 31.23, lng: 121.47, departure: '06 Nov 2026, 08:30 UTC' },
                waypoints: [
                    { name: 'East China Sea Exit Gateway', note: 'Arus laut stabil 1.4 kts', lat: 29.80, lng: 127.50, weather: '22°C ⛅ Angin 14 kts' },
                    { name: 'Tokyo Bay Outer Waypoint', note: 'Jalur padat kontainer Pasifik barat', lat: 34.60, lng: 140.80, weather: '17°C ☀️ Angin 16 kts' },
                    { name: 'Mid-Pacific Ocean Crossing Hub', note: 'Deep ocean open waters transit zone', lat: 35.20, lng: -170.00, weather: '14°C 🌊 Gelombang 2.1m' }
                ],
                destination: { name: 'Port of Los Angeles (Pier 400)', country: 'United States', iso: 'us', lat: 33.74, lng: -118.26, eta: '18 Nov 2026, 14:00 UTC' },
                distanceNM: '5,740 NM',
                distanceKm: '10,630 km',
                duration: '12 Hari',
                status: 'UNDERWAY - ON SCHEDULE'
            },
            {
                id: 'ROUTE-AE-02',
                name: 'Asia-Europe Suez Megaship Corridor',
                vessel: 'CMA CGM Palais Royal (IMO 9839181)',
                carrier: 'Ocean Alliance / CMA CGM',
                origin: { name: 'Port of Singapore (Tuas Terminal)', country: 'Singapore', iso: 'sg', lat: 1.26, lng: 103.82, departure: '04 Nov 2026, 12:00 UTC' },
                waypoints: [
                    { name: 'Selat Malaka Northern Traffic TSS', note: 'Zona pemisahan lalu lintas (TSS)', lat: 4.80, lng: 99.20, weather: '30°C 🌧️ Hujan Tropis' },
                    { name: 'Sri Lanka South Waypoint Station', note: 'Indian Ocean deep passage', lat: 5.60, lng: 80.60, weather: '29°C ⛅ Angin 15 kts' },
                    { name: 'Bab-el-Mandeb Strait Escort Point', note: 'Navigasi aman Teluk Aden', lat: 12.60, lng: 43.30, weather: '32°C ☀️ Jarak pandang baik' },
                    { name: 'Suez Canal Convoy Entry Lock', note: 'Transit konvoi Terusan Suez terjadwal', lat: 28.50, lng: 33.30, weather: '25°C 💨 Angin 22 kts' }
                ],
                destination: { name: 'Port of Rotterdam (Maasvlakte 2)', country: 'Belanda', iso: 'nl', lat: 51.92, lng: 4.48, eta: '22 Nov 2026, 06:00 UTC' },
                distanceNM: '8,350 NM',
                distanceKm: '15,464 km',
                duration: '18 Hari',
                status: 'IN TRANSIT - SUEZ APPROACH'
            },
            {
                id: 'ROUTE-NO-03',
                name: 'Nusantara-Oceania Trade Highway',
                vessel: 'KM Meratus Kalabahi (IMO 9452812)',
                carrier: 'Meratus Line / ANL Maritime',
                origin: { name: 'Pelabuhan Tanjung Priok (Jakarta)', country: 'Indonesia', iso: 'id', lat: -6.10, lng: 106.88, departure: '07 Nov 2026, 21:00 WIB' },
                waypoints: [
                    { name: 'Selat Lombok ALKI II Passage', note: 'Arus ALKI II 2.8 kts arah selatan', lat: -8.70, lng: 115.80, weather: '29°C ⛅ Laut tenang' },
                    { name: 'Northwest Australia Offshore Hub', note: 'Jalur kargo benua selatan', lat: -18.50, lng: 118.00, weather: '26°C ☀️ Angin pasat' },
                    { name: 'Great Australian Bight Waypoint', note: 'Perairan selatan benua Australia', lat: -36.00, lng: 130.00, weather: '18°C 🌊 Gelombang 1.8m' }
                ],
                destination: { name: 'Port Botany (Sydney)', country: 'Australia', iso: 'au', lat: -33.97, lng: 151.22, eta: '15 Nov 2026, 10:00 AEST' },
                distanceNM: '3,620 NM',
                distanceKm: '6,704 km',
                duration: '8 Hari',
                status: 'ON TRACK - ALKI TRANSIT'
            },
            {
                id: 'ROUTE-TA-04',
                name: 'Trans-Atlantic Northern Highway',
                vessel: 'Atlantic Star (IMO 9670573)',
                carrier: 'ACL / Grimaldi Lines',
                origin: { name: 'Port of Rotterdam', country: 'Belanda', iso: 'nl', lat: 51.92, lng: 4.48, departure: '08 Nov 2026, 16:00 UTC' },
                waypoints: [
                    { name: 'English Channel Western Exit', note: 'Lalu lintas kapal kargo padat', lat: 49.80, lng: -3.50, weather: '12°C 🌧️ Angin 26 kts' },
                    { name: 'North Atlantic Ocean Station #4', note: 'Mid-Atlantic crossing corridor', lat: 44.50, lng: -40.00, weather: '9°C ⛅ Ombak 3.0m' }
                ],
                destination: { name: 'Port of New York & New Jersey', country: 'United States', iso: 'us', lat: 40.68, lng: -74.04, eta: '15 Nov 2026, 08:00 EST' },
                distanceNM: '3,380 NM',
                distanceKm: '6,260 km',
                duration: '7 Hari',
                status: 'HIGH-SEAS CRUISE (20 KTS)'
            }
        ];

        // Layer groups for each mode
        const cargoLayerGroup   = L.layerGroup();
        const routeLayerGroup   = L.layerGroup();
        const weatherLayerGroup = L.layerGroup();
        const portsLayerGroup   = L.layerGroup();

        let currentMode = 'cargo';

        // ── MODE BUTTON STYLES ──────────────────────────────────
        function updateModeButtons(mode) {
            document.querySelectorAll('.map-mode-btn').forEach(btn => {
                btn.classList.remove(
                    'bg-cyan-600','text-white','border-cyan-600',
                    'bg-amber-500','border-amber-500',
                    'bg-purple-600','border-purple-600',
                    'bg-sky-600','border-sky-600'
                );
                btn.classList.add('border-slate-300','text-slate-500','bg-white');
                btn.style.backgroundColor = '';
                btn.style.color = '';
                btn.style.borderColor = '';
            });
            const activeMap = {
                'cargo':   { id: 'btn-cargo',   bg:'#0e6ba8', border:'#0e6ba8' },
                'route':   { id: 'btn-route',   bg:'#0284c7', border:'#0284c7' },
                'weather': { id: 'btn-weather', bg:'#d97706', border:'#d97706' },
                'ports':   { id: 'btn-ports',   bg:'#7c3aed', border:'#7c3aed' }
            };
            if (activeMap[mode]) {
                const btn = document.getElementById(activeMap[mode].id);
                btn.classList.remove('border-slate-300','text-slate-500');
                btn.style.backgroundColor = activeMap[mode].bg;
                btn.style.borderColor = activeMap[mode].border;
                btn.style.color = '#fff';
            }
        }

        // ── BANNER UPDATE ───────────────────────────────────────
        const banners = {
            cargo:   { icon:'🧭', text:'Mode: Pemantau CargoTrace — Kargo Aktif & Jalur Pengiriman Global', bg:'linear-gradient(90deg,#0A1128,#0e3a6e)' },
            route:   { icon:'📍', text:'Mode: Route Tracker — Pelacakan Rute Maritim Global Interaktif & Titik Waypoint Antar Benua', bg:'linear-gradient(90deg,#0c4a6e,#0284c7)' },
            weather: { icon:'⛅', text:'Mode: Cuaca Langsung — 60+ Titik Cuaca Maritim Terkalibrasi & Evaluasi Risiko Badai', bg:'linear-gradient(90deg,#431407,#92400e)' },
            ports:   { icon:'⚓', text:'Mode: Sebaran Pelabuhan — Data Pelabuhan Maritim Dunia Merata Global (ArcGIS Live WPI FeatureServer)', bg:'linear-gradient(90deg,#2e1065,#5b21b6)' }
        };
        function updateBanner(mode) {
            const b = banners[mode];
            document.getElementById('banner-icon').textContent = b.icon;
            document.getElementById('banner-text').textContent = b.text;
            document.getElementById('map-mode-banner').style.background = b.bg;

            const routeBar = document.getElementById('route-selector-bar');
            if (routeBar) {
                if (mode === 'route') {
                    routeBar.classList.remove('hidden');
                } else {
                    routeBar.classList.add('hidden');
                }
            }
        }

        // ── LEGEND UPDATE ───────────────────────────────────────
        const legends = {
            cargo:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Asal</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>Tujuan</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-cyan-600 inline-flex items-center justify-center text-[9px] text-white">🚢</span>Kapal</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-sky-500 inline-flex items-center justify-center text-[9px] text-white">✈️</span>Pesawat</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-emerald-600 inline-flex items-center justify-center text-[9px] text-white">🚛</span>Truk</span>
                      <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 bg-cyan-500 inline-block"></span>Jalur Ditempuh</span>
                      <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 border-b border-dashed border-cyan-400 inline-block"></span>Estimasi Rute</span>`,
            route:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block border border-white"></span>Pelabuhan Asal</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block border border-white"></span>Waypoint Transit</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block border border-white"></span>Pelabuhan Tujuan</span>
                      <span class="flex items-center gap-1.5 text-slate-400">Klik tiap titik untuk rincian rute & estimasi navigasi</span>`,
            weather: `<span class="flex items-center gap-1.5"><span class="w-3.5 h-2.5 rounded bg-blue-600 inline-block border border-white"></span>Dingin (&lt;15°C)</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-2.5 rounded bg-emerald-600 inline-block border border-white"></span>Sejuk (15–25°C)</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-2.5 rounded bg-orange-600 inline-block border border-white"></span>Hangat (&gt;25°C)</span>
                      <span class="flex items-center gap-1.5 text-slate-400">Pill cuaca tiap negara (klik untuk detail profil)</span>`,
            ports:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-600 inline-block"></span>Pelabuhan ArcGIS Live WPI</span>
                      <span class="flex items-center gap-1.5 text-slate-400">Klik marker untuk melihat profil pelabuhan</span>`
        };
        function updateLegend(mode) {
            document.getElementById('map-legend').innerHTML = legends[mode];
        }

        // ── HELPER: CURVED PATH GENERATOR (Natural Geodesic Arc) ───
        function createCurvedPath(p1, p2, numPoints = 20, curvature = 0.12) {
            const lat1 = p1[0], lng1 = p1[1];
            const lat2 = p2[0], lng2 = p2[1];

            let dLng = lng2 - lng1;
            if (dLng > 180) dLng -= 360;
            if (dLng < -180) dLng += 360;

            const dLat = lat2 - lat1;
            const dist = Math.sqrt(dLat * dLat + dLng * dLng);

            if (dist < 0.05) return [p1, p2];

            const midLat = (lat1 + lat2) / 2;
            const midLng = lng1 + dLng / 2;

            // Normal vector perpendicular to trajectory
            const nx = -dLng / dist;
            const ny = dLat / dist;

            // Arc elevation (natural great-circle bow)
            const arcHeight = Math.min(12, Math.max(1.8, dist * curvature));
            const sign = (dLng >= 0) ? 1 : -1;
            const hemisphereFactor = midLat >= 0 ? 1 : -1;

            const controlLat = midLat + nx * arcHeight * sign * (hemisphereFactor * 0.4 + 0.6);
            let controlLng = midLng + ny * arcHeight * sign;

            if (controlLng > 180) controlLng -= 360;
            if (controlLng < -180) controlLng += 360;

            const curve = [];
            for (let i = 0; i <= numPoints; i++) {
                const t = i / numPoints;
                const inv = 1 - t;
                const lat = inv * inv * lat1 + 2 * inv * t * controlLat + t * t * lat2;
                let lng = inv * inv * lng1 + 2 * inv * t * controlLng + t * t * (lng1 + dLng);
                if (lng > 180) lng -= 360;
                if (lng < -180) lng += 360;
                curve.push([lat, lng]);
            }
            return curve;
        }

        // ── HELPER: CALCULATE BEARING (Arah Menuju Tujuan) ──────
        function calculateBearing(p1, p2) {
            const lat1 = p1[0] * Math.PI / 180;
            const lat2 = p2[0] * Math.PI / 180;
            const dLng = (p2[1] - p1[1]) * Math.PI / 180;
            const y = Math.sin(dLng) * Math.cos(lat2);
            const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(dLng);
            const brng = Math.atan2(y, x) * 180 / Math.PI;
            return Math.round((brng + 360) % 360);
        }

        // ── HELPER: GET REAL VEHICLE VISUAL PER SHIPMENT ─────────
        function getShipmentVehicle(s) {
            const carrierName = (s.carrier?.name || '').toLowerCase();
            const carrierCode = (s.carrier?.code || '').toUpperCase();
            const originType  = (s.origin?.location_type || '').toUpperCase();
            const destType    = (s.destination?.location_type || '').toUpperCase();
            const originName  = (s.origin?.name || '').toLowerCase();
            const destName    = (s.destination?.name || '').toLowerCase();
            const cargo       = (s.cargo_type || '').toLowerCase();
            const notes       = (s.notes || '').toLowerCase();

            // 1. Pesawat Kargo Udara (Real Jet Freighter Top-Down)
            if (originType === 'AIRPORT' || destType === 'AIRPORT' ||
                originName.includes('bandara') || originName.includes('airport') ||
                destName.includes('bandara') || destName.includes('airport') ||
                carrierCode === 'FDX' || carrierName.includes('fedex') || carrierName.includes('express air') ||
                cargo.includes('avionics') || cargo.includes('vaccine') || cargo.includes('pharmaceutical')) {
                return {
                    type: 'plane',
                    label: 'Pesawat Kargo Udara (Air Cargo)',
                    emoji: '✈️',
                    accentColor: '#38bdf8',
                    iconW: 46,
                    iconH: 60,
                    realSvg: `
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="44" height="44" style="filter: drop-shadow(0 8px 12px rgba(0,0,0,0.6));">
                            <!-- Aircraft Silhouette & Shadow -->
                            <path d="M32 2 C30 2, 28 6, 28 14 L28 24 L2 35 L2 40 L28 34 L28 50 L18 56 L18 60 L32 58 L46 60 L46 56 L36 50 L36 34 L62 40 L62 35 L36 24 L36 14 C36 6, 34 2, 32 2 Z" fill="#0f172a" opacity="0.3" transform="translate(1.5, 3)"/>
                            <!-- Main Fuselage and Swept Wings -->
                            <path d="M32 2 C30 2, 28 6, 28 14 L28 24 L2 35 L2 40 L28 34 L28 50 L18 56 L18 60 L32 58 L46 60 L46 56 L36 50 L36 34 L62 40 L62 35 L36 24 L36 14 C36 6, 34 2, 32 2 Z" fill="#f8fafc" stroke="#64748b" stroke-width="1"/>
                            <!-- Fuselage Highlight Spine -->
                            <path d="M32 4 C31 4, 29.5 8, 29.5 16 L29.5 54 L34.5 54 L34.5 16 C34.5 8, 33 4, 32 4 Z" fill="#ffffff"/>
                            <!-- Cockpit Glass Window -->
                            <path d="M30 6 Q32 4.5 34 6 L33.5 8.5 Q32 7.5 30.5 8.5 Z" fill="#0284c7"/>
                            <!-- Livery Speed Strip (Cyan & Navy) -->
                            <path d="M12 34 L26 31 L26 33 L12 36 Z" fill="#0284c7"/>
                            <path d="M52 34 L38 31 L38 33 L52 36 Z" fill="#0284c7"/>
                            <!-- Jet Turbines (Twin Engines) -->
                            <rect x="14" y="27" width="4.5" height="11" rx="2" fill="#334155" stroke="#cbd5e1" stroke-width="0.7"/>
                            <ellipse cx="16.2" cy="28" rx="1.6" ry="1.2" fill="#38bdf8"/>
                            <rect x="45.5" y="27" width="4.5" height="11" rx="2" fill="#334155" stroke="#cbd5e1" stroke-width="0.7"/>
                            <ellipse cx="47.8" cy="28" rx="1.6" ry="1.2" fill="#38bdf8"/>
                            <!-- Tail Fin Stripe (Orange Express Accent) -->
                            <path d="M30 52 L34 52 L33 58 L31 58 Z" fill="#ea580c"/>
                        </svg>
                    `
                };
            }

            // 2. Truk Kontainer Logistik (Real Semi-Trailer Truck Top-Down)
            if (originType === 'WAREHOUSE' || destType === 'WAREHOUSE' ||
                originType === 'CUSTOMS_OFFICE' || destType === 'CUSTOMS_OFFICE' ||
                originName.includes('gudang') || destName.includes('gudang') ||
                carrierName.includes('truck') || notes.includes('truck') || notes.includes('darat')) {
                return {
                    type: 'truck',
                    label: 'Truk Kontainer (Freight Truck)',
                    emoji: '🚛',
                    accentColor: '#10b981',
                    iconW: 38,
                    iconH: 68,
                    realSvg: `
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 76" width="26" height="54" style="filter: drop-shadow(0 6px 10px rgba(0,0,0,0.55));">
                            <!-- Chassis Shadow -->
                            <rect x="7" y="4" width="22" height="68" rx="4" fill="#0f172a" opacity="0.35" transform="translate(1, 2)"/>
                            <!-- Trailer Chassis -->
                            <rect x="7" y="22" width="22" height="48" rx="2" fill="#1e293b"/>
                            <!-- 40ft Shipping Container -->
                            <rect x="8" y="23" width="20" height="46" rx="2" fill="#059669" stroke="#047857" stroke-width="0.9"/>
                            <!-- Corrugated Ridges (Panel Kontainer Nyata) -->
                            <line x1="8" y1="28" x2="28" y2="28" stroke="#047857" stroke-width="1.2"/>
                            <line x1="8" y1="33" x2="28" y2="33" stroke="#047857" stroke-width="1.2"/>
                            <line x1="8" y1="38" x2="28" y2="38" stroke="#047857" stroke-width="1.2"/>
                            <line x1="8" y1="43" x2="28" y2="43" stroke="#047857" stroke-width="1.2"/>
                            <line x1="8" y1="48" x2="28" y2="48" stroke="#047857" stroke-width="1.2"/>
                            <line x1="8" y1="53" x2="28" y2="53" stroke="#047857" stroke-width="1.2"/>
                            <line x1="8" y1="58" x2="28" y2="58" stroke="#047857" stroke-width="1.2"/>
                            <line x1="8" y1="63" x2="28" y2="63" stroke="#047857" stroke-width="1.2"/>
                            <!-- Container Castings -->
                            <rect x="8.5" y="23.5" width="2.5" height="2.5" fill="#f8fafc"/>
                            <rect x="25" y="23.5" width="2.5" height="2.5" fill="#f8fafc"/>
                            <rect x="8.5" y="66" width="2.5" height="2.5" fill="#f8fafc"/>
                            <rect x="25" y="66" width="2.5" height="2.5" fill="#f8fafc"/>
                            <!-- Tractor Cab Front -->
                            <rect x="8" y="4" width="20" height="17" rx="3" fill="#f8fafc" stroke="#64748b" stroke-width="1"/>
                            <!-- Windshield -->
                            <path d="M10 7 Q18 5 26 7 L25 10 Q18 8.5 11 10 Z" fill="#0284c7"/>
                            <!-- Side Mirrors -->
                            <rect x="5.5" y="7" width="2.5" height="4" rx="1" fill="#334155"/>
                            <rect x="28" y="7" width="2.5" height="4" rx="1" fill="#334155"/>
                            <!-- Wheels -->
                            <rect x="6" y="14" width="2" height="5" rx="0.5" fill="#0f172a"/>
                            <rect x="28" y="14" width="2" height="5" rx="0.5" fill="#0f172a"/>
                            <rect x="6" y="58" width="2" height="8" rx="0.5" fill="#0f172a"/>
                            <rect x="28" y="58" width="2" height="8" rx="0.5" fill="#0f172a"/>
                        </svg>
                    `
                };
            }

            // 3. Default: Kapal Kontainer Samudera (Real Cargo Container Vessel Top-Down)
            return {
                type: 'ship',
                label: 'Kapal Kontainer Samudera (Cargo Vessel)',
                emoji: '🚢',
                accentColor: '#38bdf8',
                iconW: 38,
                iconH: 72,
                realSvg: `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 78" width="28" height="58" style="filter: drop-shadow(0 6px 12px rgba(0,0,0,0.6));">
                        <!-- Hull Shadow -->
                        <path d="M19 2 C27 12, 33 24, 33 48 C33 66, 30 74, 19 75 C8 74, 5 66, 5 48 C5 24, 11 12, 19 2 Z" fill="#0f172a" opacity="0.35" transform="translate(1.5, 2.5)"/>
                        <!-- Main Hull (Dark Navy Blue Maritime Steel) -->
                        <path d="M19 2 C27 12, 33 24, 33 48 C33 66, 30 74, 19 75 C8 74, 5 66, 5 48 C5 24, 11 12, 19 2 Z" fill="#0f172a" stroke="#475569" stroke-width="1.2"/>
                        <!-- Red Waterline Bow Bulb Accent -->
                        <path d="M19 2 C22 7, 25 12, 25 17 L13 17 C13 12, 16 7, 19 2 Z" fill="#e11d48"/>
                        <circle cx="19" cy="9" r="1.5" fill="#ffffff"/>
                        <!-- Cargo Bay Background Deck -->
                        <rect x="9" y="19" width="20" height="38" rx="2" fill="#1e293b"/>
                        <!-- Real Stacked ISO Containers (Color-coded) -->
                        <!-- Row 1 -->
                        <rect x="10" y="20" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
                        <rect x="19.5" y="20" width="8.5" height="5" rx="0.5" fill="#e11d48" stroke="#be123c" stroke-width="0.5"/>
                        <!-- Row 2 -->
                        <rect x="10" y="26" width="8.5" height="5" rx="0.5" fill="#ea580c" stroke="#c2410c" stroke-width="0.5"/>
                        <rect x="19.5" y="26" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
                        <!-- Row 3 -->
                        <rect x="10" y="32" width="8.5" height="5" rx="0.5" fill="#eab308" stroke="#ca8a04" stroke-width="0.5"/>
                        <rect x="19.5" y="32" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
                        <!-- Row 4 -->
                        <rect x="10" y="38" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
                        <rect x="19.5" y="38" width="8.5" height="5" rx="0.5" fill="#ea580c" stroke="#c2410c" stroke-width="0.5"/>
                        <!-- Row 5 -->
                        <rect x="10" y="44" width="8.5" height="5" rx="0.5" fill="#e11d48" stroke="#be123c" stroke-width="0.5"/>
                        <rect x="19.5" y="44" width="8.5" height="5" rx="0.5" fill="#eab308" stroke="#ca8a04" stroke-width="0.5"/>
                        <!-- Row 6 -->
                        <rect x="10" y="50" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
                        <rect x="19.5" y="50" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
                        <!-- Superstructure / Wheelhouse (Bridge) -->
                        <rect x="10" y="58" width="18" height="9" rx="1.5" fill="#f8fafc" stroke="#cbd5e1" stroke-width="0.8"/>
                        <rect x="12" y="59.5" width="14" height="2" rx="0.5" fill="#0284c7"/>
                        <!-- Funnel & Radar Mast -->
                        <circle cx="19" cy="63" r="1.5" fill="#0f172a"/>
                        <rect x="17.5" y="68" width="3" height="3" rx="0.8" fill="#e11d48"/>
                    </svg>
                `
            };
        }

        // ── BUILD CARGO LAYER ───────────────────────────────────
        function buildCargoLayer() {
            cargoLayerGroup.clearLayers();
            const latLngs = [];

            if (!Array.isArray(shipments)) return latLngs;

            shipments.forEach(s => {
                if (!s || !s.origin || !s.destination) return;

                const oLat = parseFloat(s.origin.latitude);
                const oLng = parseFloat(s.origin.longitude);
                const dLat = parseFloat(s.destination.latitude);
                const dLng = parseFloat(s.destination.longitude);

                if (isNaN(oLat) || isNaN(oLng) || isNaN(dLat) || isNaN(dLng)) return;

                const originCoords = [oLat, oLng];
                const destCoords   = [dLat, dLng];
                latLngs.push(originCoords, destCoords);

                const vehicle = getShipmentVehicle(s);

                // Origin marker (🟢 Green)
                L.circleMarker(originCoords, {
                    radius: 7, fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(`
                    <div style="min-width:220px; font-family: Outfit, sans-serif;">
                        <div style="font-weight:700; font-size:12px; color:#059669; margin-bottom:4px;">🟢 Titik Asal (${s.origin.location_type || 'PORT'})</div>
                        <div style="font-weight:700; font-size:14px; color:#0f172a;">${s.origin.name || 'Lokasi Asal'}</div>
                        <div style="font-size:11px; color:#64748b; margin-bottom:8px;">${s.origin.city || ''}${s.origin.country ? ', ' + s.origin.country : ''}</div>
                        <div style="background:#F1F5F9; padding:6px 10px; border-radius:8px; font-size:11px; margin-bottom:8px;">
                            No. Resi: <b>${s.shipment_number || '-'}</b><br>
                            Armada: <b>${s.carrier?.name || '-'}</b><br>
                            Status: <span style="color:#0284c7; font-weight:700;">${s.status || '-'}</span>
                        </div>
                        <a href="${SHIPMENT_BASE_URL}/${s.id}" style="display:block; text-align:center; background:#0284c7; color:#fff; font-weight:600; padding:6px 12px; border-radius:8px; text-decoration:none; font-size:11px;">
                            Lihat Detail Pengiriman →
                        </a>
                    </div>
                `).addTo(cargoLayerGroup);

                // Destination marker (🔴 Red)
                L.circleMarker(destCoords, {
                    radius: 7, fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(`
                    <div style="min-width:220px; font-family: Outfit, sans-serif;">
                        <div style="font-weight:700; font-size:12px; color:#dc2626; margin-bottom:4px;">🔴 Titik Tujuan (${s.destination.location_type || 'PORT'})</div>
                        <div style="font-weight:700; font-size:14px; color:#0f172a;">${s.destination.name || 'Lokasi Tujuan'}</div>
                        <div style="font-size:11px; color:#64748b; margin-bottom:8px;">${s.destination.city || ''}${s.destination.country ? ', ' + s.destination.country : ''}</div>
                        <div style="background:#F1F5F9; padding:6px 10px; border-radius:8px; font-size:11px; margin-bottom:8px;">
                            No. Resi: <b>${s.shipment_number || '-'}</b><br>
                            Kargo: <b>${s.cargo_type || 'General Cargo'}</b>
                        </div>
                        <a href="${SHIPMENT_BASE_URL}/${s.id}" style="display:block; text-align:center; background:#0284c7; color:#fff; font-weight:600; padding:6px 12px; border-radius:8px; text-decoration:none; font-size:11px;">
                            Lihat Detail Pengiriman →
                        </a>
                    </div>
                `).addTo(cargoLayerGroup);

                // Current location
                let hasCurrent = false;
                let curCoords = null;
                const curLoc = s.current_location || s.currentLocation;
                if (curLoc && curLoc.latitude && curLoc.longitude) {
                    const cLat = parseFloat(curLoc.latitude);
                    const cLng = parseFloat(curLoc.longitude);
                    if (!isNaN(cLat) && !isNaN(cLng)) {
                        curCoords = [cLat, cLng];
                        hasCurrent = true;
                        latLngs.push(curCoords);
                    }
                }

                if (hasCurrent) {
                    // 1. Lintasan Ditempuh: Asal -> Posisi Terkini (Lengkung solid bercahaya)
                    const traveledCurve = createCurvedPath(originCoords, curCoords, 18, 0.12);
                    L.polyline(traveledCurve, {
                        color: '#0284c7',
                        weight: 3.5,
                        opacity: 0.9,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).bindTooltip(`<b>${s.shipment_number}</b><br><span style="font-size:11px">${s.origin.name} ➔ ${curLoc.name || 'Posisi Terkini'} (Ditempuh)</span>`, { sticky: true }).addTo(cargoLayerGroup);

                    // 2. Estimasi Lintasan Lanjutan: Posisi Terkini -> Tujuan (Lengkung putus-putus)
                    const remainingCurve = createCurvedPath(curCoords, destCoords, 18, 0.12);
                    L.polyline(remainingCurve, {
                        color: '#38bdf8',
                        weight: 2.5,
                        dashArray: '6, 8',
                        opacity: 0.8,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).bindTooltip(`<b>${s.shipment_number}</b><br><span style="font-size:11px">${curLoc.name || 'Posisi Terkini'} ➔ ${s.destination.name} (Estimasi)</span>`, { sticky: true }).addTo(cargoLayerGroup);

                    // 3. Real Vehicle Visual Marker (Tanpa Bulatan - Gambar Kendaraan Nyata Berotasi Sesuai Arah)
                    const heading = calculateBearing(curCoords, destCoords);
                    const vehicleDivIcon = L.divIcon({
                        className: 'cargo-real-vehicle-marker',
                        html: `
                            <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${heading}deg);" title="${vehicle.label} &bull; ${s.shipment_number}">
                                ${vehicle.realSvg}
                            </div>
                            <div class="cargo-vehicle-tag">
                                <span class="cargo-tag-dot" style="background:${vehicle.accentColor};"></span>
                                <span>${s.shipment_number}</span>
                            </div>
                        `,
                        iconSize: [vehicle.iconW || 44, vehicle.iconH || 68],
                        iconAnchor: [(vehicle.iconW || 44) / 2, (vehicle.iconH || 68) / 2],
                        popupAnchor: [0, -((vehicle.iconH || 68) / 2 + 5)]
                    });

                    L.marker(curCoords, { icon: vehicleDivIcon, zIndexOffset: 1000 }).bindPopup(`
                        <div style="min-width:245px; font-family: Outfit, sans-serif;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px; border-bottom:1px solid #e2e8f0; padding-bottom:6px;">
                                <span style="font-size:18px;">${vehicle.emoji}</span>
                                <div>
                                    <div style="font-weight:700; font-size:12px; color:#0284c7;">${vehicle.label}</div>
                                    <div style="font-size:10px; color:#64748b;">Posisi Kargo Terkini</div>
                                </div>
                            </div>
                            <div style="font-weight:700; font-size:14px; color:#0f172a; margin-bottom:2px;">${curLoc.name || 'Posisi Terkini'}</div>
                            <div style="font-size:11px; color:#64748b; margin-bottom:8px;">${curLoc.city || ''}${curLoc.country ? ', ' + curLoc.country : ''}</div>
                            <div style="background:#F0FDF4; border:1px solid #BBF7D0; padding:6px 10px; border-radius:8px; font-size:11px; margin-bottom:8px;">
                                <div>No. Resi: <b>${s.shipment_number || '-'}</b></div>
                                <div>Armada/Ekspedisi: <b>${s.carrier?.name || '-'}</b></div>
                                <div>Jenis Kargo: <b>${s.cargo_type || 'General Cargo'}</b></div>
                                <div>Status: <b style="color:#166534;">${s.status || '-'}</b></div>
                            </div>
                            <a href="${SHIPMENT_BASE_URL}/${s.id}" style="display:block; text-align:center; background:#0284c7; color:#fff; font-weight:600; padding:6px 12px; border-radius:8px; text-decoration:none; font-size:11px;">
                                Lihat Detail Pengiriman →
                            </a>
                        </div>
                    `).addTo(cargoLayerGroup);

                } else {
                    // Belum ada lokasi terkini terpisah, buat kurva langsung Asal ke Tujuan
                    const directCurve = createCurvedPath(originCoords, destCoords, 24, 0.12);
                    L.polyline(directCurve, {
                        color: '#0284c7',
                        weight: 2.5,
                        dashArray: '6, 8',
                        opacity: 0.75,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).bindTooltip(`<b>${s.shipment_number}</b><br><span style="font-size:11px">${s.origin.name} ➔ ${s.destination.name}</span>`, { sticky: true }).addTo(cargoLayerGroup);
                }
            });

            return latLngs;
        }

        // ── BUILD ROUTE TRACKER LAYER (Global Maritime Highways & Intercontinental Checkpoints) ──
        let routePolylines = [];
        function buildRouteLayer() {
            routeLayerGroup.clearLayers();
            routePolylines = [];

            globalRoutes.forEach((r, rIdx) => {
                const points = [
                    [r.origin.lat, r.origin.lng],
                    ...r.waypoints.map(wp => [wp.lat, wp.lng]),
                    [r.destination.lat, r.destination.lng]
                ];

                // Route Polyline with glowing dashed line
                const polyline = L.polyline(points, {
                    color: '#0284c7',
                    weight: 3.5,
                    dashArray: '8, 8',
                    opacity: 0.85
                }).bindTooltip(`<b>${r.name}</b><br><span style="font-size:11px">${r.origin.name} ➔ ${r.destination.name}</span>`, { sticky: true });
                polyline.addTo(routeLayerGroup);
                routePolylines.push(polyline);

                // 1. Origin Marker (🟢 Green Port Anchor)
                const originIcon = L.divIcon({
                    className: 'route-tracker-marker',
                    html: `
                        <div style="background:#10B981; color:#ffffff; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; box-shadow:0 0 12px rgba(16,185,129,0.8); border:2.5px solid #ffffff; cursor:pointer;">
                            ⚓
                        </div>
                    `,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -16]
                });

                const originMarker = L.marker([r.origin.lat, r.origin.lng], { icon: originIcon });
                originMarker.bindPopup(`
                    <div style="min-width:260px; font-family: Outfit, sans-serif; padding:4px 2px;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                            <span style="background:#10B981; color:#fff; font-size:10px; font-weight:800; padding:3px 8px; border-radius:6px; letter-spacing:0.5px;">PELABUHAN ASAL (ORIGIN)</span>
                            <span style="font-size:11px; color:#64748b; font-weight:600;">${r.id}</span>
                        </div>
                        <div style="font-weight:800; font-size:15px; color:#0f172a; line-height:1.2;">${r.origin.name}</div>
                        <div style="font-size:12px; color:#475569; margin-top:2px;">Negara: <b>${r.origin.country}</b></div>
                        <div style="background:#F0FDF4; border:1px solid #BBF7D0; border-radius:8px; padding:7px 10px; margin-top:10px; font-size:11px; line-height:1.5;">
                            🚢 Kapal Kargo: <b>${r.vessel}</b><br>
                            🏢 Operator: <b>${r.carrier}</b><br>
                            🕒 Waktu Berangkat: <b>${r.origin.departure}</b>
                        </div>
                        <div style="margin-top:10px; padding-top:8px; border-top:1px solid #e2e8f0; font-size:11px; color:#0284c7; font-weight:700;">
                            ➔ Rute Menuju: ${r.destination.name} (${r.distanceNM})
                        </div>
                    </div>
                `, { maxWidth: 300 }).addTo(routeLayerGroup);

                // 2. Waypoint Markers (🔵 Blue Pulsar Maritime Checkpoints)
                r.waypoints.forEach((wp, wpIdx) => {
                    const wpIcon = L.divIcon({
                        className: 'route-tracker-marker',
                        html: `
                            <div style="background:#0284c7; color:#ffffff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:11px; box-shadow:0 0 10px rgba(2,132,199,0.7); border:2px solid #ffffff; cursor:pointer;">
                                #${wpIdx + 1}
                            </div>
                        `,
                        iconSize: [26, 26],
                        iconAnchor: [13, 13],
                        popupAnchor: [0, -14]
                    });

                    const wpMarker = L.marker([wp.lat, wp.lng], { icon: wpIcon });
                    wpMarker.bindPopup(`
                        <div style="min-width:250px; font-family: Outfit, sans-serif; padding:4px 2px;">
                            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                <span style="background:#0284C7; color:#fff; font-size:10px; font-weight:800; padding:3px 8px; border-radius:6px; letter-spacing:0.5px;">WAYPOINT CHECKPOINT #${wpIdx + 1}</span>
                            </div>
                            <div style="font-weight:800; font-size:14px; color:#0f172a; line-height:1.2;">${wp.name}</div>
                            <div style="font-size:11px; color:#64748b; margin-top:2px;">Koridor: <b>${r.name}</b></div>
                            <div style="background:#F0F9FF; border:1px solid #BAE6FD; border-radius:8px; padding:7px 10px; margin-top:10px; font-size:11px; color:#0369A1; line-height:1.5;">
                                🌊 Kondisi Arus: <b>${wp.note}</b><br>
                                ⛅ Cuaca Perairan: <b>${wp.weather}</b>
                            </div>
                            <div style="margin-top:8px; font-size:11px; color:#64748b;">
                                Koordinat: <b>${wp.lat.toFixed(3)}, ${wp.lng.toFixed(3)}</b>
                            </div>
                        </div>
                    `, { maxWidth: 290 }).addTo(routeLayerGroup);
                });

                // 3. Destination Marker (🔴 Red Destination Flag)
                const destIcon = L.divIcon({
                    className: 'route-tracker-marker',
                    html: `
                        <div style="background:#F43F5E; color:#ffffff; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:13px; box-shadow:0 0 12px rgba(244,63,94,0.8); border:2.5px solid #ffffff; cursor:pointer;">
                            🏁
                        </div>
                    `,
                    iconSize: [30, 30],
                    iconAnchor: [15, 15],
                    popupAnchor: [0, -16]
                });

                const destMarker = L.marker([r.destination.lat, r.destination.lng], { icon: destIcon });
                destMarker.bindPopup(`
                    <div style="min-width:260px; font-family: Outfit, sans-serif; padding:4px 2px;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                            <span style="background:#F43F5E; color:#fff; font-size:10px; font-weight:800; padding:3px 8px; border-radius:6px; letter-spacing:0.5px;">PELABUHAN TUJUAN (DESTINATION)</span>
                            <span style="font-size:11px; color:#64748b; font-weight:600;">Terminal Bongkar</span>
                        </div>
                        <div style="font-weight:800; font-size:15px; color:#0f172a; line-height:1.2;">${r.destination.name}</div>
                        <div style="font-size:12px; color:#475569; margin-top:2px;">Negara: <b>${r.destination.country}</b></div>
                        <div style="background:#FFF1F2; border:1px solid #FECDD3; border-radius:8px; padding:7px 10px; margin-top:10px; font-size:11px; color:#9F1239; line-height:1.5;">
                            🏁 Estimasi Sandar (ETA): <b>${r.destination.eta}</b><br>
                            📏 Total Jarak Pelayaran: <b>${r.distanceNM} (~${r.distanceKm})</b><br>
                            ⏱️ Estimasi Waktu Tempuh: <b>${r.duration}</b>
                        </div>
                        <div style="margin-top:8px; font-size:11px; color:#059669; font-weight:700;">
                            Status Navigasi: ${r.status}
                        </div>
                    </div>
                `, { maxWidth: 300 }).addTo(routeLayerGroup);
            });
        }

        // Quick Focus to one of the Preset Global Routes
        function focusGlobalRoute(index) {
            if (!globalRoutes[index]) return;
            const r = globalRoutes[index];
            const allCoords = [
                [r.origin.lat, r.origin.lng],
                ...r.waypoints.map(wp => [wp.lat, wp.lng]),
                [r.destination.lat, r.destination.lng]
            ];
            map.fitBounds(L.latLngBounds(allCoords).pad(0.18), { duration: 1.2 });
        }

        // ── BUILD WEATHER LAYER (Weather Pill Badges with Live Temp & Icon: Ref media_1794280421603_3a4f8e13.jpg) ──
        function buildWeatherLayer() {
            weatherLayerGroup.clearLayers();
            weatherData.forEach(w => {
                // Color mapping: Blue (<15°C), Green (15-25°C), Orange (>25°C)
                let pillClass = 'weather-pill-blue';
                if (w.temp >= 15 && w.temp <= 25) {
                    pillClass = 'weather-pill-green';
                } else if (w.temp > 25) {
                    pillClass = 'weather-pill-orange';
                }

                // Custom Leaflet DivIcon showing exact temperature & weather emoji
                const weatherDivIcon = L.divIcon({
                    className: 'weather-pill-marker',
                    html: `
                        <div class="weather-pill-box ${pillClass}">
                            <span>${w.temp}°C</span>
                            <span style="font-size:10px; margin-top:1px;">${w.icon_emoji || '☀️'}</span>
                        </div>
                    `,
                    iconSize: [52, 34],
                    iconAnchor: [26, 17],
                    popupAnchor: [0, -18]
                });

                const weatherDetailUrl = WEATHER_PAGE_URL + '?country=' + encodeURIComponent(w.country);

                const marker = L.marker([w.lat, w.lng], { icon: weatherDivIcon });
                marker.countryCode = (w.code || '').toUpperCase();
                marker.countryName = (w.country || '').toLowerCase();

                marker.bindPopup(`
                    <div style="min-width:240px; font-family: Outfit, sans-serif; padding:4px 2px;">
                        <!-- Country & Region Header with Flag -->
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
                            <img src="https://flagcdn.com/w40/${w.iso || 'id'}.png" alt="${w.country}"
                                 style="width:30px; height:20px; object-fit:cover; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.18); border:1px solid #e2e8f0; flex-shrink:0;">
                            <div>
                                <div style="font-weight:800; font-size:15px; color:#0f172a; line-height:1.2;">${w.country}</div>
                                <div style="font-size:11px; color:#64748b; font-weight:500;">${w.region || 'Global'} &bull; ${w.city}</div>
                            </div>
                        </div>

                        <!-- Risk Pill Box -->
                        <div style="display:flex; align-items:center; justify-content:space-between; background:#FEF3C7; color:#B45309; border:1px solid #FDE68A; padding:7px 12px; border-radius:10px; font-weight:700; font-size:12px; margin-bottom:8px;">
                            <span style="display:flex; align-items:center; gap:6px;">
                                <span>🛡️</span> ${w.risk || 'Medium Risk'}
                            </span>
                            <span style="font-weight:800; color:#D97706;">${w.risk_score || 31} / 100</span>
                        </div>

                        <!-- Weather Info Box -->
                        <div style="display:flex; align-items:center; justify-content:center; gap:8px; background:#F8FAFC; color:#334155; border:1px solid #E2E8F0; padding:8px 12px; border-radius:10px; font-weight:700; font-size:12px; margin-bottom:12px;">
                            <span>🌡️ ${w.temp}°C</span>
                            <span style="color:#cbd5e1; font-weight:400;">|</span>
                            <span>${w.condition}</span>
                        </div>

                        <!-- View Full Profile Button (Guaranteed Absolute Route, No 404!) -->
                        <a href="${weatherDetailUrl}" style="display:block; text-align:center; background:#EDE9FE; color:#7C3AED; font-weight:700; padding:9px 14px; border-radius:10px; text-decoration:none; font-size:12px; transition:all 0.2s; box-shadow:0 1px 3px rgba(124,58,237,0.15);">
                            View Full Profile
                        </a>
                    </div>
                `, { maxWidth: 280 });

                marker.addTo(weatherLayerGroup);
            });
        }

        // ── BUILD PORTS LAYER (ArcGIS FeatureServer Live Ports Web Merata Global) ────────
        function buildPortsLayer() {
            portsLayerGroup.clearLayers();
            portsData.forEach(p => {
                L.circleMarker([p.lat, p.lng], {
                    radius: 6, fillColor: '#8B5CF6', color: '#ffffff', weight: 1.5, fillOpacity: 0.92
                }).bindPopup(`
                    <div style="min-width:240px; font-family: Outfit, sans-serif; padding:4px 2px;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                            <img src="https://flagcdn.com/w40/${p.iso || 'id'}.png" alt="${p.country}"
                                 style="width:30px; height:20px; object-fit:cover; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.18); border:1px solid #e2e8f0; flex-shrink:0;">
                            <div>
                                <div style="font-weight:800; font-size:14px; color:#0f172a; line-height:1.2;">${p.name}</div>
                                <div style="font-size:11px; color:#64748b;">${p.country} &bull; ${p.city}</div>
                            </div>
                        </div>
                        <div style="background:#F3E8FF; color:#6B21A8; border:1px solid #E9D5FF; padding:7px 10px; border-radius:10px; font-weight:700; font-size:11px; margin-bottom:7px;">
                            ⚓ Kode: <b>${p.code}</b> &nbsp;|&nbsp; ${p.type}
                        </div>
                        <div style="background:#F1F5F9; color:#334155; border:1px solid #E2E8F0; padding:7px 10px; border-radius:10px; font-weight:600; font-size:11px; margin-bottom:11px;">
                            📦 Kapasitas: <b>${p.capacity}</b>
                        </div>
                        <a href="${PORTS_PAGE_URL}" style="display:block; text-align:center; background:#EDE9FE; color:#7C3AED; font-weight:700; padding:8px 12px; border-radius:10px; text-decoration:none; font-size:12px; box-shadow:0 1px 3px rgba(124,58,237,0.15);">
                            Cek Direktori Pelabuhan →
                        </a>
                    </div>
                `, { maxWidth: 280 }).addTo(portsLayerGroup);
            });
        }

        // ── SWITCH MODE ─────────────────────────────────────────
        function switchMapMode(mode) {
            currentMode = mode;
            map.removeLayer(cargoLayerGroup);
            map.removeLayer(routeLayerGroup);
            map.removeLayer(weatherLayerGroup);
            map.removeLayer(portsLayerGroup);

            updateModeButtons(mode);
            updateBanner(mode);
            updateLegend(mode);

            try {
                if (mode === 'cargo') {
                    buildCargoLayer();
                    cargoLayerGroup.addTo(map);
                    map.flyTo([10, 110], 3, { duration: 1.2 });
                } else if (mode === 'route') {
                    buildRouteLayer();
                    routeLayerGroup.addTo(map);
                    focusGlobalRoute(0);
                } else if (mode === 'weather') {
                    buildWeatherLayer();
                    weatherLayerGroup.addTo(map);
                    map.flyTo([20, 20], 2.2, { duration: 1.2 });
                } else if (mode === 'ports') {
                    buildPortsLayer();
                    portsLayerGroup.addTo(map);
                    map.flyTo([20, 20], 2.2, { duration: 1.2 });
                }
            } catch (err) {
                console.error("Error switching mode to " + mode + ":", err);
            }
        }

        // ── RESET VIEW ──────────────────────────────────────────
        function resetMapView() {
            map.flyTo([15, 20], 2, { duration: 1.2 });
            document.getElementById('selected-country-card').classList.add('hidden');
            document.getElementById('country-search-input').value = '';
            filterCountriesList();
        }

        // ── FOCUS COUNTRY (from side panel) ─────────────────────
        function focusCountry(el) {
            const lat  = parseFloat(el.dataset.lat);
            const lng  = parseFloat(el.dataset.lng);
            const name = (el.dataset.name || '').trim();
            const code = (el.dataset.code || '').trim().toUpperCase();
            const iso  = el.dataset.iso || code.toLowerCase().replace('_east','');
            const hub  = el.dataset.hub;

            map.flyTo([lat, lng], 5, { duration: 1.5 });

            // Highlight list item
            document.querySelectorAll('.country-item').forEach(li => {
                li.classList.remove('bg-cyan-50','border-l-2','border-cyan-500');
            });
            el.classList.add('bg-cyan-50', 'border-l-2', 'border-cyan-500');

            // Update country card with real flag image
            const flagImg = document.getElementById('selected-flag-img');
            flagImg.src = `https://flagcdn.com/w40/${iso}.png`;
            flagImg.alt = name;
            document.getElementById('selected-name').textContent = name;
            document.getElementById('selected-hub').textContent  = ' ' + hub;
            document.getElementById('selected-country-card').classList.remove('hidden');

            // Auto open matching popup in active mode (Exact Identifier Match, never mistaken!)
            if (currentMode === 'weather') {
                let found = false;
                weatherLayerGroup.eachLayer(layer => {
                    if (!found && layer.countryCode && (layer.countryCode === code || layer.countryName === name.toLowerCase())) {
                        layer.openPopup();
                        found = true;
                    }
                });
                if (!found) {
                    weatherLayerGroup.eachLayer(layer => {
                        const lLatLng = layer.getLatLng();
                        if (!found && Math.hypot(lLatLng.lat - lat, lLatLng.lng - lng) < 2) {
                            layer.openPopup();
                            found = true;
                        }
                    });
                }
            } else if (currentMode === 'ports') {
                let closestPort = null;
                let minDist = 999999;
                portsLayerGroup.eachLayer(layer => {
                    const pLatLng = layer.getLatLng();
                    const dist = Math.hypot(pLatLng.lat - lat, pLatLng.lng - lng);
                    if (dist < minDist) {
                        minDist = dist;
                        closestPort = layer;
                    }
                });
                if (closestPort && minDist < 12) {
                    closestPort.openPopup();
                }
            } else if (currentMode === 'route') {
                let closestRouteMarker = null;
                let minRDist = 999999;
                routeLayerGroup.eachLayer(layer => {
                    if (layer.getLatLng) {
                        const dist = Math.hypot(layer.getLatLng().lat - lat, layer.getLatLng().lng - lng);
                        if (dist < minRDist) {
                            minRDist = dist;
                            closestRouteMarker = layer;
                        }
                    }
                });
                if (closestRouteMarker && minRDist < 10) {
                    closestRouteMarker.openPopup();
                }
            }
        }

        // ── FILTER COUNTRY LIST ─────────────────────────────────
        function filterCountriesList() {
            const q = document.getElementById('country-search-input').value.toLowerCase();
            document.querySelectorAll('.country-item').forEach(li => {
                li.style.display = li.dataset.name.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        // Expose functions to window immediately so onclick="" attributes can always reach them
        window.switchMapMode    = switchMapMode;
        window.resetMapView     = resetMapView;
        window.focusCountry     = focusCountry;
        window.focusGlobalRoute = focusGlobalRoute;
        window.filterCountriesList = filterCountriesList;

        // ── INIT ────────────────────────────────────────────────
        try {
            buildCargoLayer();
            cargoLayerGroup.addTo(map);
        } catch (e) {
            console.error("Error initial building cargo layer:", e);
        }
        updateModeButtons('cargo');
        updateBanner('cargo');
        updateLegend('cargo');
    });
</script>
@endpush

