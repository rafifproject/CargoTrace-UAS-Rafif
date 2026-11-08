@extends('layouts.admin')

@section('title', 'Analitik Operasional')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Analitik Operasional (Operational Analytics)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Analisis performa rantai pasok global, rasio ketepatan waktu, dan efisiensi mitra ekspedisi</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-semibold px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                Periode: Tahun Berjalan
            </span>
        </div>
    </div>

    <!-- 4 Key Performance Metrics -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Ketepatan Waktu</p>
            <h3 class="text-3xl font-black text-emerald-500 mt-1">{{ $onTimeRate }}%</h3>
            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-medium mt-1 flex items-center gap-1">
                <i data-lucide="trending-up" class="w-3 h-3"></i> Memenuhi SLA Internasional
            </span>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Durasi Rata-rata</p>
            <h3 class="text-3xl font-black text-slate-900 dark:text-white mt-1">{{ $avgDurationDays }} <span class="text-sm font-normal text-slate-400">hari</span></h3>
            <span class="text-[10px] text-slate-400 mt-1 block">Waktu tempuh port-to-port</span>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Tingkat Keterlambatan</p>
            <h3 class="text-3xl font-black text-amber-500 mt-1">{{ $delayRate }}%</h3>
            <span class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 block">Rasio shipment delay</span>
        </div>

        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Tingkat Pengecualian</p>
            <h3 class="text-3xl font-black text-rose-500 mt-1">{{ $exceptionRate }}%</h3>
            <span class="text-[10px] text-rose-600 dark:text-rose-400 mt-1 block">Hambatan rute & bea cukai</span>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Carrier Performance Chart -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Kinerja Ekspedisi (Jumlah Pengiriman)</h2>
                    <p class="text-[11px] text-slate-400">Total volume kontainer yang diangkut masing-masing mitra logistik</p>
                </div>
            </div>
            <div class="my-4 h-64">
                <canvas id="carrierChart"></canvas>
            </div>
        </div>

        <!-- Monthly Volume Trend Chart -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tren Volume Pengiriman Bulanan</h2>
                    <p class="text-[11px] text-slate-400">Pertumbuhan frekuensi pengiriman internasional kargo</p>
                </div>
            </div>
            <div class="my-4 h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Carrier Chart
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

        // Monthly Volume Chart
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($months) !!},
                datasets: [{
                    label: 'Volume (Kontainer)',
                    data: {!! json_encode($monthlyVolume) !!},
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.35,
                    pointRadius: 3
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

