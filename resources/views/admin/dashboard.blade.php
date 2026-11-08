@extends('layouts.admin')

@section('title', 'Dasbor Administrator')

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

    <!-- Peta Pengiriman Global Preview -->
    <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100 dark:border-slate-800/80 gap-2">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="globe" class="w-4 h-4 text-cyan-500"></i>
                    <span>Peta Pemantauan Rute Pengiriman Global</span>
                </h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Posisi kargo aktif berdasarkan data riwayat checkpoints pelabuhan & bandara internasional</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Pelabuhan Asal</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Pelabuhan Tujuan</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span> Kargo Bergerak</span>
            </div>
        </div>
        <div id="dashboard-map" class="w-full h-80 rounded-xl mt-4 border border-slate-200 dark:border-slate-800 z-10"></div>
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

        // 3. Leaflet Global Map
        const map = L.map('dashboard-map', {
            center: [10, 110],
            zoom: 3,
            minZoom: 2,
            maxZoom: 10
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap kontributor'
        }).addTo(map);

        // Map Routes Data from Controller
        const shipments = {!! json_encode($mapShipments) !!};
        const latLngs = [];

        shipments.forEach(s => {
            if (s.origin && s.destination) {
                const originCoords = [s.origin.latitude, s.origin.longitude];
                const destCoords = [s.destination.latitude, s.destination.longitude];
                latLngs.push(originCoords);
                latLngs.push(destCoords);

                // Origin Marker (Green)
                L.circleMarker(originCoords, {
                    radius: 6,
                    fillColor: '#10B981',
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 1
                }).bindPopup(`<b>Asal:</b> ${s.origin.name} (${s.origin.city})<br><b>No. Resi:</b> ${s.shipment_number}`).addTo(map);

                // Destination Marker (Red)
                L.circleMarker(destCoords, {
                    radius: 6,
                    fillColor: '#EF4444',
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 1
                }).bindPopup(`<b>Tujuan:</b> ${s.destination.name} (${s.destination.city})<br><b>No. Resi:</b> ${s.shipment_number}`).addTo(map);

                // Current Location (Cyan Marker if in transit)
                if (s.current_location) {
                    const currentCoords = [s.current_location.latitude, s.current_location.longitude];
                    L.circleMarker(currentCoords, {
                        radius: 7,
                        fillColor: '#00A8E8',
                        color: '#fff',
                        weight: 2,
                        fillOpacity: 1
                    }).bindPopup(`<b>Posisi Terkini:</b> ${s.current_location.name}<br><b>Status:</b> ${s.status}`).addTo(map);
                }

                // Polyline Route
                L.polyline([originCoords, destCoords], {
                    color: '#00A8E8',
                    weight: 2,
                    dashArray: '5, 8',
                    opacity: 0.7
                }).addTo(map);
            }
        });

        if (latLngs.length > 0) {
            map.fitBounds(L.latLngBounds(latLngs), { padding: [40, 40] });
        }
    });
</script>
@endpush

