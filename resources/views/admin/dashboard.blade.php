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
    /* Highlight & Focus Styles on Click */
    .cargo-real-vehicle-wrapper.vehicle-focused {
        filter: drop-shadow(0 0 16px rgba(0, 240, 255, 0.95)) drop-shadow(0 8px 20px rgba(0, 0, 0, 0.7)) !important;
        transform: scale(1.3) !important;
        z-index: 99999 !important;
    }
    .cargo-vehicle-tag.tag-focused {
        background: rgba(2, 132, 199, 0.95) !important;
        border-color: #38bdf8 !important;
        box-shadow: 0 0 12px rgba(56, 189, 248, 0.9) !important;
        font-weight: 800 !important;
    }
    .cargo-real-vehicle-marker.marker-dimmed {
        opacity: 0.2 !important;
        filter: grayscale(80%) !important;
        transition: opacity 0.3s ease, filter 0.3s ease;
    }

    /* Clean Circular Country Nodes for Route Trace (Ref Screenshot 1: media_1794417156910_bd4e5b31.jpg) */
    .country-trace-node {
        transition: transform 0.2s ease, fill 0.2s ease;
        cursor: pointer;
    }
    .country-trace-node:hover {
        fill: #38bdf8 !important;
        stroke: #ffffff !important;
        stroke-width: 2.5px !important;
    }

    /* Route Tracker Markers (Ref Screenshot 2: media_1794417172832_e6df78b9.jpg) */
    .route-node-pin {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 9px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.35);
        cursor: pointer;
        transition: transform 0.2s ease;
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }
    .route-node-pin:hover {
        transform: scale(1.2);
    }
    .route-node-origin {
        background: #10b981;
        border: 2.5px solid #ffffff;
        box-shadow: 0 0 14px rgba(16, 185, 129, 0.85);
    }
    .route-node-dest {
        background: #ef4444;
        border: 2.5px solid #ffffff;
        box-shadow: 0 0 16px rgba(239, 68, 68, 0.9);
        animation: dest-pulse 2s infinite;
    }
    @keyframes dest-pulse {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
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
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Total Pengiriman') }}</p>
                <h3 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ number_format($totalShipments) }}</h3>
                <span class="text-[10px] text-cyan-600 dark:text-cyan-400 font-medium mt-0.5 block">{{ __('Seluruh Kargo Aktif & Arsip') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center border border-blue-100 dark:border-blue-900">
                <i data-lucide="package" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 2. In Transit -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Dalam Proses') }}</p>
                <h3 class="text-2xl font-extrabold text-blue-600 dark:text-cyan-400 mt-1">{{ number_format($inTransit) }}</h3>
                <span class="text-[10px] text-slate-400 font-medium mt-0.5 block">{{ __('Sedang Bergerak') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center border border-cyan-100 dark:border-cyan-900">
                <i data-lucide="navigation" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 3. Delivered -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Terkirim') }}</p>
                <h3 class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($delivered) }}</h3>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-500 font-medium mt-0.5 block">{{ __('Selesai di Tujuan') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center border border-emerald-100 dark:border-emerald-900">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 4. Delayed -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Terlambat') }}</p>
                <h3 class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">{{ number_format($delayed) }}</h3>
                <span class="text-[10px] text-amber-600 dark:text-amber-500 font-medium mt-0.5 block">{{ __('> Batas Waktu ETA') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center border border-amber-100 dark:border-amber-900">
                <i data-lucide="clock-alert" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- 5. Open Exceptions -->
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between col-span-2 md:col-span-1">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('Pengecualian') }}</p>
                <h3 class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1">{{ number_format($openExceptions) }}</h3>
                <span class="text-[10px] text-rose-600 dark:text-rose-400 font-medium mt-0.5 block">{{ __('Membutuhkan Tindakan') }}</span>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center border border-rose-100 dark:border-rose-900">
                <i data-lucide="alert-octagon" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Ringkasan Finansial Ekspor Santos & Omset Freight Logistik (Benang Merah B2B) -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 rounded-2xl p-5 shadow-sm text-white">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-xl font-bold">
                    ☕
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">Ikhtisar Finansial Ekspor Kopi Santos (B2B)</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">T/T Wire Settled</span>
                    </div>
                    <p class="text-xs text-slate-300 mt-0.5">Pemantauan nilai komoditas biji kopi Brazil Santos & omset freight logistik global</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-6 text-xs">
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Total Nilai Komoditas (FOB)</span>
                    <span class="font-mono text-base font-extrabold text-white">${{ number_format($totalCommodityValue, 2) }}</span>
                </div>
                <div class="hidden sm:block h-8 w-px bg-slate-800"></div>
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Pendapatan Freight Terkumpul</span>
                    <span class="font-mono text-base font-extrabold text-cyan-400">${{ number_format($totalFreightRevenue, 2) }}</span>
                </div>
                <div class="hidden sm:block h-8 w-px bg-slate-800"></div>
                <div>
                    <span class="text-[10px] uppercase tracking-wider text-slate-400 block">Tingkat Settlement Proforma</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="font-mono text-base font-extrabold text-emerald-400">{{ $settlementRate }}%</span>
                        <span class="text-[10px] text-slate-400">({{ $paidInvoicesCount }}/{{ $totalShipments }} Lunas)</span>
                    </div>
                </div>
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
                <button onclick="switchMapMode('airports')" id="btn-airports"
                    class="map-mode-btn px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all duration-200 flex items-center gap-1.5">
                    ✈️ Sebaran Bandara
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
                <!-- Active Mode Banner (Standard for Cargo, Weather, Ports, Airports) -->
                <div id="map-mode-banner" class="px-4 py-2 text-xs font-semibold text-white flex items-center justify-between transition-all duration-300" style="background:linear-gradient(90deg,#0A1128,#0e3a6e);">
                    <div class="flex items-center gap-2">
                        <span id="banner-icon">🧭</span>
                        <span id="banner-text">Mode: Pemantau CargoTrace — Kargo Aktif & Jalur Pengiriman</span>
                    </div>
                    <!-- Sub-filter moda pengiriman & benua tujuan di mode CargoTrace -->
                    <div id="cargo-mode-subfilter" class="flex flex-wrap items-center gap-1.5">
                        <div class="flex items-center gap-1 bg-slate-900/80 p-0.5 rounded-lg border border-slate-700/80">
                            <button type="button" onclick="setCargoTransportFilter('ALL')" id="filter-cargo-all" class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-600 text-white transition-all">Semua</button>
                            <button type="button" onclick="setCargoTransportFilter('SEA')" id="filter-cargo-sea" class="px-2 py-0.5 rounded text-[10px] font-semibold text-slate-300 hover:text-white transition-all">🚢 Laut</button>
                            <button type="button" onclick="setCargoTransportFilter('AIR')" id="filter-cargo-air" class="px-2 py-0.5 rounded text-[10px] font-semibold text-slate-300 hover:text-white transition-all">✈️ Udara</button>
                            <button type="button" onclick="setCargoTransportFilter('LAND')" id="filter-cargo-land" class="px-2 py-0.5 rounded text-[10px] font-semibold text-slate-300 hover:text-white transition-all">🚚 Mobil / Dekat</button>
                        </div>
                        <select id="filter-cargo-continent" onchange="setCargoContinentFilter(this.value)" title="Filter Benua Tujuan"
                            class="bg-slate-900/90 text-white text-[10px] font-semibold px-2 py-1 rounded-lg border border-slate-700/80 focus:outline-none focus:ring-1 focus:ring-cyan-400 cursor-pointer">
                            <option value="ALL">🌍 Semua Benua</option>
                            <option value="ASIA">🌏 Asia</option>
                            <option value="EUROPE">🌍 Eropa</option>
                            <option value="AMERICAS">🌎 Amerika</option>
                            <option value="AFRICA">🌍 Afrika</option>
                            <option value="OCEANIA">🌏 Oseania</option>
                        </select>
                    </div>
                </div>

                <!-- ROUTE TRACKER 2-STEP INTERACTIVE BANNER (Ref Screenshot 1 & 2: media_1794417156910_bd4e5b31.jpg & media_1794417172832_e6df78b9.jpg) -->
                <div id="route-step-banner" class="hidden px-4 py-2 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs transition-all duration-200">
                    <!-- Kiri: Mode Switcher Pelabuhan Laut vs Bandara Udara -->
                    <div class="flex items-center gap-1 p-0.5 bg-slate-100 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                        <button type="button" id="trace-btn-port" onclick="setTraceTransportMode('PORT')"
                            class="px-2.5 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-slate-900 text-sky-600 dark:text-sky-400 shadow-xs flex items-center gap-1">
                            <span>⚓ Pelabuhan (Laut)</span>
                        </button>
                        <button type="button" id="trace-btn-air" onclick="setTraceTransportMode('AIR')"
                            class="px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1">
                            <span>✈️ Bandara (Udara)</span>
                        </button>
                    </div>

                    <!-- Tengah: Status Teks Interaktif Persis Screenshot (Step 1 -> Step 2 -> Route Analyzed) -->
                    <div class="text-center font-bold text-xs sm:text-sm tracking-tight" id="route-step-text-wrap">
                        <span id="route-step-text" class="text-purple-600 dark:text-purple-400 font-bold">Step 1: Click Origin Country on map</span>
                    </div>

                    <!-- Kanan: Tombol Reset Persis Screenshot -->
                    <div>
                        <button type="button" onclick="resetRouteTrackerInteractive()"
                            class="px-3.5 py-1 rounded-lg text-xs font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-xs">
                            Reset
                        </button>
                    </div>
                </div>

                <!-- Leaflet Map -->
                <div id="dashboard-map" class="flex-1 z-10" style="min-height:460px"></div>
                <!-- Legend -->
                <div id="map-legend" class="flex items-center gap-4 px-4 py-2 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-800/60 text-[11px] text-slate-500 dark:text-slate-400 flex-wrap">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block border border-white"></span>Hub Asal (Santos Brazil)</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block border border-white"></span>Pelabuhan / Bandara Transit</span>
                    <span class="flex items-center gap-1.5"><span class="text-sm leading-none">📍</span>Alamat Pengantaran Mobil (Last-Mile)</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-cyan-600 inline-flex items-center justify-center text-[9px] text-white">🚢</span>Kapal Laut</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-sky-500 inline-flex items-center justify-center text-[9px] text-white">✈️</span>Pesawat Udara</span>
                    <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-amber-500 inline-flex items-center justify-center text-[9px] text-white">🚚</span>Mobil Paket</span>
                    <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 bg-cyan-500 inline-block"></span>Jalur Ditempuh</span>
                    <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 border-b border-dashed border-cyan-400 inline-block"></span>Estimasi Sisa Rute</span>
                    <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 border-b border-dashed border-amber-500 inline-block"></span>Rute Mobil Kurir</span>
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
                    <div id="selected-carrier-wrapper" class="text-slate-600 dark:text-slate-400 mt-1 pt-1 border-t border-cyan-100 dark:border-cyan-900/60 hidden">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Ekspedisi:</span>
                        <span id="selected-carrier" class="font-bold text-amber-700 dark:text-amber-400"></span>
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

    <!-- Modal Detail Analisis Rute (View Analytics Modal) -->
    <div id="route-analytics-modal" onclick="if (event.target === this) closeRouteAnalyticsModal()" class="fixed inset-0 z-[99999] hidden overflow-y-auto bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4" style="display: none;">
        <div class="relative w-full max-w-2xl bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-2xl overflow-hidden p-6 space-y-5 animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold">
                        <i data-lucide="bar-chart-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white" id="modal-route-title">Analisis Koridor Logistik Global</h3>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400" id="modal-route-subtitle">Perhitungan multi-faktor rute pengiriman antar negara</p>
                    </div>
                </div>
                <button type="button" onclick="closeRouteAnalyticsModal()" class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-slate-800 dark:hover:text-white flex items-center justify-center transition-colors">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <!-- Origin & Destination Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <!-- Origin Node Card -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            TITIK ASAL (ORIGIN)
                        </span>
                        <span id="modal-origin-code" class="text-xs font-mono font-bold text-slate-400">IDN</span>
                    </div>
                    <div class="flex items-center gap-2.5 mb-1.5">
                        <img id="modal-origin-flag" src="" alt="" class="w-7 h-5 object-cover rounded border border-slate-200 dark:border-slate-700 shadow-xs">
                        <span id="modal-origin-country" class="font-bold text-sm text-slate-900 dark:text-white">Indonesia</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium" id="modal-origin-hub">Pelabuhan Tanjung Priok</p>
                    <p class="text-[10px] text-slate-400 font-mono mt-1" id="modal-origin-coords">-6.102, 106.882</p>
                </div>

                <!-- Destination Node Card -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                            TITIK TUJUAN (DESTINATION)
                        </span>
                        <span id="modal-dest-code" class="text-xs font-mono font-bold text-slate-400">NLD</span>
                    </div>
                    <div class="flex items-center gap-2.5 mb-1.5">
                        <img id="modal-dest-flag" src="" alt="" class="w-7 h-5 object-cover rounded border border-slate-200 dark:border-slate-700 shadow-xs">
                        <span id="modal-dest-country" class="font-bold text-sm text-slate-900 dark:text-white">Belanda</span>
                    </div>
                    <p class="text-xs text-slate-600 dark:text-slate-300 font-medium" id="modal-dest-hub">Port of Rotterdam</p>
                    <p class="text-[10px] text-slate-400 font-mono mt-1" id="modal-dest-coords">51.924, 4.477</p>
                </div>
            </div>

            <!-- Route Metrics Matrix -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Moda Kargo</span>
                    <span id="modal-mode-badge" class="text-xs font-bold text-cyan-600 dark:text-cyan-400 mt-0.5 block">⚓ Pelabuhan Laut</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Total Jarak</span>
                    <span id="modal-distance" class="text-xs font-bold text-slate-800 dark:text-white mt-0.5 block">8,920 km (4,816 NM)</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Estimasi Waktu</span>
                    <span id="modal-duration" class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 block">14 - 18 Hari</span>
                </div>
                <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-900/40 border border-slate-200/80 dark:border-slate-800 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider block">Kecepatan Armada</span>
                    <span id="modal-speed" class="text-xs font-bold text-slate-800 dark:text-white mt-0.5 block">20 Knots (~37 km/h)</span>
                </div>
            </div>

            <!-- Transit Risk Analysis Section -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 dark:text-white flex items-center gap-1.5">
                        <i data-lucide="shield-alert" class="w-4 h-4 text-amber-500"></i>
                        <span>Kalkulasi Skor Transit Risk</span>
                    </span>
                    <span id="modal-risk-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold text-white bg-emerald-500">
                        Transit Risk: 25/100 (Safe Route)
                    </span>
                </div>
                <!-- Risk Progress Bar -->
                <div class="w-full bg-slate-200 dark:bg-slate-700 h-2 rounded-full overflow-hidden">
                    <div id="modal-risk-bar" class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: 25%"></div>
                </div>
                <p id="modal-risk-description" class="text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed">
                    Koridor transit ini dikategorikan aman dengan risiko rendah. Tidak terdeteksi zona konflik maritim berstatus bahaya tinggi atau pembajakan aktif.
                </p>
                <div class="pt-2 border-t border-slate-200/80 dark:border-slate-800/80 flex items-center justify-between text-[11px] flex-wrap gap-2">
                    <span class="text-slate-400">Koridor Dilalui: <strong id="modal-corridor" class="text-slate-700 dark:text-slate-300">Selat Malaka ➔ Laut Arab ➔ Terusan Suez</strong></span>
                    <span class="text-purple-600 dark:text-purple-400 font-semibold" id="modal-insurance-rec">🛡️ Proteksi Asuransi Standar AAR Direkomendasikan</span>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeRouteAnalyticsModal()" class="px-4 py-2 rounded-xl text-xs font-semibold border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    Tutup
                </button>
                <a id="modal-order-btn" href="{{ route('admin.shipments.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 transition-colors shadow-sm flex items-center gap-1.5">
                    <span>Pesan Pengiriman Rute Ini</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.maritime-route-helper')
<script>
    // Global function registry so onclick buttons work immediately even if clicked before ready
    window.switchMapMode                 = window.switchMapMode || function(mode) { console.log('Init pending...', mode); };
    window.resetMapView                  = window.resetMapView || function() {};
    window.focusCountry                  = window.focusCountry || function(el) {};
    window.focusGlobalRoute              = window.focusGlobalRoute || function(id) {};
    window.filterCountriesList           = window.filterCountriesList || function() {};
    window.focusCargoShipment            = window.focusCargoShipment || function(id) {};
    window.resetShipmentFocus            = window.resetShipmentFocus || function() {};
    window.setTraceTransportMode         = window.setTraceTransportMode || function(mode) {};
    window.setCargoTransportFilter       = window.setCargoTransportFilter || function(filter) {};
    window.setCargoContinentFilter       = window.setCargoContinentFilter || function(continent) {};
    window.resetRouteTrackerInteractive  = window.resetRouteTrackerInteractive || function() {};
    window.openRouteAnalyticsModal       = window.openRouteAnalyticsModal || function() {};
    window.closeRouteAnalyticsModal      = window.closeRouteAnalyticsModal || function() {};

    function initDashboard() {
        if (window.__cargoTraceDashboardInitialized) return;

        // If Leaflet library is still loading asynchronously from CDN, poll for it briefly
        if (typeof L === 'undefined') {
            if (!window.__leafletWaitAttempts) window.__leafletWaitAttempts = 0;
            if (window.__leafletWaitAttempts < 50) {
                window.__leafletWaitAttempts++;
                setTimeout(initDashboard, 100);
                return;
            }
        }

        window.__cargoTraceDashboardInitialized = true;

        // 1. Donut Chart - Shipment by Status (Protected with safe checks)
        const statusEl = document.getElementById('statusChart');
        if (statusEl && typeof Chart !== 'undefined') {
            try {
                const statusCtx = statusEl.getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Dalam Proses', 'Terkirim', 'Dipesan', 'Dijemput', 'Dibuat'],
                        datasets: [{
                            data: [
                                {{ $statusCounts['IN_TRANSIT'] ?? 0 }},
                                {{ $statusCounts['DELIVERED'] ?? 0 }},
                                {{ $statusCounts['BOOKED'] ?? 0 }},
                                {{ $statusCounts['PICKED_UP'] ?? 0 }},
                                {{ $statusCounts['CREATED'] ?? 0 }}
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
            } catch (err) {
                console.warn('Status chart initialization error:', err);
            }
        }

        // 2. Line Chart - 30 Days Trend (Protected with safe checks)
        const trendEl = document.getElementById('trendChart');
        if (trendEl && typeof Chart !== 'undefined') {
            try {
                const trendCtx = trendEl.getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($trendDates ?? []) !!},
                        datasets: [{
                            label: 'Volume Pengiriman',
                            data: {!! json_encode($trendValues ?? []) !!},
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
            } catch (err) {
                console.warn('Trend chart initialization error:', err);
            }
        }

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

        // Pasang OpenStreetMap sebagai layer default yang 100% bebas watermark & reliabel
        osm.addTo(map);

        // Resize trigger untuk memastikan peta ter-render utuh & tidak blank putih
        setTimeout(function() {
            if (map) {
                map.invalidateSize();
            }
        }, 250);

        // Layer Switcher Control di sudut kanan atas peta
        const baseMaps = {
            "🗺️ Google Maps (Jalan & Toko/POI)": googleStreets,
            "🛰️ Satelit Hybrid": googleHybrid,
            "🌍 OpenStreetMap": osm
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        // Ensure popup buttons can be clicked reliably inside Leaflet without event interference
        map.on('popupopen', function(e) {
            const popupEl = e.popup ? e.popup.getElement() : null;
            if (popupEl) {
                L.DomEvent.disableClickPropagation(popupEl);
                const btn = popupEl.querySelector('#btn-view-route-analytics');
                if (btn) {
                    L.DomEvent.disableClickPropagation(btn);
                    btn.onclick = function(ev) {
                        if (ev) {
                            ev.preventDefault();
                            ev.stopPropagation();
                        }
                        openRouteAnalyticsModal();
                    };
                }
            }
        });

        // Data from PHP controller
        const shipments    = {!! json_encode($mapShipments) !!};
        const portsData    = {!! json_encode($globalPorts) !!};
        const airportsData = {!! json_encode($globalAirports ?? []) !!};
        const weatherData  = {!! json_encode($weatherHubs) !!};
        const countriesData = {!! json_encode($countriesList) !!};
        const carriersData = {!! json_encode($carriersMapData) !!};

        // Guaranteed Absolute Route URLs direct from Laravel Blade (No 404s!)
        const WEATHER_PAGE_URL = "{{ route('admin.intelligence.weather') }}";
        const PORTS_PAGE_URL   = "{{ route('admin.intelligence.ports') }}";
        const AIRPORTS_PAGE_URL = "{{ route('admin.intelligence.airports') }}";
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
        const cargoLayerGroup    = L.layerGroup();
        const routeLayerGroup    = L.layerGroup();
        const weatherLayerGroup  = L.layerGroup();
        const portsLayerGroup    = L.layerGroup();
        const airportsLayerGroup = L.layerGroup();

        let currentMode = 'cargo';
        let cargoTransportFilter = 'ALL';
        let cargoContinentFilter = 'ALL';

        function getDestinationContinent(s) {
            const c = (s.destination?.country || '').toLowerCase().trim();
            if (/australia|new zealand|selandia baru|fiji|papua/.test(c)) return 'OCEANIA';
            if (/nigeria|kenya|morocco|maroko|south africa|afrika selatan|egypt|mesir|ethiopia|ghana|tanzania|senegal|djibouti|algeria|aljazair|tunisia/.test(c)) return 'AFRICA';
            if (/belanda|netherlands|inggris|united kingdom|\buk\b|jerman|germany|prancis|france|italia|italy|spanyol|spain|poland|polandia|sweden|swedia|norway|norwegia|finland|finlandia|greece|yunani|portugal|belgia|belgium|swiss|switzerland|austria|denmark|ireland|irlandia|turkey|turki|russia|rusia|ukraine|ukraina/.test(c)) return 'EUROPE';
            if (/amerika|united states|\busa\b|canada|kanada|mexico|meksiko|brasil|brazil|chile|chili|colombia|kolombia|argentina|peru|panama|ecuador|ekuador/.test(c)) return 'AMERICAS';
            if (/indonesia|singap|malaysia|thailand|vietnam|philippines|filipina|japan|jepang|korea|china|tiongkok|india|pakistan|bangladesh|sri lanka|kazakhstan|taiwan|hong kong|saudi|uae|emirat|qatar|oman|kuwait|jordan|yordania|israel|iran|iraq|irak/.test(c)) return 'ASIA';

            const lat = parseFloat(s.destination?.latitude);
            const lng = parseFloat(s.destination?.longitude);
            if (!isNaN(lat) && !isNaN(lng)) {
                if (lng < -30) return 'AMERICAS';
                if (lat < -10 && lng > 110) return 'OCEANIA';
                if (lat >= 35 && lng >= -15 && lng <= 45) return 'EUROPE';
                if (lat < 35 && lat >= -35 && lng >= -20 && lng <= 52) return 'AFRICA';
            }
            return 'ASIA';
        }

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
                'cargo':    { id: 'btn-cargo',    bg:'#0e6ba8', border:'#0e6ba8' },
                'route':    { id: 'btn-route',    bg:'#0284c7', border:'#0284c7' },
                'weather':  { id: 'btn-weather',  bg:'#d97706', border:'#d97706' },
                'ports':    { id: 'btn-ports',    bg:'#7c3aed', border:'#7c3aed' },
                'airports': { id: 'btn-airports', bg:'#0284c7', border:'#0284c7' }
            };
            if (activeMap[mode]) {
                const btn = document.getElementById(activeMap[mode].id);
                if (btn) {
                    btn.classList.remove('border-slate-300','text-slate-500');
                    btn.style.backgroundColor = activeMap[mode].bg;
                    btn.style.borderColor = activeMap[mode].border;
                    btn.style.color = '#fff';
                }
            }
        }

        // ── BANNER UPDATE ───────────────────────────────────────
        const banners = {
            cargo:    { icon:'🧭', text:'Mode: Pemantau CargoTrace — Kargo Aktif & Jalur Pengiriman Global', bg:'linear-gradient(90deg,#0A1128,#0e3a6e)' },
            route:    { icon:'📍', text:'Mode: Route Tracker — Pelacakan Rute Maritim Global Interaktif & Titik Waypoint Antar Benua', bg:'linear-gradient(90deg,#0c4a6e,#0284c7)' },
            weather:  { icon:'⛅', text:'Mode: Cuaca Langsung — 60+ Titik Cuaca Maritim Terkalibrasi & Evaluasi Risiko Badai', bg:'linear-gradient(90deg,#431407,#92400e)' },
            ports:    { icon:'⚓', text:'Mode: Sebaran Pelabuhan — Data Pelabuhan Maritim Dunia Merata Global (ArcGIS Live WPI FeatureServer)', bg:'linear-gradient(90deg,#2e1065,#5b21b6)' },
            airports: { icon:'✈️', text:'Mode: Sebaran Bandara Kargo Global — 60+ Hub Kargo Udara Internasional (FreeAirportDB Live)', bg:'linear-gradient(90deg,#075985,#0284c7)' }
        };
        function updateBanner(mode) {
            const b = banners[mode];
            const defaultBanner = document.getElementById('map-mode-banner');
            const routeStepBanner = document.getElementById('route-step-banner');

            if (mode === 'route') {
                if (defaultBanner) defaultBanner.classList.add('hidden');
                if (routeStepBanner) routeStepBanner.classList.remove('hidden');
            } else {
                if (routeStepBanner) routeStepBanner.classList.add('hidden');
                if (defaultBanner) {
                    defaultBanner.classList.remove('hidden');
                    document.getElementById('banner-icon').textContent = b.icon;
                    document.getElementById('banner-text').textContent = b.text;
                    defaultBanner.style.background = b.bg;
                }
            }
        }

        // ── LEGEND UPDATE ───────────────────────────────────────
        const legends = {
            cargo:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block border border-white"></span>Hub Asal (Santos Brazil)</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block border border-white"></span>Pelabuhan / Bandara Transit</span>
                      <span class="flex items-center gap-1.5"><span class="text-sm leading-none">📍</span>Alamat Pengantaran Mobil (Last-Mile)</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-cyan-600 inline-flex items-center justify-center text-[9px] text-white">🚢</span>Kapal Laut</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-sky-500 inline-flex items-center justify-center text-[9px] text-white">✈️</span>Pesawat Udara</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-amber-500 inline-flex items-center justify-center text-[9px] text-white">🚚</span>Mobil Paket</span>
                      <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 bg-cyan-500 inline-block"></span>Jalur Ditempuh</span>
                      <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 border-b border-dashed border-cyan-400 inline-block"></span>Estimasi Sisa Rute</span>
                      <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 border-b border-dashed border-amber-500 inline-block"></span>Rute Mobil Kurir</span>`,
            route:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block border border-white"></span>Titik Asal</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block border border-white"></span>Titik Tujuan</span>
                      <span class="flex items-center gap-1.5"><span class="w-5 h-0.5 border-b border-dashed border-emerald-500 inline-block"></span>Jalur Terkoneksi</span>
                      <span class="flex items-center gap-1.5 text-slate-400">Pilih dua titik negara di peta atau panel pencarian</span>`,
            weather: `<span class="flex items-center gap-1.5"><span class="w-3.5 h-2.5 rounded bg-blue-600 inline-block border border-white"></span>Dingin (&lt;15°C)</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-2.5 rounded bg-emerald-600 inline-block border border-white"></span>Sejuk (15–25°C)</span>
                      <span class="flex items-center gap-1.5"><span class="w-3.5 h-2.5 rounded bg-orange-600 inline-block border border-white"></span>Hangat (&gt;25°C)</span>
                      <span class="flex items-center gap-1.5 text-slate-400">Pill cuaca tiap negara (klik untuk detail profil)</span>`,
            ports:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-purple-600 inline-block"></span>Pelabuhan ArcGIS Live WPI</span>
                      <span class="flex items-center gap-1.5 text-slate-400">Klik marker untuk melihat profil pelabuhan</span>`,
            airports: `<span class="flex items-center gap-1.5"><span class="w-3.5 h-3.5 rounded-full bg-sky-600 text-white inline-flex items-center justify-center text-[9px]">✈️</span>Bandara Kargo FreeAirportDB</span>
                       <span class="flex items-center gap-1.5 text-slate-400">Klik marker untuk melihat profil bandara & kapasitas</span>`
        };
        function updateLegend(mode) {
            document.getElementById('map-legend').innerHTML = legends[mode];
        }

        // ── HELPER: CURVED PATH GENERATOR (Natural Geodesic Arc with Lane Separation) ───
        function createCurvedPath(p1, p2, numPoints = 20, curvature = 0.12, laneShift = 0) {
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

            // Arc elevation with lane separation so parallel shipping lines don't stack
            const arcHeight = Math.min(24, Math.max(1.5, (dist * curvature) + laneShift));
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

        // ── HELPER: HITUNG PROGRES REALTIME SESUAI JADWAL BERANGKAT & ESTIMASI TIBA ──
        function getShipmentRealtimeProgress(s) {
            const now = Date.now();
            const depTime = s.actual_departure ? new Date(s.actual_departure).getTime() : (s.expected_departure ? new Date(s.expected_departure).getTime() : null);
            const arrTime = s.estimated_time_arrival ? new Date(s.estimated_time_arrival).getTime() : (s.expected_arrival ? new Date(s.expected_arrival).getTime() : null);

            if (s.status === 'DELIVERED') {
                return 1.0;
            }

            if (depTime && arrTime && arrTime > depTime) {
                const frac = (now - depTime) / (arrTime - depTime);
                if (frac >= 0.08 && frac <= 0.95) {
                    return frac;
                }
            }

            // Fallback progres acak realistis untuk armada yang sedang dalam perjalanan
            if (s.status === 'IN_TRANSIT') {
                const seed = (s.id * 37 + (s.shipment_number ? s.shipment_number.charCodeAt(s.shipment_number.length - 1) : 10));
                const pct = ((seed % 55) + 25); // 25% s/d 80%
                return pct / 100;
            }

            if (s.status === 'PICKED_UP' || s.status === 'BOOKED') {
                const seed = (s.id * 19);
                const pct = ((seed % 15) + 12); // 12% s/d 27% (tahap lepas landas / keberangkatan)
                return pct / 100;
            }

            return 0.35;
        }

        // ── HELPER: INTERPOLASI POSISI DI KORIDOR RUTE SESUAI PROGRES ──
        function getPositionOnRoute(originPt, destPt, progress, curvature = 0.12, laneShift = 0) {
            const numPoints = 50;
            const path = createCurvedPath(originPt, destPt, numPoints, curvature, laneShift);
            const idx = Math.min(numPoints, Math.max(0, Math.round(progress * numPoints)));
            return path[idx] || originPt;
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

            const modeRaw = (s.transport_mode || '').toUpperCase();

            // 0. Pesawat Kargo Udara (Real Jet Freighter Top-Down)
            // Jika transport_mode eksplisit 'SEA', wajib tetap tampil sebagai Kapal Laut
            if (modeRaw === 'AIR' || (!modeRaw && (originType === 'AIRPORT' || destType === 'AIRPORT' ||
                originName.includes('bandara') || originName.includes('airport') ||
                destName.includes('bandara') || destName.includes('airport') ||
                carrierCode === 'FDX' || carrierName.includes('fedex') || carrierName.includes('express air') ||
                cargo.includes('avionics') || cargo.includes('vaccine') || cargo.includes('pharmaceutical')))) {
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

            // 1. Truk / Mobil Paket Domestik (Hanya untuk pengiriman darat dalam satu negara)
            if (s.transport_mode === 'LAND' && (s.origin?.country && s.destination?.country && s.origin.country === s.destination.country)) {
                return {
                    type: 'van',
                    label: 'Mobil Pengantaran Kurir (Last-Mile Delivery)',
                    emoji: '🚚',
                    accentColor: '#f59e0b',
                    iconW: 36,
                    iconH: 52,
                    realSvg: `
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 64" width="28" height="48" style="filter: drop-shadow(0 6px 10px rgba(0,0,0,0.55));">
                            <rect x="5" y="4" width="26" height="56" rx="4" fill="#0f172a" opacity="0.3" transform="translate(1, 2)"/>
                            <rect x="5" y="6" width="26" height="52" rx="4" fill="#f59e0b" stroke="#d97706" stroke-width="1.2"/>
                            <rect x="7" y="7" width="22" height="15" rx="2" fill="#1e293b"/>
                            <path d="M8 9 Q18 7 28 9 L27 15 Q18 13 9 15 Z" fill="#38bdf8"/>
                            <rect x="4" y="11" width="2.5" height="4" rx="1" fill="#0f172a"/>
                            <rect x="29.5" y="11" width="2.5" height="4" rx="1" fill="#0f172a"/>
                            <rect x="7" y="24" width="22" height="32" rx="2" fill="#fbbf24" stroke="#d97706" stroke-width="0.8"/>
                            <text x="18" y="40" font-size="6" font-weight="bold" fill="#78350f" text-anchor="middle" font-family="sans-serif">SANTOS</text>
                            <text x="18" y="48" font-size="4.5" font-weight="bold" fill="#92400e" text-anchor="middle" font-family="sans-serif">☕ COFFEE</text>
                            <rect x="4" y="18" width="2" height="7" rx="0.5" fill="#0f172a"/>
                            <rect x="30" y="18" width="2" height="7" rx="0.5" fill="#0f172a"/>
                            <rect x="4" y="48" width="2" height="7" rx="0.5" fill="#0f172a"/>
                            <rect x="30" y="48" width="2" height="7" rx="0.5" fill="#0f172a"/>
                        </svg>
                    `
                };
            }

            // 2. Default: Kapal Kontainer Samudera (Real Cargo Container Vessel Top-Down)
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

        // ── HELPER: GET SHIPMENT ESTIMATED TIME OF ARRIVAL (ETA) ─
        function getShipmentETA(s) {
            const rawEta = s.estimated_time_arrival || s.expected_arrival;
            if (!rawEta) {
                return {
                    formatted: 'Menunggu Jadwal Navigasi',
                    countdown: 'Jadwal diperbarui berkala',
                    statusHtml: '<span style="color:#64748b;">Jadwal Terjadwal</span>',
                    delayMin: 0
                };
            }

            const etaDate = new Date(rawEta);
            const isValid = !isNaN(etaDate.getTime());
            let formatted = rawEta;
            let countdown = '';

            if (isValid) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const d = etaDate.getDate();
                const m = months[etaDate.getMonth()];
                const y = etaDate.getFullYear();
                const hh = String(etaDate.getHours()).padStart(2, '0');
                const mm = String(etaDate.getMinutes()).padStart(2, '0');
                formatted = `${d} ${m} ${y}, ${hh}:${mm} WIB`;

                const now = new Date();
                const diffMs = etaDate.getTime() - now.getTime();
                if (diffMs > 0) {
                    const totalHours = Math.floor(diffMs / (1000 * 60 * 60));
                    const days = Math.floor(totalHours / 24);
                    const hours = totalHours % 24;
                    if (days > 0) {
                        countdown = `⏳ Sisa ~${days} hari ${hours} jam lagi`;
                    } else {
                        countdown = `⏳ Sisa ~${hours} jam lagi`;
                    }
                } else {
                    countdown = '🏁 Tiba di tujuan / proses sandar';
                }
            }

            const delayMin = parseInt(s.delay_minutes) || 0;
            let statusHtml = '';
            if (delayMin > 0) {
                const dHours = Math.floor(delayMin / 60);
                statusHtml = `<span style="color:#ef4444; font-weight:800;">⚠️ Terlambat (+${dHours} jam)</span>`;
            } else {
                statusHtml = `<span style="color:#10b981; font-weight:800;">✅ Tepat Waktu (On Schedule)</span>`;
            }

            return { formatted, countdown, statusHtml, delayMin };
        }

        // ── HELPER: POPUP CONTENT WITH RICH ROUTE & ETA ─────────
        function generateShipmentPopupContent(s, vehicle, eta, curLoc, progress = 0.45) {
            const isAir = (s.transport_mode === 'AIR');
            const isLand = (s.transport_mode === 'LAND') || !!s.is_last_mile;
            const pct = isLand ? 95 : Math.round(progress * 100);
            const activeEmoji = isLand ? '🚚' : (isAir ? '✈️' : '🚢');
            const activeLabel = isLand ? (s.courier_name || 'Mobil Van Paket Kurir Lokal') : vehicle.label;
            return `
                <div style="min-width:290px; font-family: Outfit, sans-serif; padding:4px 2px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:6px;">
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-size:22px;">${activeEmoji}</span>
                            <div>
                                <div style="font-weight:800; font-size:14px; color:#0284c7;">${s.shipment_number}</div>
                                <div style="font-size:10px; color:#64748b;">${activeLabel}</div>
                            </div>
                        </div>
                        <span style="font-size:10px; font-weight:800; padding:3px 8px; border-radius:6px; background:#e0f2fe; color:#0369a1;">
                            ${s.status}
                        </span>
                    </div>

                    <!-- ETA Box (Estimasi Perkiraan Sampai Tujuan) -->
                    <div style="background:linear-gradient(135deg, #0f172a, #1e293b); color:#ffffff; padding:10px 12px; border-radius:10px; margin-bottom:8px; box-shadow:0 4px 12px rgba(0,0,0,0.3);">
                        <div style="font-size:10px; text-transform:uppercase; font-weight:800; color:#38bdf8; letter-spacing:0.5px; display:flex; align-items:center; gap:5px; margin-bottom:3px;">
                            <span>⏱️ Estimasi Tiba di Tujuan (ETA):</span>
                        </div>
                        <div style="font-weight:800; font-size:15px; color:#ffffff; letter-spacing:0.3px;">
                            ${eta.formatted}
                        </div>
                        <div style="font-size:11px; color:#cbd5e1; margin-top:4px; display:flex; align-items:center; justify-content:space-between;">
                            <span>${eta.countdown}</span>
                            <span>${eta.statusHtml}</span>
                        </div>
                    </div>

                    <!-- Live Real-Time Flight / Voyage / Last-Mile Progress Bar -->
                    <div style="background:#F0FDF4; border:1px solid #BBF7D0; padding:8px 10px; border-radius:10px; margin-bottom:10px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; font-weight:800; font-size:11px; color:#15803d; margin-bottom:4px;">
                            <span>${isLand ? '🚚 Progres Pengantaran Mobil Dekat:' : (isAir ? '✈️ Progres Penerbangan Realtime:' : '🚢 Progres Pelayaran Maritim:')}</span>
                            <span style="font-size:12px; color:#047857; font-weight:900;">${pct}%</span>
                        </div>
                        <div style="width:100%; height:7px; background:#dcfce7; border-radius:999px; overflow:hidden;">
                            <div style="width:${pct}%; height:100%; background:linear-gradient(90deg, #10b981, #06b6d4); border-radius:999px;"></div>
                        </div>
                        <div style="font-size:10px; color:#475569; margin-top:5px; display:flex; justify-content:space-between;">
                            <span>Kecepatan: <b>${isLand ? '45 km/h (Kurir Kota)' : (isAir ? '860 km/h (Cruising)' : '21.5 kts (Navigasi)')}</b></span>
                            <span>Posisi: <b>${isLand ? 'Darat (Last-Mile)' : (isAir ? 'FL360 (36.000 ft)' : '0 m (Laut)')}</b></span>
                        </div>
                    </div>

                    <!-- Komoditas Kopi Brazil Santos -->
                    <div style="background:#fef3c7; border:1px solid #fde68a; padding:7px 10px; border-radius:8px; font-size:11px; margin-bottom:8px;">
                        <div style="font-weight:800; color:#92400e; display:flex; align-items:center; gap:4px;">
                            <span>☕</span>
                            <span>${s.coffee_variety || s.cargo_type || 'Kopi Brazil Santos Specialty'}</span>
                        </div>
                        <div style="font-size:10px; color:#78350f; margin-top:2px;">
                            Grade: <b>${s.coffee_grade || 'Specialty Grade 1 (SCA 84+)'}</b> &bull; Total: <b>${parseFloat(s.weight || 0).toLocaleString()} kg</b>
                        </div>
                    </div>

                    <!-- Titik Awal hingga Akhir & Alamat Pengantaran Mobil -->
                    <div style="background:#F8FAFC; border:1px solid #E2E8F0; padding:8px 10px; border-radius:8px; font-size:11px; margin-bottom:10px;">
                        <div style="margin-bottom:6px;">
                            <span style="color:#059669; font-weight:800;">🟢 Titik Asal (Hub Santos Brazil):</span>
                            <div style="font-weight:700; color:#0f172a;">${s.origin.name}</div>
                            <div style="font-size:10px; color:#64748b;">${s.origin.city}, ${s.origin.country}</div>
                        </div>
                        <div style="border-top:1px dashed #cbd5e1; padding-top:6px; margin-top:6px;">
                            <span style="color:#dc2626; font-weight:800;">🔴 Simpul Transit / Pelabuhan Tujuan:</span>
                            <div style="font-weight:700; color:#0f172a;">${s.destination.name}</div>
                            <div style="font-size:10px; color:#64748b;">${s.destination.city}, ${s.destination.country}</div>
                        </div>
                        <!-- Alur Multimoda Realistis (3 Tahap) -->
                        <div style="background:#f0fdf4; border:1px solid #bbf7d0; padding:6px 8px; border-radius:6px; margin-bottom:8px; font-size:10px; color:#166534; line-height:1.4;">
                            <div style="font-weight:800; margin-bottom:3px; display:flex; align-items:center; gap:4px;">
                                <span>🌐 Alur Rute Multimoda Logistik:</span>
                            </div>
                            <div>1. 🚢 Pelayaran Samudra (Santos ➔ Pelabuhan Hub)</div>
                            <div>2. ✈️ Pesawat Kargo Domestik (Hub ➔ Bandara Kota)</div>
                            <div>3. 🚚 Mobil Van Kurir (Bandara ➔ Alamat Kafe)</div>
                        </div>

                        ${s.delivery_address ? `
                        <div style="border-top:1px dashed #cbd5e1; padding-top:6px; margin-top:6px; background:#fffbeb; margin-left:-10px; margin-right:-10px; padding-left:10px; padding-right:10px; border-radius:0 0 8px 8px;">
                            <span style="color:#d97706; font-weight:800;">🚚 Alamat Pengantaran Mobil (Last-Mile):</span>
                            <div style="font-weight:700; color:#78350f;">${s.delivery_address}</div>
                            <div style="font-size:10px; color:#92400e;">
                                ${s.delivery_district ? s.delivery_district + ', ' : ''}${s.delivery_city || ''} (${s.delivery_country || ''})
                            </div>
                            <div style="font-size:10px; color:#b45309; margin-top:2px;">
                                Armada: <b>${s.courier_name || 'Mobil Kurir Kopi Santos'}</b> &bull; ${s.courier_vehicle || 'Van Kurir'}
                            </div>
                        </div>` : ''}
                        ${curLoc && !s.delivery_address ? `
                        <div style="border-top:1px dashed #cbd5e1; padding-top:6px; margin-top:6px;">
                            <span style="color:#0284c7; font-weight:800;">📍 Posisi Terkini:</span>
                            <div style="font-weight:700; color:#0f172a;">${curLoc.name || 'Dalam Penerbangan Udara'}</div>
                        </div>` : ''}
                    </div>

                    <!-- Armada & Muatan -->
                    <div style="font-size:11px; color:#475569; margin-bottom:10px; line-height:1.4;">
                        Mitra Ekspedisi: <b>${s.carrier?.name || '-'}</b>
                    </div>

                    <!-- Tombol Aksi -->
                    <div style="display:flex; gap:6px;">
                        <a href="${SHIPMENT_BASE_URL}/${s.id}" style="flex:1; text-align:center; background:#0284c7; color:#fff; font-weight:700; padding:7px 10px; border-radius:8px; text-decoration:none; font-size:11px; box-shadow:0 2px 4px rgba(2,132,199,0.3);">
                            Lihat Detail Pengiriman →
                        </a>
                        <button type="button" onclick="window.resetShipmentFocus()" style="background:#f1f5f9; color:#475569; font-weight:700; padding:7px 10px; border-radius:8px; border:1px solid #cbd5e1; font-size:11px; cursor:pointer;" title="Kembalikan semua rute">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            `;
        }

        // ── BUILD CARGO LAYER ───────────────────────────────────
        let cargoShipmentItems = [];
        let activeCargoId = null;

        function focusCargoShipment(targetId, targetLatLng) {
            activeCargoId = targetId;

            cargoShipmentItems.forEach(it => {
                if (it.id === targetId) {
                    // Menonjolkan jalur yang dipilih
                    it.traveledPoly?.setStyle({ color: '#00F0FF', weight: 6.5, opacity: 1 });
                    it.remainingPoly?.setStyle({ color: '#38BDF8', weight: 5, opacity: 1, dashArray: '8, 8' });
                    it.directPoly?.setStyle({ color: '#00F0FF', weight: 5.5, opacity: 1 });
                    it.deliveryPoly?.setStyle({ color: '#f59e0b', weight: 6, opacity: 1 });
                    it.feederFlightPoly?.setStyle({ color: '#00F0FF', weight: 5.5, opacity: 1 });

                    // Menonjolkan titik asal dan akhir
                    it.originMarker?.setRadius(11).setStyle({ fillColor: '#10B981', color: '#ffffff', weight: 3.5, fillOpacity: 1 });
                    it.destMarker?.setRadius(11).setStyle({ fillColor: '#EF4444', color: '#ffffff', weight: 3.5, fillOpacity: 1 });
                    if (it.deliveryMarker) {
                        it.deliveryMarker.setZIndexOffset(10000);
                        const el = it.deliveryMarker.getElement();
                        if (el) el.classList.remove('marker-dimmed');
                    }
                    if (it.courierVanMarker) {
                        it.courierVanMarker.setZIndexOffset(10000);
                        const el = it.courierVanMarker.getElement();
                        if (el) el.classList.remove('marker-dimmed');
                    }
                    if (it.feederPlaneMarker) {
                        it.feederPlaneMarker.setZIndexOffset(10000);
                        const el = it.feederPlaneMarker.getElement();
                        if (el) {
                            el.classList.remove('marker-dimmed');
                            const wrap = el.querySelector('.cargo-real-vehicle-wrapper');
                            if (wrap) wrap.classList.add('vehicle-focused');
                        }
                    }

                    // Menonjolkan icon kendaraan kargo
                    if (it.vehicleMarker) {
                        it.vehicleMarker.setZIndexOffset(10000);
                        const el = it.vehicleMarker.getElement();
                        if (el) {
                            el.classList.remove('marker-dimmed');
                            const wrap = el.querySelector('.cargo-real-vehicle-wrapper');
                            if (wrap) wrap.classList.add('vehicle-focused');
                            const tag = el.querySelector('.cargo-vehicle-tag');
                            if (tag) tag.classList.add('tag-focused');
                        }
                        it.vehicleMarker.openPopup();
                    } else if (it.courierVanMarker) {
                        it.courierVanMarker.openPopup();
                    } else if (it.traveledPoly) {
                        it.traveledPoly.openPopup(targetLatLng || it.bounds.getCenter());
                    } else if (it.directPoly) {
                        it.directPoly.openPopup(targetLatLng || it.bounds.getCenter());
                    }

                    // Zoom / Fit bounds ke seluruh lintasan rute kargo tersebut
                    map.fitBounds(it.bounds, { padding: [60, 60], maxZoom: 13, duration: 1 });
                } else {
                    // Meredupkan kargo lain agar rute terpilih benar-benar menonjol
                    it.traveledPoly?.setStyle({ color: '#64748b', weight: 1.5, opacity: 0.15 });
                    it.remainingPoly?.setStyle({ color: '#64748b', weight: 1.5, opacity: 0.12, dashArray: '4, 6' });
                    it.directPoly?.setStyle({ color: '#64748b', weight: 1.5, opacity: 0.15 });
                    it.deliveryPoly?.setStyle({ color: '#64748b', weight: 1.5, opacity: 0.15 });
                    it.feederFlightPoly?.setStyle({ color: '#64748b', weight: 1.5, opacity: 0.15, dashArray: '4, 6' });
                    it.originMarker?.setRadius(5).setStyle({ opacity: 0.25, fillOpacity: 0.25 });
                    it.destMarker?.setRadius(5).setStyle({ opacity: 0.25, fillOpacity: 0.25 });
                    if (it.deliveryMarker) {
                        it.deliveryMarker.setZIndexOffset(100);
                        const el = it.deliveryMarker.getElement();
                        if (el) el.classList.add('marker-dimmed');
                    }
                    if (it.courierVanMarker) {
                        it.courierVanMarker.setZIndexOffset(100);
                        const el = it.courierVanMarker.getElement();
                        if (el) el.classList.add('marker-dimmed');
                    }
                    if (it.feederPlaneMarker) {
                        it.feederPlaneMarker.setZIndexOffset(100);
                        const el = it.feederPlaneMarker.getElement();
                        if (el) {
                            el.classList.add('marker-dimmed');
                            const wrap = el.querySelector('.cargo-real-vehicle-wrapper');
                            if (wrap) wrap.classList.remove('vehicle-focused');
                        }
                    }

                    if (it.vehicleMarker) {
                        it.vehicleMarker.setZIndexOffset(100);
                        const el = it.vehicleMarker.getElement();
                        if (el) {
                            el.classList.add('marker-dimmed');
                            const wrap = el.querySelector('.cargo-real-vehicle-wrapper');
                            if (wrap) wrap.classList.remove('vehicle-focused');
                            const tag = el.querySelector('.cargo-vehicle-tag');
                            if (tag) tag.classList.remove('tag-focused');
                        }
                    }
                }
            });
        }

        function resetCargoShipmentFocus() {
            activeCargoId = null;
            cargoShipmentItems.forEach(it => {
                it.traveledPoly?.setStyle({ color: '#0284c7', weight: 3.5, opacity: 0.9 });
                it.remainingPoly?.setStyle({ color: '#38bdf8', weight: 2.5, opacity: 0.8, dashArray: '6, 8' });
                it.directPoly?.setStyle({ color: '#0284c7', weight: 2.5, opacity: 0.75, dashArray: '6, 8' });
                it.deliveryPoly?.setStyle({ color: '#f59e0b', weight: 3.5, opacity: 0.95 });
                it.feederFlightPoly?.setStyle({ color: '#00F0FF', weight: 3.5, opacity: 0.9, dashArray: '6, 6' });

                it.originMarker?.setRadius(7).setStyle({ fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1, opacity: 1 });
                it.destMarker?.setRadius(7).setStyle({ fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1, opacity: 1 });
                if (it.deliveryMarker) {
                    it.deliveryMarker.setZIndexOffset(1200);
                    const el = it.deliveryMarker.getElement();
                    if (el) el.classList.remove('marker-dimmed');
                }
                if (it.courierVanMarker) {
                    it.courierVanMarker.setZIndexOffset(1500);
                    const el = it.courierVanMarker.getElement();
                    if (el) el.classList.remove('marker-dimmed');
                }
                if (it.feederPlaneMarker) {
                    it.feederPlaneMarker.setZIndexOffset(1400);
                    const el = it.feederPlaneMarker.getElement();
                    if (el) {
                        el.classList.remove('marker-dimmed');
                        const wrap = el.querySelector('.cargo-real-vehicle-wrapper');
                        if (wrap) wrap.classList.remove('vehicle-focused');
                    }
                }

                if (it.vehicleMarker) {
                    it.vehicleMarker.setZIndexOffset(1000);
                    const el = it.vehicleMarker.getElement();
                    if (el) {
                        el.classList.remove('marker-dimmed');
                        const wrap = el.querySelector('.cargo-real-vehicle-wrapper');
                        if (wrap) wrap.classList.remove('vehicle-focused');
                        const tag = el.querySelector('.cargo-vehicle-tag');
                        if (tag) tag.classList.remove('tag-focused');
                    }
                }
            });
            map.closePopup();
        }

        function buildCargoLayer() {
            cargoLayerGroup.clearLayers();
            cargoShipmentItems = [];
            activeCargoId = null;
            const latLngs = [];
            const occupiedVehicleCoords = [];

            if (!Array.isArray(shipments)) return latLngs;

            shipments.forEach(s => {
                if (!s || !s.origin || !s.destination) return;

                // Sub-filter moda transportasi (Semua / Laut / Udara / Mobil Dekat)
                const originType = (s.origin?.location_type || '').toUpperCase();
                const destType   = (s.destination?.location_type || '').toUpperCase();
                const sModeRaw   = (s.transport_mode || '').toUpperCase();
                const isLandCargo = (s.is_last_mile === true || s.is_last_mile === 1 || s.is_last_mile === '1' || s.is_last_mile === 'true') || (sModeRaw === 'LAND');
                const isAirCargo = !isLandCargo && (sModeRaw === 'AIR' || (!sModeRaw && (originType === 'AIRPORT' || destType === 'AIRPORT')));
                const sMode      = isLandCargo ? 'LAND' : (isAirCargo ? 'AIR' : (sModeRaw || 'SEA'));

                if (cargoTransportFilter !== 'ALL') {
                    if (cargoTransportFilter === 'LAND' && !isLandCargo) return;
                    if (cargoTransportFilter === 'AIR' && !isAirCargo) return;
                    if (cargoTransportFilter === 'SEA' && (isAirCargo || isLandCargo)) return;
                }

                if (cargoContinentFilter !== 'ALL' && getDestinationContinent(s) !== cargoContinentFilter) {
                    return;
                }

                const oLat = parseFloat(s.origin.latitude);
                const oLng = parseFloat(s.origin.longitude);
                const dLat = parseFloat(s.destination.latitude);
                const dLng = parseFloat(s.destination.longitude);

                if (isNaN(oLat) || isNaN(oLng) || isNaN(dLat) || isNaN(dLng)) return;

                const originCoords = [oLat, oLng];
                const destCoords   = [dLat, dLng];
                latLngs.push(originCoords, destCoords);

                const vehicle = getShipmentVehicle(s);
                const eta     = getShipmentETA(s);

                // Hitung pemisahan lajur koridor (Lane Separation) agar rute paralel tidak saling tumpuk
                const laneSeed = ((s.id * 11) % 9) - 4; // Deviasi per kargo (-4 s/d +4)
                const laneShift = laneSeed * 1.6; // Deviasi lateral kurva
                const laneCurvature = (isAirCargo ? 0.16 : 0.11) + (laneSeed * 0.012);

                // Hitung progres real-time berdasarkan waktu keberangkatan & perkiraan tiba
                const progress = getShipmentRealtimeProgress(s);

                // Buat lintasan rute (gunakan jalur samudra realistis untuk kapal laut)
                const seaWaypoints = window.buildMaritimeWaypoints(originCoords, destCoords, isAirCargo, s.id);
                const fullCorridor = seaWaypoints.length > 2
                    ? window.samplePolylineByRange(seaWaypoints, 0, 1, 50)
                    : createCurvedPath(originCoords, destCoords, 50, laneCurvature, laneShift);
                let progressIdx  = Math.min(fullCorridor.length - 1, Math.max(1, Math.round(progress * (fullCorridor.length - 1))));

                // Anti-Collision along the route: geser posisi progres di sepanjang koridor laut jika bertumpuk
                if (!isLandCargo && s.status !== 'DELIVERED') {
                    const offsets = [0, 5, -5, 10, -10, 15, -15, 20, -20, 25, -25];
                    for (const off of offsets) {
                        const testIdx = Math.min(fullCorridor.length - 2, Math.max(1, progressIdx + off));
                        const pt = fullCorridor[testIdx];
                        if (!pt) continue;
                        const collides = occupiedVehicleCoords.some(occ =>
                            Math.abs(occ[0] - pt[0]) < 4.5 && Math.abs(occ[1] - pt[1]) < 7.5
                        );
                        if (!collides) {
                            progressIdx = testIdx;
                            break;
                        }
                    }
                }

                let curCoords = fullCorridor[progressIdx] || originCoords;
                if (isLandCargo || s.status === 'DELIVERED') {
                    curCoords = destCoords;
                }
                latLngs.push(curCoords);

                const displayCurLoc = {
                    name: isLandCargo ? `Tiba di ${s.destination.name} &bull; Di-oper ke Mobil Paket Kurir` : (isAirCargo ? `Di Udara (Koridor Udara Internasional - FL360)` : `Di Jalur Pelayaran Samudra (Laut Lepas)`)
                };

                const popupHtml = generateShipmentPopupContent(s, vehicle, eta, displayCurLoc, progress);

                // Origin marker (🟢 Green)
                const originMarker = L.circleMarker(originCoords, {
                    radius: 7, fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                originMarker.on('click', () => focusCargoShipment(s.id, originCoords));

                // Destination marker (🔴 Red)
                const destMarker = L.circleMarker(destCoords, {
                    radius: 7, fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                destMarker.on('click', () => focusCargoShipment(s.id, destCoords));

                let traveledPoly = null;
                let remainingPoly = null;
                let directPoly = null;
                let vehicleMarker = null;

                if (isLandCargo) {
                    // Kargo sudah sampai di pelabuhan tujuan, lintasan samudra 100% selesai penuh
                    traveledPoly = L.polyline(fullCorridor, {
                        color: isAirCargo ? '#0284c7' : '#0369a1',
                        weight: 3.5,
                        opacity: 0.85,
                        lineCap: 'round',
                        cursor: 'pointer'
                    }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                    traveledPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));
                    // Tidak ada kendaraan terapung di laut/udara karena kargo sudah di-oper ke mobil paket di darat!
                } else {
                    // 1. Lintasan Ditempuh: Asal -> Posisi Terkini (Lapis lengkung solid menyala)
                    const traveledCurve = fullCorridor.slice(0, progressIdx + 1);
                    traveledPoly = L.polyline(traveledCurve, {
                        color: isAirCargo ? '#0284c7' : '#0369a1',
                        weight: 3.5,
                        opacity: 0.9,
                        lineCap: 'round',
                        lineJoin: 'round',
                        cursor: 'pointer'
                    }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                    traveledPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));

                    // 2. Estimasi Lintasan Lanjutan: Posisi Terkini -> Tujuan (Garis putus-putus)
                    const remainingCurve = fullCorridor.slice(progressIdx);
                    remainingPoly = L.polyline(remainingCurve, {
                        color: isAirCargo ? '#38bdf8' : '#7dd3fc',
                        weight: 2.5,
                        dashArray: '6, 8',
                        opacity: 0.8,
                        lineCap: 'round',
                        lineJoin: 'round',
                        cursor: 'pointer'
                    }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                    remainingPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));

                    // 3. Real Vehicle Visual Marker (Kapal Kontainer / Pesawat Udara di koridor internasional)
                    let vehicleCoords = [curCoords[0], curCoords[1]];
                    occupiedVehicleCoords.push(vehicleCoords);

                    const heading = calculateBearing(vehicleCoords, destCoords);
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

                    vehicleMarker = L.marker(vehicleCoords, { icon: vehicleDivIcon, zIndexOffset: 1000 })
                        .bindPopup(popupHtml)
                        .addTo(cargoLayerGroup);
                    vehicleMarker.on('click', () => focusCargoShipment(s.id, vehicleCoords));
                }

                // Bounds untuk zoom fokus rute
                const routePoints = [originCoords, destCoords];
                if (curCoords) routePoints.push(curCoords);

                // 4. Pin & Lintasan Mobil Kurir Pengantaran Alamat (Last-Mile Delivery)
                let deliveryMarker = null;
                let deliveryPoly = null;
                let feederFlightPoly = null;
                let feederPlaneMarker = null;
                let courierVanMarker = null;
                const rawDevLat = s.delivery_lat ? parseFloat(s.delivery_lat) : (isLandCargo ? dLat + (((s.id % 5) - 2) * 0.04 || 0.04) : NaN);
                const rawDevLng = s.delivery_lng ? parseFloat(s.delivery_lng) : (isLandCargo ? dLng + (((s.id % 7) - 3) * 0.04 || 0.04) : NaN);
                if (!isNaN(rawDevLat) && !isNaN(rawDevLng)) {
                    const devLat = rawDevLat;
                    const devLng = rawDevLng;
                    {
                        const devCoords = [devLat, devLng];
                        routePoints.push(devCoords);
                        latLngs.push(devCoords);

                        // Cek jarak antara Pelabuhan Masuk dan Kota Tujuan (km)
                        const distPortToCityKm = calculateDistanceKm(destCoords[0], destCoords[1], devLat, devLng);
                        const isDomesticFeederAir = distPortToCityKm > 35;

                        if (isDomesticFeederAir) {
                            // ✈️ Koridor Penerbangan Kargo Domestik (Pelabuhan Masuk ➔ Bandara Kota Tujuan)
                            const flightPath = createCurvedPath(destCoords, devCoords, 20, 0.12);
                            feederFlightPoly = L.polyline(flightPath, {
                                color: '#00F0FF',
                                weight: 3.5,
                                dashArray: '6, 6',
                                opacity: 0.95,
                                lineCap: 'round',
                                cursor: 'pointer'
                            }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                            feederFlightPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));

                            // Pesawat Kargo Domestik tidak ditumpuk saat pelayaran utama berjalan
                            if (false && (s.is_last_mile || s.status === 'DELIVERED')) {
                                const planeLat = destCoords[0] + (devCoords[0] - destCoords[0]) * 0.52;
                                const planeLng = destCoords[1] + (devCoords[1] - destCoords[1]) * 0.52;
                                const planeHeading = calculateBearing(destCoords, devCoords);
                                const feederPlaneSvg = `
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="38" height="38" style="filter: drop-shadow(0 6px 12px rgba(0,0,0,0.6));">
                                        <path d="M32 2 C30 2, 28 6, 28 14 L28 24 L2 35 L2 40 L28 34 L28 50 L18 56 L18 60 L32 58 L46 60 L46 56 L36 50 L36 34 L62 40 L62 35 L36 24 L36 14 C36 6, 34 2, 32 2 Z" fill="#00F0FF" stroke="#0284c7" stroke-width="1.2"/>
                                        <path d="M32 4 C31 4, 29.5 8, 29.5 16 L29.5 54 L34.5 54 L34.5 16 C34.5 8, 33 4, 32 4 Z" fill="#ffffff"/>
                                        <rect x="14" y="27" width="4.5" height="11" rx="2" fill="#334155"/>
                                        <rect x="45.5" y="27" width="4.5" height="11" rx="2" fill="#334155"/>
                                    </svg>
                                `;

                                feederPlaneMarker = L.marker([planeLat, planeLng], {
                                    icon: L.divIcon({
                                        className: 'cargo-real-vehicle-marker',
                                        html: `
                                            <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${planeHeading}deg);" title="Pesawat Kargo Domestik: ${s.shipment_number}">
                                                ${feederPlaneSvg}
                                            </div>
                                            <div class="cargo-vehicle-tag" style="white-space:nowrap; margin-top:-2px;">
                                                <span class="cargo-tag-dot" style="background:#00F0FF;"></span>
                                                <span>✈️ Pesawat Domestik: ${s.shipment_number}</span>
                                            </div>
                                        `,
                                        iconSize: [42, 42],
                                        iconAnchor: [21, 21],
                                        popupAnchor: [0, -25]
                                    }),
                                    zIndexOffset: 1400
                                }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                                feederPlaneMarker.on('click', () => focusCargoShipment(s.id, [planeLat, planeLng]));
                            }
                        }

                        // Garis jalan darat pengantaran mobil paket dari pelabuhan/bandara kedatangan ke alamat kafe
                        deliveryPoly = L.polyline([destCoords, devCoords], {
                            color: '#f59e0b',
                            weight: 3.5,
                            dashArray: '5, 6',
                            opacity: 0.95,
                            lineCap: 'round',
                            cursor: 'pointer'
                        }).bindPopup(popupHtml).addTo(cargoLayerGroup);
                        deliveryPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));

                        // 1. Mobil Van Paket Kurir (Sama seperti mobil paket pada umumnya - TANPA LINGKARAN BULAT)
                        // Tampilkan mobil van paket hanya jika sudah tahap last-mile atau DELIVERED
                        if (isLandCargo || s.status === 'DELIVERED') {
                            const vanFraction = isDomesticFeederAir ? 0.88 : 0.65;
                            const vanLat = destCoords[0] + (devCoords[0] - destCoords[0]) * vanFraction;
                            const vanLng = destCoords[1] + (devCoords[1] - destCoords[1]) * vanFraction;
                            const vanHeading = calculateBearing(destCoords, devCoords);

                            const courierVanDivIcon = L.divIcon({
                                className: 'cargo-real-vehicle-marker',
                                html: `
                                    <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${vanHeading}deg);" title="${s.courier_name || 'Mobil Paket Kurir'} &bull; ${s.shipment_number}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 68" width="30" height="54" style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));">
                                            <!-- Bayangan Mobil -->
                                            <rect x="4" y="3" width="30" height="62" rx="6" fill="#0f172a" opacity="0.35" transform="translate(1.5, 2.5)"/>
                                            <!-- Roda Mobil (4 Roda) -->
                                            <rect x="3" y="12" width="3" height="8" rx="1" fill="#0f172a"/>
                                            <rect x="32" y="12" width="3" height="8" rx="1" fill="#0f172a"/>
                                            <rect x="3" y="48" width="3" height="9" rx="1" fill="#0f172a"/>
                                            <rect x="32" y="48" width="3" height="9" rx="1" fill="#0f172a"/>
                                            <!-- Bodi Utama Mobil Van Paket (Putih Bersih) -->
                                            <rect x="5" y="4" width="28" height="60" rx="5" fill="#f8fafc" stroke="#64748b" stroke-width="1.2"/>
                                            <!-- Kabin Depan Pengemudi -->
                                            <rect x="7" y="6" width="24" height="15" rx="3" fill="#e2e8f0"/>
                                            <!-- Kaca Depan (Windshield) -->
                                            <path d="M8 8 Q19 6 30 8 L29 15 Q19 13.5 9 15 Z" fill="#0284c7"/>
                                            <!-- Kaca Samping -->
                                            <rect x="6.5" y="9.5" width="2" height="7" rx="0.5" fill="#0284c7"/>
                                            <rect x="29.5" y="9.5" width="2" height="7" rx="0.5" fill="#0284c7"/>
                                            <!-- Spion -->
                                            <rect x="3" y="10" width="2.5" height="4" rx="0.8" fill="#334155"/>
                                            <rect x="32.5" y="10" width="2.5" height="4" rx="0.8" fill="#334155"/>
                                            <!-- Boks Muatan Paket Belakang (Warna Oranye Ekspedisi) -->
                                            <rect x="6.5" y="23" width="25" height="39" rx="2.5" fill="#f59e0b" stroke="#d97706" stroke-width="0.8"/>
                                            <!-- Simbol Paket / Parcel Kurir -->
                                            <rect x="9.5" y="27" width="19" height="2" fill="#fde68a"/>
                                            <text x="19" y="42" font-size="12" font-weight="900" fill="#ffffff" text-anchor="middle" font-family="sans-serif">📦</text>
                                            <text x="19" y="54" font-size="5" font-weight="800" fill="#78350f" text-anchor="middle" font-family="sans-serif">KURIR PAKET</text>
                                        </svg>
                                    </div>
                                    <div class="cargo-vehicle-tag" style="white-space:nowrap; margin-top:-2px;">
                                        <span class="cargo-tag-dot" style="background:#f59e0b;"></span>
                                        <span>Mobil Paket: ${s.courier_vehicle ? (s.courier_vehicle.split('(')[1]?.replace(')','') || s.shipment_number) : s.shipment_number}</span>
                                    </div>
                                `,
                                iconSize: [36, 56],
                                iconAnchor: [18, 28],
                                popupAnchor: [0, -32]
                            });

                            courierVanMarker = L.marker([vanLat, vanLng], { icon: courierVanDivIcon, zIndexOffset: 1500 })
                                .bindPopup(popupHtml)
                                .addTo(cargoLayerGroup);
                            courierVanMarker.on('click', () => focusCargoShipment(s.id, [vanLat, vanLng]));
                        }

                        // 2. Pin Alamat Tujuan Penerima (Pin Lokasi Biasa - TANPA LINGKARAN BULAT)
                        const destPinIcon = L.divIcon({
                            className: 'cargo-destination-pin',
                            html: `
                                <div style="font-size:26px; line-height:1; filter:drop-shadow(0 3px 6px rgba(0,0,0,0.5)); transform:translate(-2px, -14px); cursor:pointer;" title="Alamat Pengantaran: ${s.delivery_address || s.delivery_city}">
                                    📍
                                </div>
                            `,
                            iconSize: [26, 26],
                            iconAnchor: [13, 26],
                            popupAnchor: [0, -28]
                        });

                        deliveryMarker = L.marker(devCoords, { icon: destPinIcon, zIndexOffset: 1200 })
                            .bindPopup(popupHtml)
                            .addTo(cargoLayerGroup);
                        deliveryMarker.on('click', () => focusCargoShipment(s.id, devCoords));
                    }
                }

                const bounds = L.latLngBounds(routePoints);

                cargoShipmentItems.push({
                    id: s.id,
                    s,
                    vehicle,
                    eta,
                    originMarker,
                    destMarker,
                    traveledPoly,
                    remainingPoly,
                    directPoly,
                    deliveryPoly,
                    feederFlightPoly,
                    feederPlaneMarker,
                    deliveryMarker,
                    courierVanMarker,
                    vehicleMarker,
                    curCoords,
                    bounds
                });
            });

            return latLngs;
        }

        // ── ROUTE TRACKER 2-STEP INTERACTIVE SYSTEM ───────────────────
        let traceOrigin = null;
        let traceDestination = null;
        let traceTransportMode = 'PORT'; // 'PORT' (Laut) atau 'AIR' (Udara)
        let traceNodesGroup = L.layerGroup();
        let traceRouteLine = null;
        let traceOriginMarker = null;
        let traceDestMarker = null;
        let lastAnalyzedRoute = null;

        // ISO2 to ISO3 lookup table for 3-letter codes
        const ISO3_MAP = {
            'ID':'IDN', 'MY':'MYS', 'SG':'SGP', 'TH':'THA', 'VN':'VNM', 'PH':'PHL',
            'MM':'MMR', 'KH':'KHM', 'LA':'LAO', 'BN':'BRN', 'TL':'TLS', 'CN':'CHN',
            'JP':'JPN', 'KR':'KOR', 'TW':'TWN', 'HK':'HKG', 'MO':'MAC', 'MN':'MNG',
            'KP':'PRK', 'IN':'IND', 'PK':'PAK', 'BD':'BGD', 'LK':'LKA', 'NP':'NPL',
            'BT':'BTN', 'MV':'MDV', 'AF':'AFG', 'KZ':'KAZ', 'UZ':'UZB', 'TM':'TKM',
            'KG':'KGZ', 'TJ':'TJK', 'SA':'SAU', 'AE':'ARE', 'QA':'QAT', 'KW':'KWT',
            'OM':'OMN', 'BH':'BHR', 'YE':'YEM', 'IR':'IRN', 'IQ':'IRQ', 'SY':'SYR',
            'JO':'JOR', 'LB':'LBN', 'IL':'ISR', 'PS':'PSE', 'TR':'TUR', 'CY':'CYP',
            'GE':'GEO', 'AM':'ARM', 'AZ':'AZE', 'GB':'GBR', 'DE':'DEU', 'FR':'FRA',
            'NL':'NLD', 'BE':'BEL', 'LU':'LUX', 'IT':'ITA', 'ES':'ESP', 'PT':'PRT',
            'CH':'CHE', 'AT':'AUT', 'SE':'SWE', 'NO':'NOR', 'DK':'DNK', 'FI':'FIN',
            'IS':'ISL', 'IE':'IRL', 'GR':'GRC', 'PL':'POL', 'CZ':'CZE', 'SK':'SVK',
            'HU':'HUN', 'RO':'ROU', 'BG':'BGR', 'UA':'UKR', 'BY':'BLR', 'MD':'MDA',
            'RU':'RUS', 'US':'USA', 'CA':'CAN', 'MX':'MEX', 'BR':'BRA', 'AR':'ARG',
            'CL':'CHL', 'CO':'COL', 'PE':'PER', 'EG':'EGY', 'ZA':'ZAF', 'NG':'NGA',
            'KE':'KEN', 'MA':'MAR', 'GH':'GHA', 'NE':'NER', 'NER':'NER', 'BF':'BFA',
            'BFA':'BFA', 'AU':'AUS', 'NZ':'NZL'
        };

        function getIso3Code(c) {
            const raw = (c.code || '').toUpperCase().replace('_EAST','');
            if (raw.length === 3) return raw;
            return ISO3_MAP[raw] || (raw + 'X').substring(0,3);
        }

        const AIRPORT_NAMES = {
            'ID':'Bandara Internasional Soekarno-Hatta (CGK)',
            'MY':'Kuala Lumpur International (KUL)',
            'SG':'Singapore Changi Airport (SIN)',
            'TH':'Suvarnabhumi Airport (BKK)',
            'VN':'Tan Son Nhat / Noi Bai Int\'l (SGN/HAN)',
            'PH':'Ninoy Aquino International (MNL)',
            'CN':'Shanghai Pudong Int\'l (PVG)',
            'JP':'Tokyo Haneda / Narita Int\'l (HND/NRT)',
            'KR':'Incheon International Airport (ICN)',
            'US':'John F. Kennedy / LAX (JFK/LAX)',
            'GB':'London Heathrow Airport (LHR)',
            'NL':'Amsterdam Airport Schiphol (AMS)',
            'DE':'Frankfurt Airport (FRA)',
            'FR':'Paris Charles de Gaulle (CDG)',
            'AE':'Dubai International Airport (DXB)',
            'SA':'King Abdulaziz Int\'l (JED)',
            'IN':'Indira Gandhi International (DEL)',
            'AU':'Sydney Kingsford Smith (SYD)',
            'NE':'Diori Hamani International (NIM)',
            'NER':'Diori Hamani International (NIM)',
            'BF':'Thomas Sankara International Ouagadougou (OUA)',
            'BFA':'Thomas Sankara International Ouagadougou (OUA)',
            'EG':'Cairo International Airport (CAI)',
            'ZA':'O.R. Tambo International Johannesburg (JNB)',
            'BR':'São Paulo/Guarulhos Int\'l (GRU)'
        };

        function getCountryHubName(c, mode) {
            const code = (c.code || '').toUpperCase().replace('_EAST','');
            if (mode === 'AIR') {
                return AIRPORT_NAMES[code] || `Bandara Internasional ${c.name} (${getIso3Code(c)})`;
            }
            return c.hub || `Pelabuhan Logistik ${c.name} Hub`;
        }

        function calculateDistanceKm(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return R * c;
        }

        function buildRouteLayer() {
            routeLayerGroup.clearLayers();
            traceNodesGroup.clearLayers();
            renderCountryTraceNodes();
            routeLayerGroup.addLayer(traceNodesGroup);

            if (traceOrigin && traceDestination) {
                drawTraceRoute(traceOrigin, traceDestination);
            } else if (traceOrigin) {
                renderOriginPin(traceOrigin);
            }
        }

        function renderCountryTraceNodes() {
            traceNodesGroup.clearLayers();
            countriesData.forEach(c => {
                const lat = parseFloat(c.lat);
                const lng = parseFloat(c.lng);
                if (isNaN(lat) || isNaN(lng)) return;

                const node = L.circleMarker([lat, lng], {
                    radius: 5.5,
                    fillColor: '#94a3b8',
                    color: '#ffffff',
                    weight: 1.5,
                    opacity: 0.9,
                    fillOpacity: 0.8,
                    className: 'country-trace-node'
                });

                const iso3 = getIso3Code(c);
                const hub = getCountryHubName(c, traceTransportMode);
                const modeIcon = traceTransportMode === 'AIR' ? '✈️' : '⚓';

                node.bindTooltip(`
                    <div style="font-family: Outfit, sans-serif; font-size:11px; padding:2px;">
                        <span style="font-weight:800;">${c.flag || '🌐'} ${c.name} (${iso3})</span><br>
                        <span style="color:#0284c7; font-weight:600;">${modeIcon} ${hub}</span><br>
                        <span style="color:#64748b; font-size:10px;">Klik untuk ${!traceOrigin ? 'Titik Asal (Step 1)' : 'Titik Tujuan (Step 2)'}</span>
                    </div>
                `, { direction: 'top', offset: [0, -6] });

                node.on('click', function(e) {
                    L.DomEvent.stopPropagation(e);
                    handleCountryTraceSelect(c);
                });

                node.addTo(traceNodesGroup);
            });
        }

        function handleCountryTraceSelect(c) {
            if (!traceOrigin) {
                // STEP 1: Set Origin
                traceOrigin = c;
                renderOriginPin(c);

                const bannerText = document.getElementById('route-step-text');
                if (bannerText) {
                    bannerText.innerHTML = '<span class="text-purple-600 dark:text-purple-400 font-bold">Step 2: Click Destination Country on map</span>';
                }
            } else if (traceOrigin && !traceDestination) {
                // STEP 2: Set Destination
                if (c.code === traceOrigin.code && c.name === traceOrigin.name) {
                    alert('Silakan pilih negara tujuan yang berbeda dari negara asal.');
                    return;
                }
                traceDestination = c;
                drawTraceRoute(traceOrigin, traceDestination);
            } else {
                // Reset and start as new Origin
                resetRouteTrackerInteractive(false);
                traceOrigin = c;
                renderOriginPin(c);
                const bannerText = document.getElementById('route-step-text');
                if (bannerText) {
                    bannerText.innerHTML = '<span class="text-purple-600 dark:text-purple-400 font-bold">Step 2: Click Destination Country on map</span>';
                }
            }
        }

        function renderOriginPin(c) {
            if (traceOriginMarker) {
                routeLayerGroup.removeLayer(traceOriginMarker);
            }
            const iso3 = getIso3Code(c);
            const icon = L.divIcon({
                className: 'route-node-pin-wrapper',
                html: `<div class="route-node-pin route-node-origin" style="width:26px; height:26px;">${iso3}</div>`,
                iconSize: [26, 26],
                iconAnchor: [13, 13]
            });
            traceOriginMarker = L.marker([c.lat, c.lng], { icon }).addTo(routeLayerGroup);
        }

        function renderDestPin(c) {
            if (traceDestMarker) {
                routeLayerGroup.removeLayer(traceDestMarker);
            }
            const iso3 = getIso3Code(c);
            const icon = L.divIcon({
                className: 'route-node-pin-wrapper',
                html: `<div class="route-node-pin route-node-dest" style="width:26px; height:26px;">${iso3}</div>`,
                iconSize: [26, 26],
                iconAnchor: [13, 13]
            });
            traceDestMarker = L.marker([c.lat, c.lng], { icon }).addTo(routeLayerGroup);
        }

        function drawTraceRoute(origin, dest) {
            renderOriginPin(origin);
            renderDestPin(dest);

            if (traceRouteLine) {
                routeLayerGroup.removeLayer(traceRouteLine);
            }

            const p1 = [parseFloat(origin.lat), parseFloat(origin.lng)];
            const p2 = [parseFloat(dest.lat), parseFloat(dest.lng)];
            const distKm = calculateDistanceKm(p1[0], p1[1], p2[0], p2[1]);
            const distNM = distKm / 1.852;

            // Generate Waypoints depending on mode (Sea Maritime vs Air Corridors)
            let pathPoints = [];
            let timeStr = '';
            let speedStr = '';
            let corridorStr = '';
            let riskScore = 25;
            let riskLabel = 'Safe Route';
            let riskBgColor = '#10B981';

            if (traceTransportMode === 'AIR') {
                // Great Circle Air Geodesic Arc
                const numSteps = 16;
                pathPoints.push(p1);
                for (let i = 1; i < numSteps; i++) {
                    const f = i / numSteps;
                    const lat = p1[0] + (p2[0] - p1[0]) * f;
                    const lng = p1[1] + (p2[1] - p1[1]) * f;
                    const arcOffset = Math.sin(f * Math.PI) * Math.min(12, distKm / 450);
                    pathPoints.push([lat + arcOffset, lng]);
                }
                pathPoints.push(p2);

                const airHours = (distKm / 850 + 2.5).toFixed(1);
                timeStr = `${airHours} Jam`;
                speedStr = '850 km/h (Jet Kargo Udara)';
                corridorStr = `International Airway Corridor (${getIso3Code(origin)} ➔ ${getIso3Code(dest)})`;

                riskScore = Math.min(85, Math.max(20, Math.round(distKm / 600) + 15));
            } else {
                // Maritime Sea Corridors
                const isAsiaToEurope = (p1[1] > 60 && p2[1] < 30) || (p2[1] > 60 && p1[1] < 30);
                const isAsiaToAmericas = (p1[1] > 80 && p2[1] < -60) || (p2[1] > 80 && p1[1] < -60);

                if (isAsiaToEurope) {
                    pathPoints = [
                        p1,
                        [2.5, 101.5],   // Malacca Strait
                        [6.0, 78.0],    // Indian Ocean
                        [12.5, 43.5],   // Bab el-Mandeb
                        [27.5, 34.0],   // Red Sea
                        [31.2, 32.3],   // Suez Canal
                        [36.0, 15.0],   // Central Med
                        [36.0, -5.5],   // Gibraltar
                        p2
                    ];
                    corridorStr = 'Selat Malaka ➔ Laut Arab ➔ Terusan Suez ➔ Mediterania';
                    riskScore = 58;
                } else if (isAsiaToAmericas) {
                    pathPoints = [
                        p1,
                        [35.0, 145.0],  // East Japan
                        [45.0, 180.0],  // North Pacific Great Circle
                        [40.0, -145.0], // West Coast US approach
                        p2
                    ];
                    corridorStr = 'North Pacific Great Circle Sea Highway';
                    riskScore = 32;
                } else {
                    const midLat = (p1[0] + p2[0]) / 2 - (distKm > 2000 ? 5 : 1);
                    const midLng = (p1[1] + p2[1]) / 2;
                    pathPoints = [p1, [midLat, midLng], p2];
                    corridorStr = `Maritime Coastal Route (${getIso3Code(origin)} ➔ ${getIso3Code(dest)})`;
                    riskScore = Math.min(80, Math.max(25, Math.round(distKm / 500) + 10));
                }

                const seaDays = Math.max(1, Math.round(distKm / (20 * 1.852 * 24)));
                timeStr = `${seaDays} - ${seaDays + 3} Hari`;
                speedStr = '20 Knots (~37 km/h)';
            }

            if (riskScore <= 35) {
                riskLabel = 'Safe Route';
                riskBgColor = '#10B981';
            } else if (riskScore <= 65) {
                riskLabel = 'Moderate Risk';
                riskBgColor = '#F59E0B';
            } else {
                riskLabel = 'Elevated Risk / Caution';
                riskBgColor = '#EF4444';
            }

            // Draw Dashed Polyline
            traceRouteLine = L.polyline(pathPoints, {
                color: '#10B981',
                weight: 3.5,
                dashArray: '6, 8',
                opacity: 0.95
            }).addTo(routeLayerGroup);

            // Update Banner
            const oIso3 = getIso3Code(origin);
            const dIso3 = getIso3Code(dest);
            const bannerText = document.getElementById('route-step-text');
            if (bannerText) {
                bannerText.innerHTML = `<span class="text-emerald-600 dark:text-emerald-400 font-bold">Route Analyzed: ${oIso3} ${dIso3}</span>`;
            }

            // Store for Modal
            lastAnalyzedRoute = {
                origin,
                dest,
                oIso3,
                dIso3,
                mode: traceTransportMode,
                modeName: traceTransportMode === 'AIR' ? '✈️ Bandara (Udara)' : '⚓ Pelabuhan (Laut)',
                distKm: Math.round(distKm),
                distNM: Math.round(distNM),
                timeStr,
                speedStr,
                corridorStr,
                riskScore,
                riskLabel,
                riskBgColor
            };
            window.lastAnalyzedRoute = lastAnalyzedRoute;

            // Fit Bounds
            map.fitBounds(L.latLngBounds([p1, p2]).pad(0.25), { duration: 1.0 });

            // Popup on Destination Marker (Matches Screenshot 2!)
            const oIsoLower = (origin.code || '').toLowerCase().replace('_east','');
            const dIsoLower = (dest.code || '').toLowerCase().replace('_east','');
            const modeIcon = traceTransportMode === 'AIR' ? '✈️' : '⚓';

            const popupContent = `
                <div style="min-width:210px; font-family: Outfit, sans-serif; text-align:center; padding:4px 2px;">
                    <div style="font-weight:700; font-size:12px; color:#1e293b; margin-bottom:4px;">Route Analysis</div>
                    <div style="display:flex; align-items:center; justify-content:center; gap:6px; font-weight:800; font-size:13px; margin-bottom:8px;">
                        <span><img src="https://flagcdn.com/w40/${oIsoLower}.png" style="width:18px;height:12px;object-fit:cover;border-radius:2px;display:inline;vertical-align:middle;margin-right:2px;"> ${oIso3}</span>
                        <span style="color:#94a3b8;">&rarr;</span>
                        <span><img src="https://flagcdn.com/w40/${dIsoLower}.png" style="width:18px;height:12px;object-fit:cover;border-radius:2px;display:inline;vertical-align:middle;margin-right:2px;"> ${dIso3}</span>
                    </div>
                    <div style="margin-bottom:6px;">
                        <span style="background:${riskBgColor}; color:#ffffff; font-weight:800; font-size:11px; padding:3px 12px; border-radius:9999px; box-shadow:0 1px 3px rgba(0,0,0,0.12); display:inline-block;">
                            Transit Risk: ${riskScore}/100
                        </span>
                    </div>
                    <div style="font-size:11px; color:#64748b; font-weight:600; margin-bottom:4px;">
                        ${riskLabel}
                    </div>
                    <div style="font-size:10px; color:#0369a1; font-weight:700; margin-bottom:10px;">
                        ${modeIcon} Est. ${timeStr} &bull; ${Math.round(distKm)} km
                    </div>
                    <button type="button" id="btn-view-route-analytics" onclick="event.stopPropagation(); window.openRouteAnalyticsModal();" style="width:100%; background:#7C3AED; color:#ffffff; border:none; padding:8px 12px; border-radius:10px; font-weight:700; font-size:11px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 2px 6px rgba(124,58,237,0.35); transition:background 0.2s; pointer-events:auto !important; position:relative; z-index:999;">
                        <svg style="width:13px; height:13px; pointer-events:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                        <span style="pointer-events:none;">View Analytics</span>
                    </button>
                </div>
            `;

            setTimeout(() => {
                L.popup({ maxWidth: 260, offset: [0, -10] })
                    .setLatLng(p2)
                    .setContent(popupContent)
                    .openOn(map);
            }, 300);
        }

        function setTraceTransportMode(mode) {
            traceTransportMode = mode;
            const btnPort = document.getElementById('trace-btn-port');
            const btnAir  = document.getElementById('trace-btn-air');

            if (mode === 'PORT') {
                if (btnPort) {
                    btnPort.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-slate-900 text-sky-600 dark:text-sky-400 shadow-xs flex items-center gap-1';
                }
                if (btnAir) {
                    btnAir.className = 'px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1';
                }
            } else {
                if (btnAir) {
                    btnAir.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-slate-900 text-sky-600 dark:text-sky-400 shadow-xs flex items-center gap-1';
                }
                if (btnPort) {
                    btnPort.className = 'px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1';
                }
            }

            renderCountryTraceNodes();
            if (traceOrigin && traceDestination) {
                drawTraceRoute(traceOrigin, traceDestination);
            }
        }

        function resetRouteTrackerInteractive(flyToWorld = true) {
            traceOrigin = null;
            traceDestination = null;
            lastAnalyzedRoute = null;

            if (traceRouteLine) {
                routeLayerGroup.removeLayer(traceRouteLine);
                traceRouteLine = null;
            }
            if (traceOriginMarker) {
                routeLayerGroup.removeLayer(traceOriginMarker);
                traceOriginMarker = null;
            }
            if (traceDestMarker) {
                routeLayerGroup.removeLayer(traceDestMarker);
                traceDestMarker = null;
            }

            map.closePopup();

            const bannerText = document.getElementById('route-step-text');
            if (bannerText) {
                bannerText.innerHTML = '<span class="text-purple-600 dark:text-purple-400 font-bold">Step 1: Click Origin Country on map</span>';
            }

            if (flyToWorld) {
                map.flyTo([20, 20], 2.2, { duration: 1.0 });
            }
        }

        function openRouteAnalyticsModal() {
            const r = lastAnalyzedRoute || window.lastAnalyzedRoute;
            if (!r) return;
            const modal = document.getElementById('route-analytics-modal');
            if (!modal) return;

            const oIso = (r.origin.code || '').toLowerCase().replace('_east','');
            const dIso = (r.dest.code || '').toLowerCase().replace('_east','');

            const setTxt = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

            setTxt('modal-origin-code', r.oIso3);
            const oFlag = document.getElementById('modal-origin-flag');
            if (oFlag) oFlag.src = `https://flagcdn.com/w40/${oIso}.png`;
            setTxt('modal-origin-country', r.origin.name);
            setTxt('modal-origin-hub', getCountryHubName(r.origin, r.mode));
            setTxt('modal-origin-coords', `${parseFloat(r.origin.lat).toFixed(3)}°, ${parseFloat(r.origin.lng).toFixed(3)}°`);

            setTxt('modal-dest-code', r.dIso3);
            const dFlag = document.getElementById('modal-dest-flag');
            if (dFlag) dFlag.src = `https://flagcdn.com/w40/${dIso}.png`;
            setTxt('modal-dest-country', r.dest.name);
            setTxt('modal-dest-hub', getCountryHubName(r.dest, r.mode));
            setTxt('modal-dest-coords', `${parseFloat(r.dest.lat).toFixed(3)}°, ${parseFloat(r.dest.lng).toFixed(3)}°`);

            setTxt('modal-mode-badge', r.modeName);
            setTxt('modal-distance', `${r.distKm.toLocaleString()} km (${r.distNM.toLocaleString()} NM)`);
            setTxt('modal-duration', r.timeStr);
            setTxt('modal-speed', r.speedStr);
            setTxt('modal-corridor', r.corridorStr);

            const riskBadge = document.getElementById('modal-risk-badge');
            if (riskBadge) {
                riskBadge.textContent = `Transit Risk: ${r.riskScore}/100 (${r.riskLabel})`;
                riskBadge.style.backgroundColor = r.riskBgColor;
            }

            const riskBar = document.getElementById('modal-risk-bar');
            if (riskBar) {
                riskBar.style.width = `${r.riskScore}%`;
                riskBar.style.backgroundColor = r.riskBgColor;
            }

            let riskDesc = '';
            let insRec = '';
            if (r.riskScore <= 35) {
                riskDesc = 'Koridor transit ini dikategorikan aman dengan risiko cuaca atau hambatan operasional minimal. Integritas dan ketepatan waktu pengiriman kargo berada pada level optimum.';
                insRec = '🛡️ Proteksi Asuransi Standar Logistik Direkomendasikan';
            } else if (r.riskScore <= 65) {
                riskDesc = 'Rute melintasi perairan/ruang udara dengan variabilitas cuaca sedang atau kepadatan lalu lintas transit. Disarankan monitoring real-time berkala.';
                insRec = '⚠️ Direkomendasikan Tambahan Asuransi All-Risk Transit';
            } else {
                riskDesc = 'Rute melewati koridor transit jarak jauh lintas samudra atau selat internasional yang rentan cuaca buruk / kepadatan tinggi. Wajib pemantauan telemetry ketat.';
                insRec = '🚨 Wajib Proteksi Asuransi Komprehensif Tingkat Tinggi';
            }
            setTxt('modal-risk-description', riskDesc);
            setTxt('modal-insurance-rec', insRec);

            const orderBtn = document.getElementById('modal-order-btn');
            if (orderBtn) {
                orderBtn.href = `{{ route('admin.shipments.create') }}?mode=${r.mode || 'SEA'}&origin=${encodeURIComponent(r.origin.name)}&destination=${encodeURIComponent(r.dest.name)}`;
            }

            modal.classList.remove('hidden');
            modal.style.display = 'flex';

            try {
                if (window.lucide) lucide.createIcons();
            } catch (err) {}
        }

        function closeRouteAnalyticsModal() {
            const modal = document.getElementById('route-analytics-modal');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
        }

        window.openRouteAnalyticsModal = openRouteAnalyticsModal;
        window.closeRouteAnalyticsModal = closeRouteAnalyticsModal;

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

                // Two-way synchronization saat marker cuaca di peta di-klik
                marker.on('click', function() {
                    syncSidebarWithCountry(w.code || w.iso, w.country, `${w.city} (${w.temp}°C, ${w.condition})`);
                });

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

                        <!-- Weather & Marine Operation Status -->
                        <div style="display:flex; align-items:center; justify-content:space-between; background:#F0FDFA; color:#0F766E; border:1px solid #CCFBF1; padding:7px 12px; border-radius:10px; font-weight:700; font-size:12px; margin-bottom:8px;">
                            <span style="display:flex; align-items:center; gap:6px;">
                                <span>🌊</span> Operasional Maritim
                            </span>
                            <span style="font-weight:800; color:#0D9488;">Aktif Normal</span>
                        </div>

                        <!-- Weather Info Box -->
                        <div style="display:flex; align-items:center; justify-content:center; gap:8px; background:#F8FAFC; color:#334155; border:1px solid #E2E8F0; padding:8px 12px; border-radius:10px; font-weight:700; font-size:12px; margin-bottom:12px;">
                            <span>🌡️ ${w.temp}°C</span>
                            <span style="color:#cbd5e1; font-weight:400;">|</span>
                            <span>${w.condition}</span>
                        </div>

                        <!-- View Full Profile Button -->
                        <a href="${weatherDetailUrl}" style="display:block; text-align:center; background:#EDE9FE; color:#7C3AED; font-weight:700; padding:9px 14px; border-radius:10px; text-decoration:none; font-size:12px; transition:all 0.2s; box-shadow:0 1px 3px rgba(124,58,237,0.15);">
                            Cek Profil Cuaca Lengkap →
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
                const marker = L.circleMarker([p.lat, p.lng], {
                    radius: 6, fillColor: '#8B5CF6', color: '#ffffff', weight: 1.5, fillOpacity: 0.92
                });
                marker.countryCode = (p.iso || '').toUpperCase();
                marker.countryName = (p.country || '').toLowerCase();
                marker.portName = (p.name || '').toLowerCase();

                // Two-way synchronization saat marker pelabuhan di-klik: sinkronkan sidebar
                marker.on('click', function() {
                    syncSidebarWithCountry(p.iso, p.country, p.name);
                });

                marker.bindPopup(`
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
                `, { maxWidth: 280 });
                marker.addTo(portsLayerGroup);
            });
        }

        // ── BUILD AIRPORTS LAYER (FreeAirportDB 60+ International Air Cargo Hubs) ────────
        function buildAirportsLayer() {
            airportsLayerGroup.clearLayers();
            if (!Array.isArray(airportsData)) return;

            airportsData.forEach(a => {
                const lat = parseFloat(a.lat);
                const lng = parseFloat(a.lng);
                if (isNaN(lat) || isNaN(lng)) return;

                const icon = L.divIcon({
                    className: 'airport-marker-wrapper',
                    html: `
                        <div style="background:#0284C7; color:#fff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px; border:2px solid #ffffff; box-shadow:0 3px 8px rgba(2,132,199,0.55); cursor:pointer;">
                            ✈️
                        </div>
                    `,
                    iconSize: [26, 26],
                    iconAnchor: [13, 13]
                });

                const marker = L.marker([lat, lng], { icon });
                marker.countryCode = (a.iso || '').toUpperCase();
                marker.countryName = (a.country || '').toLowerCase();
                marker.airportCode = (a.code || '').toUpperCase();

                marker.on('click', function() {
                    syncSidebarWithCountry(a.iso, a.country, a.name);
                });

                marker.bindPopup(`
                    <div style="min-width:240px; font-family: Outfit, sans-serif; padding:4px 2px;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                            <img src="https://flagcdn.com/w40/${a.iso || 'un'}.png" alt="${a.country}"
                                 style="width:30px; height:20px; object-fit:cover; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.18); border:1px solid #e2e8f0; flex-shrink:0;">
                            <div>
                                <div style="font-weight:800; font-size:14px; color:#0f172a; line-height:1.2;">${a.name}</div>
                                <div style="font-size:11px; color:#64748b;">${a.country} &bull; ${a.city}</div>
                            </div>
                        </div>
                        <div style="background:#E0F2FE; color:#0369A1; border:1px solid #BAE6FD; padding:6px 10px; border-radius:10px; font-weight:700; font-size:11px; margin-bottom:7px; display:flex; justify-content:space-between; align-items:center;">
                            <span>✈️ IATA: <strong>${a.code}</strong></span>
                            <span style="font-size:10px; color:#0284c7;">ICAO: ${a.icao || '-'}</span>
                        </div>
                        <div style="font-size:11px; color:#334155; line-height:1.6; margin-bottom:8px;">
                            <strong>Kapasitas Kargo:</strong> ${a.capacity || 'TBA'}<br>
                            <strong>Panjang Runway:</strong> ${a.runway || '-'}<br>
                            <strong>Tipe Hub:</strong> ${a.type || 'International Cargo Airport'}
                        </div>
                        <a href="${AIRPORTS_PAGE_URL}?search=${a.code}" class="block text-center text-[10px] font-bold py-1.5 px-3 rounded-lg bg-sky-600 hover:bg-sky-500 text-white transition-colors" style="text-decoration:none;">
                            Buka Direktori Bandara Lengkap &rarr;
                        </a>
                    </div>
                `, { maxWidth: 280 });

                marker.addTo(airportsLayerGroup);
            });
        }

        // ── CARGO TRANSPORT FILTER (Semua / Laut / Udara / Mobil Dekat) ────────
        function setCargoTransportFilter(mode) {
            cargoTransportFilter = mode;
            const btnAll = document.getElementById('filter-cargo-all');
            const btnSea = document.getElementById('filter-cargo-sea');
            const btnAir = document.getElementById('filter-cargo-air');
            const btnLand = document.getElementById('filter-cargo-land');

            [btnAll, btnSea, btnAir, btnLand].forEach(b => {
                if (b) {
                    b.className = 'px-2 py-0.5 rounded text-[10px] font-semibold text-slate-300 hover:text-white transition-all';
                }
            });

            if (mode === 'SEA') {
                if (btnSea) btnSea.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-blue-600 text-white shadow-xs transition-all';
            } else if (mode === 'AIR') {
                if (btnAir) btnAir.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-purple-600 text-white shadow-xs transition-all';
            } else if (mode === 'LAND') {
                if (btnLand) btnLand.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-amber-600 text-white shadow-xs transition-all';
            } else {
                if (btnAll) btnAll.className = 'px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-600 text-white shadow-xs transition-all';
            }

            const latLngs = buildCargoLayer();
            if (latLngs && latLngs.length > 0) {
                map.flyToBounds(latLngs, { padding: [50, 50], maxZoom: 6, duration: 1.2 });
            } else {
                map.setView([15, 20], 2);
            }
        }

        function setCargoContinentFilter(continent) {
            cargoContinentFilter = continent || 'ALL';
            const sel = document.getElementById('filter-cargo-continent');
            if (sel && sel.value !== cargoContinentFilter) {
                sel.value = cargoContinentFilter;
            }
            const latLngs = buildCargoLayer();
            if (latLngs && latLngs.length > 0) {
                map.flyToBounds(latLngs, { padding: [50, 50], maxZoom: 6, duration: 1.2 });
            } else {
                map.setView([15, 20], 2);
            }
        }

        // ── TWO-WAY SYNC HELPER (Marker Klik -> Update Sidebar Negara) ────────
        function syncSidebarWithCountry(codeOrIso, countryName, hubName) {
            if (!codeOrIso && !countryName) return;
            const isoClean = (codeOrIso || '').toLowerCase().replace('_east', '').trim();
            const nameClean = (countryName || '').toLowerCase().trim();

            // 1. Update Card Selected Country
            const card = document.getElementById('selected-country-card');
            const flagImg = document.getElementById('selected-flag-img');
            const nameEl = document.getElementById('selected-name');
            const hubEl = document.getElementById('selected-hub');
            const carrierWrapper = document.getElementById('selected-carrier-wrapper');
            const carrierEl = document.getElementById('selected-carrier');

            if (flagImg) {
                flagImg.src = `https://flagcdn.com/w40/${isoClean || 'id'}.png`;
                flagImg.alt = countryName || '';
            }
            if (nameEl) nameEl.textContent = countryName || '';
            if (hubEl) hubEl.textContent = ' ' + (hubName || 'Pelabuhan & Hub Logistik');

            // Tampilkan ekspedisi resmi negara tersebut jika ada
            if (typeof carriersData !== 'undefined' && Array.isArray(carriersData)) {
                const matchedCarrier = carriersData.find(c => {
                    const cIso = (c.iso || '').toLowerCase().trim();
                    const cCountry = (c.country || '').toLowerCase().trim();
                    return cIso === isoClean || cCountry === nameClean || nameClean.includes(cCountry);
                });
                if (matchedCarrier && carrierWrapper && carrierEl) {
                    carrierEl.textContent = `${matchedCarrier.name} (${matchedCarrier.code})`;
                    carrierWrapper.classList.remove('hidden');
                } else if (carrierWrapper) {
                    carrierWrapper.classList.add('hidden');
                }
            }

            if (card) card.classList.remove('hidden');

            // 2. Sorot & Auto-Scroll item di daftar negara
            let targetLi = null;
            document.querySelectorAll('.country-item').forEach(li => {
                li.classList.remove('bg-cyan-50', 'border-l-2', 'border-cyan-500');
                const liCode = (li.dataset.code || '').toLowerCase().replace('_east', '').trim();
                const liIso  = (li.dataset.iso || '').toLowerCase().trim();
                const liName = (li.dataset.name || '').toLowerCase().trim();

                if (!targetLi && (liIso === isoClean || liCode === isoClean || liName === nameClean || nameClean.includes(liName) || liName.includes(nameClean))) {
                    targetLi = li;
                }
            });

            if (targetLi) {
                targetLi.classList.add('bg-cyan-50', 'border-l-2', 'border-cyan-500');
                targetLi.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        // ── SWITCH MODE ─────────────────────────────────────────
        function switchMapMode(mode) {
            currentMode = mode;
            map.removeLayer(cargoLayerGroup);
            map.removeLayer(routeLayerGroup);
            map.removeLayer(weatherLayerGroup);
            map.removeLayer(portsLayerGroup);
            map.removeLayer(airportsLayerGroup);

            const cargoFilterEl = document.getElementById('cargo-mode-subfilter');
            if (mode === 'cargo') {
                if (cargoFilterEl) cargoFilterEl.classList.remove('hidden');
            } else {
                if (cargoFilterEl) cargoFilterEl.classList.add('hidden');
            }

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
                    map.flyTo([20, 20], 2.2, { duration: 1.2 });
                } else if (mode === 'weather') {
                    buildWeatherLayer();
                    weatherLayerGroup.addTo(map);
                    map.flyTo([20, 20], 2.2, { duration: 1.2 });
                } else if (mode === 'ports') {
                    buildPortsLayer();
                    portsLayerGroup.addTo(map);
                    map.flyTo([20, 20], 2.2, { duration: 1.2 });
                } else if (mode === 'airports') {
                    buildAirportsLayer();
                    airportsLayerGroup.addTo(map);
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

            // Tutup popup aktif sebelumnya agar tidak tertinggal negara sebelumnya
            map.closePopup();

            // Adaptive zoom based on country size (continental nations need wider field of view)
            const zoomLevel = ['RU', 'CA', 'US', 'CN', 'AU', 'BR', 'KZ', 'IN'].includes(code) ? 3.6 : 5.5;
            map.flyTo([lat, lng], zoomLevel, { duration: 1.2 });

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
                const nameLower = name.toLowerCase();
                const isRussianMatch = (code === 'RU' || nameLower.includes('rusia') || nameLower.includes('russia'));

                weatherLayerGroup.eachLayer(layer => {
                    const lCode = (layer.countryCode || '').toUpperCase();
                    const lName = (layer.countryName || '').toLowerCase();
                    const match = isRussianMatch
                        ? (lCode === 'RU' || lCode.startsWith('RU_') || lName.includes('russia') || lName.includes('rusia'))
                        : (lCode === code || lName.includes(nameLower) || nameLower.includes(lName));

                    if (!found && match) {
                        setTimeout(() => layer.openPopup(), 400);
                        found = true;
                    }
                });
                if (!found) {
                    weatherLayerGroup.eachLayer(layer => {
                        const lLatLng = layer.getLatLng();
                        if (!found && Math.hypot(lLatLng.lat - lat, lLatLng.lng - lng) < 5) {
                            setTimeout(() => layer.openPopup(), 400);
                            found = true;
                        }
                    });
                }
            } else if (currentMode === 'ports') {
                let found = false;
                const countryCodeUpper = code.toUpperCase();
                const countryNameLower = name.toLowerCase();
                const hubLower = (hub || '').toLowerCase();

                // 1. Exact match by countryCode (ISO) or countryName
                portsLayerGroup.eachLayer(layer => {
                    if (!found && layer.countryCode && (layer.countryCode === countryCodeUpper || layer.countryName === countryNameLower)) {
                        setTimeout(() => layer.openPopup(), 400);
                        found = true;
                    }
                });

                // 2. Match by Hub Name
                if (!found && hubLower) {
                    portsLayerGroup.eachLayer(layer => {
                        if (!found && layer.portName && (layer.portName.includes(hubLower) || hubLower.includes(layer.portName))) {
                            setTimeout(() => layer.openPopup(), 400);
                            found = true;
                        }
                    });
                }

                // 3. Jika pelabuhan spesifik belum tercatat di koordinat berbeda, buat popup terarah khusus negara ini
                if (!found) {
                    setTimeout(() => {
                        L.popup({ maxWidth: 280 })
                            .setLatLng([lat, lng])
                            .setContent(`
                                <div style="min-width:240px; font-family: Outfit, sans-serif; padding:4px 2px;">
                                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                                        <img src="https://flagcdn.com/w40/${iso}.png" alt="${name}"
                                             style="width:30px; height:20px; object-fit:cover; border-radius:4px; box-shadow:0 1px 3px rgba(0,0,0,0.18); border:1px solid #e2e8f0; flex-shrink:0;">
                                        <div>
                                            <div style="font-weight:800; font-size:14px; color:#0f172a; line-height:1.2;">${hub || (name + ' Gateway')}</div>
                                            <div style="font-size:11px; color:#64748b;">${name} &bull; Hub Logistik Nasional</div>
                                        </div>
                                    </div>
                                    <div style="background:#F3E8FF; color:#6B21A8; border:1px solid #E9D5FF; padding:7px 10px; border-radius:10px; font-weight:700; font-size:11px; margin-bottom:7px;">
                                        ⚓ Hub Kode: <b>${code}HUB</b> &nbsp;|&nbsp; Simpul Maritim Nasional
                                    </div>
                                    <div style="background:#F1F5F9; color:#334155; border:1px solid #E2E8F0; padding:7px 10px; border-radius:10px; font-weight:600; font-size:11px; margin-bottom:11px;">
                                        📦 Status: <b>Aktif Terintegrasi di CargoTrace</b>
                                    </div>
                                    <a href="${PORTS_PAGE_URL}" style="display:block; text-align:center; background:#EDE9FE; color:#7C3AED; font-weight:700; padding:8px 12px; border-radius:10px; text-decoration:none; font-size:12px; box-shadow:0 1px 3px rgba(124,58,237,0.15);">
                                        Cek Direktori Pelabuhan →
                                    </a>
                                </div>
                            `)
                            .openOn(map);
                    }, 400);
                }
            } else if (currentMode === 'route') {
                const countryObj = countriesData.find(c => {
                    const cCode = (c.code || '').toUpperCase().replace('_EAST','');
                    const cName = (c.name || '').toLowerCase();
                    return cCode === code.replace('_EAST','') || cName === name.toLowerCase();
                });
                if (countryObj) {
                    handleCountryTraceSelect(countryObj);
                }
            } else if (currentMode === 'cargo') {
                // Berikan info transit kargo untuk negara yang dipilih
                setTimeout(() => {
                    L.popup({ maxWidth: 280 })
                        .setLatLng([lat, lng])
                        .setContent(`
                            <div style="min-width:230px; font-family: Outfit, sans-serif; padding:4px 2px;">
                                <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                                    <img src="https://flagcdn.com/w40/${iso}.png" alt="${name}"
                                         style="width:28px; height:18px; object-fit:cover; border-radius:3px; border:1px solid #e2e8f0;">
                                    <div>
                                        <div style="font-weight:800; font-size:13px; color:#0f172a;">${name}</div>
                                        <div style="font-size:11px; color:#64748b;">${hub}</div>
                                    </div>
                                </div>
                                <div style="background:#ECFEFF; color:#0E7490; border:1px solid #CFFAFE; padding:6px 10px; border-radius:8px; font-size:11px; font-weight:600;">
                                    🚢 Jalur pelayaran & simpul kargo aktif
                                </div>
                            </div>
                        `)
                        .openOn(map);
                }, 400);
            } else if (currentMode === 'airports') {
                let foundApt = false;
                airportsLayerGroup.eachLayer(layer => {
                    if (!foundApt && layer.countryCode && (layer.countryCode === code.replace('_EAST','').toUpperCase() || layer.countryName === name.toLowerCase())) {
                        setTimeout(() => layer.openPopup(), 400);
                        foundApt = true;
                    }
                });
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
        window.switchMapMode                 = typeof switchMapMode === 'function' ? switchMapMode : function() {};
        window.resetMapView                  = typeof resetMapView === 'function' ? resetMapView : function() {};
        window.focusCountry                  = typeof focusCountry === 'function' ? focusCountry : function() {};
        window.filterCountriesList           = typeof filterCountriesList === 'function' ? filterCountriesList : function() {};
        window.focusCargoShipment            = typeof focusCargoShipment === 'function' ? focusCargoShipment : function() {};
        window.resetShipmentFocus            = typeof resetCargoShipmentFocus === 'function' ? resetCargoShipmentFocus : function() {};
        window.setTraceTransportMode         = typeof setTraceTransportMode === 'function' ? setTraceTransportMode : function() {};
        window.setCargoTransportFilter       = typeof setCargoTransportFilter === 'function' ? setCargoTransportFilter : function() {};
        window.setCargoContinentFilter       = typeof setCargoContinentFilter === 'function' ? setCargoContinentFilter : function() {};
        window.resetRouteTrackerInteractive  = typeof resetRouteTrackerInteractive === 'function' ? resetRouteTrackerInteractive : function() {};
        window.openRouteAnalyticsModal       = typeof openRouteAnalyticsModal === 'function' ? openRouteAnalyticsModal : function() {};
        window.closeRouteAnalyticsModal      = typeof closeRouteAnalyticsModal === 'function' ? closeRouteAnalyticsModal : function() {};

        // Klik di area peta kosong mengembalikan fokus rute ke normal
        map.on('click', function(e) {
            if (activeCargoId) {
                resetCargoShipmentFocus();
            }
        });

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
    }

    // Dual-trigger bootloader: ensures execution whether DOM is loading, interactive, or complete
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDashboard);
    } else {
        // Document already loaded or interactive - fire immediately
        initDashboard();
    }
</script>
@endpush

