@extends('layouts.admin')

@section('title', 'Monitoring Cuaca Global')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Monitoring Cuaca (Weather Monitoring)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Integrasi data meteorologi Open-Meteo & stasiun cuaca maritim global untuk evaluasi risiko pengiriman</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                Pembaruan: {{ $originWeather['fetched_at'] ?? now()->format('H:i') }}
            </span>
            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 transition-colors shadow-xs flex items-center gap-1.5">
                <i data-lucide="map" class="w-3.5 h-3.5"></i>
                <span>Kembali ke Peta</span>
            </a>
        </div>
    </div>

    @if(isset($selectedHub))
    <!-- Target Country Profile Highlight Card (From Map "View Full Profile" Click) -->
    <div class="p-6 rounded-2xl bg-gradient-to-r from-purple-900/20 to-indigo-900/20 dark:from-purple-950/40 dark:to-indigo-950/40 border-2 border-purple-400/30 dark:border-purple-600/30 shadow-md">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-purple-200/20">
            <div class="flex items-center gap-3.5">
                <img src="https://flagcdn.com/w40/{{ $selectedHub['iso'] }}.png" alt="{{ $selectedHub['country'] }}"
                     class="w-11 h-7.5 object-cover rounded shadow-md border border-white/20">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900/50 px-2 py-0.5 rounded">
                            Profil Terpilih dari Peta
                        </span>
                        <span class="text-xs text-slate-400 font-mono">{{ $selectedHub['region'] }}</span>
                    </div>
                    <h2 class="text-2xl font-black text-slate-900 dark:text-white mt-0.5">
                        {{ $selectedHub['country'] }} &mdash; {{ $selectedHub['city'] }}
                    </h2>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block">Indeks Risiko Rantai Pasok:</span>
                    <span class="text-lg font-black {{ ($selectedHub['risk_score'] ?? 30) >= 65 ? 'text-rose-500' : (($selectedHub['risk_score'] ?? 30) >= 30 ? 'text-amber-500' : 'text-emerald-500') }}">
                        {{ $selectedHub['risk'] ?? 'Medium Risk' }} ({{ $selectedHub['risk_score'] ?? 31 }}/100)
                    </span>
                </div>
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
                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Arah Angin Lepas Pantai</span>
            </div>
            <div class="p-3 rounded-xl bg-white/70 dark:bg-slate-900/70 border border-slate-200/50 dark:border-slate-800">
                <span class="text-[10px] text-slate-400 font-medium block">Curah Hujan / Presipitasi</span>
                <span class="text-2xl font-black text-slate-900 dark:text-white">{{ $selectedHub['precipitation'] }} mm</span>
                <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Kondisi Presipitasi</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Active Route Weather Comparison Form (Titik Asal 1 vs Titik Tujuan 2) -->
    <form id="weather-comparison-form" method="GET" action="{{ route('admin.intelligence.weather') }}" class="space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-1">
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300">
                    Dual-Point Weather Comparison
                </span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Pilih titik asal dan titik tujuan kargo dari dropdown di bawah untuk membandingkan cuaca maritim secara realtime</span>
            </div>
            <div class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                Rute: <strong class="text-cyan-600 dark:text-cyan-400">{{ $originHub['city'] ?? 'Surabaya' }}</strong> ➔ <strong class="text-rose-600 dark:text-rose-400">{{ $destHub['city'] ?? 'Sydney' }}</strong>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Origin Weather Card (Titik Asal 1) -->
            <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border-2 border-cyan-500/30 dark:border-cyan-500/40 shadow-sm relative overflow-hidden">
                <div class="flex flex-col gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-cyan-500 uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-3 h-3"></i> Titik 1: Asal Kargo
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center">
                            @if(str_contains(strtolower($originHub['condition'] ?? ''), 'rain') || str_contains(strtolower($originHub['condition'] ?? ''), 'hujan'))
                                <i data-lucide="cloud-rain" class="w-6 h-6"></i>
                            @elseif(str_contains(strtolower($originHub['condition'] ?? ''), 'cloud') || str_contains(strtolower($originHub['condition'] ?? ''), 'awan'))
                                <i data-lucide="cloud-sun" class="w-6 h-6"></i>
                            @else
                                <i data-lucide="sun" class="w-6 h-6"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Dropdown Pemilihan Titik 1 -->
                    <div>
                        <label for="origin_select" class="block text-[10px] font-semibold text-slate-400 mb-1">Pilih Titik Asal:</label>
                        <select id="origin_select" name="origin" onchange="this.form.submit()"
                                class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-cyan-500 cursor-pointer">
                            @foreach($allWeatherHubs as $hub)
                                <option value="{{ $hub['code'] }}" {{ ($originHub['code'] ?? '') == $hub['code'] ? 'selected' : '' }}>
                                    {{ $hub['country'] }} &mdash; {{ $hub['city'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-1">
                        <h2 class="text-base font-black text-slate-900 dark:text-white">{{ $originHub['city'] }}, {{ $originHub['country'] }}</h2>
                        <p class="text-[11px] text-slate-400 font-mono">Koordinat: {{ number_format($originLat, 3) }}, {{ number_format($originLng, 3) }}</p>
                    </div>
                </div>

                <div class="my-5 flex items-baseline gap-4">
                    <span class="text-5xl font-black text-slate-900 dark:text-white">{{ $originWeather['current']['temperature_2m'] ?? $originHub['temp'] }}°C</span>
                    <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $originHub['condition'] ?? 'Cerah Berawan' }}</span>
                </div>

                <div class="grid grid-cols-3 gap-3 text-xs pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Kelembaban</span>
                        <strong class="text-slate-800 dark:text-white">{{ $originWeather['current']['relative_humidity_2m'] ?? $originHub['humidity'] }}%</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Kecepatan Angin</span>
                        <strong class="text-slate-800 dark:text-white">{{ $originWeather['current']['wind_speed_10m'] ?? $originHub['wind'] }} km/j</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Risiko Maritim</span>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ str_contains(strtolower($originHub['risk'] ?? ''), 'high') ? 'bg-rose-500/10 text-rose-500' : (str_contains(strtolower($originHub['risk'] ?? ''), 'medium') || str_contains(strtolower($originHub['risk'] ?? ''), 'waspada') ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500') }}">
                            {{ $originHub['risk'] ?? 'Rendah' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Destination Weather Card (Titik Tujuan 2) -->
            <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border-2 border-rose-500/30 dark:border-rose-500/40 shadow-sm relative overflow-hidden">
                <div class="flex flex-col gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="navigation" class="w-3 h-3"></i> Titik 2: Tujuan Kargo
                        </span>
                        <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center">
                            @if(str_contains(strtolower($destHub['condition'] ?? ''), 'rain') || str_contains(strtolower($destHub['condition'] ?? ''), 'hujan'))
                                <i data-lucide="cloud-rain" class="w-6 h-6"></i>
                            @elseif(str_contains(strtolower($destHub['condition'] ?? ''), 'cloud') || str_contains(strtolower($destHub['condition'] ?? ''), 'awan'))
                                <i data-lucide="cloud-sun" class="w-6 h-6"></i>
                            @else
                                <i data-lucide="sun" class="w-6 h-6"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Dropdown Pemilihan Titik 2 -->
                    <div>
                        <label for="dest_select" class="block text-[10px] font-semibold text-slate-400 mb-1">Pilih Titik Tujuan:</label>
                        <select id="dest_select" name="dest" onchange="this.form.submit()"
                                class="w-full px-3 py-2 text-xs font-bold rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/60 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-rose-500 cursor-pointer">
                            @foreach($allWeatherHubs as $hub)
                                <option value="{{ $hub['code'] }}" {{ ($destHub['code'] ?? '') == $hub['code'] ? 'selected' : '' }}>
                                    {{ $hub['country'] }} &mdash; {{ $hub['city'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-1">
                        <h2 class="text-base font-black text-slate-900 dark:text-white">{{ $destHub['city'] }}, {{ $destHub['country'] }}</h2>
                        <p class="text-[11px] text-slate-400 font-mono">Koordinat: {{ number_format($destLat, 3) }}, {{ number_format($destLng, 3) }}</p>
                    </div>
                </div>

                <div class="my-5 flex items-baseline gap-4">
                    <span class="text-5xl font-black text-slate-900 dark:text-white">{{ $destWeather['current']['temperature_2m'] ?? $destHub['temp'] }}°C</span>
                    <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $destHub['condition'] ?? 'Hujan Ringan & Berangin' }}</span>
                </div>

                <div class="grid grid-cols-3 gap-3 text-xs pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Kelembaban</span>
                        <strong class="text-slate-800 dark:text-white">{{ $destWeather['current']['relative_humidity_2m'] ?? $destHub['humidity'] }}%</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Kecepatan Angin</span>
                        <strong class="text-slate-800 dark:text-white">{{ $destWeather['current']['wind_speed_10m'] ?? $destHub['wind'] }} km/j</strong>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Risiko Maritim</span>
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ str_contains(strtolower($destHub['risk'] ?? ''), 'high') ? 'bg-rose-500/10 text-rose-500' : (str_contains(strtolower($destHub['risk'] ?? ''), 'medium') || str_contains(strtolower($destHub['risk'] ?? ''), 'waspada') ? 'bg-amber-500/10 text-amber-500' : 'bg-emerald-500/10 text-emerald-500') }}">
                            {{ $destHub['risk'] ?? 'Waspada' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- 3-Day Forecast Cards -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
            <i data-lucide="calendar" class="w-4 h-4 text-cyan-500"></i>
            <span>Prakiraan Cuaca 3 Hari Kedepan (Rute Aktif Kargo)</span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-white block">Hari Ini</span>
                    <span class="text-[11px] text-slate-400">Cerah Berawan</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-black text-slate-900 dark:text-white">26° / 32°</span>
                    <span class="block text-[10px] text-slate-400">Hujan 0 mm</span>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-white block">Besok</span>
                    <span class="text-[11px] text-slate-400">Hujan Lokal</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-black text-slate-900 dark:text-white">25° / 31°</span>
                    <span class="block text-[10px] text-slate-400">Hujan 2.4 mm</span>
                </div>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-800 dark:text-white block">Lusa</span>
                    <span class="text-[11px] text-slate-400">Cerah Hangat</span>
                </div>
                <div class="text-right">
                    <span class="text-sm font-black text-slate-900 dark:text-white">26° / 33°</span>
                    <span class="block text-[10px] text-slate-400">Hujan 0 mm</span>
                </div>
            </div>
        </div>
    </div>

    <!-- 195+ Global Maritime Weather Hubs Directory -->
    @if(isset($allWeatherHubs))
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
                        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ ($hub['risk_score'] ?? 30) >= 65 ? 'bg-rose-50 text-rose-600 dark:bg-rose-950/40 dark:text-rose-400' : (($hub['risk_score'] ?? 30) >= 30 ? 'bg-amber-50 text-amber-600 dark:bg-amber-950/40 dark:text-amber-400' : 'bg-emerald-50 text-emerald-600 dark:bg-emerald-950/40 dark:text-emerald-400') }}">
                            {{ $hub['risk_score'] ?? 30 }}/100
                        </span>
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
                        Pilih &rarr;
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
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
