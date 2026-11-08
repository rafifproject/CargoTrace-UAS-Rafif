@extends('layouts.admin')

@section('title', 'Monitoring Cuaca Global')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Monitoring Cuaca (Weather Monitoring)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Integrasi data meteorologi Open-Meteo untuk evaluasi risiko maritim & penerbangan kargo</p>
        </div>
        <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
            Pembaruan Terakhir: {{ $originWeather['fetched_at'] ?? now()->format('H:i') }}
        </span>
    </div>

    <!-- Active Route Weather Comparison (Mockup 13: Surabaya ID vs Sydney AU) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Origin Weather Card (Surabaya, ID) -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] font-bold text-cyan-500 uppercase tracking-wider">Titik Asal Kargo</span>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white">Surabaya, Indonesia</h2>
                    <p class="text-[11px] text-slate-400">Port Tanjung Perak (-7.195, 112.729)</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-amber-500 border border-amber-500/20 flex items-center justify-center">
                    <i data-lucide="sun" class="w-7 h-7"></i>
                </div>
            </div>

            <div class="my-6 flex items-baseline gap-4">
                <span class="text-5xl font-black text-slate-900 dark:text-white">{{ $originWeather['current']['temperature_2m'] ?? 32 }}°C</span>
                <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Cerah Berawan</span>
            </div>

            <div class="grid grid-cols-3 gap-3 text-xs pt-4 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Kelembaban</span>
                    <strong class="text-slate-800 dark:text-white">{{ $originWeather['current']['relative_humidity_2m'] ?? 72 }}%</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Kecepatan Angin</span>
                    <strong class="text-slate-800 dark:text-white">{{ $originWeather['current']['wind_speed_10m'] ?? 12 }} km/j</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Risiko Maritim</span>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500">Rendah</span>
                </div>
            </div>
        </div>

        <!-- Destination Weather Card (Sydney, AU) -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] font-bold text-rose-500 uppercase tracking-wider">Titik Tujuan Kargo</span>
                    <h2 class="text-lg font-black text-slate-900 dark:text-white">Sydney, Australia</h2>
                    <p class="text-[11px] text-slate-400">Port Botany (-33.856, 151.209)</p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 text-blue-500 border border-blue-500/20 flex items-center justify-center">
                    <i data-lucide="cloud-rain" class="w-7 h-7"></i>
                </div>
            </div>

            <div class="my-6 flex items-baseline gap-4">
                <span class="text-5xl font-black text-slate-900 dark:text-white">{{ $destWeather['current']['temperature_2m'] ?? 26 }}°C</span>
                <span class="text-sm font-semibold text-slate-600 dark:text-slate-300">Hujan Ringan & Berangin</span>
            </div>

            <div class="grid grid-cols-3 gap-3 text-xs pt-4 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Kelembaban</span>
                    <strong class="text-slate-800 dark:text-white">{{ $destWeather['current']['relative_humidity_2m'] ?? 84 }}%</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Kecepatan Angin</span>
                    <strong class="text-slate-800 dark:text-white">{{ $destWeather['current']['wind_speed_10m'] ?? 28 }} km/j</strong>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block font-medium">Risiko Maritim</span>
                    <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500">Waspada</span>
                </div>
            </div>
        </div>
    </div>

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
</div>
@endsection

