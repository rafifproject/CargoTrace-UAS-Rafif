@extends('layouts.customer')

@section('title', 'Beranda Customer')

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
{{-- Header Sambutan (Mockup 03) --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Selamat Datang, {{ $user->company_name ?: $user->name }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pantau status dan perkembangan pengiriman kargo Anda di CargoTrace secara real-time.</p>
    </div>
    <a href="{{ route('customer.shipments.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors shadow-sm">
        <i data-lucide="package" class="w-4 h-4"></i>
        Lihat Semua Pengiriman
    </a>
</div>

{{-- KPI Cards (Mockup 03: 4 Kartu) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pengiriman</span>
            <div class="w-8 h-8 rounded-lg bg-cyan-500/10 flex items-center justify-center">
                <i data-lucide="package" class="w-4 h-4 text-cyan-500"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-slate-800 dark:text-white">{{ $totalShipments }}</div>
        <p class="text-[11px] text-slate-400 mt-1">Kargo terdaftar</p>
    </div>

    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Dalam Perjalanan</span>
            <div class="w-8 h-8 rounded-lg bg-blue-500/10 flex items-center justify-center">
                <i data-lucide="truck" class="w-4 h-4 text-blue-500"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-blue-500">{{ $inTransit }}</div>
        <p class="text-[11px] text-slate-400 mt-1">Sedang transit / berlayar</p>
    </div>

    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terkirim</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-emerald-500">{{ $delivered }}</div>
        <p class="text-[11px] text-slate-400 mt-1">Telah sampai di tujuan</p>
    </div>

    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terlambat</span>
            <div class="w-8 h-8 rounded-lg bg-rose-500/10 flex items-center justify-center">
                <i data-lucide="clock-alert" class="w-4 h-4 text-rose-500"></i>
            </div>
        </div>
        <div class="text-3xl font-bold text-rose-500">{{ $delayed }}</div>
        <p class="text-[11px] text-slate-400 mt-1">Melewati jadwal ETA</p>
    </div>
</div>

{{-- Charts Row (Status Donut + 30-Day Trend) --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
    {{-- Status Donut Chart --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Status Pengiriman</h3>
        <div class="flex items-center gap-6">
            <div class="relative w-32 h-32 flex-shrink-0">
                <canvas id="donutChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalShipments }}</span>
                    <span class="text-[10px] text-slate-400">Kargo</span>
                </div>
            </div>
            <div class="space-y-2 text-xs flex-1">
                @php
                    $statusLabels = [
                        'IN_TRANSIT' => ['Dalam Perjalanan', 'bg-blue-500'],
                        'DELIVERED'  => ['Terkirim', 'bg-emerald-500'],
                        'DELAYED'    => ['Terlambat', 'bg-rose-500'],
                        'BOOKED'     => ['Dipesan', 'bg-amber-500'],
                        'PICKED_UP'  => ['Diambil', 'bg-purple-500'],
                    ];
                @endphp
                @foreach($statusCounts as $key => $count)
                    @if(isset($statusLabels[$key]))
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $statusLabels[$key][1] }} flex-shrink-0"></span>
                                <span class="text-slate-500 dark:text-slate-400">{{ $statusLabels[$key][0] }}</span>
                            </div>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $count }}</span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Tren Pengiriman (30 Hari) --}}
    <div class="lg:col-span-3 bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Tren Pengiriman (30 Hari)</h3>
        <canvas id="trendChart" height="85"></canvas>
    </div>
</div>

{{-- Peta Pemantauan Kargo Aktif Saya --}}
<div class="mb-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-cyan-500"></i>
                <span>Peta Pemantauan Kargo Aktif Saya</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan visual posisi kapal, pesawat, dan armada kargo Anda di rute logistik global secara real-time.</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-cyan-50 text-cyan-700 dark:bg-cyan-950/40 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-800">
                <span class="w-2 h-2 rounded-full bg-cyan-500 animate-ping"></span>
                {{ count($mapShipments) }} Kargo Terpantau
            </span>
            <button type="button" onclick="resetDashboardMap()" class="px-3 py-1.5 rounded-lg text-xs font-medium border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-xs flex items-center gap-1">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                <span>Reset View</span>
            </button>
            <a href="{{ route('customer.shipments.map') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-cyan-600 hover:bg-cyan-500 text-white transition-colors shadow-xs flex items-center gap-1">
                <span>Peta Rute Lengkap</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>
    </div>

    <div class="relative">
        <div id="customerDashboardMap" class="w-full h-[450px] z-10"></div>

        <!-- Floating Legend Badge -->
        <div class="absolute bottom-4 left-4 z-20 px-3.5 py-2 rounded-xl bg-slate-900/90 backdrop-blur-md border border-slate-700/80 text-white shadow-xl flex items-center gap-4 text-xs">
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                <span class="text-slate-300 text-[11px]">Asal</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                <span class="text-slate-300 text-[11px]">Tujuan</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-[12px]">🚢</span>
                <span class="text-slate-300 text-[11px]">Kapal</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-[12px]">✈️</span>
                <span class="text-slate-300 text-[11px]">Pesawat</span>
            </div>
            <div class="flex items-center gap-1.5">
                <span class="text-[12px]">🚚</span>
                <span class="text-slate-300 text-[11px]">Darat</span>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Row: Recent Shipments + Active Exception Alerts --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Recent Shipments Table --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Pengiriman Kargo Terbaru</h3>
            <a href="{{ route('customer.shipments.index') }}" class="text-xs text-cyan-500 hover:text-cyan-400 font-medium">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 dark:bg-slate-900/50">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-slate-500 dark:text-slate-400">No. Resi</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Rute</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Status</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">ETA</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentShipments as $shipment)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/40 transition-colors">
                        <td class="px-5 py-3">
                            <span class="font-mono font-semibold text-slate-700 dark:text-slate-200">{{ $shipment->shipment_number }}</span>
                            <p class="text-slate-400 text-[11px] mt-0.5">{{ $shipment->cargo_type }}</p>
                        </td>
                        <td class="px-3 py-3">
                            <span class="text-slate-700 dark:text-slate-300">{{ $shipment->origin?->city }}</span>
                            <span class="text-slate-400">→</span>
                            <span class="text-slate-700 dark:text-slate-300">{{ $shipment->destination?->city }}</span>
                        </td>
                        <td class="px-3 py-3">
                            @php
                                $statusMap = ['CREATED' => ['Dibuat','slate'], 'BOOKED' => ['Dipesan','amber'], 'PICKED_UP' => ['Diambil','purple'], 'IN_TRANSIT' => ['Dalam Perjalanan','blue'], 'DELIVERED' => ['Terkirim','emerald']];
                                [$label, $color] = $statusMap[$shipment->status] ?? [$shipment->status, 'slate'];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/40 dark:text-{{ $color }}-300">{{ $label }}</span>
                        </td>
                        <td class="px-3 py-3 text-slate-500 dark:text-slate-400">
                            {{ $shipment->estimated_time_arrival ? \Carbon\Carbon::parse($shipment->estimated_time_arrival)->format('d M Y') : '-' }}
                        </td>
                        <td class="px-3 py-3">
                            <a href="{{ route('customer.shipments.show', $shipment->id) }}" class="text-cyan-500 hover:text-cyan-400 font-semibold">Lacak Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Belum ada data pengiriman aktif.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Active Exceptions Panel --}}
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Status Kendala</h3>
            <a href="{{ route('customer.exceptions.index') }}" class="text-xs text-amber-500 hover:text-amber-400 font-medium">Lihat Semua →</a>
        </div>
        <div class="p-4 space-y-3">
            @forelse($activeExceptions as $ex)
            <div class="p-3 rounded-xl border {{ $ex->severity === 'CRITICAL' ? 'bg-rose-50 border-rose-200 dark:bg-rose-950/30 dark:border-rose-800/50' : 'bg-amber-50 border-amber-200 dark:bg-amber-950/30 dark:border-amber-800/50' }}">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <span class="text-xs font-semibold {{ $ex->severity === 'CRITICAL' ? 'text-rose-700 dark:text-rose-300' : 'text-amber-700 dark:text-amber-300' }}">{{ $ex->exception_type }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded font-bold {{ $ex->severity === 'CRITICAL' ? 'bg-rose-200 text-rose-800 dark:bg-rose-900 dark:text-rose-200' : 'bg-amber-200 text-amber-800 dark:bg-amber-900 dark:text-amber-200' }}">{{ $ex->severity }}</span>
                </div>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ $ex->shipment?->shipment_number }}</p>
                <p class="text-[11px] text-slate-600 dark:text-slate-400 mt-1 line-clamp-2">{{ $ex->description }}</p>
            </div>
            @empty
            <div class="py-8 text-center text-sm text-slate-400">
                <i data-lucide="shield-check" class="w-8 h-8 mx-auto mb-2 text-emerald-400 opacity-60"></i>
                <p>Tidak ada kendala pada kargo Anda.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const isDark = document.documentElement.classList.contains('dark');
