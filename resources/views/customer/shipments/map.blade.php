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
    .cargo-real-vehicle-marker.marker-dimmed,
    .cargo-destination-pin.marker-dimmed {
        opacity: 0.2 !important;
        filter: grayscale(80%) !important;
        transition: opacity 0.3s ease, filter 0.3s ease;
    }

    /* Route Tracker 2-Step Nodes & Pins */
    .country-trace-node {
        transition: all 0.2s ease;
        cursor: pointer !important;
    }
    .country-trace-node:hover {
        fill: #3b82f6 !important;
        fill-opacity: 1 !important;
        r: 8px !important;
    }
    .route-node-pin-wrapper {
        background: transparent !important;
        border: none !important;
    }
    .route-node-pin {
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        font-weight: 900;
        font-size: 9px;
        letter-spacing: -0.2px;
        border: 2px solid #ffffff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
    }
    .route-node-origin {
        background: #10B981 !important;
    }
    .route-node-dest {
        background: #EF4444 !important;
        animation: pulse-ring 1.8s infinite;
    }
    @keyframes pulse-ring {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7), 0 4px 10px rgba(0, 0, 0, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0), 0 4px 10px rgba(0, 0, 0, 0.4); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0), 0 4px 10px rgba(0, 0, 0, 0.4); }
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

        <!-- Filter Moda & Mode Toggle Tabs -->
        <div class="flex items-center gap-2 flex-wrap">
            <form method="GET" action="{{ route('customer.shipments.map') }}" class="flex items-center gap-1.5">
                @if(request('shipment_id'))
                    <input type="hidden" name="shipment_id" value="{{ request('shipment_id') }}">
                @endif
                <select name="transport_mode" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500 shadow-xs">
                    <option value="">Semua Moda Pengiriman</option>
                    <option value="SEA" {{ request('transport_mode') === 'SEA' ? 'selected' : '' }}>🚢 Kapal Laut Saja</option>
                    <option value="AIR" {{ request('transport_mode') === 'AIR' ? 'selected' : '' }}>✈️ Pesawat Udara Saja</option>
                    <option value="LAND" {{ request('transport_mode') === 'LAND' ? 'selected' : '' }}>🚚 Mobil / Truk Kargo (Darat)</option>
                </select>
            </form>

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
    </div>

    <!-- Map Container -->
    <div class="relative rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- ROUTE TRACKER 2-STEP INTERACTIVE BANNER (Ref Screenshot 1 & 2) -->
        <div id="route-step-banner" class="hidden px-4 py-2.5 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between shadow-xs transition-all duration-200">
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
    </div>
</div>

