@extends('layouts.operator')

@section('title', 'Informasi Nilai Tukar')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Informasi Mata Uang & Kurs Global</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Pantau fluktuasi nilai tukar untuk perhitungan biaya logistik, pabean, dan freight forwarding.</p>
    </div>
</div>

{{-- Top Currency Cards (Mockup 10) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-slate-400 uppercase">USD / IDR</span>
            <span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded">+0.24%</span>
        </div>
        <div class="text-2xl font-black text-slate-800 dark:text-white">Rp 15.825,00</div>
        <div class="text-[11px] text-slate-400 mt-1">Dolar AS ke Rupiah Indonesia</div>
    </div>

    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-slate-400 uppercase">EUR / USD</span>
            <span class="text-xs font-bold text-emerald-500 bg-emerald-500/10 px-2 py-0.5 rounded">+0.12%</span>
        </div>
        <div class="text-2xl font-black text-slate-800 dark:text-white">$ 1,0875</div>
        <div class="text-[11px] text-slate-400 mt-1">Euro ke Dolar AS</div>
    </div>

    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-5 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex items-center justify-between mb-2">
            <span class="text-xs font-semibold text-slate-400 uppercase">GBP / USD</span>
            <span class="text-xs font-bold text-rose-500 bg-rose-500/10 px-2 py-0.5 rounded">-0.18%</span>
        </div>
        <div class="text-2xl font-black text-slate-800 dark:text-white">$ 1,2679</div>
        <div class="text-[11px] text-slate-400 mt-1">Poundsterling ke Dolar AS</div>
    </div>
</div>

{{-- Converter & 30-Day Trend Chart (Mockup 10) --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6 mb-6">
    {{-- Converter Box --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
            <i data-lucide="calculator" class="w-4 h-4 text-cyan-500"></i>
            Konverter Nilai Logistik
        </h3>
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Dari Mata Uang</label>
                <div class="flex gap-2">
                    <select id="conv-from" class="w-28 px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200">
                        <option value="1">USD</option>
                        <option value="1.0875">EUR</option>
                        <option value="1.2679">GBP</option>
                        <option value="0.74">SGD</option>
                    </select>
                    <input type="number" id="conv-amount" value="1000" class="flex-1 px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-200">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Ke (Tujuan)</label>
                <input type="text" value="IDR (Rupiah Indonesia)" readonly class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-500 cursor-not-allowed">
            </div>
            <div class="pt-2">
                <span class="text-xs text-slate-400 block mb-1">Hasil Konversi:</span>
                <div id="conv-result" class="text-xl font-bold font-mono text-cyan-600 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800/50 p-3 rounded-xl">
                    Rp 15.825.000,00
                </div>
            </div>
        </div>
    </div>

    {{-- 30-Day Trend Chart --}}
    <div class="lg:col-span-3 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
            <i data-lucide="trending-up" class="w-4 h-4 text-cyan-500"></i>
            Tren Nilai Tukar USD / IDR (30 Hari Terakhir)
        </h3>
        <canvas id="currencyChart" height="90"></canvas>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const isDark = document.documentElement.classList.contains('dark');
    const ctx = document.getElementById('currencyChart').getContext('2d');

    const labels = @json($dates);
    const data = @json($rates);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'USD ke IDR',
                data: data,
                borderColor: '#06b6d4',
                backgroundColor: 'rgba(6,182,212,0.08)',
                borderWidth: 2,
                pointRadius: 1.5,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { color: isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b', maxTicksLimit: 8 }
                },
                y: {
                    grid: { color: isDark ? 'rgba(100,116,139,0.15)' : 'rgba(100,116,139,0.1)' },
                    ticks: { color: isDark ? '#94a3b8' : '#64748b' }
                }
            }
        }
    });

    const amountInput = document.getElementById('conv-amount');
    const fromSelect = document.getElementById('conv-from');
    const resultBox = document.getElementById('conv-result');

    function updateConv() {
        const amt = parseFloat(amountInput.value) || 0;
        const rateFactor = parseFloat(fromSelect.value) || 1;
        const idrRate = 15825;
        const total = amt * rateFactor * idrRate;
        resultBox.textContent = 'Rp ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2 });
    }

    amountInput.addEventListener('input', updateConv);
    fromSelect.addEventListener('change', updateConv);
});
</script>
@endpush

