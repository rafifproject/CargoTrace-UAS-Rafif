@extends('layouts.admin')

@section('title', 'Pusat Pengecualian (Exception Overview)')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Pengecualian (Exception Overview)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pemantauan dan mitigasi kendala operasional, cuaca, regulasi pabean, dan hambatan rute</p>
        </div>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Total Pengecualian</p>
                <h3 class="text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $totalCount }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-500 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Status: Terbuka</p>
                <h3 class="text-2xl font-black text-rose-500 mt-1">{{ $openCount }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-500 flex items-center justify-center">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Investigasi</p>
                <h3 class="text-2xl font-black text-amber-500 mt-1">{{ $investigatingCount }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-500 flex items-center justify-center">
                <i data-lucide="search" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase">Terselesaikan</p>
                <h3 class="text-2xl font-black text-emerald-500 mt-1">{{ $resolvedCount }}</h3>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-500 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Donut Chart Exception Types -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Pengecualian per Jenis</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Proporsi penyebab keterlambatan dan risiko</p>
            </div>
            <div class="relative my-4 flex items-center justify-center h-48">
                <canvas id="excTypeChart"></canvas>
            </div>
            <div class="grid grid-cols-2 gap-2 text-xs pt-3 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span> Cuaca ({{ $typesCount['WEATHER_DELAY'] }})</div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Bea Cukai ({{ $typesCount['CUSTOMS_HOLD'] }})</div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> Kongesti ({{ $typesCount['PORT_CONGESTION'] }})</div>
                <div class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Kerusakan ({{ $typesCount['DAMAGED'] }})</div>
            </div>
        </div>

        <!-- Trend Line Chart -->
        <div class="p-5 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm lg:col-span-2 flex flex-col justify-between">
            <div>
                <h2 class="text-sm font-bold text-slate-900 dark:text-white">Tren Pengecualian (30 Hari)</h2>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">Pola frekuensi munculnya issue pengiriman bulanan</p>
            </div>
            <div class="my-4 h-56">
                <canvas id="excTrendChart"></canvas>
            </div>
            <div class="text-xs text-slate-400 pt-3 border-t border-slate-100 dark:border-slate-800">
                Data dihimpun dari laporan operator pelabuhan & sistem telemetri logistik.
            </div>
        </div>
    </div>

    <!-- Register Table -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="text-sm font-bold text-slate-900 dark:text-white">Daftar Pengecualian Kargo (Exception Register)</h2>
            <form method="GET" action="{{ route('admin.exceptions.index') }}" class="flex items-center gap-2">
                <select name="status" onchange="this.form.submit()" class="px-2.5 py-1 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-700 dark:text-slate-200">
                    <option value="">Semua Status</option>
                    <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>OPEN</option>
                    <option value="INVESTIGATING" {{ request('status') === 'INVESTIGATING' ? 'selected' : '' }}>INVESTIGATING</option>
                    <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>RESOLVED</option>
                </select>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">No. Pengiriman</th>
                        <th class="py-3 px-4">Jenis Masalah</th>
                        <th class="py-3 px-4">Tingkat</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Deskripsi Hambatan</th>
                        <th class="py-3 px-4">Pelapor</th>
                        <th class="py-3 px-4 text-center">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($exceptions as $exc)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-600 dark:text-cyan-400">
                                <a href="{{ route('admin.shipments.show', $exc->shipment_id) }}" class="hover:underline">
                                    {{ $exc->shipment->shipment_number ?? '-' }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-800 dark:text-white">
                                {{ $exc->exception_type }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $exc->severity === 'CRITICAL' || $exc->severity === 'HIGH' ? 'bg-rose-500/10 text-rose-500' : 'bg-amber-500/10 text-amber-500' }}">
                                    {{ $exc->severity }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $exc->status === 'RESOLVED' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-rose-500/10 text-rose-500' }}">
                                    {{ $exc->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 max-w-xs text-slate-600 dark:text-slate-300">
                                <p class="line-clamp-2">{{ $exc->description }}</p>
                                @if($exc->resolution_notes)
                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 italic">Solusi: {{ $exc->resolution_notes }}</p>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400">
                                {{ $exc->reporter->name ?? 'Sistem' }}
                                <span class="block text-[10px]">{{ $exc->created_at->format('d M, H:i') }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($exc->isOpen())
                                    <button onclick="openResolveModal('{{ $exc->id }}', '{{ $exc->shipment->shipment_number }}')" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-colors">
                                        Selesaikan
                                    </button>
                                @else
                                    <span class="text-[10px] text-emerald-500 font-semibold flex items-center justify-center gap-1">
                                        <i data-lucide="check" class="w-3 h-3"></i> Tuntas
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada pengecualian yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($exceptions->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $exceptions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Resolve Exception -->
<div id="resolveModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 shadow-2xl">
        <h3 class="text-base font-bold text-slate-900 dark:text-white">Tandai Pengecualian Selesai</h3>
        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Kargo: <strong id="modalShipmentNumber" class="text-cyan-500"></strong>
        </p>
        <form id="resolveForm" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label for="resolution_notes" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Catatan Tindakan / Resolusi</label>
                <textarea id="resolution_notes" name="resolution_notes" rows="3" required placeholder="Contoh: Dokumen perizinan impor telah diverifikasi dan kontainer diizinkan melanjutkan perjalanan..." class="w-full p-2.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2">
                <button type="button" onclick="closeResolveModal()" class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-500 transition-colors">
                    Simpan & Selesaikan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Donut Chart
        const excCtx = document.getElementById('excTypeChart').getContext('2d');
        new Chart(excCtx, {
            type: 'doughnut',
            data: {
                labels: ['Cuaca', 'Bea Cukai', 'Kongesti', 'Kerusakan', 'Lainnya'],
                datasets: [{
                    data: [
                        {{ $typesCount['WEATHER_DELAY'] }},
                        {{ $typesCount['CUSTOMS_HOLD'] }},
                        {{ $typesCount['PORT_CONGESTION'] }},
                        {{ $typesCount['DAMAGED'] }},
                        {{ $typesCount['DOCUMENTATION_ISSUE'] + $typesCount['MISROUTED'] }}
                    ],
                    backgroundColor: ['#00A8E8', '#F59E0B', '#3B82F6', '#EF4444', '#94A3B8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '70%'
            }
        });

        // Trend Chart
        const trendCtx = document.getElementById('excTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendDates) !!},
                datasets: [{
                    label: 'Pengecualian Baru',
                    data: {!! json_encode($trendValues) !!},
                    borderColor: '#EF4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.3
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

    function openResolveModal(id, number) {
        document.getElementById('modalShipmentNumber').textContent = number;
        document.getElementById('resolveForm').action = '/admin/exceptions/' + id + '/resolve';
        document.getElementById('resolveModal').classList.remove('hidden');
    }

    function closeResolveModal() {
        document.getElementById('resolveModal').classList.add('hidden');
    }
</script>
@endpush

