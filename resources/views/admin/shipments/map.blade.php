@extends('layouts.admin')

@section('title', 'Peta Pengiriman Global')

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
                    <div class="pt-2 text-[11px] text-slate-400 space-y-1">
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Titik Keberangkatan (Asal)</div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Titik Kedatangan (Tujuan)</div>
                        <div class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span> Posisi Kargo Saat Ini</div>
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

        shipments.forEach(s => {
            if (s.origin && s.destination) {
                const originPt = [s.origin.latitude, s.origin.longitude];
                const destPt = [s.destination.latitude, s.destination.longitude];
                allBounds.push(originPt);
                allBounds.push(destPt);

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

                // Current Location (Cyan Marker)
                let polyPoints = [originPt];
                if (s.current_location) {
                    const curPt = [s.current_location.latitude, s.current_location.longitude];
                    allBounds.push(curPt);
                    polyPoints.push(curPt);

                    L.circleMarker(curPt, {
                        radius: 8,
                        fillColor: '#00A8E8',
                        color: '#fff',
                        weight: 2,
                        fillOpacity: 1
                    }).bindPopup(`<b>Posisi Terkini:</b> ${s.current_location.name}<br><b>No. Resi:</b> <a href="/admin/shipments/${s.id}">${s.shipment_number}</a><br><b>Status:</b> ${s.status}`).addTo(map);
                }
                polyPoints.push(destPt);

                // Polyline Route
                L.polyline(polyPoints, {
                    color: '#00A8E8',
                    weight: 3,
                    dashArray: '6, 8',
                    opacity: 0.8
                }).addTo(map);
            }
        });

        if (allBounds.length > 0) {
            map.fitBounds(L.latLngBounds(allBounds), { padding: [50, 50] });
        }
    });
</script>
@endpush