const gridColor = isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)';
const textColor = isDark ? '#94a3b8' : '#64748b';

// Donut Chart
const donutCtx = document.getElementById('donutChart').getContext('2d');
new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: ['Dalam Perjalanan', 'Terkirim', 'Terlambat', 'Dipesan', 'Diambil'],
        datasets: [{
            data: [
                {{ $statusCounts['IN_TRANSIT'] }},
                {{ $statusCounts['DELIVERED'] }},
                {{ $statusCounts['DELAYED'] }},
                {{ $statusCounts['BOOKED'] }},
                {{ $statusCounts['PICKED_UP'] }}
            ],
            backgroundColor: ['#3b82f6', '#10b981', '#f43f5e', '#f59e0b', '#a855f7'],
            borderWidth: 0,
            hoverOffset: 4,
        }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { display: false } },
    }
});

// Trend Line Chart
const trendCtx = document.getElementById('trendChart').getContext('2d');
new Chart(trendCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($trendDates) !!},
        datasets: [{
            label: 'Total Kargo',
            data: {!! json_encode($trendValues) !!},
            borderColor: '#06b6d4',
            backgroundColor: 'rgba(6,182,212,0.08)',
            borderWidth: 2,
            pointBackgroundColor: '#06b6d4',
            pointRadius: 3,
            tension: 0.35,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 } } },
            y: { grid: { color: gridColor }, ticks: { color: textColor, font: { size: 10 }, stepSize: 1 }, beginAtZero: true },
        }
    }
});

