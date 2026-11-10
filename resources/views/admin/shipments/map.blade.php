@extends('layouts.admin')

@section('title', 'Peta Pengiriman Global')

@push('styles')
<style>
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
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Peta Pengiriman Global</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan visual posisi kargo internasional dan jalur lalu lintas pelabuhan/bandara</p>
        </div>
        <div>
            <form method="GET" action="{{ route('admin.shipments.map') }}" class="flex items-center gap-2">
                <select name="shipment_id" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <option value="">Tampilkan Semua Rute Kargo</option>
                    @foreach($allShipments as $s)
                        <option value="{{ $s->id }}" {{ request('shipment_id') == $s->id ? 'selected' : '' }}>
                            {{ $s->shipment_number }} &ndash; {{ $s->origin->city }} &rarr; {{ $s->destination->city }}
                        </option>
                    @endforeach
                </select>
                @if(request('shipment_id'))
                    <a href="{{ route('admin.shipments.map') }}" class="px-2.5 py-1.5 text-xs text-slate-500 hover:text-slate-700 dark:hover:text-slate-200 bg-slate-100 dark:bg-slate-800 rounded-lg" title="Reset Tampilan">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Map Container with Floating Route Info Card -->
    <div class="relative rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <!-- Interactive Leaflet Map -->
        <div id="globalMap" class="w-full h-[600px] z-10"></div>

        <!-- Floating Route Info Box (Mockup style) -->
        <div class="absolute top-4 right-4 z-20 w-80 p-4 rounded-xl bg-slate-900/90 backdrop-blur-md border border-slate-700/80 text-white shadow-xl">
            <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                <span class="text-xs font-bold text-cyan-400">Informasi Rute Kargo</span>
                <span class="text-[10px] text-slate-400 font-mono">{{ count($mapShipments) }} Rute Terpilih</span>
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
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Pelabuhan Asal</span>
                            <div class="font-medium text-slate-200">{{ $focused->origin->name }} ({{ $focused->origin->city }})</div>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Pelabuhan Tujuan</span>
                            <div class="font-medium text-slate-200">{{ $focused->destination->name }} ({{ $focused->destination->city }})</div>
                        </div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Ekspedisi & Status</span>
                        <div class="font-medium text-slate-200">{{ $focused->carrier->name }} &bull; {{ $focused->status }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Status Ketepatan</span>
                        <div class="{{ $focused->isDelayed() ? 'text-rose-400 font-bold' : 'text-emerald-400 font-medium' }}">
                            {{ $focused->isDelayed() ? 'Terlambat ' . $focused->formatted_delay : 'Tepat Waktu' }}
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-300">Menampilkan seluruh jaringan lalu lintas kargo aktif di peta dunia.</p>
                    <div class="pt-2 text-[11px] text-slate-400 space-y-1.5">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Titik Keberangkatan (Asal)</div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Titik Kedatangan (Tujuan)</div>
                        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-cyan-600 inline-flex items-center justify-center text-[9px] text-white">🚢</span> Kapal Kontainer Laut</div>
                        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-sky-500 inline-flex items-center justify-center text-[9px] text-white">✈️</span> Pesawat Kargo Udara</div>
                        <div class="flex items-center gap-2"><span class="w-3.5 h-3.5 rounded-full bg-emerald-600 inline-flex items-center justify-center text-[9px] text-white">🚛</span> Truk Kontainer Logistik</div>
                        <div class="flex items-center gap-2"><span class="w-5 h-0.5 bg-cyan-500 inline-block"></span> Jalur Telah Ditempuh</div>
                        <div class="flex items-center gap-2"><span class="w-5 h-0.5 border-b border-dashed border-cyan-400 inline-block"></span> Estimasi Sisa Rute</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const map = L.map('globalMap', {
            center: [15, 100],
            zoom: 3,
            minZoom: 2,
            maxZoom: 19
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap kontributor | CargoTrace',
            maxZoom: 19,
            maxNativeZoom: 19
        }).addTo(map);

        const shipments = {!! json_encode($mapShipments) !!};
        const allBounds = [];

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

            const nx = -dLng / dist;
            const ny = dLat / dist;

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

        shipments.forEach(s => {
            if (s.origin && s.destination) {
                const oLat = parseFloat(s.origin.latitude);
                const oLng = parseFloat(s.origin.longitude);
                const dLat = parseFloat(s.destination.latitude);
                const dLng = parseFloat(s.destination.longitude);

                if (isNaN(oLat) || isNaN(oLng) || isNaN(dLat) || isNaN(dLng)) return;

                const originPt = [oLat, oLng];
                const destPt   = [dLat, dLng];
                allBounds.push(originPt, destPt);

                const vehicle = getShipmentVehicle(s);

                // Origin (Green Marker)
                L.circleMarker(originPt, {
                    radius: 7,
                    fillColor: '#10B981',
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 1
                }).bindPopup(`<b>Pelabuhan Asal:</b> ${s.origin.name}<br>${s.origin.city}, ${s.origin.country}<br><b>Resi:</b> ${s.shipment_number}`).addTo(map);

                // Destination (Red Marker)
                L.circleMarker(destPt, {
                    radius: 7,
                    fillColor: '#EF4444',
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 1
                }).bindPopup(`<b>Pelabuhan Tujuan:</b> ${s.destination.name}<br>${s.destination.city}, ${s.destination.country}<br><b>Resi:</b> ${s.shipment_number}`).addTo(map);

                // Current Location (Vehicle Marker)
                const curLoc = s.current_location || s.currentLocation;
                let hasCurrent = false;
                let curPt = null;
                if (curLoc && curLoc.latitude && curLoc.longitude) {
                    const cLat = parseFloat(curLoc.latitude);
                    const cLng = parseFloat(curLoc.longitude);
                    if (!isNaN(cLat) && !isNaN(cLng)) {
                        curPt = [cLat, cLng];
                        hasCurrent = true;
                        allBounds.push(curPt);
                    }
                }

                if (hasCurrent) {
                    // Lintasan yang telah ditempuh: Asal -> Posisi Saat Ini (Solid cyan lengkung)
                    const traveledPath = createCurvedPath(originPt, curPt, 18, 0.12);
                    L.polyline(traveledPath, {
                        color: '#0284c7',
                        weight: 3.5,
                        opacity: 0.9,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).bindTooltip(`<b>${s.shipment_number}</b><br><span style="font-size:11px">Asal ➔ Posisi Saat Ini (Ditempuh)</span>`, { sticky: true }).addTo(map);

                    // Estimasi sisa rute: Posisi Saat Ini -> Tujuan (Dashed cyan lengkung)
                    const remainingPath = createCurvedPath(curPt, destPt, 18, 0.12);
                    L.polyline(remainingPath, {
                        color: '#38bdf8',
                        weight: 2.5,
                        dashArray: '6, 8',
                        opacity: 0.8,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).bindTooltip(`<b>${s.shipment_number}</b><br><span style="font-size:11px">Posisi Saat Ini ➔ Tujuan (Estimasi)</span>`, { sticky: true }).addTo(map);

                    // Marker Kendaraan Kargo Realistis (Tanpa Bulatan - Dynamic Bearing)
                    const heading = calculateBearing(curPt, destPt);
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

                    L.marker(curPt, { icon: vehicleDivIcon, zIndexOffset: 1000 }).bindPopup(`
                        <div style="min-width:240px; font-family: Outfit, sans-serif;">
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
                                <div>Ekspedisi: <b>${s.carrier?.name || '-'}</b></div>
                                <div>Jenis Kargo: <b>${s.cargo_type || 'General Cargo'}</b></div>
                                <div>Status: <b style="color:#166534;">${s.status || '-'}</b></div>
                            </div>
                            <a href="/admin/shipments/${s.id}" style="display:block; text-align:center; background:#0284c7; color:#fff; font-weight:600; padding:6px 12px; border-radius:8px; text-decoration:none; font-size:11px;">
                                Lihat Detail Pengiriman →
                            </a>
                        </div>
                    `).addTo(map);

                } else {
                    // Belum ada posisi terpisah, rute lengkung dari Asal ke Tujuan
                    const directPath = createCurvedPath(originPt, destPt, 24, 0.12);
                    L.polyline(directPath, {
                        color: '#0284c7',
                        weight: 2.5,
                        dashArray: '6, 8',
                        opacity: 0.75,
                        lineCap: 'round',
                        lineJoin: 'round'
                    }).bindTooltip(`<b>${s.shipment_number}</b><br><span style="font-size:11px">${s.origin.name} ➔ ${s.destination.name}</span>`, { sticky: true }).addTo(map);
                }
            }
        });

        if (allBounds.length > 0) {
            map.fitBounds(L.latLngBounds(allBounds), { padding: [50, 50] });
        }
    });
</script>
@endpush

