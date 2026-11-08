@extends('layouts.operator')

@section('title', 'Intelijen Risiko Rantai Pasok')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Intelijen Risiko Rantai Pasok</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Skor komposit mitigasi risiko operasional, cuaca, geopolitik, dan pabean rute kargo.</p>
    </div>
</div>

{{-- Top Row: Speedometer Score + Component Breakdown (Mockup 12) --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
    {{-- Speedometer / Score Card --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm flex flex-col items-center justify-center text-center">
        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Skor Risiko Rantai Pasok</span>
        <div class="relative w-44 h-24 flex items-end justify-center mb-2 overflow-hidden">
            <div class="w-40 h-40 rounded-full border-[12px] border-slate-200 dark:border-slate-800 border-t-amber-500 border-r-amber-500 transform -rotate-45"></div>
            <div class="absolute bottom-0 text-center">
                <span class="text-4xl font-extrabold text-slate-800 dark:text-white">{{ $risk['score'] ?? 58 }}</span>
                <span class="text-xs text-slate-400 block font-semibold">/ 100</span>
            </div>
        </div>
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 mt-2">
            Status: {{ $risk['level'] ?? 'Sedang (Moderate)' }}
        </span>
        <p class="text-xs text-slate-400 mt-3 max-w-xs leading-relaxed">
            Rute utama dalam kondisi operasional stabil dengan beberapa potensi perlambatan cuaca di kawasan Asia Tenggara.
        </p>
    </div>

    {{-- Component Breakdown --}}
    <div class="lg:col-span-3 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-5">Faktor Penentu Risiko</h3>
        <div class="space-y-4 text-xs">
            <div>
                <div class="flex justify-between font-semibold mb-1">
                    <span class="text-slate-600 dark:text-slate-300">Risiko Cuaca & Maritim</span>
                    <span class="text-rose-500 font-bold">65%</span>
                </div>
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-rose-500 rounded-full" style="width: 65%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between font-semibold mb-1">
                    <span class="text-slate-600 dark:text-slate-300">Risiko Inflasi & Kurs</span>
                    <span class="text-amber-500 font-bold">58%</span>
                </div>
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-amber-500 rounded-full" style="width: 58%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between font-semibold mb-1">
                    <span class="text-slate-600 dark:text-slate-300">Risiko Kepabeanan & Rute</span>
                    <span class="text-cyan-500 font-bold">42%</span>
                </div>
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-cyan-500 rounded-full" style="width: 42%"></div>
                </div>
            </div>

            <div>
                <div class="flex justify-between font-semibold mb-1">
                    <span class="text-slate-600 dark:text-slate-300">Sentimen Berita Logistik</span>
                    <span class="text-emerald-500 font-bold">36%</span>
                </div>
                <div class="h-2 w-full bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full" style="width: 36%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 7-Day Trend Chart --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
    <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
        <i data-lucide="activity" class="w-4 h-4 text-cyan-500"></i>
        Tren Indeks Risiko 7 Hari Terakhir
    </h3>
    <canvas id="riskTrendChart" height="80"></canvas>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const ctx = document.getElementById('riskTrendChart').getContext('2d');

    const days = ['10 Jan', '11 Jan', '12 Jan', '13 Jan', '14 Jan', '15 Jan', '16 Jan'];
    const scores = [52, 54, 60, 63, 59, 58, 58];

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: days,
            datasets: [{
                label: 'Skor Risiko',
                data: scores,
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                borderWidth: 2,
                tension: 0.35,
                fill: true,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { color: isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                },
                y: {
                    grid: { color: isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' },
                    min: 40,
                    max: 80
                }
            }
        }
    });
});
</script>
@endpush