// ==========================================
// PETA PEMANTAUAN KARGO AKTIF CUSTOMER (LEAFLET)
// ==========================================
let customerDashboardMap = null;
let dashboardBounds = [];

const dashboardShipments = @json($mapShipments);

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

function createCurvedPath(start, end, numPoints = 20, bendFactor = 0.12) {
    const pts = [];
    const lat1 = start[0], lon1 = start[1];
    const lat2 = end[0], lon2 = end[1];
    const midLat = (lat1 + lat2) / 2;
    const midLon = (lon1 + lon2) / 2;
    const dLon = lon2 - lon1;
    const dLat = lat2 - lat1;
    const dist = Math.sqrt(dLat * dLat + dLon * dLon);
    const ctrlLat = midLat + (-dLon / (dist || 1)) * dist * bendFactor;
    const ctrlLon = midLon + (dLat / (dist || 1)) * dist * bendFactor;

    for (let i = 0; i <= numPoints; i++) {
        const t = i / numPoints;
        const lat = (1 - t) * (1 - t) * lat1 + 2 * (1 - t) * t * ctrlLat + t * t * lat2;
        const lon = (1 - t) * (1 - t) * lon1 + 2 * (1 - t) * t * ctrlLon + t * t * lon2;
        pts.push([lat, lon]);
    }
    return pts;
}

function calculateBearing(start, end) {
    const lat1 = start[0] * Math.PI / 180;
    const lon1 = start[1] * Math.PI / 180;
    const lat2 = end[0] * Math.PI / 180;
    const lon2 = end[1] * Math.PI / 180;
    const y = Math.sin(lon2 - lon1) * Math.cos(lat2);
    const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(lat2) * Math.cos(lon2 - lon1);
    const brng = Math.atan2(y, x) * 180 / Math.PI;
    return (brng + 360) % 360;
}

