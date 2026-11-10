@extends('layouts.admin')

@section('title', 'Peta Pengiriman Global')

@push('styles')
<style>
    /* Custom Leaflet Vehicle Markers for Cargo Tracker */
    .cargo-vehicle-marker {
        background: transparent !important;
        border: none !important;
    }
    .cargo-vehicle-badge {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.45), 0 0 0 2.5px #ffffff;
        transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.2s ease;
        cursor: pointer;
    }
    .cargo-vehicle-badge:hover {
        transform: scale(1.25);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.6), 0 0 0 3.5px #38BDF8;
        z-index: 99999 !important;
    }
    .cargo-vehicle-pulse {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: inherit;
        opacity: 0.65;
        animation: cargoPulseRadar 2.2s cubic-bezier(0.24, 0, 0.38, 1) infinite;
        z-index: -1;
    }
    @keyframes cargoPulseRadar {
        0% { transform: scale(1); opacity: 0.7; }
        60% { transform: scale(2.2); opacity: 0; }
        100% { transform: scale(2.2); opacity: 0; }
    }
    .cargo-vehicle-ship {
        background: linear-gradient(135deg, #0284c7, #0369a1);
    }
    .cargo-vehicle-plane {
        background: linear-gradient(135deg, #0284c7, #2563eb);
    }
    .cargo-vehicle-truck {
        background: linear-gradient(135deg, #059669, #047857);
    }
    .cargo-vehicle-train {
        background: linear-gradient(135deg, #d97706, #b45309);
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
            maxZoom: 12
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap kontributor | CargoTrace'
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

        // ── HELPER: GET VEHICLE ICON & TYPE PER SHIPMENT ─────────
        function getShipmentVehicle(s) {
            const carrierName = (s.carrier?.name || '').toLowerCase();
            const carrierCode = (s.carrier?.code || '').toUpperCase();
            const originType  = (s.origin?.location_type || '').toUpperCase();
            const destType    = (s.destination?.location_type || '').toUpperCase();
            const originName  = (s.origin?.name || '').toLowerCase();
            const destName    = (s.destination?.name || '').toLowerCase();
            const cargo       = (s.cargo_type || '').toLowerCase();
            const notes       = (s.notes || '').toLowerCase();

            // 1. Pesawat Kargo Udara (Airplane)
            if (originType === 'AIRPORT' || destType === 'AIRPORT' ||
                originName.includes('bandara') || originName.includes('airport') ||
                destName.includes('bandara') || destName.includes('airport') ||
                carrierCode === 'FDX' || carrierName.includes('fedex') || carrierName.includes('express air') ||
                cargo.includes('avionics') || cargo.includes('vaccine') || cargo.includes('pharmaceutical')) {
                return {
                    type: 'plane',
                    label: 'Pesawat Kargo Udara (Air Cargo)',
                    emoji: '✈️',
                    badgeClass: 'cargo-vehicle-plane',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M21 16v-2l-8-5V3.5c0-.83-.67-1.5-1.5-1.5S10 2.67 10 3.5V9l-8 5v2l8-2.5V19l-2 1.5V22l3.5-1 3.5 1v-1.5L13 19v-5.5l8 2.5z"/></svg>`
                };
            }

            // 2. Kereta Api Logistik (Freight Train)
            if (cargo.includes('coal') || cargo.includes('ore') || cargo.includes('grain') ||
                originName.includes('stasiun') || destName.includes('stasiun') ||
                originName.includes('rail') || destName.includes('rail') ||
                carrierName.includes('rail') || notes.includes('rail') || notes.includes('kereta')) {
                return {
                    type: 'train',
                    label: 'Kereta Logistik (Freight Train)',
                    emoji: '🚆',
                    badgeClass: 'cargo-vehicle-train',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M12 2c-4 0-8 .5-8 4v9.5C4 17.43 5.57 19 7.5 19L6 20.5v.5h12v-.5L16.5 19c1.93 0 3.5-1.57 3.5-3.5V6c0-3.5-4-4-8-4zM7.5 17c-.83 0-1.5-.67-1.5-1.5S6.67 14 7.5 14s1.5.67 1.5 1.5S8.33 17 7.5 17zm9 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm1.5-6H6V7h12v4z"/></svg>`
                };
            }

            // 3. Truk Kontainer Darat (Freight Truck)
            if (originType === 'WAREHOUSE' || destType === 'WAREHOUSE' ||
                originType === 'CUSTOMS_OFFICE' || destType === 'CUSTOMS_OFFICE' ||
                originName.includes('gudang') || destName.includes('gudang') ||
                carrierName.includes('truck') || notes.includes('truck') || notes.includes('darat')) {
                return {
                    type: 'truck',
                    label: 'Truk Kontainer (Freight Truck)',
                    emoji: '🚛',
                    badgeClass: 'cargo-vehicle-truck',
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/></svg>`
                };
            }

            // 4. Default: Kapal Kontainer Laut (Cargo Vessel)
            return {
                type: 'ship',
                label: 'Kapal Kontainer (Cargo Vessel)',
                emoji: '🚢',
                badgeClass: 'cargo-vehicle-ship',
                svg: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M20 21c-1.39 0-2.78-.47-4-1.32-2.44 1.71-5.56 1.71-8 0C6.78 20.53 5.39 21 4 21H2v2h2c1.38 0 2.74-.35 4-.99 2.52 1.29 5.48 1.29 8 0 1.26.65 2.62.99 4 .99h2v-2h-2zM3.95 19H4c1.6 0 3.02-.88 4-2 .98 1.12 2.4 2 4 2s3.02-.88 4-2c.98 1.12 2.4 2 4 2h.05l1.89-6.68c.08-.26.06-.54-.06-.78s-.32-.42-.58-.5L20 11.02V8c0-.55-.45-1-1-1h-2V4c0-.55-.45-1-1-1H8c-.55 0-1 .45-1 1v3H5c-.55 0-1 .45-1 1v3.02l-1.3.36c-.26.08-.46.26-.58.5s-.14.52-.06.78L3.95 19zM10 5h4v2h-4V5zm-3 4h10v2.24l-5 1.36-5-1.36V9z"/></svg>`
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

                    // Marker Kendaraan Kargo
                    const vehicleDivIcon = L.divIcon({
                        className: 'cargo-vehicle-marker',
                        html: `
                            <div class="cargo-vehicle-badge ${vehicle.badgeClass}" title="${vehicle.label} &bull; ${s.shipment_number}">
                                <div class="cargo-vehicle-pulse"></div>
                                ${vehicle.svg}
                            </div>
                        `,
                        iconSize: [34, 34],
                        iconAnchor: [17, 17],
                        popupAnchor: [0, -18]
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

