@extends('layouts.admin')

@section('title', 'Kurs & Nilai Tukar Global')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Kurs & Nilai Tukar (Currency & Exchange)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan konversi valuta asing dan volatilitas kurs logistik internasional (ExchangeRate-API Live)</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 px-3 py-1.5 rounded-lg border border-emerald-200 dark:border-emerald-800 flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $currencies['provider'] ?? 'ExchangeRate-API' }} ({{ count($currencies['rates']) }} Mata Uang Aktif)</span>
            </span>
        </div>
    </div>

    <!-- Converter & Trend Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Interactive Multi-Currency Converter Widget (160+ Mata Uang) -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Konverter Mata Uang Cepat</h2>
                    <span class="text-[10px] font-bold text-cyan-600 dark:text-cyan-400 bg-cyan-50 dark:bg-cyan-950/60 px-2 py-0.5 rounded-md border border-cyan-200 dark:border-cyan-800">
                        Live 166 Valas
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mb-4">Kalkulasi biaya freight, bea pabean, & transaksi ekspor-impor internasional</p>

                <div class="space-y-3.5">
                    <div>
                        <label for="convAmount" class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Nominal Jumlah</label>
                        <input type="number" id="convAmount" value="1" min="0.01" step="any" oninput="calculateConvert()"
                               class="w-full px-3 py-2 text-sm bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-mono font-bold focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="fromCurr" class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Dari Mata Uang</label>
                            <select id="fromCurr" onchange="calculateConvert()"
                                    class="w-full px-2.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                @foreach($currencies['rates'] as $cCode => $cRate)
                                    @php
                                        $meta = $currencyMeta[$cCode] ?? null;
                                        $label = $cCode . ($meta ? ' - ' . $meta['name'] : '');
                                    @endphp
                                    <option value="{{ $cCode }}" {{ $cCode === 'USD' ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="toCurr" class="block text-[11px] font-semibold text-slate-500 dark:text-slate-400 mb-1">Ke Mata Uang</label>
                            <select id="toCurr" onchange="calculateConvert()"
                                    class="w-full px-2.5 py-2 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-900 dark:text-white font-bold focus:outline-none focus:ring-1 focus:ring-cyan-500">
                                @foreach($currencies['rates'] as $cCode => $cRate)
                                    @php
                                        $meta = $currencyMeta[$cCode] ?? null;
                                        $label = $cCode . ($meta ? ' - ' . $meta['name'] : '');
                                    @endphp
                                    <option value="{{ $cCode }}" {{ $cCode === 'IDR' ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] uppercase font-bold text-slate-400">Hasil Konversi Real-Time:</span>
                    <span class="text-[10px] font-mono text-slate-400" id="rateRateInfo">1 USD = Rp 15.824,50</span>
                </div>
                <div class="text-2xl font-black text-cyan-600 dark:text-cyan-400 mt-1" id="convResult">
                    Rp 15.824,50
                </div>
            </div>
        </div>

        <!-- 30-Day USD/IDR Trend Line Chart -->
        <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-slate-100 dark:border-slate-800 gap-2">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tren Kurs USD / IDR (30 Hari Terakhir)</h2>
                    <p class="text-[11px] text-slate-400">Tingkat fluktuasi nilai tukar acuan pelayaran & logistik internasional</p>
                </div>
                <span class="text-xs font-mono font-bold text-emerald-500 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20 self-start sm:self-auto">
                    1 {{ $base }} &asymp; {{ number_format($currencies['rates']['IDR'] ?? 15825, 2, ',', '.') }} IDR
                </span>
            </div>
            <div class="my-4 h-56">
                <canvas id="currencyChart"></canvas>
            </div>
            <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800">
                <span>Pembaruan: {{ $currencies['fetched_at'] ?? now()->format('d M Y, H:i') }}</span>
                <span class="text-cyan-600 dark:text-cyan-400 font-medium">Data Kurs Terverifikasi Pasar Global</span>
            </div>
        </div>
    </div>

    <!-- Currency Rates Directory (Full 160+ Currencies Grid with Real-Time Search & Base Selector) -->
    <div class="p-6 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-3">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="coins" class="w-4 h-4 text-cyan-500"></i>
                    <span>Direktori Nilai Tukar Seluruh Mata Uang Dunia (166 Valas)</span>
                </h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                    Basis Kurs Acuan: <strong class="text-cyan-600 dark:text-cyan-400">{{ $base }} 1.00</strong> &bull; Total Terdaftar: <strong>{{ count($currencies['rates']) }} Negara / Valuta</strong>
                </p>
            </div>

            <!-- Controls: Base Selector & Search -->
            <div class="flex items-center gap-2.5 flex-wrap">
                <!-- Base Currency Dropdown -->
                <div class="flex items-center gap-1.5 text-xs text-slate-500">
                    <span class="font-semibold text-slate-700 dark:text-slate-300">Basis:</span>
                    <select onchange="window.location.href = '?base=' + this.value"
                            class="px-2.5 py-1.5 text-xs font-bold rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                        @foreach(['USD','EUR','IDR','SGD','GBP','JPY','CNY','AUD','CAD','SAR','AED','MYR'] as $b)
                            <option value="{{ $b }}" {{ $base === $b ? 'selected' : '' }}>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Input -->
                <div class="relative w-full sm:w-64">
                    <input type="text" id="currencySearchInput" oninput="filterCurrencyCards()"
                           placeholder="Cari mata uang / negara (misal: IDR, Rupiah, EUR, JPY)..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5"></i>
                </div>
            </div>
        </div>

        <!-- 166 Currency Cards Grid -->
        <div id="currencyCardsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3.5 mt-4">
            @foreach($currencies['rates'] as $cur => $rate)
                @php
                    $meta = $currencyMeta[$cur] ?? null;
                    $flagIso = $meta['flag'] ?? strtolower(substr($cur, 0, 2));
                    $currName = $meta['name'] ?? $cur;
                    $countryName = $meta['country'] ?? 'Global';
                    $formattedRate = is_numeric($rate) ? ($rate > 100 ? number_format($rate, 2, ',', '.') : number_format($rate, 4, ',', '.')) : $rate;
                @endphp
                <div class="currency-card p-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800 hover:border-cyan-500/40 hover:shadow-xs transition-all duration-150 flex flex-col justify-between text-left"
                     data-code="{{ strtolower($cur) }}"
                     data-name="{{ strtolower($currName) }}"
                     data-country="{{ strtolower($countryName) }}">
                    <!-- Card Header: Flag & Code -->
                    <div class="flex items-center justify-between mb-1.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <div class="w-5 h-3.5 rounded overflow-hidden shadow-2xs border border-slate-300 dark:border-slate-700 flex-shrink-0 bg-slate-200 dark:bg-slate-800 flex items-center justify-center">
                                <img src="https://flagcdn.com/w40/{{ $flagIso }}.png"
                                     alt="{{ $countryName }}"
                                     class="w-full h-full object-cover"
                                     onerror="this.style.display='none';">
                            </div>
                            <span class="text-xs font-black text-slate-900 dark:text-white tracking-wider truncate">{{ $cur }}</span>
                        </div>
                        <span class="text-[9px] font-mono text-cyan-600 dark:text-cyan-400 font-semibold bg-cyan-50 dark:bg-cyan-950/40 px-1.5 py-0.5 rounded">
                            {{ $base }}
                        </span>
                    </div>

                    <!-- Currency Full Name -->
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate mb-2 font-medium" title="{{ $currName }} ({{ $countryName }})">
                        {{ $currName }}
                    </p>

                    <!-- Exchange Rate Value -->
                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800">
                        <span class="text-[9px] text-slate-400 block font-semibold uppercase">Nilai Tukar:</span>
                        <div class="text-sm font-black text-slate-900 dark:text-white font-mono tracking-tight mt-0.5 truncate">
                            {{ $formattedRate }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty search state -->
        <div id="currencyNoResult" class="hidden py-12 text-center">
            <i data-lucide="help-circle" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
            <p class="text-xs text-slate-500 font-medium">Mata uang tidak ditemukan. Silakan coba kata kunci lain (misal: USD, IDR, Rupiah, EUR, Ringgit, Yen).</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // All 160+ currency rates passed to JavaScript for instantaneous client-side cross conversion
    const allRates = {!! json_encode($currencies['rates']) !!};
    const baseCurrency = "{{ $base }}";

    function calculateConvert() {
        const amount = parseFloat(document.getElementById('convAmount').value) || 0;
        const from = document.getElementById('fromCurr').value;
        const to = document.getElementById('toCurr').value;

        const rateFrom = allRates[from] || 1.0;
        const rateTo   = allRates[to] || 1.0;

        // Formula cross-currency relative to base
        const inBase = from === baseCurrency ? amount : (amount / rateFrom);
        const result = to === baseCurrency ? inBase : (inBase * rateTo);

        // Unit rate info
        const unitInBase = from === baseCurrency ? 1 : (1 / rateFrom);
        const unitResult = to === baseCurrency ? unitInBase : (unitInBase * rateTo);

        const formatOptions = result > 100 
            ? { minimumFractionDigits: 2, maximumFractionDigits: 2 }
            : { minimumFractionDigits: 4, maximumFractionDigits: 4 };

        document.getElementById('convResult').textContent = 
            (to === 'IDR' ? 'Rp ' : to + ' ') + result.toLocaleString('id-ID', formatOptions);

        document.getElementById('rateRateInfo').textContent =
            '1 ' + from + ' = ' + (to === 'IDR' ? 'Rp ' : to + ' ') + unitResult.toLocaleString('id-ID', formatOptions);
    }

    function filterCurrencyCards() {
        const query = document.getElementById('currencySearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.currency-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const code = card.dataset.code || '';
            const name = card.dataset.name || '';
            const country = card.dataset.country || '';

            const matches = code.includes(query) || name.includes(query) || country.includes(query);
            card.style.display = matches ? '' : 'none';
            if (matches) visibleCount++;
        });

        const noResult = document.getElementById('currencyNoResult');
        if (noResult) {
            noResult.classList.toggle('hidden', visibleCount > 0);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        calculateConvert();

        // 30 Days Trend Line Chart
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
                    pointRadius: 2,
                    pointHoverRadius: 5
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
