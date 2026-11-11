@extends('layouts.operator')

@section('title', 'Pemantauan Cuaca')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pemantauan Cuaca Rute Aktif</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Analisis kondisi atmosfer pelabuhan asal dan tujuan untuk mengantisipasi keterlambatan rute kargo.</p>
    </div>
</div>

{{-- Weather Cards for Origin & Destination (Mockup 09) --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    {{-- Lokasi Asal --}}
    <div class="bg-gradient-to-br from-cyan-600 to-blue-800 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden">
        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-cyan-200 font-semibold">Titik Keberangkatan (Asal)</span>
                <h3 class="text-xl font-bold mt-0.5">{{ $originWeather['location_name'] ?? 'Surabaya Port (ID)' }}</h3>
            </div>
            <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                <i data-lucide="cloud-sun" class="w-8 h-8 text-amber-300"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-4 mb-4">
            <span class="text-5xl font-extrabold tracking-tight">{{ round($originWeather['temperature'] ?? 31) }}°C</span>
            <span class="text-sm text-cyan-100 font-medium">{{ $originWeather['condition'] ?? 'Cerah Berawan' }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-white/15 text-xs text-cyan-100">
            <div>
                <span class="text-cyan-200 text-[10px] block">Kecepatan Angin</span>
                <span class="font-bold text-white text-sm">{{ $originWeather['wind_speed'] ?? 14 }} km/j</span>
            </div>
            <div>
                <span class="text-cyan-200 text-[10px] block">Kelembapan</span>
                <span class="font-bold text-white text-sm">{{ $originWeather['humidity'] ?? 78 }}%</span>
            </div>
            <div>
                <span class="text-cyan-200 text-[10px] block">Risiko Maritim</span>
                <span class="font-bold text-emerald-300 text-sm">Rendah</span>
            </div>
        </div>
    </div>

    {{-- Lokasi Tujuan --}}
    <div class="bg-gradient-to-br from-slate-800 to-indigo-950 text-white rounded-2xl p-6 shadow-lg relative overflow-hidden border border-slate-700">
        <div class="absolute -right-4 -bottom-4 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl"></div>
        <div class="flex items-center justify-between mb-4">
            <div>
                <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Titik Tujuan</span>
                <h3 class="text-xl font-bold mt-0.5">{{ $destWeather['location_name'] ?? 'Singapore Port (SG)' }}</h3>
            </div>
            <div class="p-3 bg-white/10 rounded-xl backdrop-blur-sm">
                <i data-lucide="cloud-rain" class="w-8 h-8 text-cyan-300"></i>
            </div>
        </div>
        <div class="flex items-baseline gap-4 mb-4">
            <span class="text-5xl font-extrabold tracking-tight">{{ round($destWeather['temperature'] ?? 28) }}°C</span>
            <span class="text-sm text-slate-300 font-medium">{{ $destWeather['condition'] ?? 'Hujan Ringan' }}</span>
        </div>
        <div class="grid grid-cols-3 gap-2 pt-4 border-t border-white/15 text-xs text-slate-300">
            <div>
                <span class="text-slate-400 text-[10px] block">Kecepatan Angin</span>
                <span class="font-bold text-white text-sm">{{ $destWeather['wind_speed'] ?? 22 }} km/j</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block">Kelembapan</span>
                <span class="font-bold text-white text-sm">{{ $destWeather['humidity'] ?? 86 }}%</span>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] block">Risiko Maritim</span>
                <span class="font-bold text-amber-300 text-sm">Sedang</span>
            </div>
        </div>
    </div>
</div>

{{-- 7-Day Forecast Trend Chart (Mockup 09) --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm mb-6">
    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
        <i data-lucide="trending-up" class="w-4 h-4 text-cyan-500"></i>
        Prakiraan Suhu 7 Hari ke Depan (°C)
    </h3>
    <canvas id="weatherTrendChart" height="90"></canvas>
</div>

@if(isset($selectedHub))
{{-- Target Country Profile Highlight Card --}}
<div class="p-6 rounded-2xl bg-gradient-to-r from-purple-900/20 to-indigo-900/20 dark:from-purple-950/40 dark:to-indigo-950/40 border-2 border-purple-400/30 dark:border-purple-600/30 shadow-md mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-purple-200/20">
        <div class="flex items-center gap-3.5">
            <img src="https://flagcdn.com/w40/{{ $selectedHub['iso'] }}.png" alt="{{ $selectedHub['country'] }}"
                 class="w-11 h-7.5 object-cover rounded shadow-md border border-white/20">
            <div>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/50 px-2 py-0.5 rounded">
                        Stasiun Cuaca Terpilih
                    </span>
                    <span class="text-xs text-slate-400 font-mono">{{ $selectedHub['region'] }}</span>
                </div>
                <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                    {{ $selectedHub['country'] }} &mdash; {{ $selectedHub['city'] }}
                </h2>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20 flex items-center justify-center font-bold text-xl">
                {{ $selectedHub['icon_emoji'] ?? '☀️' }}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-4 pt-2">
        <div class="p-3 rounded-xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/50 dark:border-slate-800">
            <span class="text-[10px] text-slate-400 font-medium block">Suhu Udara Terkini</span>
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $selectedHub['temp'] }}°C</span>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">{{ $selectedHub['condition'] }}</span>
        </div>
        <div class="p-3 rounded-xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/50 dark:border-slate-800">
            <span class="text-[10px] text-slate-400 font-medium block">Kelembaban Relatif</span>
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $selectedHub['humidity'] }}%</span>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Kondisi Udara Laut</span>
        </div>
        <div class="p-3 rounded-xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/50 dark:border-slate-800">
            <span class="text-[10px] text-slate-400 font-medium block">Kecepatan Angin</span>
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $selectedHub['wind'] }} km/j</span>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Arah Angin Pelabuhan</span>
        </div>
        <div class="p-3 rounded-xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/50 dark:border-slate-800">
            <span class="text-[10px] text-slate-400 font-medium block">Curah Hujan / Presipitasi</span>
            <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $selectedHub['precipitation'] }} mm</span>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Presipitasi</span>
        </div>
    </div>
