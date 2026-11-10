@extends('layouts.admin')

@section('title', 'Indikator Ekonomi Global')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Indikator Ekonomi (Economic Indicators)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Analisis makro ekonomi negara mitra dagang berbasis data World Bank & UN Comtrade</p>
        </div>
        <form method="GET" action="{{ route('admin.intelligence.economy') }}">
            <select name="country" onchange="this.form.submit()" class="px-3 py-1.5 text-xs bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 font-bold focus:ring-1 focus:ring-cyan-500">
                @if(isset($countriesList))
                    @foreach($countriesList as $c)
                        <option value="{{ $c['code'] }}" {{ $country === $c['code'] ? 'selected' : '' }}>
                            {{ $c['flag'] ?? '' }} {{ $c['name'] }} ({{ $c['code'] }})
                        </option>
                    @endforeach
                @else
                    <option value="ID" {{ $country === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
                    <option value="AU" {{ $country === 'AU' ? 'selected' : '' }}>Australia (AU)</option>
                    <option value="SG" {{ $country === 'SG' ? 'selected' : '' }}>Singapura (SG)</option>
                    <option value="NL" {{ $country === 'NL' ? 'selected' : '' }}>Belanda (NL)</option>
                    <option value="DE" {{ $country === 'DE' ? 'selected' : '' }}>Jerman (DE)</option>
                    <option value="CN" {{ $country === 'CN' ? 'selected' : '' }}>Tiongkok (CN)</option>
                    <option value="US" {{ $country === 'US' ? 'selected' : '' }}>Amerika Serikat (US)</option>
                @endif
            </select>
        </form>
    </div>

    <!-- 4 Key Metrics (Mockup 15: GDP, Inflasi, Ekspor, Impor) -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-400 uppercase">Produk Domestik Bruto (GDP)</p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $data['gdp'] }} <span class="text-xs font-normal text-slate-400">USD</span></h3>
            <span class="text-[10px] text-cyan-600 dark:text-cyan-400 mt-1 block">Ukuran Ekonomi Nasional</span>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-400 uppercase">Tingkat Inflasi Tahunan</p>
            <h3 class="text-3xl font-black text-amber-500 mt-1">{{ $data['inflation'] }}</h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Stabilitas Harga Konsumen</span>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-400 uppercase">Volume Ekspor Barang</p>
            <h3 class="text-3xl font-black text-emerald-500 mt-1">{{ $data['export'] }} <span class="text-xs font-normal text-slate-400">USD</span></h3>
            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 block">Komoditas & Manufaktur</span>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-400 uppercase">Volume Impor Barang</p>
            <h3 class="text-3xl font-black text-blue-500 mt-1">{{ $data['import'] }} <span class="text-xs font-normal text-slate-400">USD</span></h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Bahan Baku & Modal</span>
        </div>
    </div>

    <!-- GDP Growth Trend Chart -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tren Pertumbuhan GDP (% Tahunan) &bull; {{ $data['name'] }}</h2>
                <p class="text-[11px] text-slate-400">Dinamika pemulihan ekonomi dan daya serap logistik perdagangan</p>
            </div>
            <span class="text-xs font-semibold text-slate-500 font-mono">{{ $data['source'] }}</span>
        </div>
        <div class="my-4 h-64">
            <canvas id="growthChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('growthChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($years) !!},
                datasets: [{
                    label: 'Pertumbuhan GDP (%)',
                    data: {!! json_encode($growth) !!},
                    backgroundColor: '#00A8E8',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { font: { size: 10 } } }
                }
            }
        });
    });
</script>
@endpush