<!-- MODAL: VIEW ANALYTICS RUTE LENGKAP -->
<div id="route-analytics-modal" onclick="if (event.target === this) closeRouteAnalyticsModal()" class="fixed inset-0 z-[99999] flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4" style="display: none;">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl w-full max-w-2xl max-h-[92vh] overflow-y-auto shadow-2xl p-5 sm:p-6 space-y-4">
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-purple-500/10 text-purple-600 dark:text-purple-400 flex items-center justify-center font-bold text-sm">
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
            <a id="modal-order-btn" href="{{ route('customer.shipments.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-cyan-600 hover:bg-cyan-500 transition-colors shadow-sm flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Pesan Pengiriman Rute Ini Sekarang</span>
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@include('partials.maritime-route-helper')
<script>
    const shipments = {!! json_encode($mapShipments) !!};
    const countriesData = {!! json_encode($countriesList) !!};

    let map = null;
    let currentMapMode = 'tracking'; // 'tracking' or 'routetrace'
    let traceTransportMode = 'PORT'; // 'PORT' (Laut) or 'AIR' (Udara)

    // Route Trace 2-Step Interactive State
    let traceOrigin = null;
    let traceDestination = null;
    let traceNodesGroup = L.layerGroup();
    let traceRouteLine = null;
    let traceOriginMarker = null;
    let traceDestMarker = null;
    let lastAnalyzedRoute = null;

    // Tracking Mode Layers
    const trackingLayerGroup = L.layerGroup();
    const routeTraceLayerGroup = L.layerGroup();

    let cargoShipmentItems = [];
    let activeCargoId = null;

    function focusCargoShipment(targetId, targetLatLng) {
        activeCargoId = targetId;

        cargoShipmentItems.forEach(it => {
            if (it.id === targetId) {
                // Menonjolkan jalur yang dipilih (High-contrast cyan glowing)
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
                    it.traveledPoly.openPopup(targetLatLng || (it.bounds ? it.bounds.getCenter() : null));
                } else if (it.directPoly) {
                    it.directPoly.openPopup(targetLatLng || (it.bounds ? it.bounds.getCenter() : null));
                }

                // Zoom / Fit bounds ke seluruh lintasan rute kargo tersebut
                if (it.bounds && map) {
                    map.fitBounds(it.bounds, { padding: [60, 60], maxZoom: 10, duration: 1 });
                }
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
            it.directPoly?.setStyle({ color: '#0284c7', weight: 3, dashArray: '6, 6', opacity: 0.85 });
            it.deliveryPoly?.setStyle({ color: '#f59e0b', weight: 3.5, opacity: 0.95 });
            it.feederFlightPoly?.setStyle({ color: '#00F0FF', weight: 3.5, opacity: 0.9, dashArray: '6, 6' });

            it.originMarker?.setRadius(7).setStyle({ fillColor: '#10B981', color: '#fff', weight: 2.5, fillOpacity: 1, opacity: 1 });
            it.destMarker?.setRadius(7).setStyle({ fillColor: '#EF4444', color: '#fff', weight: 2.5, fillOpacity: 1, opacity: 1 });

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
        if (map) map.closePopup();
    }

    window.focusCargoShipment = focusCargoShipment;
    window.resetCustomerShipmentFocus = resetCargoShipmentFocus;

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

    document.addEventListener('DOMContentLoaded', function () {
        initCustomerMap();
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

        // Klik di area peta kosong mengembalikan fokus rute ke normal
        map.on('click', function(e) {
            if (activeCargoId) {
                resetCargoShipmentFocus();
            }
        });

        trackingLayerGroup.addTo(map);

        setTimeout(() => {
            if (map) map.invalidateSize();
        }, 200);

        window.addEventListener('resize', () => {
            if (map) map.invalidateSize();
        });
    }

    function switchMapMode(mode) {
        currentMapMode = mode;
        const tabTracking = document.getElementById('tab-btn-tracking');
        const tabTrace = document.getElementById('tab-btn-routetrace');
        const panelTracking = document.getElementById('tracking-panel');
        const routeStepBanner = document.getElementById('route-step-banner');

        if (mode === 'tracking') {
            tabTracking.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-slate-900 text-cyan-600 dark:text-cyan-400 shadow-xs';
            tabTrace.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            if (panelTracking) panelTracking.classList.remove('hidden');
            if (routeStepBanner) routeStepBanner.classList.add('hidden');

            map.removeLayer(routeTraceLayerGroup);
            trackingLayerGroup.addTo(map);
            setTimeout(() => { if (map) map.invalidateSize(); }, 100);
            map.flyTo([15, 100], 3, { duration: 1 });
        } else {
            tabTrace.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-slate-900 text-purple-600 dark:text-purple-400 shadow-xs';
            tabTracking.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            if (panelTracking) panelTracking.classList.add('hidden');
            if (routeStepBanner) routeStepBanner.classList.remove('hidden');

            map.removeLayer(trackingLayerGroup);
            routeTraceLayerGroup.addTo(map);
            buildRouteTraceLayer();
            setTimeout(() => { if (map) map.invalidateSize(); }, 100);
            map.flyTo([20, 20], 2.2, { duration: 1 });
        }
    }

    function setTraceTransportMode(mode) {
        traceTransportMode = mode;
        const btnPort = document.getElementById('trace-btn-port');
        const btnAirport = document.getElementById('trace-btn-air');

        if (mode === 'PORT') {
            if (btnPort) {
                btnPort.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-slate-900 text-sky-600 dark:text-sky-400 shadow-xs flex items-center gap-1';
            }
            if (btnAirport) {
                btnAirport.className = 'px-2.5 py-1 rounded-md text-[11px] font-semibold transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1';
            }
        } else {
            if (btnAirport) {
                btnAirport.className = 'px-2.5 py-1 rounded-md text-[11px] font-bold transition-all bg-white dark:bg-slate-900 text-purple-600 dark:text-purple-400 shadow-xs flex items-center gap-1';
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

    function resetRouteTrackerInteractive(flyToWorld = false) {
        traceOrigin = null;
        traceDestination = null;
        lastAnalyzedRoute = null;

        if (traceRouteLine) {
            routeTraceLayerGroup.removeLayer(traceRouteLine);
            traceRouteLine = null;
        }
        if (traceOriginMarker) {
            routeTraceLayerGroup.removeLayer(traceOriginMarker);
            traceOriginMarker = null;
        }
        if (traceDestMarker) {
            routeTraceLayerGroup.removeLayer(traceDestMarker);
            traceDestMarker = null;
        }

        renderCountryTraceNodes();

        const bannerText = document.getElementById('route-step-text');
        if (bannerText) {
            bannerText.innerHTML = '<span class="text-purple-600 dark:text-purple-400 font-bold">Step 1: Click Origin Country on map</span>';
        }

        if (flyToWorld && map) {
            map.flyTo([20, 20], 2.2, { duration: 1 });
        }
    }

    function buildRouteTraceLayer() {
        routeTraceLayerGroup.clearLayers();
        traceNodesGroup.clearLayers();
        renderCountryTraceNodes();
        routeTraceLayerGroup.addLayer(traceNodesGroup);

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
            traceOrigin = c;
            renderOriginPin(c);

            const bannerText = document.getElementById('route-step-text');
            if (bannerText) {
                bannerText.innerHTML = '<span class="text-purple-600 dark:text-purple-400 font-bold">Step 2: Click Destination Country on map</span>';
            }
        } else if (traceOrigin && !traceDestination) {
            if (c.code === traceOrigin.code && c.name === traceOrigin.name) {
                alert('Silakan pilih negara tujuan yang berbeda dari negara asal.');
                return;
            }
            traceDestination = c;
            drawTraceRoute(traceOrigin, traceDestination);
        } else {
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
            routeTraceLayerGroup.removeLayer(traceOriginMarker);
        }
        const iso3 = getIso3Code(c);
        const icon = L.divIcon({
            className: 'route-node-pin-wrapper',
            html: `<div class="route-node-pin route-node-origin" style="width:26px; height:26px;">${iso3}</div>`,
            iconSize: [26, 26],
            iconAnchor: [13, 13]
        });
        traceOriginMarker = L.marker([c.lat, c.lng], { icon }).addTo(routeTraceLayerGroup);
    }

    function renderDestPin(c) {
        if (traceDestMarker) {
            routeTraceLayerGroup.removeLayer(traceDestMarker);
        }
        const iso3 = getIso3Code(c);
        const icon = L.divIcon({
            className: 'route-node-pin-wrapper',
            html: `<div class="route-node-pin route-node-dest" style="width:26px; height:26px;">${iso3}</div>`,
            iconSize: [26, 26],
            iconAnchor: [13, 13]
        });
        traceDestMarker = L.marker([c.lat, c.lng], { icon }).addTo(routeTraceLayerGroup);
    }

    function drawTraceRoute(origin, dest) {
        renderOriginPin(origin);
        renderDestPin(dest);

        if (traceRouteLine) {
            routeTraceLayerGroup.removeLayer(traceRouteLine);
        }

        const p1 = [parseFloat(origin.lat), parseFloat(origin.lng)];
        const p2 = [parseFloat(dest.lat), parseFloat(dest.lng)];
        const distKm = calculateDistanceKm(p1[0], p1[1], p2[0], p2[1]);
        const distNM = distKm / 1.852;

        let pathPoints = [];
        let timeStr = '';
        let speedStr = '';
        let corridorStr = '';
        let riskScore = 25;
        let riskLabel = 'Safe Route';
        let riskBgColor = '#10B981';

        if (traceTransportMode === 'AIR') {
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

        traceRouteLine = L.polyline(pathPoints, {
            color: '#10B981',
            weight: 3.5,
            dashArray: '6, 8',
            opacity: 0.95
        }).addTo(routeTraceLayerGroup);

        const oIso3 = getIso3Code(origin);
        const dIso3 = getIso3Code(dest);
        const bannerText = document.getElementById('route-step-text');
        if (bannerText) {
            bannerText.innerHTML = `<span class="text-emerald-600 dark:text-emerald-400 font-bold">Route Analyzed: ${oIso3} ${dIso3}</span>`;
        }

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

        map.fitBounds(L.latLngBounds([p1, p2]).pad(0.25), { duration: 1.0 });

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
            if (traceDestMarker) {
                traceDestMarker.bindPopup(popupContent, {
                    offset: [0, -14],
                    closeButton: true,
                    className: 'route-analysis-popup'
                }).openPopup();
            }
        }, 500);
    }

    function openRouteAnalyticsModal() {
        const data = lastAnalyzedRoute || window.lastAnalyzedRoute;
        if (!data) return;

        const modal = document.getElementById('route-analytics-modal');
        if (!modal) return;

        const o = data.origin;
        const d = data.dest;
        const oIsoLower = (o.code || '').toLowerCase().replace('_east','');
        const dIsoLower = (d.code || '').toLowerCase().replace('_east','');

        const setTxt = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

        setTxt('modal-origin-code', data.oIso3);
        setTxt('modal-origin-country', o.name);
        const oFlag = document.getElementById('modal-origin-flag');
        if (oFlag) oFlag.src = `https://flagcdn.com/w40/${oIsoLower}.png`;
        setTxt('modal-origin-hub', getCountryHubName(o, data.mode));
        setTxt('modal-origin-coords', `${parseFloat(o.lat).toFixed(3)}°, ${parseFloat(o.lng).toFixed(3)}°`);

        setTxt('modal-dest-code', data.dIso3);
        setTxt('modal-dest-country', d.name);
        const dFlag = document.getElementById('modal-dest-flag');
        if (dFlag) dFlag.src = `https://flagcdn.com/w40/${dIsoLower}.png`;
        setTxt('modal-dest-hub', getCountryHubName(d, data.mode));
        setTxt('modal-dest-coords', `${parseFloat(d.lat).toFixed(3)}°, ${parseFloat(d.lng).toFixed(3)}°`);

        setTxt('modal-mode-badge', data.modeName);
        setTxt('modal-distance', `${data.distKm.toLocaleString()} km (${data.distNM.toLocaleString()} NM)`);
        setTxt('modal-duration', data.timeStr);
        setTxt('modal-speed', data.speedStr);

        const riskBadge = document.getElementById('modal-risk-badge');
        const riskBar = document.getElementById('modal-risk-bar');
        if (riskBadge) {
            riskBadge.textContent = `Transit Risk: ${data.riskScore}/100 (${data.riskLabel})`;
            riskBadge.style.backgroundColor = data.riskBgColor;
        }
        if (riskBar) {
            riskBar.style.width = `${data.riskScore}%`;
            riskBar.style.backgroundColor = data.riskBgColor;
        }

        let riskDesc = '';
        let insRec = '';
        if (data.riskScore <= 35) {
            riskDesc = 'Koridor transit ini dikategorikan aman dengan risiko cuaca atau hambatan operasional minimal. Integritas dan ketepatan waktu pengiriman kargo Anda berada pada level optimum.';
            insRec = '🛡️ Proteksi Asuransi Standar Logistik Direkomendasikan';
        } else if (data.riskScore <= 65) {
            riskDesc = 'Rute melintasi perairan/ruang udara dengan variabilitas cuaca sedang atau kepadatan lalu lintas transit. Disarankan monitoring real-time berkala.';
            insRec = '⚠️ Direkomendasikan Tambahan Asuransi All-Risk Transit';
        } else {
            riskDesc = 'Rute melewati koridor transit jarak jauh lintas samudra atau selat internasional yang rentan cuaca buruk / kepadatan tinggi. Wajib pemantauan telemetry ketat.';
            insRec = '🚨 Wajib Proteksi Asuransi Komprehensif Tingkat Tinggi';
        }
        setTxt('modal-risk-description', riskDesc);
        setTxt('modal-insurance-rec', insRec);
        setTxt('modal-corridor', data.corridorStr);

        modal.classList.remove('hidden');
        modal.style.display = 'flex';

        try {
            if (window.lucide) lucide.createIcons();
        } catch (e) {}
    }

    function closeRouteAnalyticsModal() {
        const modal = document.getElementById('route-analytics-modal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
    }

    window.switchMapMode = switchMapMode;
    window.setTraceTransportMode = setTraceTransportMode;
    window.resetRouteTrackerInteractive = resetRouteTrackerInteractive;
    window.openRouteAnalyticsModal = openRouteAnalyticsModal;
    window.closeRouteAnalyticsModal = closeRouteAnalyticsModal;

    function buildTrackingMode() {
        trackingLayerGroup.clearLayers();
        cargoShipmentItems = [];
        activeCargoId = null;
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
            const carrierName = (s.carrier?.name || '').toLowerCase();
            const carrierCode = (s.carrier?.code || '').toUpperCase();
            const originType  = (s.origin?.location_type || '').toUpperCase();
            const destType    = (s.destination?.location_type || '').toUpperCase();
            const originName  = (s.origin?.name || '').toLowerCase();
            const destName    = (s.destination?.name || '').toLowerCase();
            const cargo       = (s.cargo_type || '').toLowerCase();

            // 0. Pesawat Kargo Udara (Real Jet Freighter Top-Down)
            if (s.transport_mode === 'AIR' || originType === 'AIRPORT' || destType === 'AIRPORT' ||
                originName.includes('bandara') || originName.includes('airport') ||
                destName.includes('bandara') || destName.includes('airport') ||
                carrierCode === 'FDX' || carrierName.includes('fedex') || carrierName.includes('express air') ||
                cargo.includes('air') || cargo.includes('avionics') || cargo.includes('vaccine') || cargo.includes('pharmaceutical')) {
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

            // 1. Truk / Mobil Paket Domestik (Hanya jika moda adalah LAND khusus tanpa laut)
            if (s.transport_mode === 'LAND') {
                return {
                    type: 'van',
                    label: 'Mobil Pengantaran Kurir (Last-Mile Delivery)',
                    emoji: '🚚',
                    accentColor: '#f59e0b',
                    iconW: 36,
                    iconH: 56,
                    realSvg: `
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
                        <rect x="10" y="20" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
                        <rect x="19.5" y="20" width="8.5" height="5" rx="0.5" fill="#e11d48" stroke="#be123c" stroke-width="0.5"/>
                        <rect x="10" y="26" width="8.5" height="5" rx="0.5" fill="#ea580c" stroke="#c2410c" stroke-width="0.5"/>
                        <rect x="19.5" y="26" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
                        <rect x="10" y="32" width="8.5" height="5" rx="0.5" fill="#eab308" stroke="#ca8a04" stroke-width="0.5"/>
                        <rect x="19.5" y="32" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
                        <rect x="10" y="38" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
                        <rect x="19.5" y="38" width="8.5" height="5" rx="0.5" fill="#ea580c" stroke="#c2410c" stroke-width="0.5"/>
                        <rect x="10" y="44" width="8.5" height="5" rx="0.5" fill="#e11d48" stroke="#be123c" stroke-width="0.5"/>
                        <rect x="19.5" y="44" width="8.5" height="5" rx="0.5" fill="#eab308" stroke="#ca8a04" stroke-width="0.5"/>
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

        // ── HELPER: HITUNG PROGRES REALTIME SESUAI JADWAL BERANGKAT & ESTIMASI TIBA ──
        function getShipmentRealtimeProgress(s) {
            const now = Date.now();
            const depTime = s.actual_departure ? new Date(s.actual_departure).getTime() : (s.expected_departure ? new Date(s.expected_departure).getTime() : null);
            const arrTime = s.estimated_time_arrival ? new Date(s.estimated_time_arrival).getTime() : (s.expected_arrival ? new Date(s.expected_arrival).getTime() : null);

            if (s.status === 'DELIVERED') return 1.0;

            if (depTime && arrTime && arrTime > depTime) {
                const frac = (now - depTime) / (arrTime - depTime);
                if (frac >= 0.08 && frac <= 0.95) return frac;
            }

            if (s.status === 'IN_TRANSIT') {
                const seed = (s.id * 37 + (s.shipment_number ? s.shipment_number.charCodeAt(s.shipment_number.length - 1) : 10));
                return ((seed % 55) + 25) / 100;
            }

            return 0.35;
        }

        function getPositionOnRoute(originPt, destPt, progress) {
            const numPoints = 40;
            const path = createCurvedPath(originPt, destPt, numPoints, 0.14);
            const idx = Math.min(numPoints, Math.max(0, Math.round(progress * numPoints)));
            return path[idx] || originPt;
        }

        function generateShipmentPopupContent(s, vehicle, curLoc, progress = 0.45) {
            const isAir = (s.transport_mode === 'AIR') || (vehicle.type === 'AIR');
            const pct = Math.round(progress * 100);

            const hasDelivery = !!(s.delivery_lat && s.delivery_lng);
            let multimodalHtml = '';
            if (hasDelivery && s.destination) {
                const devLat = parseFloat(s.delivery_lat);
                const devLng = parseFloat(s.delivery_lng);
                const distKm = (!isNaN(devLat) && !isNaN(devLng)) ? calculateDistanceKm(parseFloat(s.destination.latitude), parseFloat(s.destination.longitude), devLat, devLng) : 0;
                const isDomesticAir = distKm > 35;

                multimodalHtml = `
                    <div style="background:#f1f5f9; border:1px solid #cbd5e1; border-radius:8px; padding:6px 8px; margin-bottom:8px; font-size:10.5px; line-height:1.45;">
                        <div style="font-weight:800; color:#0369a1; margin-bottom:3px; display:flex; align-items:center; gap:4px;">
                            <span>🔄</span> <span>Alur Multimoda (Brazil ➔ Pelabuhan ➔ Pesawat ➔ Kurir):</span>
                        </div>
                        <div>🚢 <b>1. Samudra:</b> ${s.origin?.name || 'Pelabuhan Santos'} ➔ ${s.destination?.name || 'Pelabuhan Hub'}</div>
                        ${isDomesticAir ? `<div>✈️ <b>2. Pesawat Domestik:</b> Pelabuhan Hub ➔ Bandara <b>${s.delivery_city}</b></div>` : ''}
                        <div>🚚 <b>${isDomesticAir ? '3' : '2'}. Kurir Terakhir:</b> Mobil Paket menuju <b>${s.delivery_address || s.delivery_city}</b></div>
                    </div>
                `;
            }

            return `
                <div style="font-family:'Outfit',sans-serif; min-width:265px; padding:2px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; border-bottom:1px solid #e2e8f0; padding-bottom:6px;">
                        <span style="font-weight:900; font-size:13px; color:#0284c7;">${s.shipment_number}</span>
                        <span style="font-weight:800; font-size:10px; color:#15803d; background:#dcfce7; padding:2px 7px; border-radius:5px;">${s.status}</span>
                    </div>

                    <!-- Live Real-Time Flight / Voyage Progress Bar -->
                    <div style="background:#F0FDF4; border:1px solid #BBF7D0; padding:6px 8px; border-radius:8px; margin-bottom:8px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; font-weight:800; font-size:10px; color:#15803d; margin-bottom:3px;">
                            <span>${isAir ? '✈️ Progres Penerbangan:' : '🚢 Progres Pelayaran:'}</span>
                            <span style="font-size:11px; color:#047857;">${pct}%</span>
                        </div>
                        <div style="width:100%; height:5px; background:#dcfce7; border-radius:999px; overflow:hidden;">
                            <div style="width:${pct}%; height:100%; background:linear-gradient(90deg, #10b981, #06b6d4); border-radius:999px;"></div>
                        </div>
                    </div>

                    ${multimodalHtml}

                    <div style="font-size:11px; color:#334155; line-height:1.5; margin-bottom:8px;">
                        <div>🛫 <b>Asal:</b> ${s.origin?.name || '-'} (${s.origin?.city || '-'})</div>
                        <div>🛬 <b>Tujuan:</b> ${s.destination?.name || '-'} (${s.destination?.city || '-'})</div>
                        <div>📍 <b>Posisi Terkini:</b> ${curLoc?.name || (isAir ? 'Di Udara (Air Corridor)' : 'Dalam Perjalanan Laut')}</div>
                        <div>🏢 <b>Ekspedisi:</b> ${s.carrier?.name || '-'}</div>
                        <div>📦 <b>Kargo:</b> ${s.cargo_type}</div>
                    </div>
                    <div style="display:flex; gap:6px; margin-top:8px;">
                        <a href="/customer/shipments/${s.id}" style="flex:1; text-align:center; background:#0284c7; color:#fff; font-weight:700; padding:6px 10px; border-radius:8px; text-decoration:none; font-size:11px; box-shadow:0 2px 4px rgba(2,132,199,0.3);">
                            Buka Detail Kargo & Riwayat →
                        </a>
                        <button type="button" onclick="window.resetCustomerShipmentFocus()" style="background:#f1f5f9; color:#475569; font-weight:700; padding:6px 10px; border-radius:8px; border:1px solid #cbd5e1; font-size:11px; cursor:pointer;" title="Kembalikan semua rute">
                            🔄 Reset
                        </button>
                    </div>
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
                const isAir = (s.transport_mode === 'AIR') || (vehicle.type === 'AIR');
                const progress = getShipmentRealtimeProgress(s);
                const seaWaypoints = window.buildMaritimeWaypoints(originPt, destPt, isAir);

                const curLoc = s.current_location || s.currentLocation;
                const isAtOrigin = curLoc && curLoc.latitude && curLoc.longitude &&
                    Math.abs(parseFloat(curLoc.latitude) - oLat) < 0.25 &&
                    Math.abs(parseFloat(curLoc.longitude) - oLng) < 0.25;

                let curPt = null;
                if (curLoc && curLoc.latitude && curLoc.longitude && !isAtOrigin && s.status !== 'DELIVERED') {
                    curPt = [parseFloat(curLoc.latitude), parseFloat(curLoc.longitude)];
                } else if (s.status === 'DELIVERED') {
                    curPt = destPt;
                } else if (seaWaypoints.length > 2) {
                    const sampled = window.samplePolylineByRange(seaWaypoints, 0, progress, 1);
                    curPt = sampled[sampled.length - 1] || originPt;
                } else {
                    curPt = getPositionOnRoute(originPt, destPt, progress);
                }
                allBounds.push(curPt);

                const displayCurLoc = (!isAtOrigin && curLoc?.name) ? curLoc : {
                    name: isAir ? `Di Udara (Koridor Udara Internasional - FL360)` : `Di Laut Lepas (Jalur Maritim Samudra)`
                };

                const popupHtml = generateShipmentPopupContent(s, vehicle, displayCurLoc, progress);

                // Origin Hub (🟢 Green with white border)
                const originMarker = L.circleMarker(originPt, {
                    radius: 7, fillColor: '#10B981', color: '#fff', weight: 2.5, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                originMarker.on('click', () => focusCargoShipment(s.id, originPt));

                // Destination Hub (🔴 Red with white border)
                const destMarker = L.circleMarker(destPt, {
                    radius: 7, fillColor: '#EF4444', color: '#fff', weight: 2.5, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                destMarker.on('click', () => focusCargoShipment(s.id, destPt));

                let traveledPoly = null;
                let remainingPoly = null;
                let directPoly = null;
                let vehicleMarker = null;

                const isDelivered = (s.status === 'DELIVERED');

                // Main route paths (oceanic waypoints)
                if (curPt) {
                    const traveledPath = seaWaypoints.length > 2
                        ? window.samplePolylineByRange(seaWaypoints, 0, progress, 24)
                        : createCurvedPath(originPt, curPt, 20, isAir ? 0.15 : 0.11);
                    traveledPoly = L.polyline(traveledPath, {
                        color: isAir ? '#0284c7' : '#0369a1', weight: 3.5, opacity: 0.9, lineCap: 'round', lineJoin: 'round', cursor: 'pointer'
                    }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                    traveledPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));

                    const remainingPath = seaWaypoints.length > 2
                        ? window.samplePolylineByRange(seaWaypoints, progress, 1, 24)
                        : createCurvedPath(curPt, destPt, 20, isAir ? 0.15 : 0.11);
                    remainingPoly = L.polyline(remainingPath, {
                        color: isAir ? '#38bdf8' : '#7dd3fc', weight: 2.5, dashArray: '6, 8', opacity: 0.8, lineCap: 'round', lineJoin: 'round', cursor: 'pointer'
                    }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                    remainingPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));

                    if (!isDelivered) {
                        const nextBearingPt = (remainingPath && remainingPath.length > 1) ? remainingPath[1] : destPt;
                        const heading = calculateBearing(curPt, nextBearingPt);
                        const vW = vehicle.iconW || 44;
                        const vH = vehicle.iconH || 68;

                        vehicleMarker = L.marker(curPt, {
                            icon: L.divIcon({
                                className: 'cargo-real-vehicle-marker',
                                html: `
                                    <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${heading}deg);" title="${vehicle.label} &bull; ${s.shipment_number}">
                                        ${vehicle.realSvg}
                                    </div>
                                    <div class="cargo-vehicle-tag" style="white-space:nowrap; margin-top:-2px;">
                                        <span class="cargo-tag-dot" style="background:${vehicle.accentColor || vehicle.color || '#38bdf8'};"></span>
                                        <span>${s.shipment_number}</span>
                                    </div>
                                `,
                                iconSize: [vW, vH],
                                iconAnchor: [vW / 2, vH / 2],
                                popupAnchor: [0, -(vH / 2 + 5)]
                            }),
                            zIndexOffset: 1000
                        }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                        vehicleMarker.on('click', () => focusCargoShipment(s.id, curPt));
                    }
                } else {
                    const directPath = createCurvedPath(originPt, destPt, 20, 0.12);
                    directPoly = L.polyline(directPath, {
                        color: '#0284c7', weight: 3, dashArray: '6, 6', opacity: 0.85, cursor: 'pointer'
                    }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                    directPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));
                }

                const routePoints = [originPt, destPt];
                if (curPt) routePoints.push(curPt);

                let deliveryPoly = null;
                let feederFlightPoly = null;
                let feederPlaneMarker = null;
                let destPinMarker = null;
                let courierVanMarker = null;

                // If customer has last-mile delivery address / courier van
                if (s.delivery_lat && s.delivery_lng) {
                    const devLat = parseFloat(s.delivery_lat);
                    const devLng = parseFloat(s.delivery_lng);
                    if (!isNaN(devLat) && !isNaN(devLng)) {
                        const devCoords = [devLat, devLng];
                        allBounds.push(devCoords);
                        routePoints.push(devCoords);

                        const distPortToCityKm = calculateDistanceKm(destPt[0], destPt[1], devLat, devLng);
                        const isDomesticFeederAir = distPortToCityKm > 35;

                        if (isDomesticFeederAir) {
                            // ✈️ Koridor Penerbangan Domestik (Pelabuhan Masuk ➔ Kota Tujuan)
                            const flightPath = createCurvedPath(destPt, devCoords, 20, 0.12);
                            feederFlightPoly = L.polyline(flightPath, {
                                color: '#00F0FF', weight: 3.5, dashArray: '6, 6', opacity: 0.95, lineCap: 'round', cursor: 'pointer'
                            }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                            feederFlightPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));

                            // Pesawat Kargo Domestik tidak ditumpuk saat pelayaran utama berjalan
                            if (false && (s.is_last_mile || s.status === 'DELIVERED')) {
                                const planeLat = destPt[0] + (devCoords[0] - destPt[0]) * 0.52;
                                const planeLng = destPt[1] + (devCoords[1] - destPt[1]) * 0.52;
                                const planeHeading = calculateBearing(destPt, devCoords);
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
                                }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                                feederPlaneMarker.on('click', () => focusCargoShipment(s.id, [planeLat, planeLng]));
                            }
                        } else {
                            // Jalur Darat Langsung Dekat (Metropolitan Area)
                            deliveryPoly = L.polyline([destPt, devCoords], {
                                color: '#f59e0b', weight: 3.5, dashArray: '5, 6', opacity: 0.95, lineCap: 'round', cursor: 'pointer'
                            }).bindPopup(popupHtml).addTo(trackingLayerGroup);
                            deliveryPoly.on('click', (e) => focusCargoShipment(s.id, e.latlng));
                        }

                        // Pin Alamat Penerima (📍)
                        const destPin = L.divIcon({
                            className: 'cargo-destination-pin',
                            html: `<div style="font-size:26px; line-height:1; filter:drop-shadow(0 3px 6px rgba(0,0,0,0.5)); transform:translate(-2px, -14px); cursor:pointer;" title="Alamat Pengantaran: ${s.delivery_address || s.delivery_city}">📍</div>`,
                            iconSize: [26, 26],
                            iconAnchor: [13, 26],
                            popupAnchor: [0, -28]
                        });
                        destPinMarker = L.marker(devCoords, { icon: destPin, zIndexOffset: 1200 })
                            .bindPopup(popupHtml)
                            .addTo(trackingLayerGroup);
                        destPinMarker.on('click', () => focusCargoShipment(s.id, devCoords));

                        // 🚚 Mobil Van Kurir Paket hanya muncul saat status DELIVERED
                        if (isDelivered && s.is_last_mile) {
                            const vanFraction = isDomesticFeederAir ? 0.88 : 0.65;
                            const vanLat = destPt[0] + (devCoords[0] - destPt[0]) * vanFraction;
                            const vanLng = destPt[1] + (devCoords[1] - destPt[1]) * vanFraction;
                            const vanHeading = calculateBearing(destPt, devCoords);
                            const vanPlate = s.courier_vehicle ? (s.courier_vehicle.split('(')[1]?.replace(')','') || s.shipment_number) : s.shipment_number;

                            const courierVanDivIcon = L.divIcon({
                                className: 'cargo-real-vehicle-marker',
                                html: `
                                    <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${vanHeading}deg);" title="${s.courier_name || 'Mobil Paket Kurir'} &bull; ${s.shipment_number}">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 68" width="30" height="54" style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));">
                                            <rect x="4" y="3" width="30" height="62" rx="6" fill="#0f172a" opacity="0.35" transform="translate(1.5, 2.5)"/>
                                            <rect x="3" y="12" width="3" height="8" rx="1" fill="#0f172a"/>
                                            <rect x="32" y="12" width="3" height="8" rx="1" fill="#0f172a"/>
                                            <rect x="3" y="48" width="3" height="9" rx="1" fill="#0f172a"/>
                                            <rect x="32" y="48" width="3" height="9" rx="1" fill="#0f172a"/>
                                            <rect x="5" y="4" width="28" height="60" rx="5" fill="#f8fafc" stroke="#64748b" stroke-width="1.2"/>
                                            <rect x="7" y="6" width="24" height="15" rx="3" fill="#e2e8f0"/>
                                            <path d="M8 8 Q19 6 30 8 L29 15 Q19 13.5 9 15 Z" fill="#0284c7"/>
                                            <rect x="6.5" y="9.5" width="2" height="7" rx="0.5" fill="#0284c7"/>
                                            <rect x="29.5" y="9.5" width="2" height="7" rx="0.5" fill="#0284c7"/>
                                            <rect x="3" y="10" width="2.5" height="4" rx="0.8" fill="#334155"/>
                                            <rect x="32.5" y="10" width="2.5" height="4" rx="0.8" fill="#334155"/>
                                            <rect x="6.5" y="23" width="25" height="39" rx="2.5" fill="#f59e0b" stroke="#d97706" stroke-width="0.8"/>
                                            <rect x="9.5" y="27" width="19" height="2" fill="#fde68a"/>
                                            <text x="19" y="42" font-size="12" font-weight="900" fill="#ffffff" text-anchor="middle" font-family="sans-serif">📦</text>
                                            <text x="19" y="54" font-size="5" font-weight="800" fill="#78350f" text-anchor="middle" font-family="sans-serif">KURIR PAKET</text>
                                        </svg>
                                    </div>
                                    <div class="cargo-vehicle-tag" style="white-space:nowrap; margin-top:-2px;">
                                        <span class="cargo-tag-dot" style="background:#f59e0b;"></span>
                                        <span>Mobil Paket: ${vanPlate}</span>
                                    </div>
                                `,
                                iconSize: [36, 56],
                                iconAnchor: [18, 28],
                                popupAnchor: [0, -32]
                            });

                            courierVanMarker = L.marker([vanLat, vanLng], { icon: courierVanDivIcon, zIndexOffset: 1500 })
                                .bindPopup(popupHtml)
                                .addTo(trackingLayerGroup);
                            courierVanMarker.on('click', () => focusCargoShipment(s.id, [vanLat, vanLng]));
                        }
                    }
                }

                const shipmentBounds = L.latLngBounds(routePoints);
                cargoShipmentItems.push({
                    id: s.id,
                    s,
                    vehicle,
                    originMarker,
                    destMarker,
                    traveledPoly,
                    remainingPoly,
                    directPoly,
                    vehicleMarker,
                    deliveryPoly,
                    feederFlightPoly,
                    feederPlaneMarker,
                    deliveryMarker: destPinMarker,
                    courierVanMarker,
                    bounds: shipmentBounds
                });
            }
        });

        if (allBounds.length > 0) {
            map.fitBounds(allBounds, { padding: [50, 50], maxZoom: 5 });
        }
    }
</script>
@endpush
