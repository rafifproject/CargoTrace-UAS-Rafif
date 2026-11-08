@extends('layouts.operator')

@section('title', 'Indikator Ekonomi')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Indikator Ekonomi Makro</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan PDB, inflasi, neraca ekspor/impor negara mitra dagang logistik.</p>
    </div>

    {{-- Country Selector --}}
    <form method="GET" action="{{ route('operator.intelligence.economy') }}" class="flex items-center gap-2">
        <select name="country" onchange="this.form.submit()" class="px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-[#0A1128] text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 shadow-sm">
            <option value="ID" {{ $country === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
            <option value="SG" {{ $country === 'SG' ? 'selected' : '' }}>Singapura (SG)</option>
            <option value="CN" {{ $country === 'CN' ? 'selected' : '' }}>Tiongkok (CN)</option>
            <option value="US" {{ $country === 'US' ? 'selected' : '' }}>Amerika Serikat (US)</option>
            <option value="AU" {{ $country === 'AU' ? 'selected' : '' }}>Australia (AU)</option>
            <option value="NL" {{ $country === 'NL' ? 'selected' : '' }}>Belanda (NL)</option>
        </select>
    </form>
</div>

{{-- 4 KPI Cards (Mockup Economy) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">PDB (Nominal)</span>
        <div class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $data['gdp'] ?? '1,37 T USD' }}</div>
        <p class="text-[11px] text-emerald-500 font-medium mt-1">Stabil / Bertumbuh</p>
    </div>
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">Tingkat Inflasi</span>
        <div class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $data['inflation'] ?? '2,48%' }}</div>
        <p class="text-[11px] text-slate-400 mt-1">Indeks Harga Konsumen</p>
    </div>
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">Nilai Ekspor</span>
        <div class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $data['exports'] ?? '264,8 B USD' }}</div>
        <p class="text-[11px] text-emerald-500 font-medium mt-1">+4.2% YoY</p>
    </div>
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <span class="text-xs font-semibold text-slate-400 uppercase">Nilai Impor</span>
        <div class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $data['imports'] ?? '233,4 B USD' }}</div>
        <p class="text-[11px] text-slate-400 mt-1">Surplus Neraca Dagang</p>
    </div>
</div>

{{-- Historical GDP Growth Chart --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
        <i data-lucide="bar-chart-3" class="w-4 h-4 text-cyan-500"></i>
        Tren Pertumbuhan PDB Tahunan (%)
    </h3>
    <canvas id="gdpChart" height="80"></canvas>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const ctx = document.getElementById('gdpChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($years),
            datasets: [{
                label: 'Pertumbuhan (%)',
                data: @json($growth),
                backgroundColor: '#06b6d4',
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                },
                y: {
                    grid: { color: isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                }
            }
        }
    });
});
</script>
@endpush