function getVehicleConfig(s) {
    const mode = (s.transport_mode || 'SEA').toUpperCase();
    if (mode === 'AIR') {
        return {
            type: 'AIR', label: 'Pesawat Kargo', color: '#38BDF8',
            realSvg: `<svg width="38" height="38" viewBox="0 0 64 64" fill="none"><path d="M32 6L36 24L58 34V40L36 32V48L42 54V58L32 54L22 58V54L28 48V32L6 40V34L28 24L32 6Z" fill="#0284c7" stroke="#ffffff" stroke-width="2" stroke-linejoin="round"/></svg>`
        };
    }
    if (mode === 'LAND' || mode === 'ROAD') {
        return {
            type: 'LAND', label: 'Mobil / Truk Kargo', color: '#F59E0B',
            realSvg: `<svg width="38" height="38" viewBox="0 0 64 64" fill="none"><rect x="8" y="20" width="36" height="26" rx="3" fill="#0284c7" stroke="#ffffff" stroke-width="2"/><path d="M44 26L54 32V46H44V26Z" fill="#38bdf8" stroke="#ffffff" stroke-width="2"/><circle cx="18" cy="46" r="5" fill="#1e293b" stroke="#ffffff" stroke-width="2"/><circle cx="44" cy="46" r="5" fill="#1e293b" stroke="#ffffff" stroke-width="2"/></svg>`
        };
    }
    return {
        type: 'SEA', label: 'Kapal Kontainer', color: '#00D2FF',
        realSvg: `<svg width="40" height="40" viewBox="0 0 64 64" fill="none"><path d="M6 38L14 50H50L58 38L54 28H10L6 38Z" fill="#0284c7" stroke="#ffffff" stroke-width="2.5" stroke-linejoin="round"/><rect x="18" y="20" width="8" height="8" rx="1.5" fill="#f59e0b" stroke="#ffffff" stroke-width="1.5"/><rect x="28" y="20" width="8" height="8" rx="1.5" fill="#ef4444" stroke="#ffffff" stroke-width="1.5"/><rect x="38" y="20" width="8" height="8" rx="1.5" fill="#10b981" stroke="#ffffff" stroke-width="1.5"/></svg>`
    };
}

