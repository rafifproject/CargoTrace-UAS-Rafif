@extends('layouts.operator')

@section('title', 'Detail Pengiriman – ' . $shipment->shipment_number)

@section('content')
{{-- Breadcrumbs & Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
            <a href="{{ route('operator.shipments.index') }}" class="hover:text-cyan-500">Semua Pengiriman</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="font-mono text-slate-700 dark:text-slate-300">{{ $shipment->shipment_number }}</span>
        </div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold font-mono text-slate-800 dark:text-white">{{ $shipment->shipment_number }}</h1>
            @php
                $statusMap = ['CREATED' => ['Dibuat','slate'], 'BOOKED' => ['Dipesan','amber'], 'PICKED_UP' => ['Diambil','purple'], 'IN_TRANSIT' => ['Dalam Perjalanan','blue'], 'DELIVERED' => ['Terkirim','emerald']];
                [$statusLabel, $statusColor] = $statusMap[$shipment->status] ?? [$shipment->status, 'slate'];
            @endphp
            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-700 dark:bg-{{ $statusColor }}-900/40 dark:text-{{ $statusColor }}-300">{{ $statusLabel }}</span>
            @if(($shipment->transport_mode ?? 'SEA') === 'AIR')
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300 flex items-center gap-1">
                    ✈️ Udara (Air Cargo)
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 flex items-center gap-1">
                    🚢 Laut (Maritime)
                </span>
            @endif
            @if($shipment->delay_minutes > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Terlambat +{{ round($shipment->delay_minutes/60) }} jam</span>
            @endif
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('operator.shipments.edit', $shipment) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="pencil" class="w-3.5 h-3.5"></i> Edit Status
        </a>
        <button onclick="document.getElementById('event-modal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold transition-colors shadow-sm">
            <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i> Catat Event Tracking
        </button>
    </div>
</div>

{{-- Stepper Component (Mockup 05) --}}
@php
    $steps = [
        'CREATED' => ['Dibuat', 'box'],
        'BOOKED' => ['Dipesan', 'calendar'],
        'PICKED_UP' => ['Diambil', 'truck'],
        'IN_TRANSIT' => ['Dalam Perjalanan', 'navigation'],
        'DELIVERED' => ['Terkirim', 'check-circle-2'],
    ];
    $statusOrder = array_keys($steps);
    $currentIndex = array_search($shipment->status, $statusOrder);
    if ($currentIndex === false) $currentIndex = 0;
@endphp
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 mb-6 shadow-sm">
    <div class="relative flex items-center justify-between">
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 w-full bg-slate-200 dark:bg-slate-800 z-0"></div>
        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-0.5 bg-cyan-500 z-0 transition-all duration-500" style="width: {{ ($currentIndex / (count($steps) - 1)) * 100 }}%"></div>
        @foreach($steps as $key => [$label, $icon])
            @php
                $stepIndex = array_search($key, $statusOrder);
                $isCompleted = $stepIndex < $currentIndex;
                $isCurrent = $stepIndex === $currentIndex;
            @endphp
            <div class="relative z-10 flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs transition-colors {{ $isCompleted ? 'bg-cyan-600 text-white' : ($isCurrent ? 'bg-cyan-500 text-white ring-4 ring-cyan-500/20 shadow-md' : 'bg-slate-100 dark:bg-slate-800 text-slate-400') }}">
                    <i data-lucide="{{ $icon }}" class="w-4 h-4"></i>
                </div>
                <span class="mt-2 text-[11px] font-medium {{ $isCurrent ? 'text-cyan-600 dark:text-cyan-400 font-bold' : ($isCompleted ? 'text-slate-700 dark:text-slate-300' : 'text-slate-400') }}">{{ $label }}</span>
            </div>
        @endforeach
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="flex gap-2 border-b border-slate-200 dark:border-slate-800 mb-6 text-sm">
    <button onclick="switchTab('info')" id="tab-btn-info" class="tab-btn active px-4 py-2.5 font-semibold text-cyan-600 dark:text-cyan-400 border-b-2 border-cyan-500">Informasi Umum</button>
    <button onclick="switchTab('tracking')" id="tab-btn-tracking" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Riwayat Pelacakan ({{ $shipment->trackingEvents->count() }})</button>
    <button onclick="switchTab('map')" id="tab-btn-map" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Peta Rute</button>
    <button onclick="switchTab('exceptions')" id="tab-btn-exceptions" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Pengecualian ({{ $shipment->exceptions->count() }})</button>
</div>

{{-- TAB 1: INFORMASI UMUM --}}
<div id="tab-info" class="tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Info Ringkas --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Informasi Kargo & Pelanggan</h3>
                <div class="grid grid-cols-2 gap-y-4 text-xs">
                    <div>
                        <span class="text-slate-400">Pelanggan</span>
                        <p class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->customer?->name ?? '-' }}</p>
                        <p class="text-slate-400">{{ $shipment->customer?->email ?? '' }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Ekspedisi</span>
                        <p class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->carrier?->name ?? '-' }} ({{ $shipment->carrier?->code ?? '-' }})</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Jenis Kargo</span>
                        <p class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->cargo_type }}</p>
                    </div>
                    <div>
                        <span class="text-slate-400">Berat & Volume</span>
                        <p class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ number_format($shipment->weight, 2) }} kg / {{ number_format($shipment->volume, 2) }} m³</p>
                    </div>
                </div>
            </div>

            {{-- Detail Item --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Daftar Item Kargo</h3>
                <table class="w-full text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-900/50">
                        <tr>
                            <th class="text-left px-3 py-2 text-slate-500 font-semibold">Nama Item</th>
                            <th class="text-left px-3 py-2 text-slate-500 font-semibold">HS Code</th>
                            <th class="text-left px-3 py-2 text-slate-500 font-semibold">Jumlah</th>
                            <th class="text-right px-3 py-2 text-slate-500 font-semibold">Nilai Pabean</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($shipment->items as $item)
                        <tr>
                            <td class="px-3 py-2.5 font-medium text-slate-700 dark:text-slate-200">{{ $item->item_name }}</td>
                            <td class="px-3 py-2.5 font-mono text-slate-500">{{ $item->hs_code ?? '-' }}</td>
                            <td class="px-3 py-2.5 text-slate-600 dark:text-slate-300">{{ $item->quantity }} pcs</td>
                            <td class="px-3 py-2.5 text-right font-mono text-slate-700 dark:text-slate-200">${{ number_format($item->declared_value, 2) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="px-3 py-4 text-center text-slate-400">Tidak ada rincian item.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sched & Rute --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Jadwal & Rute</h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400">Asal</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->origin?->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $shipment->origin?->city }}, {{ $shipment->origin?->country }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Tujuan</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->destination?->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $shipment->destination?->city }}, {{ $shipment->destination?->country }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Lokasi Saat Ini</span>
                        <div class="font-semibold text-cyan-600 dark:text-cyan-400 mt-0.5">{{ $shipment->currentLocation?->name ?? 'Belum ditentukan' }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Estimasi Tiba (ETA)</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->estimated_time_arrival ? \Carbon\Carbon::parse($shipment->estimated_time_arrival)->format('d M Y, H:i') : '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TAB 2: RIWAYAT PELACAKAN (Timeline) --}}
<div id="tab-tracking" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Timeline Riwayat Pelacakan</h3>
            <button onclick="document.getElementById('event-modal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold">
                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Catat Event
            </button>
        </div>
        <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
            @forelse($shipment->trackingEvents as $event)
            <div class="relative">
                <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-cyan-500 border-2 border-white dark:border-[#0A1128]"></span>
                <div class="text-xs">
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-slate-800 dark:text-slate-100">{{ $event->event_type }}</span>
                        <span class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($event->event_time)->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-slate-600 dark:text-slate-300 mt-1">{{ $event->description }}</p>
                    <div class="flex items-center gap-3 mt-1.5 text-[11px] text-slate-400">
                        @if($event->location)
                            <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3 h-3 text-cyan-500"></i> {{ $event->location->name }}</span>
                        @endif
                        @if($event->creator)
                            <span class="flex items-center gap-1"><i data-lucide="user" class="w-3 h-3"></i> {{ $event->creator->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400">Belum ada event pelacakan.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- TAB 3: PETA RUTE --}}
<div id="tab-map" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Peta Visualisasi Rute</h3>
        <div id="shipment-detail-map" style="height: 420px; border-radius: 12px; background: #0A1128;"></div>
    </div>
</div>

{{-- TAB 4: PENGECUALIAN --}}
<div id="tab-exceptions" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Daftar Pengecualian pada Pengiriman Ini</h3>
            <button onclick="document.getElementById('exception-modal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold">
                <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Laporkan Masalah
            </button>
        </div>
        <div class="space-y-3">
            @forelse($shipment->exceptions as $ex)
            <div class="p-4 rounded-xl border {{ $ex->severity === 'CRITICAL' ? 'bg-rose-50 border-rose-200 dark:bg-rose-950/20 dark:border-rose-800/40' : 'bg-amber-50 border-amber-200 dark:bg-amber-950/20 dark:border-amber-800/40' }} text-xs">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="font-bold text-slate-800 dark:text-slate-100">{{ $ex->exception_type }}</span>
                        <span class="ml-2 px-1.5 py-0.5 rounded text-[10px] font-bold {{ $ex->severity === 'CRITICAL' ? 'bg-rose-200 text-rose-800 dark:bg-rose-950 dark:text-rose-200' : 'bg-amber-200 text-amber-800 dark:bg-amber-950 dark:text-amber-200' }}">{{ $ex->severity }}</span>
                    </div>
                    <span class="font-bold uppercase text-[10px] {{ $ex->status === 'RESOLVED' ? 'text-emerald-600' : 'text-amber-600' }}">{{ $ex->status }}</span>
                </div>
                <p class="text-slate-600 dark:text-slate-300 mt-2">{{ $ex->description }}</p>
                @if($ex->resolution_notes)
                    <div class="mt-2 pt-2 border-t border-slate-200 dark:border-slate-800 text-[11px] text-emerald-700 dark:text-emerald-400">
                        <b>Solusi:</b> {{ $ex->resolution_notes }}
                    </div>
                @endif
            </div>
            @empty
            <p class="text-xs text-slate-400 py-4 text-center">Tidak ada pengecualian pada pengiriman ini.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- MODAL: Catat Tracking Event --}}
<div id="event-modal" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Catat Event Pelacakan</h3>
            <button onclick="document.getElementById('event-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form method="POST" action="{{ route('operator.shipments.tracking.store', $shipment) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Tipe Event <span class="text-rose-500">*</span></label>
                <select name="event_type" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200">
                    <option value="STATUS_CHANGE">Perubahan Status</option>
                    <option value="CHECKPOINT_REACHED">Checkpoint Tercapai</option>
                    <option value="DEPARTURE">Keberangkatan</option>
                    <option value="ARRIVAL">Kedatangan</option>
                    <option value="CUSTOMS_CLEARED">Pabean Selesai</option>
                    <option value="EXCEPTION_NOTED">Catatan Masalah</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Lokasi</label>
                <select name="location_id" class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200">
                    <option value="">— Pilih Lokasi Terdaftar —</option>
                    @foreach($locations as $l)
                        <option value="{{ $l->id }}">{{ $l->name }} ({{ $l->country }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Waktu Event <span class="text-rose-500">*</span></label>
                    <input type="datetime-local" name="event_time" value="{{ now()->format('Y-m-d\TH:i') }}" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Status Kargo Saat Ini <span class="text-rose-500">*</span></label>
                    <select name="shipment_status_at_event" required class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200">
                        <option value="CREATED" {{ $shipment->status === 'CREATED' ? 'selected' : '' }}>Dibuat</option>
                        <option value="BOOKED" {{ $shipment->status === 'BOOKED' ? 'selected' : '' }}>Dipesan</option>
                        <option value="PICKED_UP" {{ $shipment->status === 'PICKED_UP' ? 'selected' : '' }}>Diambil</option>
                        <option value="IN_TRANSIT" {{ $shipment->status === 'IN_TRANSIT' ? 'selected' : '' }}>Dalam Perjalanan</option>
                        <option value="DELIVERED" {{ $shipment->status === 'DELIVERED' ? 'selected' : '' }}>Terkirim</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">Deskripsi Catatan Event <span class="text-rose-500">*</span></label>
                <textarea name="description" rows="3" required placeholder="cth. Kontainer telah dimuat ke kapal CMA CGM Voltaire di dermaga 3." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('event-modal').classList.add('hidden')" class="px-4 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs bg-cyan-600 hover:bg-cyan-500 text-white font-semibold rounded-lg">Simpan Event</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: Laporkan Exception --}}
<div id="exception-modal" class="fixed inset-0 z-50 bg-black/60 flex items-center justify-center p-4 hidden">
    <div class="bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 rounded-2xl max-w-lg w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-slate-800 dark:text-white">Laporkan Pengecualian</h3>
            <button onclick="document.getElementById('exception-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <form method="POST" action="{{ route('operator.exceptions.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="shipment_id" value="{{ $shipment->id }}">
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
                <textarea name="description" rows="3" required placeholder="Jelaskan detail insiden atau hambatan secara rinci..." class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('exception-modal').classList.add('hidden')" class="px-4 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs bg-rose-600 hover:bg-rose-500 text-white font-semibold rounded-lg">Laporkan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(el => {
        el.classList.remove('active', 'border-b-2', 'border-cyan-500', 'text-cyan-600', 'dark:text-cyan-400');
        el.classList.add('text-slate-500', 'dark:text-slate-400');
    });

    const activeContent = document.getElementById(`tab-${name}`);
    const activeBtn = document.getElementById(`tab-btn-${name}`);
    if (activeContent) activeContent.classList.remove('hidden');
    if (activeBtn) {
        activeBtn.classList.add('active', 'border-b-2', 'border-cyan-500', 'text-cyan-600', 'dark:text-cyan-400');
        activeBtn.classList.remove('text-slate-500', 'dark:text-slate-400');
    }

    if (name === 'map') {
        setTimeout(initDetailMap, 100);
    }
}

let mapInitialized = false;
function initDetailMap() {
    if (mapInitialized) return;
    mapInitialized = true;

    const oLat = {{ $shipment->origin?->latitude ?? 0 }};
    const oLng = {{ $shipment->origin?->longitude ?? 0 }};
    const dLat = {{ $shipment->destination?->latitude ?? 0 }};
    const dLng = {{ $shipment->destination?->longitude ?? 0 }};

    const map = L.map('shipment-detail-map', { maxZoom: 20 }).setView([oLat, oLng], 4);

    const googleStreets = L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
    });
    const googleHybrid = L.tileLayer('https://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps Satelit'
    });
    const osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    });

    googleStreets.addTo(map);

    L.control.layers({
        "🗺️ Google Maps": googleStreets,
        "🛰️ Satelit Hybrid": googleHybrid,
        "🌍 OpenStreetMap": osm
    }, null, { position: 'topright' }).addTo(map);

    L.circleMarker([oLat, oLng], { radius: 8, color: '#ffffff', fillColor: '#10b981', fillOpacity: 1, weight: 2.5 })
        .addTo(map).bindPopup('<b>🟢 Titik Asal:</b> {{ $shipment->origin?->name }}<br><span style="font-size:11px; color:#64748b;">{{ $shipment->origin?->city }}, {{ $shipment->origin?->country }}</span>');
    L.circleMarker([dLat, dLng], { radius: 8, color: '#ffffff', fillColor: '#f43f5e', fillOpacity: 1, weight: 2.5 })
        .addTo(map).bindPopup('<b>🔴 Titik Tujuan:</b> {{ $shipment->destination?->name }}<br><span style="font-size:11px; color:#64748b;">{{ $shipment->destination?->city }}, {{ $shipment->destination?->country }}</span>');

    @if($shipment->currentLocation)
        const cLat = {{ $shipment->currentLocation->latitude }};
        const cLng = {{ $shipment->currentLocation->longitude }};

        L.polyline([[oLat, oLng], [cLat, cLng]], { color: '#00F0FF', weight: 4.5, opacity: 0.95 }).addTo(map);
        L.polyline([[cLat, cLng], [dLat, dLng]], { color: '#38BDF8', weight: 3.5, dashArray: '6, 8', opacity: 0.85 }).addTo(map);

        const vIcon = L.divIcon({
            className: 'cargo-v-icon',
            html: `
                <div style="background:#0284c7; color:#fff; width:34px; height:34px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:16px; box-shadow:0 0 14px rgba(0,240,255,0.9); border:2.5px solid #fff;">
                    🚢
                </div>
            `,
            iconSize: [34, 34],
            iconAnchor: [17, 17]
        });

        L.marker([cLat, cLng], { icon: vIcon })
            .addTo(map)
            .bindPopup('<b>📍 Lokasi Terkini:</b> {{ $shipment->currentLocation->name }}<br><span style="font-size:11px; color:#0284c7; font-weight:700;">Status: {{ $shipment->status }}</span>')
            .openPopup();
        map.fitBounds([[oLat, oLng], [dLat, dLng], [cLat, cLng]], { padding: [50, 50] });
    @else
        L.polyline([[oLat, oLng], [dLat, dLng]], { color: '#00F0FF', weight: 3.5, dashArray: '6,6' }).addTo(map);
        map.fitBounds([[oLat, oLng], [dLat, dLng]], { padding: [50, 50] });
    @endif
}
</script>
@endpush
