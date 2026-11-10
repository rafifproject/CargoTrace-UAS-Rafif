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
                <!-- Leaflet Map -->
                <div id="dashboard-map" class="flex-1 z-10" style="min-height:460px"></div>
                <!-- Legend -->
                <div id="map-legend" class="flex items-center gap-4 px-4 py-2 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-100 dark:border-slate-800/60 text-[11px] text-slate-500 dark:text-slate-400 flex-wrap">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Asal</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>Tujuan</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-cyan-400 inline-block"></span>Posisi Terkini</span>
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
                <div id="selected-country-card" class="hidden mx-3 mt-3 p-3 rounded-xl bg-cyan-50 dark:bg-cyan-900/20 border border-cyan-200 dark:border-cyan-700/50 text-xs">
                    <div class="flex items-center gap-2 mb-1">
                        <span id="selected-flag" class="text-xl">🌍</span>
                        <span id="selected-name" class="font-bold text-slate-800 dark:text-white text-sm"></span>
                    </div>
                    <div class="text-slate-500 dark:text-slate-400">
                        <span class="font-semibold text-slate-700 dark:text-slate-300">Hub Utama:</span>
                        <span id="selected-hub"></span>
                    </div>
                </div>
                <!-- Scrollable Country List -->
                <div class="flex-1 overflow-y-auto" style="max-height:400px">
                    <ul id="country-list" class="divide-y divide-slate-100 dark:divide-slate-800">
                        @foreach($countriesList as $country)
                        <li class="country-item px-4 py-2.5 flex items-center gap-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors"
                            data-name="{{ $country['name'] }}"
                            data-code="{{ $country['code'] }}"
                            data-lat="{{ $country['lat'] }}"
                            data-lng="{{ $country['lng'] }}"
                            data-flag="{{ $country['flag'] }}"
                            data-hub="{{ $country['hub'] }}"
                            onclick="focusCountry(this)">
                            <span class="text-xl leading-none">{{ $country['flag'] }}</span>
                            <span class="text-xs font-medium text-slate-700 dark:text-slate-200">{{ $country['name'] }}</span>
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
            maxZoom: 12
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Data from PHP controller
        const shipments    = {!! json_encode($mapShipments) !!};
        const portsData    = {!! json_encode($globalPorts) !!};
        const weatherData  = {!! json_encode($weatherHubs) !!};
        const countriesData = {!! json_encode($countriesList) !!};

        // Layer groups for each mode
        const cargoLayerGroup   = L.layerGroup();
        const weatherLayerGroup = L.layerGroup();
        const portsLayerGroup   = L.layerGroup();

        let currentMode = 'cargo';

        // ── MODE BUTTON STYLES ──────────────────────────────────
        function updateModeButtons(mode) {
            document.querySelectorAll('.map-mode-btn').forEach(btn => {
                btn.classList.remove(
                    'bg-cyan-600','text-white','border-cyan-600',
                    'bg-amber-500','border-amber-500',
                    'bg-blue-600','border-blue-600'
                );
                btn.classList.add('border-slate-300','text-slate-500','bg-white');
                // dark support
                btn.style.backgroundColor = '';
                btn.style.color = '';
                btn.style.borderColor = '';
            });
            const activeMap = {
                'cargo':   { id: 'btn-cargo',   bg:'#0e6ba8', border:'#0e6ba8' },
                'weather': { id: 'btn-weather', bg:'#d97706', border:'#d97706' },
                'ports':   { id: 'btn-ports',   bg:'#059669', border:'#059669' }
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
            cargo:   { icon:'🧭', text:'Mode: Pemantau CargoTrace — Rute & Kargo Aktif', bg:'linear-gradient(90deg,#0A1128,#0e3a6e)' },
            weather: { icon:'⛅', text:'Mode: Cuaca Langsung — Kondisi Maritim Global', bg:'linear-gradient(90deg,#431407,#92400e)' },
            ports:   { icon:'⚓', text:'Mode: Sebaran Pelabuhan — Direktori Port Global', bg:'linear-gradient(90deg,#052e16,#065f46)' }
        };
        function updateBanner(mode) {
            const b = banners[mode];
            document.getElementById('banner-icon').textContent = b.icon;
            document.getElementById('banner-text').textContent = b.text;
            document.getElementById('map-mode-banner').style.background = b.bg;
        }

        // ── LEGEND UPDATE ───────────────────────────────────────
        const legends = {
            cargo:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Asal</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>Tujuan</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-cyan-400 inline-block"></span>Posisi Terkini</span>`,
            weather: `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block"></span>Aman</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>Waspada</span>
                      <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 inline-block"></span>Bahaya / Storm</span>`,
            ports:   `<span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>Pelabuhan Global</span>
                      <span class="flex items-center gap-1.5">Klik marker untuk detail</span>`
        };
        function updateLegend(mode) {
            document.getElementById('map-legend').innerHTML = legends[mode];
        }

        // ── BUILD CARGO LAYER ───────────────────────────────────
        function buildCargoLayer() {
            cargoLayerGroup.clearLayers();
            const latLngs = [];

            shipments.forEach(s => {
                if (!s.origin || !s.destination) return;

                const originCoords = [parseFloat(s.origin.latitude), parseFloat(s.origin.longitude)];
                const destCoords   = [parseFloat(s.destination.latitude), parseFloat(s.destination.longitude)];
                latLngs.push(originCoords, destCoords);

                // Origin marker
                L.circleMarker(originCoords, {
                    radius: 7, fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(`
                    <div style="min-width:200px">
                        <b class="text-emerald-600">🟢 Pelabuhan Asal</b><br>
                        <b>${s.origin.name}</b> — ${s.origin.city}<br>
                        <span style="font-size:11px;color:#64748b">No. Resi: <b>${s.shipment_number}</b></span><br>
                        <span style="font-size:11px">Status: <b>${s.status}</b></span><br>
                        <a href="/admin/shipments/${s.id}" style="color:#0ea5e9;font-size:11px;font-weight:600">📦 Lihat Detail Pengiriman →</a>
                    </div>
                `).addTo(cargoLayerGroup);

                // Destination marker
                L.circleMarker(destCoords, {
                    radius: 7, fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(`
                    <div style="min-width:200px">
                        <b class="text-rose-600">🔴 Pelabuhan Tujuan</b><br>
                        <b>${s.destination.name}</b> — ${s.destination.city}<br>
                        <span style="font-size:11px;color:#64748b">No. Resi: <b>${s.shipment_number}</b></span><br>
                        <a href="/admin/shipments/${s.id}" style="color:#0ea5e9;font-size:11px;font-weight:600">📦 Lihat Detail Pengiriman →</a>
                    </div>
                `).addTo(cargoLayerGroup);

                // Current location
                if (s.current_location) {
                    const cur = [parseFloat(s.current_location.latitude), parseFloat(s.current_location.longitude)];
                    latLngs.push(cur);
                    L.circleMarker(cur, {
                        radius: 8, fillColor: '#00A8E8', color: '#fff', weight: 2, fillOpacity: 0.9
                    }).bindPopup(`
                        <div style="min-width:200px">
                            <b style="color:#0ea5e9">🚢 Posisi Terkini</b><br>
                            ${s.current_location.name}<br>
                            <span style="font-size:11px">Kargo: <b>${s.shipment_number}</b> | Status: <b>${s.status}</b></span><br>
                            <a href="/admin/shipments/${s.id}" style="color:#0ea5e9;font-size:11px;font-weight:600">📦 Lihat Detail Pengiriman →</a>
                        </div>
                    `).addTo(cargoLayerGroup);
                }

                // Animated dashed polyline
                L.polyline([originCoords, destCoords], {
                    color: '#00A8E8', weight: 2.5, dashArray: '6, 10', opacity: 0.75
                }).addTo(cargoLayerGroup);
            });

            return latLngs;
        }

        // ── BUILD WEATHER LAYER ─────────────────────────────────
        const riskColors = { 'Aman': '#10B981', 'Waspada': '#F59E0B', 'Bahaya / Storm': '#EF4444' };
        function buildWeatherLayer() {
            weatherLayerGroup.clearLayers();
            weatherData.forEach(w => {
                const color = riskColors[w.risk] || '#94A3B8';
                L.circleMarker([w.lat, w.lng], {
                    radius: 11, fillColor: color, color: '#fff', weight: 2, fillOpacity: 0.85
                }).bindPopup(`
                    <div style="min-width:220px">
                        <b>${w.flag} ${w.country}</b> — ${w.city}<br>
                        <hr style="margin:4px 0;border-color:#e2e8f0">
                        🌡️ Suhu: <b>${w.temp}°C</b> &nbsp;|&nbsp; ${w.condition}<br>
                        💧 Kelembaban: <b>${w.humidity}%</b><br>
                        💨 Angin: <b>${w.wind} km/h</b><br>
                        🌧️ Presipitasi: <b>${w.precipitation} mm</b><br>
                        <span style="display:inline-block;margin-top:4px;padding:2px 8px;border-radius:9999px;background:${color};color:#fff;font-size:10px;font-weight:700">${w.risk}</span><br>
                        <a href="/admin/intelligence/weather" style="color:#0ea5e9;font-size:11px;font-weight:600">⛅ Cek Detail Cuaca Lengkap →</a>
                    </div>
                `).addTo(weatherLayerGroup);
            });
        }

        // ── BUILD PORTS LAYER ───────────────────────────────────
        function buildPortsLayer() {
            portsLayerGroup.clearLayers();
            portsData.forEach(p => {
                L.circleMarker([p.lat, p.lng], {
                    radius: 6, fillColor: '#3B82F6', color: '#fff', weight: 2, fillOpacity: 0.9
                }).bindPopup(`
                    <div style="min-width:220px">
                        <b>⚓ ${p.name}</b><br>
                        <span style="font-size:11px;color:#64748b">${p.country} — ${p.city}</span><br>
                        <hr style="margin:4px 0;border-color:#e2e8f0">
                        📋 Kode WPI: <b>${p.code}</b><br>
                        📦 Kapasitas: <b>${p.capacity}</b><br>
                        🏭 Tipe: <b>${p.type}</b><br>
                        <a href="/admin/intelligence/ports" style="color:#0ea5e9;font-size:11px;font-weight:600">⚓ Cek Direktori Pelabuhan →</a>
                    </div>
                `).addTo(portsLayerGroup);
            });
        }

        // ── SWITCH MODE ─────────────────────────────────────────
        function switchMapMode(mode) {
            currentMode = mode;
            map.removeLayer(cargoLayerGroup);
            map.removeLayer(weatherLayerGroup);
            map.removeLayer(portsLayerGroup);

            updateModeButtons(mode);
            updateBanner(mode);
            updateLegend(mode);

            if (mode === 'cargo') {
                buildCargoLayer();
                cargoLayerGroup.addTo(map);
                map.flyTo([10, 110], 3, { duration: 1.2 });
            } else if (mode === 'weather') {
                buildWeatherLayer();
                weatherLayerGroup.addTo(map);
                map.flyTo([20, 60], 2, { duration: 1.2 });
            } else if (mode === 'ports') {
                buildPortsLayer();
                portsLayerGroup.addTo(map);
                map.flyTo([20, 60], 2, { duration: 1.2 });
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
            const name = el.dataset.name;
            const flag = el.dataset.flag;
            const hub  = el.dataset.hub;

            map.flyTo([lat, lng], 5, { duration: 1.5 });

            // Highlight list item
            document.querySelectorAll('.country-item').forEach(li => {
                li.classList.remove('bg-cyan-50','dark:bg-cyan-900/20','border-l-2','border-cyan-500');
            });
            el.classList.add('bg-cyan-50', 'border-l-2', 'border-cyan-500');

            // Update country card
            document.getElementById('selected-flag').textContent = flag;
            document.getElementById('selected-name').textContent = name;
            document.getElementById('selected-hub').textContent  = ' ' + hub;
            document.getElementById('selected-country-card').classList.remove('hidden');
        }

        // ── FILTER COUNTRY LIST ─────────────────────────────────
        function filterCountriesList() {
            const q = document.getElementById('country-search-input').value.toLowerCase();
            document.querySelectorAll('.country-item').forEach(li => {
                li.style.display = li.dataset.name.toLowerCase().includes(q) ? '' : 'none';
            });
        }

        // ── INIT ────────────────────────────────────────────────
        buildCargoLayer();
        cargoLayerGroup.addTo(map);
        updateModeButtons('cargo');
        updateBanner('cargo');
        updateLegend('cargo');

        // Expose functions to window so onclick="" attributes can reach them
        window.switchMapMode    = switchMapMode;
        window.resetMapView     = resetMapView;
        window.focusCountry     = focusCountry;
        window.filterCountriesList = filterCountriesList;
    });
</script>
@endpush

