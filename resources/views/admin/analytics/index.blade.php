@extends('layouts.admin')

@section('title', 'Kecerdasan Bisnis & Analitik Operasional')

@push('styles')
<style>
    @media print {
        aside, header, .no-print { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; }
        .print-full { width: 100% !important; max-width: 100% !important; }
    }
    .metric-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .metric-card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="space-y-7 pb-10">
    <!-- Header & Action Toolbar -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 border-b border-slate-200 dark:border-slate-800 pb-5">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300 border border-cyan-200 dark:border-cyan-700">
                    Executive Business Intelligence
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">Hub Ekspor Pelabuhan Santos (Brazil) ke Pasar Global</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight font-outfit">
                Kecerdasan Bisnis & Analitik Rantai Pasok
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Pemantauan komprehensif omzet ekspor, komoditas terlaris, negara pemesan paling konsisten, dan data mentah kinerja logistik.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 no-print">
            <!-- Period Pill -->
            <div class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 shadow-2xs">
                <i data-lucide="calendar" class="w-3.5 h-3.5 text-cyan-500"></i>
                <span>Tahun Berjalan 2026</span>
            </div>

            <!-- Print / Export Report -->
            <button onclick="window.print()" type="button"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-2xs">
                <i data-lucide="printer" class="w-3.5 h-3.5 text-slate-400"></i>
                <span>Cetak / Ekspor PDF</span>
            </button>

            <!-- Quick Jump to Raw Data -->
            <a href="#raw-data-section"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 text-xs font-bold text-white shadow-sm transition-all">
                <i data-lucide="table-2" class="w-3.5 h-3.5"></i>
                <span>Lihat Data Mentah &darr;</span>
            </a>
        </div>
    </div>

    <!-- Quick Navigation Filter Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 no-print border-b border-slate-200/80 dark:border-slate-800/80 text-xs font-semibold">
        <a href="#financial-overview" class="px-3.5 py-1.5 rounded-lg bg-cyan-500/10 text-cyan-700 dark:text-cyan-300 border border-cyan-500/20 whitespace-nowrap">
            💰 Ringkasan Finansial Bisnis
        </a>
        <a href="#best-sellers" class="px-3.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors whitespace-nowrap">
            ☕ Komoditas Kopi Terlaris
        </a>
        <a href="#top-countries" class="px-3.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors whitespace-nowrap">
            🌍 Negara &amp; Pelanggan Konsisten
        </a>
        <a href="#operational-sla" class="px-3.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors whitespace-nowrap">
            ⏱️ SLA &amp; Kinerja Ekspedisi
        </a>
        <a href="#raw-data-section" class="px-3.5 py-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors whitespace-nowrap">
            📊 Tabel Data Mentah
        </a>
    </div>

    <!-- ==========================================
         SEKSI 1: METRIK KEUANGAN & BISNIS UTAMA
         ========================================== -->
    <div id="financial-overview" class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i data-lucide="badge-dollar-sign" class="w-4 h-4 text-emerald-500"></i>
                <span>1. Penghitungan Finansial &amp; Omzet Bisnis Santos</span>
            </h2>
            <span class="text-xs text-slate-400">Total Transaksi: {{ $totalShipments }} Kontainer Ekspor</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- 1. Total Pendapatan Freight (Lunas) -->
            <div class="metric-card p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pendapatan Freight (Kas Masuk)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300">
                        LUNAS (SETTLED)
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400 font-mono mt-2">
                    ${{ number_format($totalFreightRevenue, 2) }}
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    <span>Potensi Total Omzet:</span>
                    <strong class="text-slate-700 dark:text-slate-300 font-mono">${{ number_format($totalPotentialRevenue, 2) }}</strong>
                </div>
            </div>

            <!-- 2. Nilai Perputaran Komoditas Ekspor (GMV) -->
            <div class="metric-card p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Nilai Komoditas Biji Kopi (GMV)</span>
                    <div class="w-7 h-7 rounded-lg bg-cyan-50 dark:bg-cyan-950/60 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                        <i data-lucide="package-check" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-mono mt-2">
                    ${{ number_format($totalCommodityValue, 2) }}
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    <span>FOB Santos Green &amp; Roast Beans</span>
                    <span class="text-cyan-600 dark:text-cyan-400 font-semibold">Specialty Export</span>
                </div>
            </div>

            <!-- 3. Rasio Pelunasan & Piutang (Cashflow Health) -->
            <div class="metric-card p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Kesehatan Pelunasan (Settlement)</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $settlementRate >= 75 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                        {{ $settlementRate }}%
                    </span>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-mono mt-2">
                    {{ $paidInvoicesCount }} <span class="text-sm font-normal text-slate-400">/ {{ $totalShipments }} Invoice</span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    <span>Piutang Menunggu Wire:</span>
                    <strong class="text-amber-600 dark:text-amber-400 font-mono">${{ number_format($unpaidRevenue, 2) }}</strong>
                </div>
            </div>

            <!-- 4. Total Tonase & Rata-rata Nilai Per Kontainer -->
            <div class="metric-card p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Tonase Kargo Ekspor</span>
                    <div class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center">
                        <i data-lucide="scale" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white font-mono mt-2">
                    {{ number_format($totalExportWeightTons, 1) }} <span class="text-sm font-normal text-slate-400">Ton</span>
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 pt-2 border-t border-slate-100 dark:border-slate-800/80">
                    <span>Rata-rata Ongkir / Kontainer:</span>
                    <strong class="text-slate-700 dark:text-slate-300 font-mono">${{ number_format($avgRevenuePerShipment, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         SEKSI 2: DIAGRAM BISNIS LANGSUNG DAPAT DIPAHAMI
         ========================================== -->
    <div class="space-y-3">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
            <i data-lucide="bar-chart-3" class="w-4 h-4 text-cyan-500"></i>
            <span>2. Diagram Visualisasi Bisnis &amp; Tren Pertumbuhan</span>
        </h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Diagram 1: Tren Omzet Pendapatan Bulanan & Proyeksi Ekspor -->
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span>📈 Tren Omzet &amp; Pendapatan Ekspor Bulanan (USD)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Progres pertumbuhan pendapatan freight Santos Exporters S.A.</p>
                    </div>
                    <span class="text-xs font-mono font-bold text-emerald-500">+38.5% YoY</span>
                </div>
                <div class="my-4 h-64">
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                    <span>Puncak Ekspor: <strong>Kuartal IV (Panen Raya Brazil)</strong></span>
                    <span class="text-cyan-500 font-medium">Data Realtime</span>
                </div>
            </div>

            <!-- Diagram 2: Komoditas Varietas Kopi Terlaris (Market Share) -->
            <div id="best-sellers" class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span>☕ Varietas Kopi Brazil Santos Terlaris (Pangsa Pasar)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Distribusi volume tonase per jenis biji kopi spesialti yang diekspor</p>
                    </div>
                    <span class="text-xs font-bold text-cyan-600 dark:text-cyan-400">Total: {{ count($topVarieties) }} Varietas</span>
                </div>
                <div class="my-4 h-64 flex items-center justify-center">
                    <canvas id="varietyChart"></canvas>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                    <span>Terlaris #1: <strong class="text-slate-700 dark:text-slate-200">{{ $topVarieties[0]['name'] ?? 'Santos Bourbon' }}</strong></span>
                    <span class="text-emerald-500 font-bold font-mono">{{ $topVarieties[0]['share_percentage'] ?? 0 }}% Volume</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Diagram 3: Negara Tujuan Pemesan Terbanyak (Volume & Order Count) -->
            <div id="top-countries" class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span>🌍 Negara Pemesan Terbanyak (Jumlah Pengiriman)</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Frekuensi order internasional port-to-port &amp; direct last-mile roastery</p>
                    </div>
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400">{{ count($topCountries) }} Negara Tujuan</span>
                </div>
                <div class="my-4 h-64">
                    <canvas id="countryChart"></canvas>
                </div>
                <div class="flex items-center justify-between text-xs text-slate-400 pt-2 border-t border-slate-100 dark:border-slate-800/60">
                    <span>Pasar Dominan: <strong class="text-slate-700 dark:text-slate-200">{{ $topCountries[0]['country'] ?? '-' }}</strong></span>
                    <span class="text-purple-500 font-semibold">Tingkat Konsistensi Tertinggi</span>
                </div>
            </div>

            <!-- Diagram 4: Kinerja Moda Angkutan Ekspor (Laut vs Udara vs Darat) -->
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                            <span>🚢 Komposisi Moda Distribusi Kargo Internasional</span>
                        </h3>
                        <p class="text-[11px] text-slate-400">Rasio pengangkutan: Kapal Laut (Maritime), Pesawat Udara, &amp; Kurir Darat</p>
                    </div>
                </div>
                <div class="my-4 h-64 flex items-center justify-center">
                    <canvas id="transportModeChart"></canvas>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center text-xs pt-2 border-t border-slate-100 dark:border-slate-800/60">
                    <div>
                        <span class="text-slate-400 block text-[10px]">🚢 Laut (Maritime)</span>
                        <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $modeSea }} Kargo</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">✈️ Udara (Express)</span>
                        <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $modeAir }} Kargo</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">🚚 Darat (Last-Mile)</span>
                        <strong class="text-slate-800 dark:text-slate-200 font-mono">{{ $modeLand }} Armada</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================
         SEKSI 3: DATA MENTAH LENGKAP (RAW DATA TABLES)
         ========================================== -->
    <div id="raw-data-section" class="space-y-6 pt-4">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="sheet" class="w-5 h-5 text-emerald-500"></i>
                    <span>3. Data Mentah Bisnis &amp; Rekapitulasi Rinci (Raw Data Tables)</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    Data tabular lengkap yang dapat langsung dianalisis, diaudit, atau disalin untuk laporan dewan komisaris dan manajerial.
                </p>
            </div>
        </div>

        <!-- Tabel 1: Data Mentah Komoditas Varietas Kopi Terlaris -->
        <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/40">
                <div class="flex items-center gap-2">
                    <i data-lucide="coffee" class="w-4 h-4 text-cyan-500"></i>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                        Data Mentah: Peringkat Komoditas Kopi Brazil Santos Terlaris
                    </h3>
                </div>
                <span class="text-[11px] font-semibold text-slate-400">Total Tonase: {{ number_format($totalExportWeightTons, 1) }} Ton</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4 text-center w-12">Rank</th>
                            <th class="py-3 px-4 text-left">Varietas Biji Kopi Santos</th>
                            <th class="py-3 px-4 text-left">Grade SCA / Mutu</th>
                            <th class="py-3 px-4 text-right">Volume Ekspor (Kg)</th>
                            <th class="py-3 px-4 text-right">Tonase (Ton)</th>
                            <th class="py-3 px-4 text-center">Jumlah Order</th>
                            <th class="py-3 px-4 text-right">Omzet Ongkir (USD)</th>
                            <th class="py-3 px-4 text-right">Nilai Komoditas (USD)</th>
                            <th class="py-3 px-4 text-center">Pangsa Pasar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($topVarieties as $idx => $v)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-3 px-4 text-center font-bold font-mono">
                                @if($idx == 0)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 text-[10px]">🥇</span>
                                @elseif($idx == 1)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-slate-200 text-slate-800 dark:bg-slate-700 dark:text-slate-200 text-[10px]">🥈</span>
                                @elseif($idx == 2)
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-amber-800/20 text-amber-900 dark:bg-amber-950 dark:text-amber-400 text-[10px]">🥉</span>
                                @else
                                    <span class="text-slate-400">#{{ $idx + 1 }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-100">
                                {{ $v['name'] }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                                <span class="px-2 py-0.5 rounded-md text-[10px] bg-slate-100 dark:bg-slate-800 font-medium">
                                    {{ $v['grade'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-medium text-slate-700 dark:text-slate-300">
                                {{ number_format($v['total_weight_kg']) }} kg
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                {{ number_format($v['total_weight_tons'], 1) }} T
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-300 text-[10px] font-bold">
                                    {{ $v['orders_count'] }}x
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                                ${{ number_format($v['total_freight_usd'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-700 dark:text-slate-300">
                                ${{ number_format($v['total_commodity_usd'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <div class="w-12 h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                        <div class="h-full bg-cyan-500 rounded-full" style="width: {{ $v['share_percentage'] }}%"></div>
                                    </div>
                                    <span class="font-mono text-[10px] font-bold text-slate-600 dark:text-slate-300">{{ $v['share_percentage'] }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada data varietas kopi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel 2: Data Mentah Negara Pemesan Terbanyak & Tingkat Konsistensi -->
        <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/40">
                <div class="flex items-center gap-2">
                    <i data-lucide="globe" class="w-4 h-4 text-indigo-500"></i>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                        Data Mentah: Peringkat Negara Tujuan Ekspor &amp; Konsistensi Pesanan
                    </h3>
                </div>
                <span class="text-[11px] font-semibold text-slate-400">Total Negara: {{ count($topCountries) }} Destinasi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4 text-center w-12">Rank</th>
                            <th class="py-3 px-4 text-left">Negara Tujuan Ekspor</th>
                            <th class="py-3 px-4 text-left">Kota / Pelabuhan Hub</th>
                            <th class="py-3 px-4 text-center">Jumlah Pengiriman</th>
                            <th class="py-3 px-4 text-right">Total Tonase (Ton)</th>
                            <th class="py-3 px-4 text-right">Nilai Freight (USD)</th>
                            <th class="py-3 px-4 text-center">Tingkat Konsistensi</th>
                            <th class="py-3 px-4 text-center">Kategori Pasar</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($topCountries as $idx => $c)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-3 px-4 text-center font-bold font-mono">
                                #{{ $idx + 1 }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                                <span class="text-base">{{ $c['flag'] }}</span>
                                <span>{{ $c['country'] }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                                {{ $c['top_cities'] }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300 text-[10px] font-bold">
                                    {{ $c['orders_count'] }} Kontainer
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                {{ number_format($c['total_weight_tons'], 1) }} T
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                                ${{ number_format($c['total_revenue_usd'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                    ✓ {{ $c['consistency_rate'] }}% On-Track
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ str_contains($c['tier'], 'Tier A') ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/40 dark:text-cyan-300' : (str_contains($c['tier'], 'Tier B') ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300') }}">
                                    {{ $c['tier'] }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada data negara tujuan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel 3: Data Mentah Pelanggan & Roastery Paling Konsisten (Customer Loyalty) -->
        <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/70 dark:bg-slate-900/40">
                <div class="flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-purple-500"></i>
                    <h3 class="text-xs sm:text-sm font-bold text-slate-800 dark:text-slate-200">
                        Data Mentah: Roastery &amp; Pembeli B2B Paling Konsisten (Repeat Order)
                    </h3>
                </div>
                <span class="text-[11px] font-semibold text-slate-400">Total B2B Buyer: {{ count($topCustomers) }} Mitra</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                        <tr>
                            <th class="py-3 px-4 text-center w-12">Rank</th>
                            <th class="py-3 px-4 text-left">Nama Pembeli / Roastery</th>
                            <th class="py-3 px-4 text-left">Entitas Perusahaan</th>
                            <th class="py-3 px-4 text-center">Frekuensi Pesanan</th>
                            <th class="py-3 px-4 text-right">Volume Pembelian (Ton)</th>
                            <th class="py-3 px-4 text-right">Total Belanja Freight (USD)</th>
                            <th class="py-3 px-4 text-center">Kepatuhan Bayar</th>
                            <th class="py-3 px-4 text-center">Status Konsistensi</th>
                            <th class="py-3 px-4 text-right">Order Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($topCustomers as $idx => $cust)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/30 transition-colors">
                            <td class="py-3 px-4 text-center font-bold font-mono">
                                #{{ $idx + 1 }}
                            </td>
                            <td class="py-3 px-4 font-semibold text-slate-800 dark:text-slate-100">
                                {{ $cust['name'] }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 dark:text-slate-400">
                                {{ $cust['company'] }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 text-[10px] font-bold">
                                    {{ $cust['orders_count'] }}x Transaksi
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-900 dark:text-white">
                                {{ number_format($cust['total_weight_tons'], 1) }} T
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-semibold">
                                ${{ number_format($cust['total_spend_usd'], 2) }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $cust['payment_reliability'] >= 80 ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                                    {{ $cust['payment_reliability'] }}% Lunas
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold {{ str_contains($cust['repeat_status'], 'Utama') ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300' : 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300' }}">
                                    {{ $cust['repeat_status'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-400 text-[11px]">
                                {{ $cust['last_order'] }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada data pelanggan B2B.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==========================================
         SEKSI 4: KINERJA OPERASIONAL SLA & MITRA LOGISTIK
         ========================================== -->
    <div id="operational-sla" class="space-y-4 pt-6 border-t border-slate-200 dark:border-slate-800">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 flex items-center gap-2">
                <i data-lucide="timer" class="w-4 h-4 text-amber-500"></i>
                <span>4. SLA Operasional &amp; Efisiensi Rantai Pasok Port-to-Port</span>
            </h2>
            <span class="text-xs text-slate-400">Standar Internasional Ekspor Maritim</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Ketepatan Waktu</p>
                <h3 class="text-3xl font-black text-emerald-500 mt-1 font-mono">{{ $onTimeRate }}%</h3>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium mt-1 flex items-center gap-1">
                    <i data-lucide="trending-up" class="w-3 h-3"></i> Memenuhi SLA Internasional
                </span>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Durasi Rata-rata</p>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1 font-mono">{{ $avgDurationDays }} <span class="text-sm font-normal text-slate-400">hari</span></h3>
                <span class="text-[10px] text-slate-400 mt-1 block">Waktu tempuh port-to-port</span>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Tingkat Keterlambatan</p>
                <h3 class="text-3xl font-black text-amber-500 mt-1 font-mono">{{ $delayRate }}%</h3>
                <span class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 block">Rasio shipment delay</span>
            </div>

            <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Tingkat Pengecualian</p>
                <h3 class="text-3xl font-black text-rose-500 mt-1 font-mono">{{ $exceptionRate }}%</h3>
                <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 block">Hambatan rute &amp; bea cukai</span>
            </div>
        </div>

        <!-- Carrier Performance Chart -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Kinerja Ekspedisi Logistik (Volume Angkut Kontainer)</h3>
                    <p class="text-[11px] text-slate-400">Distribusi alokasi kontainer ke armada kapal Maersk, MSC, CMA CGM, dll.</p>
                </div>
            </div>
            <div class="my-4 h-64">
                <canvas id="carrierChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Palette Colors for consistent branding
        const colors = {
            cyan: '#00A8E8',
            blue: '#034078',
            emerald: '#10B981',
            amber: '#F59E0B',
            purple: '#8B5CF6',
            rose: '#EF4444',
            teal: '#14B8A6',
            palette: ['#00A8E8', '#10B981', '#8B5CF6', '#F59E0B', '#EF4444', '#3B82F6', '#14B8A6', '#EC4899']
        };

        // 1. Monthly Revenue Trend Chart (Line Area)
        const revCtx = document.getElementById('monthlyRevenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [
                    {
                        label: 'Pendapatan Freight ($ USD)',
                        data: {!! json_encode($monthlyRevenue) !!},
                        borderColor: colors.emerald,
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        fill: true,
                        tension: 0.35,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Volume Kontainer (TEU)',
                        data: {!! json_encode($monthlyVolume) !!},
                        borderColor: colors.cyan,
                        backgroundColor: 'transparent',
                        borderDash: [4, 4],
                        pointRadius: 3,
                        tension: 0.35,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { font: { size: 11 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.datasetIndex === 0) {
                                    return ' ' + ctx.dataset.label + ': $' + ctx.parsed.y.toLocaleString();
                                }
                                return ' ' + ctx.dataset.label + ': ' + ctx.parsed.y + ' TEU';
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: {
                            font: { size: 10 },
                            callback: function(v) { return '$' + (v / 1000) + 'k'; }
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            font: { size: 10 },
                            callback: function(v) { return v + ' TEU'; }
                        }
                    }
                }
            }
        });

        // 2. Best-Selling Coffee Varieties Doughnut Chart
        const varietyCtx = document.getElementById('varietyChart').getContext('2d');
        new Chart(varietyCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($varietyChartLabels) !!},
                datasets: [{
                    data: {!! json_encode($varietyChartWeights) !!},
                    backgroundColor: colors.palette,
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { font: { size: 10 }, boxWidth: 12 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.label + ': ' + ctx.parsed.toLocaleString() + ' Ton';
                            }
                        }
                    }
                },
                cutout: '62%'
            }
        });

        // 3. Top Countries Bar Chart
        const countryCtx = document.getElementById('countryChart').getContext('2d');
        new Chart(countryCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($countryChartLabels) !!},
                datasets: [{
                    label: 'Jumlah Pengiriman (Kontainer)',
                    data: {!! json_encode($countryChartOrders) !!},
                    backgroundColor: colors.purple,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' Pengiriman: ' + ctx.parsed.x + ' Kontainer';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: { font: { size: 10 }, precision: 0 }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });

        // 4. Transport Mode Polar / Doughnut Chart
        const modeCtx = document.getElementById('transportModeChart').getContext('2d');
        new Chart(modeCtx, {
            type: 'doughnut',
            data: {
                labels: ['Kapal Laut (Maritime)', 'Pesawat Udara (Express)', 'Mobil Van / Darat (Last-Mile)'],
                datasets: [{
                    data: [{{ $modeSea }}, {{ $modeAir }}, {{ $modeLand }}],
                    backgroundColor: ['#00A8E8', '#8B5CF6', '#F59E0B'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { font: { size: 10 }, boxWidth: 12 }
                    }
                },
                cutout: '58%'
            }
        });

        // 5. Carrier Performance Chart (Existing)
        const carrierCtx = document.getElementById('carrierChart').getContext('2d');
        new Chart(carrierCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($carrierLabels) !!},
                datasets: [{
                    label: 'Pengiriman Kargo',
                    data: {!! json_encode($carrierCounts) !!},
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
                    y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { font: { size: 10 }, precision: 0 } }
                }
            }
        });
    });
</script>
@endpush
