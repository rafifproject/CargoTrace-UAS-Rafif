@extends('layouts.customer')

@section('title', 'Pemantauan Cuaca Rute')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pemantauan Cuaca Rute Kargo</h1>
        @if($activeShipment)
            <p class="text-sm text-cyan-600 dark:text-cyan-400 font-medium mt-0.5">
                Rute Aktif: {{ $activeShipment->origin?->city }} ({{ $activeShipment->origin?->country }}) → {{ $activeShipment->destination?->city }} ({{ $activeShipment->destination?->country }}) • Resi: {{ $activeShipment->shipment_number }}
            </p>
        @else
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Kondisi atmosfer dan maritim pada rute pengiriman kargo Anda.
            </p>
        @endif
    </div>
</div>

{{-- Weather Cards: Asal vs Tujuan (Mockup 09) --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Asal --}}
    <div class="bg-gradient-to-br from-cyan-600 to-blue-800 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-cyan-200 font-semibold">Titik Keberangkatan (Asal)</span>
                <h3 class="text-xl font-bold mt-0.5">{{ $originWeather['location_name'] ?? ($activeShipment?->origin?->name ?? 'Pelabuhan Asal') }}</h3>
            </div>
            <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                <i data-lucide="cloud-sun" class="w-8 h-8 text-amber-300"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-4 mb-4">
            <span class="text-5xl font-extrabold tracking-tight">{{ round($originWeather['temperature'] ?? 32) }}°C</span>
            <span class="text-sm text-cyan-100 font-medium">{{ $originWeather['condition'] ?? 'Berawan' }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-white/15 text-xs text-cyan-100">
            <div>
                <span class="text-cyan-200 text-[10px] block">Kelembapan</span>
                <span class="font-bold text-white text-sm">{{ $originWeather['humidity'] ?? 76 }}%</span>
            </div>
            <div>
                <span class="text-cyan-200 text-[10px] block">Kecepatan Angin</span>
                <span class="font-bold text-white text-sm">{{ $originWeather['wind_speed'] ?? 12 }} km/j</span>
            </div>
            <div>
                <span class="text-cyan-200 text-[10px] block">Curah Hujan</span>
                <span class="font-bold text-white text-sm">0 mm</span>
            </div>
        </div>
    </div>

    {{-- Tujuan --}}
    <div class="bg-gradient-to-br from-slate-800 to-indigo-950 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden border border-slate-700">
        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Titik Kedatangan (Tujuan)</span>
                <h3 class="text-xl font-bold mt-0.5">{{ $destWeather['location_name'] ?? ($activeShipment?->destination?->name ?? 'Pelabuhan Tujuan') }}</h3>
            </div>
            <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                <i data-lucide="cloud-rain" class="w-8 h-8 text-cyan-300"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-4 mb-4">
            <span class="text-5xl font-extrabold tracking-tight">{{ round($destWeather['temperature'] ?? 29) }}°C</span>
            <span class="text-sm text-slate-300 font-medium">{{ $destWeather['condition'] ?? 'Hujan Ringan' }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-white/15 text-xs text-slate-300">
            <div>
                <span class="text-slate-400 text-[10px] block">Kelembapan</span>
                <span class="font-bold text-white text-sm">{{ $destWeather['humidity'] ?? 82 }}%</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block">Kecepatan Angin</span>
                <span class="font-bold text-white text-sm">{{ $destWeather['wind_speed'] ?? 14 }} km/j</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block">Curah Hujan</span>
                <span class="font-bold text-white text-sm">1 mm</span>
            </div>
        </div>
    </div>
</div>

{{-- 7-Day Temperature Trend Chart (Mockup 09) --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
        <i data-lucide="trending-up" class="w-4 h-4 text-cyan-500"></i>
        Prakiraan Suhu Rute 7 Hari (°C)
    </h3>
    <canvas id="weatherTrendChart" height="90"></canvas>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const ctx = document.getElementById('weatherTrendChart').getContext('2d');

    const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    const originTemps = [32, 33, 31, 30, 32, 33, 32];
    const destTemps = [29, 28, 29, 30, 29, 28, 29];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: days,
            datasets: [
                {
                    label: 'Pelabuhan Asal',
                    data: originTemps,
                    borderColor: '#06b6d4',
                    backgroundColor: 'rgba(6,182,212,0.1)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                },
                {
                    label: 'Pelabuhan Tujuan',
                    data: destTemps,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99,102,241,0.1)',
                    tension: 0.35,
                    fill: true,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    labels: { color: isDark ? '#cbd5e1' : '#475569' }
                }
            },
            scales: {
                x: {
                    grid: { color: isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                },
                y: {
                    grid: { color: isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' },
                    suggestedMin: 25,
                    suggestedMax: 36
                }
            }
        }
    });
});
</script>
@endpush

