@extends('layouts.operator')

@section('title', 'Peta Pengiriman')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Peta Pengiriman</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Visualisasi rute pengiriman global, lokasi real-time, dan status perjalanan.</p>
    </div>

    {{-- Filter Dropdown --}}
    <form method="GET" action="{{ route('operator.shipments.map') }}" class="flex items-center gap-2">
        <select name="shipment_id" onchange="this.form.submit()" class="px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-[#0A1128] text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono shadow-sm">
            <option value="">— Tampilkan Semua Pengiriman —</option>
            @foreach($allShipments as $s)
                <option value="{{ $s->id }}" {{ $selectedShipmentId == $s->id ? 'selected' : '' }}>
                    {{ $s->shipment_number }} ({{ $s->origin?->city }} → {{ $s->destination?->city }})
                </option>
            @endforeach
        </select>
        @if($selectedShipmentId)
            <a href="{{ route('operator.shipments.map') }}" class="px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Semua</a>
        @endif
    </form>
</div>

{{-- Map Container with Floating Card --}}
<div class="relative bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
    <div id="operator-global-map" style="height: 600px; width: 100%; background: #0A1128;"></div>

    {{-- Legend / Info Overlay --}}
    <div class="absolute bottom-6 left-6 z-[1000] bg-white/90 dark:bg-[#0A1128]/90 backdrop-blur-md border border-slate-200 dark:border-slate-800 rounded-xl p-3 shadow-lg text-xs space-y-2">
        <div class="font-semibold text-slate-700 dark:text-slate-200 mb-1">Keterangan Peta</div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block"></span>
            <span class="text-slate-600 dark:text-slate-300">Pelabuhan / Bandara Asal</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-rose-500 inline-block"></span>
            <span class="text-slate-600 dark:text-slate-300">Tujuan Akhir</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-cyan-400 inline-block ring-2 ring-cyan-400/30"></span>
            <span class="text-slate-600 dark:text-slate-300">Posisi Kargo Terkini</span>
        </div>
        <div class="flex items-center gap-2">
            <span class="w-4 h-0.5 border-t-2 border-dashed border-cyan-500 inline-block"></span>
            <span class="text-slate-600 dark:text-slate-300">Rute Perjalanan</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('operator-global-map', {
        zoomControl: true,
        worldCopyJump: true
    }).setView([15, 110], 3);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18
    }).addTo(map);

    const bounds = [];
    const shipments = @json($mapShipments);

    shipments.forEach(s => {
        if (!s.origin || !s.destination) return;

        const oLat = parseFloat(s.origin.latitude);
        const oLng = parseFloat(s.origin.longitude);
        const dLat = parseFloat(s.destination.latitude);
        const dLng = parseFloat(s.destination.longitude);

        if (!isNaN(oLat) && !isNaN(oLng) && !isNaN(dLat) && !isNaN(dLng)) {
            bounds.push([oLat, oLng]);
            bounds.push([dLat, dLng]);

            // Marker Asal
            L.circleMarker([oLat, oLng], {
                radius: 7,
                color: '#10b981',
                fillColor: '#10b981',
                fillOpacity: 0.9,
                weight: 2
            }).addTo(map).bindPopup(`<b>Asal:</b> ${s.origin.name}<br><b>Kargo:</b> ${s.shipment_number}`);

            // Marker Tujuan
            L.circleMarker([dLat, dLng], {
                radius: 7,
                color: '#f43f5e',
                fillColor: '#f43f5e',
                fillOpacity: 0.9,
                weight: 2
            }).addTo(map).bindPopup(`<b>Tujuan:</b> ${s.destination.name}<br><b>Kargo:</b> ${s.shipment_number}`);

            // Garis Rute
            L.polyline([[oLat, oLng], [dLat, dLng]], {
                color: '#06b6d4',
                weight: 2.5,
                opacity: 0.8,
                dashArray: '6, 8'
            }).addTo(map);

            // Posisi Terkini jika ada
            if (s.current_location) {
                const cLat = parseFloat(s.current_location.latitude);
                const cLng = parseFloat(s.current_location.longitude);
                if (!isNaN(cLat) && !isNaN(cLng)) {
                    bounds.push([cLat, cLng]);
                    L.circleMarker([cLat, cLng], {
                        radius: 8,
                        color: '#00e5ff',
                        fillColor: '#0284c7',
                        fillOpacity: 1,
                        weight: 3
                    }).addTo(map).bindPopup(`
                        <div class="text-xs">
                            <b class="text-cyan-600">${s.shipment_number}</b><br>
                            <b>Status:</b> ${s.status}<br>
                            <b>Posisi:</b> ${s.current_location.name}
                        </div>
                    `);
                }
            }
        }
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [50, 50] });
    }
});
</script>
@endpush

