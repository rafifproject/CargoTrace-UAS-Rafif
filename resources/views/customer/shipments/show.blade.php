@extends('layouts.customer')

@section('title', 'Detail Pengiriman – ' . $shipment->shipment_number)

@push('styles')
<style>
    /* Realistic Vehicle Markers for Cargo Tracker */
    .cargo-real-vehicle-marker {
        background: transparent !important;
        border: none !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
    }
    .cargo-real-vehicle-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.5));
    }
    .cargo-real-vehicle-marker:hover .cargo-real-vehicle-wrapper {
        filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.7)) drop-shadow(0 0 10px rgba(56, 189, 248, 0.8));
        transform: scale(1.22) !important;
        z-index: 99999 !important;
    }
    .cargo-vehicle-tag {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
        padding: 2px 7px;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 6px;
        color: #ffffff;
        font-family: 'Outfit', sans-serif;
        font-weight: 800;
        font-size: 10px;
        line-height: 1.2;
        white-space: nowrap;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.4);
        pointer-events: none;
    }
    .cargo-tag-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        display: inline-block;
    }
</style>
@endpush

@section('content')
{{-- Breadcrumbs & Header (Mockup 05) --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
            <a href="{{ route('customer.shipments.index') }}" class="hover:text-cyan-500">Pengiriman Saya</a>
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
                    ✈️ Kargo Udara Kilat (Air Express)
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300 flex items-center gap-1">
                    🚢 Multimoda Samudra + Pesawat Domestik
                </span>
            @endif
            @if($shipment->delay_minutes > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">Terlambat +{{ round($shipment->delay_minutes/60) }} jam</span>
            @endif
            @if($shipment->payment_status === 'PAID')
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 flex items-center gap-1">
                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Invoice Lunas (Settled)
                </span>
            @else
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300 flex items-center gap-1">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu Pembayaran
                </span>
            @endif
        </div>
    </div>
</div>

{{-- Stepper Progress Component (Mockup 05) --}}
@php
    $steps = [
        'CREATED'    => ['Dibuat', 'box'],
        'BOOKED'     => ['Diproses', 'calendar'],
        'PICKED_UP'  => ['Dikirim', 'truck'],
        'IN_TRANSIT' => ['Dalam Perjalanan', 'navigation'],
        'DELIVERED'  => ['Terkirim', 'check-circle-2'],
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
    <button onclick="switchTab('info')" id="tab-btn-info" class="tab-btn active px-4 py-2.5 font-semibold text-cyan-600 dark:text-cyan-400 border-b-2 border-cyan-500">Ringkasan</button>
    <button onclick="switchTab('tracking')" id="tab-btn-tracking" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Riwayat Pelacakan ({{ $shipment->trackingEvents->count() }})</button>
    <button onclick="switchTab('map')" id="tab-btn-map" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Peta Rute</button>
    <button onclick="switchTab('exceptions')" id="tab-btn-exceptions" class="tab-btn px-4 py-2.5 font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200">Kendala ({{ $shipment->exceptions->count() }})</button>
</div>

{{-- TAB 1: RINGKASAN --}}
<div id="tab-info" class="tab-content">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            {{-- Informasi Pengiriman --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Informasi Pengiriman</h3>
                <div class="grid grid-cols-2 gap-y-4 text-xs">
                    <div>
                        <span class="text-slate-400">Penyedia Ekspedisi</span>
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
                    <div>
                        <span class="text-slate-400">Instruksi Khusus</span>
                        <p class="text-slate-600 dark:text-slate-300 mt-0.5">{{ $shipment->notes ?: 'Tidak ada instruksi khusus' }}</p>
                    </div>
                </div>
            </div>

            {{-- Detail Item Kargo --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Rincian Barang Kargo</h3>
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

        {{-- Sisi Kanan: Jadwal, Lokasi & Proforma Invoice B2B --}}
        <div class="space-y-6">
            {{-- Proforma Invoice & Settlement Card (B2B Coffee Export) --}}
            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border {{ $shipment->payment_status === 'PAID' ? 'border-emerald-200 dark:border-emerald-900/50' : 'border-amber-200 dark:border-amber-900/50 ring-1 ring-amber-500/20' }} p-5 shadow-sm">
                <div class="flex items-center justify-between mb-3.5">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Proforma Invoice B2B</span>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white font-mono mt-0.5">{{ $shipment->invoice_number ?? 'INV-2026-' . str_pad($shipment->id, 4, '0', STR_PAD_LEFT) }}</h3>
                    </div>
                    @if($shipment->payment_status === 'PAID')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                            LUNAS (SETTLED) ✓
                        </span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800 animate-pulse">
                            UNPAID 💳
                        </span>
                    @endif
                </div>

                <div class="space-y-2.5 text-xs pb-3 border-b border-slate-100 dark:border-slate-800">
                    <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                        <span>Nilai Komoditas (Biji Kopi Santos)</span>
                        <span class="font-mono font-semibold">${{ number_format($shipment->total_commodity_value, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600 dark:text-slate-300">
                        <span>Tarif Freight & Handling Ekspor</span>
                        <span class="font-mono font-semibold">${{ number_format($shipment->shipping_cost, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-dashed border-slate-200 dark:border-slate-700">
                        <span class="font-bold text-slate-800 dark:text-white">Total Tagihan (USD)</span>
                        <span class="font-mono font-extrabold text-sm text-cyan-600 dark:text-cyan-400">${{ number_format($shipment->total_invoice_amount, 2) }}</span>
                    </div>
                </div>

                @if($shipment->payment_status === 'PAID')
                    <div class="mt-3 bg-emerald-50/60 dark:bg-emerald-950/20 rounded-xl p-3 border border-emerald-100 dark:border-emerald-900/40 text-[11px] space-y-1.5">
                        <div class="flex items-center gap-1.5 text-emerald-700 dark:text-emerald-300 font-semibold">
                            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                            <span>Settlement Dana Terkonfirmasi</span>
                        </div>
                        <div class="text-slate-500 dark:text-slate-400 text-[10px]">
                            Metode: <strong class="text-slate-700 dark:text-slate-200">{{ $shipment->payment_method ?? 'T/T Bank Wire (SWIFT Transfer)' }}</strong>
                        </div>
                        <div class="text-slate-500 dark:text-slate-400 text-[10px]">
                            Waktu Verifikasi: <strong class="text-slate-700 dark:text-slate-200">{{ $shipment->paid_at ? \Carbon\Carbon::parse($shipment->paid_at)->format('d M Y, H:i') : '-' }}</strong>
                        </div>
                    </div>
                    <button onclick="window.print()" class="mt-3 w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs font-semibold flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="printer" class="w-3.5 h-3.5"></i> Cetak / Simpan Proforma Invoice
                    </button>
                @else
                    {{-- Instruksi Bank Wire SWIFT --}}
                    <div class="mt-3 bg-slate-50 dark:bg-slate-900/60 rounded-xl p-3 border border-slate-100 dark:border-slate-800 text-[10px] space-y-1">
                        <span class="font-bold text-slate-700 dark:text-slate-300 block mb-1">🏛️ Rekening Penerima Ekspor Santos (T/T via SWIFT):</span>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Beneficiary:</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Santos Coffee Exporters S.A.</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Bank:</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200">Banco do Brasil S.A. (São Paulo)</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>SWIFT / BIC:</span>
                            <span class="font-mono font-bold text-slate-800 dark:text-white">BRASBRRJSPO</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>IBAN / A/C:</span>
                            <span class="font-mono text-slate-700 dark:text-slate-300">BR82 0000 0012 3456 7890 1234</span>
                        </div>
                        <div class="flex justify-between text-slate-500 dark:text-slate-400">
                            <span>Reference:</span>
                            <span class="font-mono text-cyan-600 dark:text-cyan-400 font-bold">{{ $shipment->invoice_number }}</span>
                        </div>
                    </div>

                    {{-- Form Simulasi Pembayaran B2B --}}
                    <form method="POST" action="{{ route('customer.shipments.pay', $shipment->id) }}" class="mt-3.5" onsubmit="return confirm('Konfirmasi simulasi pembayaran T/T Bank Wire untuk Invoice {{ $shipment->invoice_number }} senilai ${{ number_format($shipment->total_invoice_amount, 2) }}?');">
                        @csrf
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-md transition-all flex items-center justify-center gap-2">
                            <i data-lucide="check-check" class="w-4 h-4"></i>
                            <span>Simulasikan Pembayaran Bank Wire (T/T)</span>
                        </button>
                    </form>
                    <p class="text-[9px] text-slate-400 text-center mt-1.5 italic">
                        * Simulasi pelunasan invoice ekspor B2B internasional. Tidak ada pemotongan kartu komersial nyata.
                    </p>
                @endif
            </div>

            <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Waktu & Lokasi Perjalanan</h3>
                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400">Pelabuhan / Bandara Asal</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->origin?->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $shipment->origin?->city }}, {{ $shipment->origin?->country }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Pelabuhan / Bandara Tujuan</span>
                        <div class="font-semibold text-slate-800 dark:text-slate-100 mt-0.5">{{ $shipment->destination?->name }}</div>
                        <div class="text-[11px] text-slate-400">{{ $shipment->destination?->city }}, {{ $shipment->destination?->country }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Posisi Kargo Terkini</span>
                        <div class="font-semibold text-cyan-600 dark:text-cyan-400 mt-0.5">{{ $shipment->currentLocation?->name ?? 'Dalam perjalanan' }}</div>
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-2">
                        <span class="text-slate-400">Estimasi Kedatangan (ETA)</span>
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
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-6">Riwayat Checkpoint Pengiriman</h3>
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
                    @if($event->location)
                        <div class="flex items-center gap-1 mt-1 text-[11px] text-slate-400">
                            <i data-lucide="map-pin" class="w-3 h-3 text-cyan-500"></i> {{ $event->location->name }}, {{ $event->location->country }}
                        </div>
                    @endif
                </div>
            </div>
            @empty
            <p class="text-xs text-slate-400 py-4">Belum ada riwayat pelacakan.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- TAB 3: PETA RUTE --}}
<div id="tab-map" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Peta Visualisasi Rute Kargo</h3>
        <div id="customer-detail-map" style="height: 420px; border-radius: 12px; background: #0A1128;"></div>
    </div>
</div>

{{-- TAB 4: KENDALA / PENGECUALIAN --}}
<div id="tab-exceptions" class="tab-content hidden">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4">Catatan Kendala & Mitigasi Rute</h3>
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
                        <b>Tindakan Penyelesaian Operator:</b> {{ $ex->resolution_notes }}
                    </div>
                @endif
            </div>
            @empty
            <div class="py-6 text-center text-xs text-slate-400">
                <i data-lucide="shield-check" class="w-8 h-8 mx-auto mb-2 opacity-50 text-emerald-500"></i>
                <p>Tidak ada kendala pada pengiriman ini. Perjalanan berlangsung normal.</p>
            </div>
            @endforelse
        </div>
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

    const map = L.map('customer-detail-map', { maxZoom: 20 }).setView([oLat, oLng], 4);

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

    const boundsPoints = [[oLat, oLng], [dLat, dLng]];

    function calculateBearing(p1, p2) {
        const lat1 = p1[0] * Math.PI / 180;
        const lat2 = p2[0] * Math.PI / 180;
        const dLng = (p2[1] - p1[1]) * Math.PI / 180;
        const y = Math.sin(dLng) * Math.cos(lat2);
        const x = Math.cos(lat1) * Math.sin(lat2) - Math.sin(lat1) * Math.cos(dLng);
        const brng = Math.atan2(y, x) * 180 / Math.PI;
        return Math.round((brng + 360) % 360);
    }

    function calculateDistanceKm(lat1, lon1, lat2, lon2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    const shipSvg = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 78" width="28" height="58" style="filter: drop-shadow(0 6px 12px rgba(0,0,0,0.6));">
            <path d="M19 2 C27 12, 33 24, 33 48 C33 66, 30 74, 19 75 C8 74, 5 66, 5 48 C5 24, 11 12, 19 2 Z" fill="#0f172a" opacity="0.35" transform="translate(1.5, 2.5)"/>
            <path d="M19 2 C27 12, 33 24, 33 48 C33 66, 30 74, 19 75 C8 74, 5 66, 5 48 C5 24, 11 12, 19 2 Z" fill="#0f172a" stroke="#475569" stroke-width="1.2"/>
            <path d="M19 2 C22 7, 25 12, 25 17 L13 17 C13 12, 16 7, 19 2 Z" fill="#e11d48"/>
            <circle cx="19" cy="9" r="1.5" fill="#ffffff"/>
            <rect x="9" y="19" width="20" height="38" rx="2" fill="#1e293b"/>
            <rect x="10" y="20" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
            <rect x="19.5" y="20" width="8.5" height="5" rx="0.5" fill="#e11d48" stroke="#be123c" stroke-width="0.5"/>
            <rect x="10" y="26" width="8.5" height="5" rx="0.5" fill="#ea580c" stroke="#c2410c" stroke-width="0.5"/>
            <rect x="19.5" y="26" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
            <rect x="10" y="32" width="8.5" height="5" rx="0.5" fill="#eab308" stroke="#ca8a04" stroke-width="0.5"/>
            <rect x="19.5" y="32" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
            <rect x="10" y="38" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
            <rect x="19.5" y="38" width="8.5" height="5" rx="0.5" fill="#ea580c" stroke="#c2410c" stroke-width="0.5"/>
            <rect x="10" y="44" width="8.5" height="5" rx="0.5" fill="#e11d48" stroke="#be123c" stroke-width="0.5"/>
            <rect x="19.5" y="44" width="8.5" height="5" rx="0.5" fill="#eab308" stroke="#ca8a04" stroke-width="0.5"/>
            <rect x="10" y="50" width="8.5" height="5" rx="0.5" fill="#0284c7" stroke="#0369a1" stroke-width="0.5"/>
            <rect x="19.5" y="50" width="8.5" height="5" rx="0.5" fill="#059669" stroke="#047857" stroke-width="0.5"/>
            <rect x="10" y="58" width="18" height="9" rx="1.5" fill="#f8fafc" stroke="#cbd5e1" stroke-width="0.8"/>
            <rect x="12" y="59.5" width="14" height="2" rx="0.5" fill="#0284c7"/>
            <circle cx="19" cy="63" r="1.5" fill="#0f172a"/>
            <rect x="17.5" y="68" width="3" height="3" rx="0.8" fill="#e11d48"/>
        </svg>
    `;

    const planeSvg = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="44" height="44" style="filter: drop-shadow(0 8px 12px rgba(0,0,0,0.6));">
            <path d="M32 2 C30 2, 28 6, 28 14 L28 24 L2 35 L2 40 L28 34 L28 50 L18 56 L18 60 L32 58 L46 60 L46 56 L36 50 L36 34 L62 40 L62 35 L36 24 L36 14 C36 6, 34 2, 32 2 Z" fill="#0f172a" opacity="0.3" transform="translate(1.5, 3)"/>
            <path d="M32 2 C30 2, 28 6, 28 14 L28 24 L2 35 L2 40 L28 34 L28 50 L18 56 L18 60 L32 58 L46 60 L46 56 L36 50 L36 34 L62 40 L62 35 L36 24 L36 14 C36 6, 34 2, 32 2 Z" fill="#f8fafc" stroke="#64748b" stroke-width="1"/>
            <path d="M32 4 C31 4, 29.5 8, 29.5 16 L29.5 54 L34.5 54 L34.5 16 C34.5 8, 33 4, 32 4 Z" fill="#ffffff"/>
            <path d="M30 6 Q32 4.5 34 6 L33.5 8.5 Q32 7.5 30.5 8.5 Z" fill="#0284c7"/>
            <path d="M12 34 L26 31 L26 33 L12 36 Z" fill="#0284c7"/>
            <path d="M52 34 L38 31 L38 33 L52 36 Z" fill="#0284c7"/>
            <rect x="14" y="27" width="4.5" height="11" rx="2" fill="#334155" stroke="#cbd5e1" stroke-width="0.7"/>
            <ellipse cx="16.2" cy="28" rx="1.6" ry="1.2" fill="#38bdf8"/>
            <rect x="45.5" y="27" width="4.5" height="11" rx="2" fill="#334155" stroke="#cbd5e1" stroke-width="0.7"/>
            <ellipse cx="47.8" cy="28" rx="1.6" ry="1.2" fill="#38bdf8"/>
            <path d="M30 52 L34 52 L33 58 L31 58 Z" fill="#ea580c"/>
        </svg>
    `;

    const vanSvg = `
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 38 68" width="30" height="54" style="filter: drop-shadow(0 4px 8px rgba(0,0,0,0.5));">
            <rect x="4" y="3" width="30" height="62" rx="6" fill="#0f172a" opacity="0.35" transform="translate(1.5, 2.5)"/>
            <rect x="3" y="12" width="3" height="8" rx="1" fill="#0f172a"/>
            <rect x="32" y="12" width="3" height="8" rx="1" fill="#0f172a"/>
            <rect x="3" y="48" width="3" height="9" rx="1" fill="#0f172a"/>
            <rect x="32" y="48" width="3" height="9" rx="1" fill="#0f172a"/>
            <rect x="5" y="4" width="28" height="60" rx="5" fill="#f8fafc" stroke="#64748b" stroke-width="1.2"/>
            <rect x="7" y="6" width="24" height="15" rx="3" fill="#e2e8f0"/>
            <path d="M8 8 Q19 6 30 8 L29 15 Q19 13.5 9 15 Z" fill="#0284c7"/>
            <rect x="6.5" y="9.5" width="2" height="7" rx="0.5" fill="#0284c7"/>
            <rect x="29.5" y="9.5" width="2" height="7" rx="0.5" fill="#0284c7"/>
            <rect x="3" y="10" width="2.5" height="4" rx="0.8" fill="#334155"/>
            <rect x="32.5" y="10" width="2.5" height="4" rx="0.8" fill="#334155"/>
            <rect x="6.5" y="23" width="25" height="39" rx="2.5" fill="#f59e0b" stroke="#d97706" stroke-width="0.8"/>
            <rect x="9.5" y="27" width="19" height="2" fill="#fde68a"/>
            <text x="19" y="42" font-size="12" font-weight="900" fill="#ffffff" text-anchor="middle" font-family="sans-serif">📦</text>
            <text x="19" y="54" font-size="5" font-weight="800" fill="#78350f" text-anchor="middle" font-family="sans-serif">KURIR PAKET</text>
        </svg>
    `;

    @if($shipment->delivery_lat && $shipment->delivery_lng)
        const devLat = {{ $shipment->delivery_lat }};
        const devLng = {{ $shipment->delivery_lng }};
        boundsPoints.push([devLat, devLng]);

        const distKm = calculateDistanceKm(dLat, dLng, devLat, devLng);
        const isDomesticFeederAir = distKm > 35;

        if (isDomesticFeederAir) {
            // Koridor Penerbangan Domestik dari Pelabuhan Hub ke Kota Tujuan
            const feederPoints = [];
            for (let i = 0; i <= 20; i++) {
                const t = i / 20;
                const fLat = (1 - t) * dLat + t * devLat + Math.sin(t * Math.PI) * 0.4;
                const fLng = (1 - t) * dLng + t * devLng + Math.sin(t * Math.PI) * 0.4;
                feederPoints.push([fLat, fLng]);
            }
            L.polyline(feederPoints, { color: '#00F0FF', weight: 4, dashArray: '6, 6', opacity: 0.95 }).addTo(map);

            @if($shipment->is_last_mile || $shipment->status === 'DELIVERED')
                const planeLat = dLat + (devLat - dLat) * 0.52;
                const planeLng = dLng + (devLng - dLng) * 0.52;
                const planeHeading = calculateBearing([dLat, dLng], [devLat, devLng]);

                const feederPlaneDivIcon = L.divIcon({
                    className: 'cargo-real-vehicle-marker',
                    html: `
                        <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${planeHeading}deg);" title="Pesawat Kargo Domestik Feeder: {{ $shipment->shipment_number }}">
                            ${planeSvg}
                        </div>
                        <div class="cargo-vehicle-tag" style="white-space:nowrap; margin-top:-2px;">
                            <span class="cargo-tag-dot" style="background:#00F0FF;"></span>
                            <span>✈️ Feeder: {{ $shipment->shipment_number }}</span>
                        </div>
                    `,
                    iconSize: [42, 42],
                    iconAnchor: [21, 21],
                    popupAnchor: [0, -25]
                });
                L.marker([planeLat, planeLng], { icon: feederPlaneDivIcon, zIndexOffset: 1400 })
                    .addTo(map)
                    .bindPopup('<b>✈️ Pesawat Kargo Domestik:</b> Penerbangan lanjutan dari pelabuhan hub {{ $shipment->destination?->name }} ke bandara kota {{ $shipment->delivery_city }}');
            @endif
        } else {
            // Garis jalan darat pengantaran mobil kurir langsung
            L.polyline([[dLat, dLng], [devLat, devLng]], { color: '#f59e0b', weight: 3.5, dashArray: '5, 6', opacity: 0.95 }).addTo(map);
        }

        // Pin Alamat Penerima (📍)
        const destPin = L.divIcon({
            className: 'cargo-destination-pin',
            html: `<div style="font-size:26px; line-height:1; filter:drop-shadow(0 3px 6px rgba(0,0,0,0.5)); transform:translate(-2px, -14px); cursor:pointer;" title="{{ $shipment->delivery_address }}">📍</div>`,
            iconSize: [26, 26],
            iconAnchor: [13, 26]
        });
        L.marker([devLat, devLng], { icon: destPin }).addTo(map)
            .bindPopup('<b>🚚 Alamat Pengantaran:</b> {{ $shipment->delivery_address }}<br><span style="font-size:11px; color:#92400e;">{{ $shipment->delivery_district ? $shipment->delivery_district . ", " : "" }}{{ $shipment->delivery_city }} ({{ $shipment->delivery_country }})</span>');

        @if($shipment->is_last_mile || $shipment->status === 'DELIVERED')
            const vanFraction = isDomesticFeederAir ? 0.88 : 0.65;
            const vanLat = dLat + (devLat - dLat) * vanFraction;
            const vanLng = dLng + (devLng - dLng) * vanFraction;
            const vanHeading = calculateBearing([dLat, dLng], [devLat, devLng]);
            const vanPlate = '{{ $shipment->courier_vehicle ? (str_contains($shipment->courier_vehicle, "(") ? str_replace(")", "", explode("(", $shipment->courier_vehicle)[1]) : $shipment->shipment_number) : $shipment->shipment_number }}';

            const vanIcon = L.divIcon({
                className: 'cargo-real-vehicle-marker',
                html: `
                    <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${vanHeading}deg);" title="{{ $shipment->courier_name ?? 'Mobil Paket Kurir' }} &bull; {{ $shipment->shipment_number }}">
                        ${vanSvg}
                    </div>
                    <div class="cargo-vehicle-tag" style="white-space:nowrap; margin-top:-2px;">
                        <span class="cargo-tag-dot" style="background:#f59e0b;"></span>
                        <span>Mobil Paket: ${vanPlate}</span>
                    </div>
                `,
                iconSize: [36, 56],
                iconAnchor: [18, 28],
                popupAnchor: [0, -32]
            });
            L.marker([vanLat, vanLng], { icon: vanIcon, zIndexOffset: 1500 }).addTo(map)
                .bindPopup('<b>🚚 Armada Kurir Paket:</b> {{ $shipment->courier_name ?? "Mobil Van Paket" }}<br><span style="font-size:11px; color:#d97706; font-weight:700;">{{ $shipment->courier_vehicle ?? "Van Kurir" }}</span>');
        @endif
    @endif

    @if($shipment->currentLocation)
        const cLat = {{ $shipment->currentLocation->latitude }};
        const cLng = {{ $shipment->currentLocation->longitude }};
        boundsPoints.push([cLat, cLng]);

        L.polyline([[oLat, oLng], [cLat, cLng]], { color: '#0284c7', weight: 4.5, opacity: 0.95 }).addTo(map);
        L.polyline([[cLat, cLng], [dLat, dLng]], { color: '#38bdf8', weight: 3.5, dashArray: '6, 8', opacity: 0.85 }).addTo(map);

        const isAir = '{{ $shipment->transport_mode }}' === 'AIR';
        const heading = calculateBearing([cLat, cLng], [dLat, dLng]);
        const vehicleSvg = isAir ? planeSvg : shipSvg;
        const vW = isAir ? 46 : 38;
        const vH = isAir ? 60 : 72;

        const vIcon = L.divIcon({
            className: 'cargo-real-vehicle-marker',
            html: `
                <div class="cargo-real-vehicle-wrapper" style="transform: rotate(${heading}deg);" title="{{ $shipment->shipment_number }}">
                    ${vehicleSvg}
                </div>
                <div class="cargo-vehicle-tag" style="white-space:nowrap; margin-top:-2px;">
                    <span class="cargo-tag-dot" style="background:${isAir ? '#38bdf8' : '#00d2ff'};"></span>
                    <span>{{ $shipment->shipment_number }}</span>
                </div>
            `,
            iconSize: [vW, vH],
            iconAnchor: [vW / 2, vH / 2],
            popupAnchor: [0, -(vH / 2 + 5)]
        });

        L.marker([cLat, cLng], { icon: vIcon, zIndexOffset: 1000 })
            .addTo(map)
            .bindPopup('<b>📍 Lokasi Saat Ini:</b> {{ $shipment->currentLocation->name }}<br><span style="font-size:11px; color:#0284c7; font-weight:700;">Status: {{ $shipment->status }}</span>');
    @else
        L.polyline([[oLat, oLng], [dLat, dLng]], { color: '#0284c7', weight: 3.5, opacity: 0.85 }).addTo(map);
    @endif

    map.fitBounds(boundsPoints, { padding: [50, 50] });
}
</script>
@endpush

