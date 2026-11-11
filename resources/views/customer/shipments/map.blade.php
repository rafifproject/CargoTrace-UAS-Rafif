@extends('layouts.customer')

@section('title', 'Peta Pengiriman & Route Trace')

@push('styles')
<style>
    /* Realistic Vehicle Markers for Cargo Tracker */
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
    <!-- Header with View Switcher -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Peta Pengiriman & Route Trace Saya</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pantau posisi kargo Anda atau gunakan simulator rute interaktif (Pelabuhan Laut vs Bandara Udara)</p>
        </div>

        <!-- Mode Toggle Tabs -->
        <div class="flex items-center gap-2 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
            <button type="button" id="tab-btn-tracking" onclick="switchMapMode('tracking')" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-slate-900 text-cyan-600 dark:text-cyan-400 shadow-xs">
                <i data-lucide="radio" class="w-3.5 h-3.5 text-cyan-500 animate-pulse"></i>
                <span>Pengiriman Saya Aktif</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-cyan-100 dark:bg-cyan-900/50 text-cyan-700 dark:text-cyan-300 font-mono">{{ count($mapShipments) }}</span>
            </button>
            <button type="button" id="tab-btn-routetrace" onclick="switchMapMode('routetrace')" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                <i data-lucide="route" class="w-3.5 h-3.5 text-amber-500"></i>
                <span>Simulator Route Trace (Asal ➔ Tujuan)</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300 font-bold">Interaktif</span>
            </button>
        </div>
    </div>

    <!-- Map Container -->
    <div class="relative rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Interactive Leaflet Map -->
        <div id="customerMap" class="w-full h-[650px] z-10"></div>

        <!-- PANEL 1: Floating Info Box for Active Shipments -->
        <div id="tracking-panel" class="absolute top-4 right-4 z-20 w-80 p-4 rounded-2xl bg-slate-900/90 backdrop-blur-md border border-slate-700/80 text-white shadow-2xl transition-all">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <span class="text-xs font-bold text-cyan-400 flex items-center gap-1.5">
                    <i data-lucide="navigation" class="w-3.5 h-3.5"></i>
                    <span>Informasi Kargo Saya</span>
                </span>
                <span class="text-[10px] text-slate-400 font-mono">{{ count($mapShipments) }} Kargo</span>
            </div>
            
            <div class="mt-3 space-y-2 text-xs">
                @if($selectedShipmentId && count($mapShipments) > 0)
                    @php $focused = $mapShipments->first(); @endphp
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">No. Pengiriman</span>
                        <div class="font-bold text-cyan-400">{{ $focused->shipment_number }}</div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Asal</span>
                            <div class="font-medium text-slate-200">{{ $focused->origin?->name }} ({{ $focused->origin?->city }})</div>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Tujuan</span>
                            <div class="font-medium text-slate-200">{{ $focused->destination?->name }} ({{ $focused->destination?->city }})</div>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Ekspedisi & Status</span>
                        <div class="font-medium text-slate-200">{{ $focused->carrier?->name }} &bull; {{ $focused->status }}</div>
                    </div>
                @else
                    <p class="text-xs text-slate-300">Menampilkan rute kargo Anda yang sedang berlayar.</p>
                    <div class="pt-2 text-[11px] text-slate-400 space-y-1.5 border-t border-slate-800">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Titik Keberangkatan</div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Titik Kedatangan</div>
                        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-cyan-600 inline-flex items-center justify-center text-[9px] text-white">🚢</span> Kapal Kontainer</div>
                        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-sky-500 inline-flex items-center justify-center text-[9px] text-white">✈️</span> Pesawat Kargo</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- PANEL 2: Floating Interactive Route Trace Controller -->
        <div id="routetrace-panel" class="hidden absolute top-4 left-4 z-20 w-96 max-w-[calc(100%-2rem)] p-4 rounded-2xl bg-slate-900/95 backdrop-blur-md border border-amber-500/40 text-white shadow-2xl space-y-3.5">
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center font-bold">
                        <i data-lucide="route" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white">Route Trace Simulator</h3>
                        <p class="text-[10px] text-slate-400">Pilih dua titik negara atau klik titik pin di peta</p>
                    </div>
                </div>
                <button type="button" onclick="resetRouteTrace()" class="text-[10px] text-slate-400 hover:text-amber-400 font-medium flex items-center gap-1">
                    <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                    <span>Reset</span>
                </button>
            </div>

            <!-- Mode Selector -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">Pilih Moda & Jenis Jalur</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" id="mode-btn-port" onclick="setTraceMode('PORT')" class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border text-xs font-bold transition-all bg-cyan-600/30 border-cyan-500 text-cyan-300">
                        <span class="text-sm">⚓</span>
                        <span>Pelabuhan (Laut)</span>
                    </button>
                    <button type="button" id="mode-btn-airport" onclick="setTraceMode('AIRPORT')" class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border text-xs font-bold transition-all bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white">
                        <span class="text-sm">✈️</span>
                        <span>Bandara (Udara)</span>
                    </button>
                </div>
            </div>

            <!-- Origin & Dest Selectors -->
            <div class="space-y-2">
                <div>
                    <label class="block text-[10px] font-semibold text-emerald-400 mb-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>Titik Asal (Klik Titik 1 di Peta / Pilih)</span>
                    </label>
                    <select id="select-origin-hub" onchange="onHubSelectChange()" class="w-full px-2.5 py-1.5 text-xs bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-1 focus:ring-emerald-500">
                        <option value="">— Pilih Titik Asal —</option>
                    </select>
                </div>

                <div class="flex justify-center -my-1">
                    <button type="button" onclick="swapTracePoints()" class="p-1 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 hover:text-amber-400 transition-colors" title="Tukar Asal & Tujuan">
                        <i data-lucide="arrow-down-up" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <div>
                    <label class="block text-[10px] font-semibold text-rose-400 mb-1 flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        <span>Titik Tujuan (Klik Titik 2 di Peta / Pilih)</span>
                    </label>
                    <select id="select-dest-hub" onchange="onHubSelectChange()" class="w-full px-2.5 py-1.5 text-xs bg-slate-800 border border-slate-700 rounded-lg text-white focus:outline-none focus:ring-1 focus:ring-rose-500">
                        <option value="">— Pilih Titik Tujuan —</option>
                    </select>
                </div>
            </div>

            <!-- Calculation Summary -->
            <div id="trace-result-card" class="hidden p-3 rounded-xl bg-slate-800/80 border border-slate-700 space-y-2.5">
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 rounded-lg bg-slate-900/60 border border-slate-800">
                        <span class="text-[9px] text-slate-400 uppercase block font-semibold">Jarak Tempuh</span>
                        <span class="text-xs font-black text-amber-400 font-mono" id="res-distance">0 NM</span>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-900/60 border border-slate-800">
                        <span class="text-[9px] text-slate-400 uppercase block font-semibold">Estimasi Waktu</span>
                        <span class="text-xs font-black text-cyan-400 font-mono" id="res-time">0 Hari</span>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-900/60 border border-slate-800">
                        <span class="text-[9px] text-slate-400 uppercase block font-semibold">Perkiraan Risk</span>
                        <span class="text-xs font-black" id="res-risk">Low</span>
                    </div>
                </div>

                <div class="p-2 rounded-lg bg-slate-900/60 text-[10px] text-slate-300 flex items-start gap-2">
                    <i data-lucide="info" class="w-3.5 h-3.5 text-amber-400 shrink-0 mt-0.5"></i>
                    <span id="res-corridor-desc">Jalur rute melewati koridor pelayaran maritim internasional.</span>
                </div>

                <button type="button" onclick="openRouteDetailModal()" class="w-full py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition-all flex items-center justify-center gap-1.5">
                    <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                    <span>Lihat Detail Rute & Analisis Risiko</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: DETAIL RUTE & ANALISIS RISIKO -->
