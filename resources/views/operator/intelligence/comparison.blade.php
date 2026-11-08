@extends('layouts.operator')

@section('title', 'Perbandingan Negara Mitra')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Perbandingan Negara Mitra Dagang</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Komparasi indikator logistik, inflasi, dan neraca perdagangan antar dua negara mitra.</p>
    </div>
</div>

{{-- Country Selector Form (Mockup 14) --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('operator.intelligence.comparison') }}" class="flex flex-wrap items-end gap-4">
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Negara A (Asal)</label>
            <select name="country_a" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="ID" {{ $countryA === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
                <option value="SG" {{ $countryA === 'SG' ? 'selected' : '' }}>Singapura (SG)</option>
                <option value="CN" {{ $countryA === 'CN' ? 'selected' : '' }}>Tiongkok (CN)</option>
                <option value="US" {{ $countryA === 'US' ? 'selected' : '' }}>Amerika Serikat (US)</option>
                <option value="AU" {{ $countryA === 'AU' ? 'selected' : '' }}>Australia (AU)</option>
            </select>
        </div>
        <div class="flex-1 min-w-40">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Negara B (Tujuan / Mitra)</label>
            <select name="country_b" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="SG" {{ $countryB === 'SG' ? 'selected' : '' }}>Singapura (SG)</option>
                <option value="ID" {{ $countryB === 'ID' ? 'selected' : '' }}>Indonesia (ID)</option>
                <option value="CN" {{ $countryB === 'CN' ? 'selected' : '' }}>Tiongkok (CN)</option>
                <option value="US" {{ $countryB === 'US' ? 'selected' : '' }}>Amerika Serikat (US)</option>
                <option value="AU" {{ $countryB === 'AU' ? 'selected' : '' }}>Australia (AU)</option>
                <option value="NL" {{ $countryB === 'NL' ? 'selected' : '' }}>Belanda (NL)</option>
            </select>
        </div>
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors">
            <i data-lucide="scale" class="w-3.5 h-3.5"></i> Bandingkan
        </button>
    </form>
</div>

{{-- Comparison Table & Visual Bar (Mockup 14) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Side by Side Table --}}
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Tabel Komparasi Makroekonomi</h3>
        <table class="w-full text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="text-left px-3 py-2.5 font-semibold text-slate-500">Indikator</th>
                    <th class="text-right px-3 py-2.5 font-semibold text-cyan-600 dark:text-cyan-400">{{ $countryA }}</th>
                    <th class="text-right px-3 py-2.5 font-semibold text-indigo-600 dark:text-indigo-400">{{ $countryB }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                <tr>
                    <td class="px-3 py-3 font-medium text-slate-700 dark:text-slate-200">PDB Nominal</td>
                    <td class="px-3 py-3 text-right font-bold text-slate-800 dark:text-white">{{ $dataA['gdp'] ?? '1,37 T USD' }}</td>
                    <td class="px-3 py-3 text-right font-bold text-slate-800 dark:text-white">{{ $dataB['gdp'] ?? '501,4 B USD' }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-3 font-medium text-slate-700 dark:text-slate-200">Tingkat Inflasi</td>
                    <td class="px-3 py-3 text-right text-slate-600 dark:text-slate-300">{{ $dataA['inflation'] ?? '2,48%' }}</td>
                    <td class="px-3 py-3 text-right text-slate-600 dark:text-slate-300">{{ $dataB['inflation'] ?? '2,40%' }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-3 font-medium text-slate-700 dark:text-slate-200">Ekspor Tahunan</td>
                    <td class="px-3 py-3 text-right text-slate-600 dark:text-slate-300">{{ $dataA['exports'] ?? '264,8 B USD' }}</td>
                    <td class="px-3 py-3 text-right text-slate-600 dark:text-slate-300">{{ $dataB['exports'] ?? '475,2 B USD' }}</td>
                </tr>
                <tr>
                    <td class="px-3 py-3 font-medium text-slate-700 dark:text-slate-200">Impor Tahunan</td>
                    <td class="px-3 py-3 text-right text-slate-600 dark:text-slate-300">{{ $dataA['imports'] ?? '233,4 B USD' }}</td>
                    <td class="px-3 py-3 text-right text-slate-600 dark:text-slate-300">{{ $dataB['imports'] ?? '420,1 B USD' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Visual Chart Comparison --}}
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm flex flex-col justify-between">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Grafik Komparasi PDB Relatif</h3>
        <canvas id="comparisonChart" height="110"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const ctx = document.getElementById('comparisonChart').getContext('2d');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['PDB (Triliun USD)', 'Ekspor (Ratusan M USD)', 'Impor (Ratusan M USD)'],
            datasets: [
                {
                    label: '{{ $countryA }}',
                    data: [1.37, 2.64, 2.33],
                    backgroundColor: '#06b6d4',
                    borderRadius: 6
                },
                {
                    label: '{{ $countryB }}',
                    data: [0.50, 4.75, 4.20],
                    backgroundColor: '#6366f1',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { labels: { color: isDark ? '#cbd5e1' : '#475569' } }
            },
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

