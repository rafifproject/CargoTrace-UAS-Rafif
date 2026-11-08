@extends('layouts.admin')

@section('title', 'Kurs & Nilai Tukar Global')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Kurs & Nilai Tukar (Currency & Exchange)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan konversi valuta asing dan volatilitas kurs logistik internasional (ExchangeRate-API)</p>
        </div>
        <span class="text-xs text-slate-500 font-mono bg-slate-100 dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
            Sumber: {{ $currencies['fetched_at'] ?? 'ExchangeRate-API' }}
        </span>
    </div>

    <!-- Converter & Trend Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Interactive Converter Widget -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-1">Konverter Mata Uang Cepat</h2>
                <p class="text-[11px] text-slate-400 mb-4">Kalkulasi biaya freight & bea pabean</p>

                <div class="space-y-3">
                    <div>
                        <label for="convAmount" class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Nominal</label>
                        <input type="number" id="convAmount" value="1" min="1" oninput="calculateConvert()" class="w-full px-3 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label for="fromCurr" class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Dari</label>
                            <select id="fromCurr" onchange="calculateConvert()" class="w-full px-2 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold">
                                <option value="USD" selected>USD (Dolar AS)</option>
                                <option value="EUR">EUR (Euro)</option>
                                <option value="SGD">SGD (Dolar SG)</option>
                            </select>
                        </div>
                        <div>
                            <label for="toCurr" class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Ke</label>
                            <select id="toCurr" onchange="calculateConvert()" class="w-full px-2 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold">
                                <option value="IDR" selected>IDR (Rupiah)</option>
                                <option value="EUR">EUR (Euro)</option>
                                <option value="USD">USD (Dolar AS)</option>
                                <option value="SGD">SGD (Dolar SG)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <span class="text-[10px] uppercase font-bold text-slate-400">Hasil Konversi:</span>
                <div class="text-2xl font-black text-cyan-600 dark:text-cyan-400 mt-0.5" id="convResult">
                    Rp 15.824,50
                </div>
            </div>
        </div>

        <!-- 30-Day USD/IDR Trend Line Chart -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tren Kurs USD / IDR (30 Hari Terakhir)</h2>
                    <p class="text-[11px] text-slate-400">Tingkat fluktuasi nilai tukar acuan pelayaran internasional</p>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-500 bg-emerald-500/10 px-2.5 py-1 rounded-lg">
                    1 USD &asymp; 15.824 IDR
                </span>
            </div>
            <div class="my-4 h-56">
                <canvas id="currencyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Currency Rates Table -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-4">Nilai Tukar Valuta Asing Terhadap USD (Base: USD 1.00)</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3 text-center">
            @foreach($currencies['rates'] as $cur => $rate)
                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $cur }}</span>
                    <div class="text-base font-black text-slate-900 dark:text-white mt-1">{{ is_numeric($rate) ? number_format($rate, 2) : $rate }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const rates = {
        IDR: {{ $currencies['rates']['IDR'] ?? 15824.5 }},
        EUR: {{ $currencies['rates']['EUR'] ?? 0.92 }},
        SGD: {{ $currencies['rates']['SGD'] ?? 1.34 }},
        USD: 1.0
    };

    function calculateConvert() {
        const amount = parseFloat(document.getElementById('convAmount').value) || 0;
        const from = document.getElementById('fromCurr').value;
        const to = document.getElementById('toCurr').value;

        const inUSD = from === 'USD' ? amount : amount / (rates[from] || 1);
        const result = inUSD * (rates[to] || 1);

        document.getElementById('convResult').textContent = 
            (to === 'IDR' ? 'Rp ' : to + ' ') + result.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('currencyChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dates) !!},
                datasets: [{
                    label: 'USD ke IDR',
                    data: {!! json_encode($rates) !!},
                    borderColor: '#00A8E8',
                    backgroundColor: 'rgba(0, 168, 232, 0.08)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 2
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