function initCustomerDashboardMap() {
    const mapEl = document.getElementById('customerDashboardMap');
    if (!mapEl) return;

    customerDashboardMap = L.map('customerDashboardMap', {
        center: [15, 100],
        zoom: 3,
        minZoom: 2,
        maxZoom: 18
    });

    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps Satelit'
    });

    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
    });

    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    });

    googleHybrid.addTo(customerDashboardMap);

    L.control.layers({
        "🛰️ Satelit Hybrid": googleHybrid,
        "🗺️ Peta Jalan": googleStreets,
        "🌐 OpenStreetMap": osm
    }, null, { position: 'topright' }).addTo(customerDashboardMap);

    dashboardBounds = [];

    dashboardShipments.forEach(s => {
        if (s.origin && s.destination) {
            const oLat = parseFloat(s.origin.latitude);
            const oLng = parseFloat(s.origin.longitude);
            const dLat = parseFloat(s.destination.latitude);
            const dLng = parseFloat(s.destination.longitude);

            if (!isNaN(oLat) && !isNaN(oLng) && !isNaN(dLat) && !isNaN(dLng)) {
                const originPt = [oLat, oLng];
                const destPt = [dLat, dLng];
                dashboardBounds.push(originPt, destPt);

                const progress = getShipmentRealtimeProgress(s);
                let curPt = null;

                if (s.current_location && s.current_location.latitude && s.current_location.longitude) {
                    const cLat = parseFloat(s.current_location.latitude);
                    const cLng = parseFloat(s.current_location.longitude);
                    if (!isNaN(cLat) && !isNaN(cLng)) {
                        curPt = [cLat, cLng];
                    }
                }

                if (!curPt) {
                    const numPoints = 30;
                    const path = createCurvedPath(originPt, destPt, numPoints, 0.12);
                    const idx = Math.min(numPoints, Math.max(0, Math.round(progress * numPoints)));
                    curPt = path[idx] || originPt;
                }

                dashboardBounds.push(curPt);

                const vehicle = getVehicleConfig(s);
                const pct = Math.round(progress * 100);
                const isAir = (vehicle.type === 'AIR');

                const popupHtml = `
                    <div style="font-family:'Outfit',sans-serif; min-width:240px; padding:2px;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px; border-bottom:1px solid #e2e8f0; padding-bottom:5px;">
                            <span style="font-weight:900; font-size:12px; color:#0284c7;">${s.shipment_number}</span>
                            <span style="font-weight:800; font-size:10px; color:#15803d; background:#dcfce7; padding:2px 6px; border-radius:4px;">${s.status}</span>
                        </div>
                        <div style="background:#F0FDF4; border:1px solid #BBF7D0; padding:5px 7px; border-radius:6px; margin-bottom:6px;">
                            <div style="display:flex; justify-content:space-between; align-items:center; font-weight:800; font-size:10px; color:#15803d; margin-bottom:2px;">
                                <span>${isAir ? '✈️ Penerbangan:' : '🚢 Pelayaran:'}</span>
                                <span>${pct}%</span>
                            </div>
                            <div style="width:100%; height:4px; background:#dcfce7; border-radius:999px; overflow:hidden;">
                                <div style="width:${pct}%; height:100%; background:linear-gradient(90deg, #10b981, #06b6d4); border-radius:999px;"></div>
                            </div>
                        </div>
                        <div style="font-size:11px; color:#334155; line-height:1.45; margin-bottom:6px;">
                            <div>🛫 <b>Asal:</b> ${s.origin?.city || s.origin?.name}</div>
                            <div>🛬 <b>Tujuan:</b> ${s.destination?.city || s.destination?.name}</div>
                            <div>🏢 <b>Ekspedisi:</b> ${s.carrier?.name || '-'}</div>
                            <div>📦 <b>Kargo:</b> ${s.cargo_type}</div>
                        </div>
                        <a href="/customer/shipments/${s.id}" style="display:block; text-align:center; background:#0284c7; color:#fff; font-weight:700; padding:5px; border-radius:6px; text-decoration:none; font-size:11px;">
                            Buka Detail Kargo →
                        </a>
                    </div>
                `;

                // Origin Marker
                L.circleMarker(originPt, {
                    radius: 6, fillColor: '#10B981', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(customerDashboardMap);

                // Dest Marker
                L.circleMarker(destPt, {
                    radius: 6, fillColor: '#EF4444', color: '#fff', weight: 2, fillOpacity: 1
                }).bindPopup(popupHtml).addTo(customerDashboardMap);

                // Traveled Path
                const traveledPath = createCurvedPath(originPt, curPt, 16, 0.1);
                L.polyline(traveledPath, {
                    color: '#0284c7', weight: 3, opacity: 0.9
                }).bindPopup(popupHtml).addTo(customerDashboardMap);

                // Remaining Path
                const remainingPath = createCurvedPath(curPt, destPt, 16, 0.1);
                L.polyline(remainingPath, {
                    color: '#38bdf8', weight: 2, dashArray: '5, 5', opacity: 0.8
                }).bindPopup(popupHtml).addTo(customerDashboardMap);

                // Vehicle Marker with rotation
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
                        iconSize: [40, 40],
                        iconAnchor: [20, 20]
                    })
                }).bindPopup(popupHtml).addTo(customerDashboardMap);
            }
        }
    });

    if (dashboardBounds.length > 0) {
        customerDashboardMap.fitBounds(dashboardBounds, { padding: [40, 40], maxZoom: 6 });
    }

    setTimeout(() => {
        if (customerDashboardMap) customerDashboardMap.invalidateSize();
    }, 250);

    window.addEventListener('resize', () => {
        if (customerDashboardMap) customerDashboardMap.invalidateSize();
    });
}

function resetDashboardMap() {
    if (!customerDashboardMap) return;
    if (dashboardBounds.length > 0) {
        customerDashboardMap.fitBounds(dashboardBounds, { padding: [40, 40], maxZoom: 6 });
    } else {
        customerDashboardMap.flyTo([15, 100], 3, { duration: 1 });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    initCustomerDashboardMap();
});
</script>
@endpush