</div>
@endif

@if(isset($allWeatherHubs))
{{-- Global Weather Hubs Directory (205 Countries) --}}
<div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-3">
        <div>
            <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="cloud" class="w-4 h-4 text-cyan-500"></i>
                <span>Direktori Stasiun Cuaca Maritim & Terestrial Seluruh Dunia ({{ count($allWeatherHubs) }} Negara)</span>
            </h2>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                Data realtime meteorologi pelabuhan dan koridor samudra di 195+ negara dan wilayah maritim global
            </p>
        </div>
        <div class="relative w-full sm:w-64">
            <input type="text" id="weatherHubSearch" oninput="filterWeatherHubs()"
                   placeholder="Cari negara / wilayah..."
                   class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
        </div>
    </div>

    <div id="weatherHubGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3.5 mt-4">
        @foreach($allWeatherHubs as $hub)
        @php
            $isHubSelected = isset($selectedHub) && ($selectedHub['country'] === $hub['country'] || $selectedHub['code'] === $hub['code']);
            $hubAliases = strtolower($hub['country'] . ' ' . $hub['city'] . ' ' . $hub['code'] . ' ' . ($hub['iso'] ?? ''));
            if (str_starts_with(strtoupper($hub['code']), 'RU')) {
                $hubAliases .= ' rusia russia moscow moskow siberia vladivostok yakutsk novisibirsk';
            } elseif ($hub['code'] === 'US') {
                $hubAliases .= ' amerika serikat united states usa america';
            } elseif ($hub['code'] === 'GB') {
                $hubAliases .= ' inggris united kingdom uk britain';
            } elseif ($hub['code'] === 'DE') {
                $hubAliases .= ' jerman germany';
            } elseif ($hub['code'] === 'FR') {
                $hubAliases .= ' prancis france';
            } elseif ($hub['code'] === 'NL') {
                $hubAliases .= ' belanda netherlands holland';
            } elseif ($hub['code'] === 'JP') {
                $hubAliases .= ' jepang japan';
            } elseif ($hub['code'] === 'CN') {
                $hubAliases .= ' tiongkok china cina';
            }
        @endphp
        <div class="weather-hub-card p-3.5 rounded-xl bg-slate-50 dark:bg-slate-900/60 border {{ $isHubSelected ? 'border-purple-500 ring-2 ring-purple-500/20' : 'border-slate-200 dark:border-slate-800' }} hover:border-cyan-500/50 transition-all flex flex-col justify-between"
             data-country="{{ strtolower($hub['country']) }}"
             data-city="{{ strtolower($hub['city']) }}"
             data-code="{{ strtolower($hub['code']) }}"
             data-aliases="{{ $hubAliases }}">
            <div>
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <img src="https://flagcdn.com/w40/{{ $hub['iso'] }}.png" alt="{{ $hub['country'] }}"
                             class="w-6 h-4 object-cover rounded shadow-2xs border border-slate-200 dark:border-slate-700">
                        <span class="text-xs font-bold text-slate-900 dark:text-white truncate">{{ $hub['country'] }}</span>
                    </div>
                    <span class="text-sm">{{ $hub['icon_emoji'] ?? '☀️' }}</span>
                </div>
                <p class="text-[11px] text-slate-400 truncate">{{ $hub['city'] }}</p>
            </div>

            <div class="mt-3 pt-2.5 border-t border-slate-200/60 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-base font-black text-slate-900 dark:text-white">{{ $hub['temp'] }}°C</span>
                    <span class="text-[10px] text-slate-400 ml-1">{{ $hub['condition'] }}</span>
                </div>
                <a href="?country={{ urlencode($hub['country']) }}"
                   class="text-[11px] font-bold text-cyan-600 dark:text-cyan-400 hover:underline">
                    Lihat &rarr;
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const ctx = document.getElementById('weatherTrendChart').getContext('2d');

    const days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    const originTemps = [31, 32, 30, 29, 31, 33, 32];
    const destTemps = [28, 27, 28, 29, 28, 27, 28];

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
                    suggestedMin: 24,
                    suggestedMax: 36
                }
            }
        }
    });
});

function filterWeatherHubs() {
    const query = document.getElementById('weatherHubSearch').value.toLowerCase().trim();
    document.querySelectorAll('.weather-hub-card').forEach(card => {
        const country = card.dataset.country || '';
        const city = card.dataset.city || '';
        const aliases = card.dataset.aliases || '';
        const code = card.dataset.code || '';
        const match = country.includes(query) || city.includes(query) || aliases.includes(query) || code.includes(query);
        card.style.display = match ? '' : 'none';
    });
}
</script>
@endpush