<div id="routeDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-500 flex items-center justify-center font-bold text-sm">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Detail Navigasi & Analisis Risiko Rute</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">Laporan evaluasi kelayakan rute titik asal menuju titik tujuan</p>
                </div>
            </div>
            <button type="button" onclick="closeRouteDetailModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-lg">✕</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
            <div class="space-y-1">
                <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Titik Keberangkatan (Asal)
                </span>
                <h4 class="font-bold text-slate-900 dark:text-white text-xs" id="modal-origin-name">-</h4>
                <p class="text-[11px] text-slate-500" id="modal-origin-loc">-</p>
                <p class="text-[10px] font-mono text-slate-400" id="modal-origin-coords">-</p>
            </div>
            <div class="space-y-1 md:border-l md:border-slate-200 dark:md:border-slate-800 md:pl-3">
                <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Titik Kedatangan (Tujuan)
                </span>
                <h4 class="font-bold text-slate-900 dark:text-white text-xs" id="modal-dest-name">-</h4>
                <p class="text-[11px] text-slate-500" id="modal-dest-loc">-</p>
                <p class="text-[10px] font-mono text-slate-400" id="modal-dest-coords">-</p>
            </div>
        </div>

        <div class="space-y-2">
            <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="scale" class="w-3.5 h-3.5 text-cyan-500"></i>
                <span>Perbandingan Karakteristik Rute (Moda: <span id="modal-selected-mode" class="text-amber-500">Laut</span>)</span>
            </h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] text-slate-400 block mb-0.5 font-medium">Jarak Tempuh</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white text-sm" id="modal-metric-dist">0 NM</span>
                    <span class="text-[9px] text-slate-500 block" id="modal-metric-dist-km">(~0 KM)</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] text-slate-400 block mb-0.5 font-medium">Estimasi Waktu</span>
                    <span class="font-mono font-bold text-cyan-600 dark:text-cyan-400 text-sm" id="modal-metric-time">0 Hari</span>
                    <span class="text-[9px] text-slate-500 block" id="modal-metric-time-detail">+ Port Handling</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] text-slate-400 block mb-0.5 font-medium">Kecepatan Rata-rata</span>
                    <span class="font-mono font-bold text-slate-900 dark:text-white text-sm" id="modal-metric-speed">20 Knots</span>
                    <span class="text-[9px] text-slate-500 block" id="modal-metric-speed-km">~37 km/h</span>
                </div>
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] text-slate-400 block mb-0.5 font-medium">Emisi Karbon Est.</span>
                    <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-sm" id="modal-metric-carbon">Rendah</span>
                    <span class="text-[9px] text-slate-500 block" id="modal-metric-carbon-unit">Efisiensi Tinggi</span>
                </div>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 space-y-2.5">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                    <i data-lucide="shield-alert" class="w-3.5 h-3.5 text-amber-500"></i>
                    <span>Analisis Skor Risiko Rute (Calculated Risk Profile)</span>
                </span>
                <span id="modal-risk-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                    Low Risk (20%)
                </span>
            </div>
            <div class="w-full bg-slate-200 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                <div id="modal-risk-progress" class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: 20%;"></div>
            </div>
            <p class="text-[11px] text-slate-600 dark:text-slate-400 leading-relaxed" id="modal-risk-narrative">
                Kondisi rute dinilai stabil dengan tingkat eksposur minimal terhadap cuaca laut ekstrem.
            </p>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-end gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeRouteDetailModal()" class="w-full sm:w-auto px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition-colors">
                Tutup
            </button>
            <a id="btn-order-route" href="{{ route('customer.shipments.create') }}" class="w-full sm:w-auto px-4 py-2 text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 rounded-xl shadow-sm transition-colors flex items-center justify-center gap-1.5">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                <span>Pesan Pengiriman Rute Ini Sekarang</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rawRouteHubs = {!! json_encode($routeHubs ?? []) !!};
    const shipments = {!! json_encode($mapShipments) !!};

    let map = null;
    let currentMapMode = 'tracking';
    let currentTraceMode = 'PORT';

    let selectedOriginHub = null;
    let selectedDestHub = null;
    let tracePolyline = null;
    let traceOriginMarker = null;
    let traceDestMarker = null;
    let traceMidMarker = null;
    let traceHubMarkers = [];

    const trackingLayerGroup = L.layerGroup();
    const routeTraceLayerGroup = L.layerGroup();

    document.addEventListener('DOMContentLoaded', function () {
        initCustomerMap();
        populateHubSelects();
        buildTrackingMode();
    });

    function initCustomerMap() {
        map = L.map('customerMap', {
            center: [15, 100],
            zoom: 3,
            minZoom: 2,
            maxZoom: 20
        });

        const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps'
        });

        const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
            maxZoom: 20,
            subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
            attribution: '&copy; Google Maps Satelit'
        });

        const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        });

        googleHybrid.addTo(map);

        const baseMaps = {
            "🛰️ Satelit Hybrid": googleHybrid,
            "🗺️ Google Maps Jalan": googleStreets,
            "🌐 OpenStreetMap": osm
        };
        L.control.layers(baseMaps, null, { position: 'topright' }).addTo(map);

        trackingLayerGroup.addTo(map);
    }

    function switchMapMode(mode) {
        currentMapMode = mode;
        const tabTracking = document.getElementById('tab-btn-tracking');
        const tabTrace = document.getElementById('tab-btn-routetrace');
        const panelTracking = document.getElementById('tracking-panel');
        const panelTrace = document.getElementById('routetrace-panel');

        if (mode === 'tracking') {
            tabTracking.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-slate-900 text-cyan-600 dark:text-cyan-400 shadow-xs';
            tabTrace.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            panelTracking.classList.remove('hidden');
            panelTrace.classList.add('hidden');

            map.removeLayer(routeTraceLayerGroup);
            trackingLayerGroup.addTo(map);
            map.flyTo([15, 100], 3, { duration: 1 });
        } else {
            tabTrace.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-slate-900 text-amber-600 dark:text-amber-400 shadow-xs';
            tabTracking.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            panelTracking.classList.add('hidden');
            panelTrace.classList.remove('hidden');

            map.removeLayer(trackingLayerGroup);
            routeTraceLayerGroup.addTo(map);
            renderRouteTraceHubPins();
            map.flyTo([20, 20], 2.2, { duration: 1 });
        }
    }

    function populateHubSelects() {
        const originSelect = document.getElementById('select-origin-hub');
        const destSelect = document.getElementById('select-dest-hub');

        originSelect.innerHTML = '<option value="">— Pilih Titik Asal —</option>';
        destSelect.innerHTML = '<option value="">— Pilih Titik Tujuan —</option>';

        const filteredHubs = rawRouteHubs.filter(h => h.location_type === currentTraceMode);

        filteredHubs.forEach(h => {
            const label = `${h.name} (${h.city}, ${h.country})`;
            originSelect.add(new Option(label, h.id));
            destSelect.add(new Option(label, h.id));
        });
    }

    function setTraceMode(mode) {
        currentTraceMode = mode;
        const btnPort = document.getElementById('mode-btn-port');
        const btnAirport = document.getElementById('mode-btn-airport');

        if (mode === 'PORT') {
            btnPort.className = 'flex items-center justify-center gap-2 py-2 px-3 rounded-xl border text-xs font-bold transition-all bg-cyan-600/30 border-cyan-500 text-cyan-300';
            btnAirport.className = 'flex items-center justify-center gap-2 py-2 px-3 rounded-xl border text-xs font-bold transition-all bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white';
        } else {
            btnAirport.className = 'flex items-center justify-center gap-2 py-2 px-3 rounded-xl border text-xs font-bold transition-all bg-sky-600/30 border-sky-500 text-sky-300';
            btnPort.className = 'flex items-center justify-center gap-2 py-2 px-3 rounded-xl border text-xs font-bold transition-all bg-slate-800/80 border-slate-700 text-slate-400 hover:text-white';
        }

        populateHubSelects();
        renderRouteTraceHubPins();
        calculateAndDrawTraceRoute();
    }

    function renderRouteTraceHubPins() {
        routeTraceLayerGroup.clearLayers();
        traceHubMarkers = [];

        const filteredHubs = rawRouteHubs.filter(h => h.location_type === currentTraceMode);

        filteredHubs.forEach(hub => {
            const isPort = (hub.location_type === 'PORT');
            const pinColor = isPort ? '#00A8E8' : '#F59E0B';
            const iconSymbol = isPort ? '⚓' : '✈️';

            const customIcon = L.divIcon({
                className: 'hub-interactive-pin',
                html: `
                    <div style="background:${pinColor}; color:#ffffff; width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:800; border:2px solid #ffffff; box-shadow:0 2px 8px rgba(0,0,0,0.4); cursor:pointer;">
                        ${iconSymbol}
                    </div>
                `,
                iconSize: [26, 26],
                iconAnchor: [13, 13]
            });

            const marker = L.marker([hub.latitude, hub.longitude], { icon: customIcon });

            marker.bindTooltip(`<b>${hub.name}</b><br><span style="font-size:11px">${hub.city}, ${hub.country}</span>`, {
                direction: 'top',
                offset: [0, -12]
            });

            marker.on('click', function () {
                onHubPinClicked(hub);
            });

            marker.addTo(routeTraceLayerGroup);
            traceHubMarkers.push({ hub: hub, marker: marker });
        });

        if (selectedOriginHub && selectedDestHub) {
            drawTraceRouteVisuals();
        }
    }

    function onHubPinClicked(hub) {
        if (!selectedOriginHub) {
            selectedOriginHub = hub;
            document.getElementById('select-origin-hub').value = hub.id;
        } else if (!selectedDestHub && selectedOriginHub.id !== hub.id) {
            selectedDestHub = hub;
            document.getElementById('select-dest-hub').value = hub.id;
        } else {
            selectedOriginHub = hub;
            selectedDestHub = null;
            document.getElementById('select-origin-hub').value = hub.id;
            document.getElementById('select-dest-hub').value = '';
        }
        calculateAndDrawTraceRoute();
    }

    function onHubSelectChange() {
        const originId = parseInt(document.getElementById('select-origin-hub').value);
        const destId = parseInt(document.getElementById('select-dest-hub').value);

        selectedOriginHub = rawRouteHubs.find(h => h.id === originId) || null;
        selectedDestHub = rawRouteHubs.find(h => h.id === destId) || null;

        calculateAndDrawTraceRoute();
    }

    function swapTracePoints() {
        const temp = selectedOriginHub;
        selectedOriginHub = selectedDestHub;
        selectedDestHub = temp;

        document.getElementById('select-origin-hub').value = selectedOriginHub ? selectedOriginHub.id : '';
        document.getElementById('select-dest-hub').value = selectedDestHub ? selectedDestHub.id : '';

        calculateAndDrawTraceRoute();
    }

    function resetRouteTrace() {
        selectedOriginHub = null;
        selectedDestHub = null;
        document.getElementById('select-origin-hub').value = '';
        document.getElementById('select-dest-hub').value = '';
        document.getElementById('trace-result-card').classList.add('hidden');
        renderRouteTraceHubPins();
    }

    function calculateAndDrawTraceRoute() {
        const resultCard = document.getElementById('trace-result-card');

        if (!selectedOriginHub || !selectedDestHub || selectedOriginHub.id === selectedDestHub.id) {
            resultCard.classList.add('hidden');
            if (tracePolyline) routeTraceLayerGroup.removeLayer(tracePolyline);
            if (traceOriginMarker) routeTraceLayerGroup.removeLayer(traceOriginMarker);
            if (traceDestMarker) routeTraceLayerGroup.removeLayer(traceDestMarker);
            if (traceMidMarker) routeTraceLayerGroup.removeLayer(traceMidMarker);
            return;
        }

        resultCard.classList.remove('hidden');
        drawTraceRouteVisuals();
    }

    function drawTraceRouteVisuals() {
        if (tracePolyline) routeTraceLayerGroup.removeLayer(tracePolyline);
        if (traceOriginMarker) routeTraceLayerGroup.removeLayer(traceOriginMarker);
        if (traceDestMarker) routeTraceLayerGroup.removeLayer(traceDestMarker);
        if (traceMidMarker) routeTraceLayerGroup.removeLayer(traceMidMarker);

        const o = selectedOriginHub;
        const d = selectedDestHub;

        const p1 = [parseFloat(o.latitude), parseFloat(o.longitude)];
        const p2 = [parseFloat(d.latitude), parseFloat(d.longitude)];

        let pathCoords = [];
        let distanceNM = 0;
        let speedKnots = 20;
        let transitHours = 0;
        let riskScore = 20;
        let corridorDesc = '';

        if (currentTraceMode === 'PORT') {
            pathCoords = generateMaritimeSeaRoute(p1, p2);
            distanceNM = computePolylineDistance(pathCoords);
            speedKnots = 20;
            transitHours = Math.round(distanceNM / speedKnots) + 36;
            riskScore = calculateMaritimeRisk(distanceNM, pathCoords);
            corridorDesc = `Jalur Pelayaran Laut: Melintasi koridor maritim internasional (${Math.round(distanceNM)} NM).`;
        } else {
            pathCoords = createCurvedPath(p1, p2, 28, 0.16);
            const distKM = calculateHaversineKM(p1[0], p1[1], p2[0], p2[1]);
            distanceNM = Math.round(distKM * 0.539957);
            speedKnots = 460;
            transitHours = Math.round(distKM / 850) + 14;
            riskScore = calculateAirRisk(distKM);
            corridorDesc = `Koridor Penerbangan Udara: Lintasan penerbangan langsung lintas benua (~${Math.round(distKM)} km).`;
        }

        const routeColor = (currentTraceMode === 'PORT') ? '#00D2FF' : '#A855F7';

        tracePolyline = L.polyline(pathCoords, {
            color: routeColor,
            weight: 4,
            dashArray: '8, 8',
            opacity: 0.95,
            lineCap: 'round',
            lineJoin: 'round'
        }).addTo(routeTraceLayerGroup);

        traceOriginMarker = L.circleMarker(p1, {
            radius: 9, fillColor: '#10B981', color: '#FFFFFF', weight: 3, fillOpacity: 1
        }).bindTooltip(`<b>Asal:</b> ${o.name}`, { permanent: true, direction: 'top', offset: [0, -8] })
          .addTo(routeTraceLayerGroup);

        traceDestMarker = L.circleMarker(p2, {
            radius: 9, fillColor: '#EF4444', color: '#FFFFFF', weight: 3, fillOpacity: 1
        }).bindTooltip(`<b>Tujuan:</b> ${d.name}`, { permanent: true, direction: 'top', offset: [0, -8] })
          .addTo(routeTraceLayerGroup);

        const midIdx = Math.floor(pathCoords.length / 2);
        const midPoint = pathCoords[midIdx];
        const vehicleIcon = (currentTraceMode === 'PORT') ? '🚢' : '✈️';
        traceMidMarker = L.marker(midPoint, {
            icon: L.divIcon({
                className: 'trace-vehicle-symbol',
                html: `<div style="font-size:18px; filter:drop-shadow(0 2px 6px rgba(0,0,0,0.6));">${vehicleIcon}</div>`,
                iconSize: [24, 24],
                iconAnchor: [12, 12]
            })
        }).addTo(routeTraceLayerGroup);

        map.fitBounds(tracePolyline.getBounds(), { padding: [50, 50], maxZoom: 8 });

        const days = Math.floor(transitHours / 24);
        const remHours = transitHours % 24;
        const timeStr = days > 0 ? `${days}h ${remHours}j` : `${remHours} jam`;

        document.getElementById('res-distance').textContent = `${Math.round(distanceNM)} NM`;
        document.getElementById('res-time').textContent = timeStr;
        document.getElementById('res-corridor-desc').textContent = corridorDesc;

        const resRiskEl = document.getElementById('res-risk');
        if (riskScore <= 30) {
            resRiskEl.textContent = `Aman (${riskScore}%)`;
            resRiskEl.className = 'text-xs font-black text-emerald-400';
        } else if (riskScore <= 60) {
            resRiskEl.textContent = `Sedang (${riskScore}%)`;
            resRiskEl.className = 'text-xs font-black text-amber-400';
        } else {
            resRiskEl.textContent = `Tinggi (${riskScore}%)`;
            resRiskEl.className = 'text-xs font-black text-rose-400';
        }

        window._activeTraceData = {
            origin: o,
            dest: d,
            mode: currentTraceMode,
            distanceNM: Math.round(distanceNM),
            distanceKM: Math.round(distanceNM * 1.852),
            transitHours: transitHours,
            timeStr: timeStr,
            riskScore: riskScore,
            speedKnots: speedKnots
        };
    }

    function generateMaritimeSeaRoute(p1, p2) {
        const oLng = p1[1], dLng = p2[1];
        let waypoints = [];

        if ((oLng > 90 && dLng < 30) || (dLng > 90 && oLng < 30)) {
            waypoints.push([4.21, 99.85]); // Malacca
            waypoints.push([12.5, 60.0]);
            waypoints.push([12.5, 44.0]);  // Bab el Mandeb
            waypoints.push([27.8, 34.2]);
            waypoints.push([31.26, 32.30]);// Suez
            if (dLng < 0 || oLng < 0) {
                waypoints.push([36.12, -5.44]); // Gibraltar
            }
        } else if ((oLng > 100 && dLng < -70) || (dLng > 100 && oLng < -70)) {
            waypoints.push([20.0, 130.0]);
            waypoints.push([32.0, -165.0]);
        }

        if (waypoints.length > 0) {
            const fullPoints = [p1, ...waypoints, p2];
            let smoothPoints = [];
            for (let i = 0; i < fullPoints.length - 1; i++) {
                const curved = createCurvedPath(fullPoints[i], fullPoints[i+1], 10, 0.05);
                smoothPoints = smoothPoints.concat(curved);
            }
            return smoothPoints;
        }

        return createCurvedPath(p1, p2, 24, 0.12);
    }

    function calculateHaversineKM(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    }

    function computePolylineDistance(coords) {
        let totalKM = 0;
        for (let i = 0; i < coords.length - 1; i++) {
            totalKM += calculateHaversineKM(coords[i][0], coords[i][1], coords[i+1][0], coords[i+1][1]);
        }
        return totalKM * 0.539957;
    }

    function calculateMaritimeRisk(distanceNM, coords) {
        let base = Math.min(45, Math.round(distanceNM / 250));
        coords.forEach(pt => {
            if (pt[0] > 10 && pt[0] < 30 && pt[1] > 30 && pt[1] < 45) base += 15;
            if (pt[0] > 1 && pt[0] < 6 && pt[1] > 98 && pt[1] < 104) base += 5;
        });
        return Math.min(88, Math.max(15, base));
    }

    function calculateAirRisk(distKM) {
        let base = Math.min(40, Math.round(distKM / 400));
        return Math.min(75, Math.max(12, base));
    }

    function openRouteDetailModal() {
        const data = window._activeTraceData;
        if (!data) return;

        document.getElementById('modal-origin-name').textContent = data.origin.name;
        document.getElementById('modal-origin-loc').textContent = `${data.origin.city}, ${data.origin.country}`;
        document.getElementById('modal-origin-coords').textContent = `Koordinat: ${parseFloat(data.origin.latitude).toFixed(4)}°, ${parseFloat(data.origin.longitude).toFixed(4)}°`;

        document.getElementById('modal-dest-name').textContent = data.dest.name;
        document.getElementById('modal-dest-loc').textContent = `${data.dest.city}, ${data.dest.country}`;
        document.getElementById('modal-dest-coords').textContent = `Koordinat: ${parseFloat(data.dest.latitude).toFixed(4)}°, ${parseFloat(data.dest.longitude).toFixed(4)}°`;

        document.getElementById('modal-selected-mode').textContent = (data.mode === 'PORT') ? '⚓ Pelabuhan (Jalur Laut)' : '✈️ Bandara (Jalur Udara)';

        document.getElementById('modal-metric-dist').textContent = `${data.distanceNM} NM`;
        document.getElementById('modal-metric-dist-km').textContent = `(~${data.distanceKM} KM)`;
        document.getElementById('modal-metric-time').textContent = data.timeStr;

        if (data.mode === 'PORT') {
            document.getElementById('modal-metric-speed').textContent = `~20 Knots`;
            document.getElementById('modal-metric-speed-km').textContent = `~37 km/h (Kapal Kontainer)`;
            document.getElementById('modal-metric-carbon').textContent = `Rendah`;
            document.getElementById('modal-metric-carbon-unit').textContent = `~15g CO2 / Ton-KM`;
        } else {
            document.getElementById('modal-metric-speed').textContent = `~460 Knots`;
            document.getElementById('modal-metric-speed-km').textContent = `~850 km/h (Pesawat Kargo)`;
            document.getElementById('modal-metric-carbon').textContent = `Tinggi`;
            document.getElementById('modal-metric-carbon-unit').textContent = `~500g CO2 / Ton-KM`;
        }

        const badge = document.getElementById('modal-risk-badge');
        const progress = document.getElementById('modal-risk-progress');
        const narrative = document.getElementById('modal-risk-narrative');

        progress.style.width = `${data.riskScore}%`;

        if (data.riskScore <= 30) {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20';
            badge.textContent = `Low Risk (${data.riskScore}%)`;
            progress.className = 'bg-emerald-500 h-full rounded-full transition-all duration-500';
            narrative.textContent = `Rute aman dengan stabilitas navigasi tinggi. Kondisi operasional kedua titik dalam status optimal.`;
        } else if (data.riskScore <= 60) {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20';
            badge.textContent = `Moderate Risk (${data.riskScore}%)`;
            progress.className = 'bg-amber-500 h-full rounded-full transition-all duration-500';
            narrative.textContent = `Rute melintasi perairan maritim dengan variabilitas gelombang normal. Direkomendasikan proteksi standar.`;
        } else {
            badge.className = 'px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-500 border border-rose-500/20';
            badge.textContent = `High Risk (${data.riskScore}%)`;
            progress.className = 'bg-rose-500 h-full rounded-full transition-all duration-500';
            narrative.textContent = `Rute melewati koridor jarak jauh internasional dengan chokepoint padat. Wajib menyertakan asuransi komprehensif.`;
        }

        document.getElementById('routeDetailModal').classList.remove('hidden');
    }

    function closeRouteDetailModal() {
        document.getElementById('routeDetailModal').classList.add('hidden');
    }

    function buildTrackingMode() {
        const allBounds = [];

        function calculateBearing(p1, p2) {
            const lat1 = p1[0] * Math.PI / 180;
            const lat2 = p2[0] * Math.PI / 180;
            const dLng = (p2[1] - p1[1]) * Math.PI / 180;
            const y = Math.sin(dLng) * Math.cos(lat2);
            const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(dLng);
            return (Math.atan2(y, x) * 180 / Math.PI + 360) % 360;
        }

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
            const nx = -dLng / dist;
            const ny = dLat / dist;
            const arcHeight = Math.min(12, Math.max(1.8, dist * curvature));
            const sign = (dLng >= 0) ? 1 : -1;
            const hemisphereFactor = midLat >= 0 ? 1 : -1;

            const controlLat = midLat + nx * arcHeight * sign * (hemisphereFactor * 0.4 + 0.6);
            let controlLng = midLng + ny * arcHeight * sign;
            if (controlLng > 180) controlLng -= 360;
            if (controlLng < -180) controlLng += 360;

            const points = [];
            for (let i = 0; i <= numPoints; i++) {
                const t = i / numPoints;
                const lat = (1 - t) * (1 - t) * lat1 + 2 * (1 - t) * t * controlLat + t * t * lat2;
                let lng = (1 - t) * (1 - t) * lng1 + 2 * (1 - t) * t * controlLng + t * t * (lng1 + dLng);
                if (lng > 180) lng -= 360;
                if (lng < -180) lng += 360;
                points.push([lat, lng]);
            }
            return points;
        }

        window.createCurvedPath = createCurvedPath;

        function getShipmentVehicle(s) {
            const cargo = (s.cargo_type || '').toLowerCase();
            const originType = s.origin ? (s.origin.location_type || '').toUpperCase() : '';
            const destType   = s.destination ? (s.destination.location_type || '').toUpperCase() : '';

            if (originType === 'AIRPORT' || destType === 'AIRPORT' || cargo.includes('air') || cargo.includes('express')) {
                return {
                    type: 'AIR', label: 'Pesawat Kargo', color: '#38bdf8',
                    realSvg: `<svg width="36" height="36" viewBox="0 0 48 48" fill="none"><path d="M24 2L28 16L44 24L28 26L27 40L33 44L24 43L15 44L21 40L20 26L4 24L20 16L24 2Z" fill="#38bdf8" stroke="#ffffff" stroke-width="2.5" stroke-linejoin="round"/></svg>`
                };
            }
            return {
                type: 'SEA', label: 'Kapal Kontainer', color: '#00D2FF',
                realSvg: `<svg width="40" height="40" viewBox="0 0 64 64" fill="none"><path d="M6 38L14 50H50L58 38L54 28H10L6 38Z" fill="#0284c7" stroke="#ffffff" stroke-width="2.5" stroke-linejoin="round"/><rect x="18" y="20" width="8" height="8" rx="1.5" fill="#f59e0b" stroke="#ffffff" stroke-width="1.5"/><rect x="28" y="20" width="8" height="8" rx="1.5" fill="#ef4444" stroke="#ffffff" stroke-width="1.5"/><rect x="38" y="20" width="8" height="8" rx="1.5" fill="#10b981" stroke="#ffffff" stroke-width="1.5"/></svg>`
            };
        }

        function generateShipmentPopupContent(s, vehicle, curLoc) {
            return `
                <div style="font-family:'Outfit',sans-serif; min-width:250px; padding:2px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:6px;">
                        <span style="font-weight:900; font-size:13px; color:#0284c7;">${s.shipment_number}</span>
                        <span style="font-weight:800; font-size:10px; color:#15803d; background:#dcfce7; padding:2px 7px; border-radius:5px;">${s.status}</span>
                    </div>
                    <div style="font-size:11px; color:#334155; line-height:1.5; margin-bottom:8px;">
                        <div>🛫 <b>Asal:</b> ${s.origin?.name || '-'} (${s.origin?.city || '-'})</div>
                        <div>🛬 <b>Tujuan:</b> ${s.destination?.name || '-'} (${s.destination?.city || '-'})</div>
                        <div>📍 <b>Posisi:</b> ${curLoc?.name || 'Dalam Perjalanan Laut'}</div>
                        <div>🏢 <b>Ekspedisi:</b> ${s.carrier?.name || '-'}</div>
                        <div>📦 <b>Kargo:</b> ${s.cargo_type}</div>
                    </div>
                    <a href="/customer/shipments/${s.id}" style="display:block; text-align:center; background:#0284c7; color:#fff; font-weight:700; padding:6px; border-radius:8px; text-decoration:none; font-size:11px;">
                        Buka Detail Kargo & Riwayat →
                    </a>
                </div>
            `;
        }

        shipments.forEach(s => {
            if (s.origin && s.destination) {
                const oLat = parseFloat(s.origin.latitude);
                const oLng = parseFloat(s.origin.longitude);
                const dLat = parseFloat(s.destination.latitude);
                const dLng = parseFloat(s.destination.longitude);
                if (isNaN(oLat) || isNaN(oLng) || isNaN(dLat) || isNaN(dLng)) return;

                const originPt = [oLat, oLng];
                const destPt = [dLat, dLng];
                allBounds.push(originPt, destPt);

                const vehicle = getShipmentVehicle(s);
                const curLoc = s.current_location || s.currentLocation;
                let curPt = null;
                if (curLoc && curLoc.latitude && curLoc.longitude) {
                    curPt = [parseFloat(curLoc.latitude), parseFloat(curLoc.longitude)];
                    allBounds.push(curPt);
                }

                const popupHtml = generateShipmentPopupContent(s, vehicle, curLoc);

                L.circleMarker(originPt, {
                    radius: 7, fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(trackingLayerGroup);

                L.circleMarker(destPt, {
                    radius: 7, fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(trackingLayerGroup);

                if (curPt) {
                    const traveledPath = createCurvedPath(originPt, curPt, 16, 0.1);
                    L.polyline(traveledPath, {
                        color: '#0284c7', weight: 3.5, opacity: 0.9
                    }).bindPopup(popupHtml).addTo(trackingLayerGroup);

                    const remainingPath = createCurvedPath(curPt, destPt, 16, 0.1);
                    L.polyline(remainingPath, {
                        color: '#38bdf8', weight: 2.5, dashArray: '6, 6', opacity: 0.8
                    }).bindPopup(popupHtml).addTo(trackingLayerGroup);

                    const heading = calculateBearing(curPt, destPt);
                    L.marker(curPt, {
                        icon: L.divIcon({
                            className: 'cargo-real-vehicle-marker',
                            html: `
                                <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${heading}deg);">
                                    ${vehicle.realSvg}
                                </div>
                                <div class="cargo-vehicle-tag">
                                    <span class="cargo-tag-dot" style="background:${vehicle.color};"></span>
                                    <span>${s.shipment_number}</span>
                                </div>
                            `,
                            iconSize: [44, 44],
                            iconAnchor: [22, 22]
                        })
                    }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                } else {
                    const directPath = createCurvedPath(originPt, destPt, 20, 0.12);
                    L.polyline(directPath, {
                        color: '#0284c7', weight: 3, dashArray: '6, 6', opacity: 0.85
                    }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                }
            }
        });

        if (allBounds.length > 0) {
            map.fitBounds(allBounds, { padding: [50, 50], maxZoom: 5 });
        }
    }
</script>
@endpush
