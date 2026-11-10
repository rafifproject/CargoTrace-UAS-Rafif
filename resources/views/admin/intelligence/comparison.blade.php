@extends('layouts.admin')

@section('title', 'Perbandingan Negara')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Perbandingan Negara (Country Comparison)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Komparasi indikator ekonomi makro, kapasitas logistik, dan skala perdagangan bilateral</p>
        </div>

        <!-- Country Selectors (Mockup 18: Negara A vs Negara B) -->
        <form method="GET" action="{{ route('admin.intelligence.comparison') }}" class="flex items-center gap-2">
            <div>
                <label for="country_a" class="sr-only">Negara A</label>
                <select id="country_a" name="country_a" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white font-bold">
                    @if(isset($countriesList))
                        @foreach($countriesList as $c)
                            <option value="{{ $c['code'] }}" {{ $countryA === $c['code'] ? 'selected' : '' }}>
                                {{ $c['flag'] ?? '' }} {{ $c['name'] }} ({{ $c['code'] }})
                            </option>
                        @endforeach
                    @else
                        <option value="ID" {{ $countryA === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
                        <option value="SG" {{ $countryA === 'SG' ? 'selected' : '' }}>Singapura (SG)</option>
                        <option value="NL" {{ $countryA === 'NL' ? 'selected' : '' }}>Belanda (NL)</option>
                        <option value="DE" {{ $countryA === 'DE' ? 'selected' : '' }}>Jerman (DE)</option>
                        <option value="CN" {{ $countryA === 'CN' ? 'selected' : '' }}>Tiongkok (CN)</option>
                        <option value="US" {{ $countryA === 'US' ? 'selected' : '' }}>Amerika Serikat (US)</option>
                    @endif
                </select>
            </div>
            <span class="text-xs font-black text-cyan-500">VS</span>
            <div>
                <label for="country_b" class="sr-only">Negara B</label>
                <select id="country_b" name="country_b" onchange="this.form.submit()" class="px-2.5 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white font-bold">
                    @if(isset($countriesList))
                        @foreach($countriesList as $c)
                            <option value="{{ $c['code'] }}" {{ $countryB === $c['code'] ? 'selected' : '' }}>
                                {{ $c['flag'] ?? '' }} {{ $c['name'] }} ({{ $c['code'] }})
                            </option>
                        @endforeach
                    @else
                        <option value="AU" {{ $countryB === 'AU' ? 'selected' : '' }}>Australia (AU)</option>
                        <option value="ID" {{ $countryB === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
                        <option value="SG" {{ $countryB === 'SG' ? 'selected' : '' }}>Singapura (SG)</option>
                        <option value="NL" {{ $countryB === 'NL' ? 'selected' : '' }}>Belanda (NL)</option>
                        <option value="DE" {{ $countryB === 'DE' ? 'selected' : '' }}>Jerman (DE)</option>
                        <option value="CN" {{ $countryB === 'CN' ? 'selected' : '' }}>Tiongkok (CN)</option>
                        <option value="US" {{ $countryB === 'US' ? 'selected' : '' }}>Amerika Serikat (US)</option>
                    @endif
                </select>
            </div>
        </form>
    </div>

    <!-- Side-by-Side Comparison Cards (Mockup 18) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Negara A -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border-2 border-cyan-500/40 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] font-bold text-cyan-500 uppercase tracking-wider">Negara A</span>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $dataA['name'] }}</h2>
                </div>
                <span class="text-lg font-mono font-bold text-cyan-400 bg-cyan-500/10 px-3 py-1 rounded-xl">
                    {{ $dataA['code'] }}
                </span>
            </div>

            <dl class="space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Produk Domestik Bruto (GDP)</dt>
                    <dd class="font-bold text-slate-800 dark:text-white">{{ $dataA['gdp'] }} USD</dd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Tingkat Inflasi</dt>
                    <dd class="font-bold text-slate-800 dark:text-white">{{ $dataA['inflation'] }}</dd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Volume Ekspor</dt>
                    <dd class="font-bold text-emerald-500">{{ $dataA['export'] }} USD</dd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Volume Impor</dt>
                    <dd class="font-bold text-blue-500">{{ $dataA['import'] }} USD</dd>
                </div>
                <div class="flex justify-between py-1.5">
                    <dt class="text-slate-400">Estimasi Populasi</dt>
                    <dd class="font-bold text-slate-800 dark:text-white">{{ $dataA['population'] }}</dd>
                </div>
            </dl>
        </div>

        <!-- Negara B -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border-2 border-blue-500/40 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider">Negara B</span>
                    <h2 class="text-xl font-black text-slate-900 dark:text-white">{{ $dataB['name'] }}</h2>
                </div>
                <span class="text-lg font-mono font-bold text-blue-400 bg-blue-500/10 px-3 py-1 rounded-xl">
                    {{ $dataB['code'] }}
                </span>
            </div>

            <dl class="space-y-3 text-xs">
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Produk Domestik Bruto (GDP)</dt>
                    <dd class="font-bold text-slate-800 dark:text-white">{{ $dataB['gdp'] }} USD</dd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Tingkat Inflasi</dt>
                    <dd class="font-bold text-slate-800 dark:text-white">{{ $dataB['inflation'] }}</dd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Volume Ekspor</dt>
                    <dd class="font-bold text-emerald-500">{{ $dataB['export'] }} USD</dd>
                </div>
                <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800/80">
                    <dt class="text-slate-400">Volume Impor</dt>
                    <dd class="font-bold text-blue-500">{{ $dataB['import'] }} USD</dd>
                </div>
                <div class="flex justify-between py-1.5">
                    <dt class="text-slate-400">Estimasi Populasi</dt>
                    <dd class="font-bold text-slate-800 dark:text-white">{{ $dataB['population'] }}</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
@endsection

