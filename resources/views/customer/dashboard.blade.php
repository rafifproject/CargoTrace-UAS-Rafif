@extends('layouts.customer')

@section('title', 'Beranda Customer')

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
</script>
@endpush

