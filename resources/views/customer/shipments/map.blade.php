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
<div id="route-analytics-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm hidden p-4">
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

        trackingLayerGroup.addTo(map);
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
            map.flyTo([15, 100], 3, { duration: 1 });
        } else {
            tabTrace.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all bg-white dark:bg-slate-900 text-purple-600 dark:text-purple-400 shadow-xs';
            tabTracking.className = 'flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            if (panelTracking) panelTracking.classList.add('hidden');
            if (routeStepBanner) routeStepBanner.classList.remove('hidden');

            map.removeLayer(trackingLayerGroup);
            routeTraceLayerGroup.addTo(map);
            buildRouteTraceLayer();
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
                <button onclick="openRouteAnalyticsModal()" style="width:100%; background:#7C3AED; color:#ffffff; border:none; padding:7px 12px; border-radius:10px; font-weight:700; font-size:11px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:6px; box-shadow:0 2px 6px rgba(124,58,237,0.35); transition:background 0.2s;">
                    <svg style="width:13px; height:13px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                    <span>View Analytics</span>
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
        const data = lastAnalyzedRoute;
        if (!data) return;

        const modal = document.getElementById('route-analytics-modal');
        if (!modal) return;

        const o = data.origin;
        const d = data.dest;
        const oIsoLower = (o.code || '').toLowerCase().replace('_east','');
        const dIsoLower = (d.code || '').toLowerCase().replace('_east','');

        document.getElementById('modal-origin-code').textContent = data.oIso3;
        document.getElementById('modal-origin-country').textContent = o.name;
        document.getElementById('modal-origin-flag').src = `https://flagcdn.com/w40/${oIsoLower}.png`;
        document.getElementById('modal-origin-hub').textContent = getCountryHubName(o, data.mode);
        document.getElementById('modal-origin-coords').textContent = `${parseFloat(o.lat).toFixed(3)}°, ${parseFloat(o.lng).toFixed(3)}°`;

        document.getElementById('modal-dest-code').textContent = data.dIso3;
        document.getElementById('modal-dest-country').textContent = d.name;
        document.getElementById('modal-dest-flag').src = `https://flagcdn.com/w40/${dIsoLower}.png`;
        document.getElementById('modal-dest-hub').textContent = getCountryHubName(d, data.mode);
        document.getElementById('modal-dest-coords').textContent = `${parseFloat(d.lat).toFixed(3)}°, ${parseFloat(d.lng).toFixed(3)}°`;

        document.getElementById('modal-mode-badge').textContent = data.modeName;
        document.getElementById('modal-distance').textContent = `${data.distKm.toLocaleString()} km (${data.distNM.toLocaleString()} NM)`;
        document.getElementById('modal-duration').textContent = data.timeStr;
        document.getElementById('modal-speed').textContent = data.speedStr;

        const riskBadge = document.getElementById('modal-risk-badge');
        const riskBar = document.getElementById('modal-risk-bar');
        riskBadge.textContent = `Transit Risk: ${data.riskScore}/100 (${data.riskLabel})`;
        riskBadge.style.backgroundColor = data.riskBgColor;
        riskBar.style.width = `${data.riskScore}%`;
        riskBar.style.backgroundColor = data.riskBgColor;

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
        document.getElementById('modal-risk-description').textContent = riskDesc;
        document.getElementById('modal-insurance-rec').textContent = insRec;
        document.getElementById('modal-corridor').textContent = data.corridorStr;

        modal.classList.remove('hidden');
    }

    function closeRouteAnalyticsModal() {
        const modal = document.getElementById('route-analytics-modal');
        if (modal) modal.classList.add('hidden');
    }

    window.switchMapMode = switchMapMode;
    window.setTraceTransportMode = setTraceTransportMode;
    window.resetRouteTrackerInteractive = resetRouteTrackerInteractive;
    window.openRouteAnalyticsModal = openRouteAnalyticsModal;
    window.closeRouteAnalyticsModal = closeRouteAnalyticsModal;

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
