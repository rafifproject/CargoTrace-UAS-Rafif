@extends('layouts.operator')

@section('title', 'Manajemen Pengecualian')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Manajemen Pengecualian</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Tangani gangguan operasional, kendala rute, atau insiden kargo.</p>
    </div>

    <button onclick="document.getElementById('new-exception-modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold transition-colors shadow-sm">
        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
        Laporkan Pengecualian
    </button>
</div>

{{-- KPI Mini Cards (Mockup 08) --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Insiden</div>
        <div class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalExceptions }}</div>
    </div>
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="text-xs font-semibold text-rose-500 uppercase tracking-wider mb-1">Terbuka (Open)</div>
        <div class="text-2xl font-bold text-rose-500">{{ $openExceptions }}</div>
    </div>
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="text-xs font-semibold text-amber-500 uppercase tracking-wider mb-1">Diselidiki</div>
        <div class="text-2xl font-bold text-amber-500">{{ $investigating }}</div>
    </div>
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl p-4 border border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="text-xs font-semibold text-emerald-500 uppercase tracking-wider mb-1">Selesai</div>
        <div class="text-2xl font-bold text-emerald-500">{{ $resolved }}</div>
    </div>
</div>

{{-- Filter Box --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-5 shadow-sm">
    <form method="GET" action="{{ route('operator.exceptions.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Cari Nomor Pengiriman</label>
            <div class="relative">
                <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Resi..." class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
            <select name="status" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">Semua Status</option>
                <option value="OPEN" {{ request('status') === 'OPEN' ? 'selected' : '' }}>Terbuka</option>
                <option value="INVESTIGATING" {{ request('status') === 'INVESTIGATING' ? 'selected' : '' }}>Diselidiki</option>
                <option value="RESOLVED" {{ request('status') === 'RESOLVED' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="min-w-36">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Tingkat</label>
            <select name="severity" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                <option value="">Semua Tingkat</option>
                <option value="LOW" {{ request('severity') === 'LOW' ? 'selected' : '' }}>Rendah</option>
                <option value="MEDIUM" {{ request('severity') === 'MEDIUM' ? 'selected' : '' }}>Sedang</option>
                <option value="HIGH" {{ request('severity') === 'HIGH' ? 'selected' : '' }}>Tinggi</option>
                <option value="CRITICAL" {{ request('severity') === 'CRITICAL' ? 'selected' : '' }}>Kritis</option>
            </select>
        </div>
        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors">
            <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filter
        </button>
        @if(request()->hasAny(['search', 'status', 'severity']))
            <a href="{{ route('operator.exceptions.index') }}" class="px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium hover:bg-slate-100 dark:hover:bg-slate-800">Reset</a>
        @endif
    </form>
</div>

{{-- Exceptions Table (Mockup 08) --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-slate-500 dark:text-slate-400">ID / Kode</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">No. Pengiriman</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Jenis Masalah</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Tingkat</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Status</th>
                    <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Deskripsi</th>
                    <th class="text-center px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($exceptions as $ex)
                <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/40 transition-colors">
                    <td class="px-5 py-3.5 font-mono font-semibold text-slate-700 dark:text-slate-200">
                        <a href="{{ route('operator.exceptions.show', $ex->id) }}" class="text-cyan-600 dark:text-cyan-400 hover:underline">
                            EX-{{ str_pad($ex->id, 5, '0', STR_PAD_LEFT) }}
                        </a>
                    </td>
                    <td class="px-3 py-3.5">
                        <a href="{{ route('operator.shipments.show', $ex->shipment_id) }}" class="font-mono text-cyan-600 dark:text-cyan-400 hover:underline font-semibold">
                            {{ $ex->shipment?->shipment_number ?? '-' }}
                        </a>
                    </td>
                    <td class="px-3 py-3.5 font-medium text-slate-700 dark:text-slate-200">
                        {{ $ex->exception_type }}
                    </td>
                    <td class="px-3 py-3.5">
                        @php
                            $sevColors = [
                                'LOW' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                                'MEDIUM' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                                'HIGH' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300',
                                'CRITICAL' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold {{ $sevColors[$ex->severity] ?? 'bg-slate-100' }}">
                            {{ $ex->severity }}
                        </span>
                    </td>
                    <td class="px-3 py-3.5">
                        @if($ex->status === 'RESOLVED')
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Selesai
                            </span>
                        @elseif($ex->status === 'INVESTIGATING')
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-amber-500">
                                <i data-lucide="loader" class="w-3.5 h-3.5 animate-spin"></i> Diselidiki
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-rose-500">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Terbuka
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-slate-600 dark:text-slate-300 max-w-xs truncate" title="{{ $ex->description }}">
                        {{ $ex->description }}
                        @if($ex->resolution_notes)
                            <div class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-0.5 truncate">
                                <b>Solusi:</b> {{ $ex->resolution_notes }}
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-3.5 text-center">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('operator.exceptions.show', $ex->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 font-medium text-[11px] transition-colors" title="Lihat Detail Pengecualian">
                                <i data-lucide="eye" class="w-3 h-3"></i> Detail
                            </a>
                            @if($ex->status !== 'RESOLVED')
                                <button onclick="openResolveModal({{ $ex->id }}, '{{ addslashes($ex->shipment?->shipment_number ?? '') }}')" class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-medium text-[11px] transition-colors shadow-sm">
                                    <i data-lucide="check" class="w-3 h-3"></i> Selesaikan
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-slate-400">
                        <i data-lucide="shield-check" class="w-8 h-8 mx-auto mb-2 opacity-40 text-emerald-500"></i>
                        <p>Tidak ada catatan pengecualian yang cocok dengan filter.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($exceptions->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
            {{ $exceptions->links() }}
        </div>
    @endif
</div>

{{-- MODAL LAPORKAN EXCEPTION BARU --}}
<div id="new-exception-modal" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-500"></i>
                Laporkan Pengecualian Baru
            </h3>
            <button onclick="document.getElementById('new-exception-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('operator.exceptions.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Nomor Pengiriman <span class="text-rose-500">*</span></label>
                <select name="shipment_id" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 font-mono">
                    <option value="">— Pilih Pengiriman —</option>
                    @foreach($shipments as $s)
                        <option value="{{ $s->id }}">{{ $s->shipment_number }} ({{ $s->origin?->city }} → {{ $s->destination?->city }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Jenis Masalah <span class="text-rose-500">*</span></label>
                    <select name="exception_type" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200">
                        <option value="WEATHER_DELAY">Cuaca Buruk (WEATHER_DELAY)</option>
                        <option value="PORT_CONGESTION">Kepadatan Pelabuhan (PORT_CONGESTION)</option>
                        <option value="CUSTOMS_HOLD">Tertahan Pabean (CUSTOMS_HOLD)</option>
                        <option value="DAMAGED">Kerusakan Kargo (DAMAGED)</option>
                        <option value="MISROUTED">Salah Rute (MISROUTED)</option>
                        <option value="DOCUMENTATION_ISSUE">Kendala Dokumen (DOCUMENTATION_ISSUE)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Tingkat Keparahan <span class="text-rose-500">*</span></label>
                    <select name="severity" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200">
                        <option value="LOW">Rendah (Low)</option>
                        <option value="MEDIUM" selected>Sedang (Medium)</option>
                        <option value="HIGH">Tinggi (High)</option>
                        <option value="CRITICAL">Kritis (Critical)</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Deskripsi Pengecualian <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="3" required placeholder="Jelaskan detail insiden secara akurat..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('new-exception-modal').classList.add('hidden')" class="px-4 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-lg">Laporkan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL SELESAIKAN EXCEPTION --}}
<div id="resolve-modal" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500"></i>
                Selesaikan Pengecualian
            </h3>
            <button onclick="document.getElementById('resolve-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form id="resolve-form" method="POST" action="" class="space-y-4">
            @csrf
            @method('PATCH')
            <p class="text-xs text-slate-500 dark:text-slate-400">
                Penyelesaian untuk pengecualian pada kargo <span id="resolve-shipment-number" class="font-mono font-bold text-slate-800 dark:text-white"></span>:
            </p>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Catatan Tindakan Penyelesaian <span class="text-rose-500">*</span></label>
                <textarea name="resolution_notes" rows="4" required placeholder="Tuliskan langkah mitigasi atau hasil penyelesaian yang telah dilakukan..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('resolve-modal').classList.add('hidden')" class="px-4 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg">Konfirmasi Selesai</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openResolveModal(id, shipmentNumber) {
    const form = document.getElementById('resolve-form');
    form.action = `/operator/exceptions/${id}/resolve`;
    document.getElementById('resolve-shipment-number').textContent = shipmentNumber;
    document.getElementById('resolve-modal').classList.remove('hidden');
}
</script>
@endpush
