@extends('layouts.operator')

@section('title', 'Edit Pengiriman – ' . $shipment->shipment_number)

@section('content')
<div class="mb-6">
    <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
        <a href="{{ route('operator.shipments.index') }}" class="hover:text-cyan-500">Semua Pengiriman</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <a href="{{ route('operator.shipments.show', $shipment) }}" class="hover:text-cyan-500">{{ $shipment->shipment_number }}</a>
        <i data-lucide="chevron-right" class="w-3 h-3"></i>
        <span>Edit</span>
    </div>
    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Edit Pengiriman</h1>
</div>

{{-- Current Info Card (Read-Only) --}}
<div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-5">
    <div class="flex flex-wrap gap-6 text-xs">
        <div>
            <span class="text-slate-400 dark:text-slate-500">No. Resi</span>
            <div class="font-mono font-bold text-slate-800 dark:text-white mt-0.5">{{ $shipment->shipment_number }}</div>
        </div>
        <div>
            <span class="text-slate-400 dark:text-slate-500">Pelanggan</span>
            <div class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->customer?->name ?? '-' }}</div>
        </div>
        <div>
            <span class="text-slate-400 dark:text-slate-500">Ekspedisi</span>
            <div class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->carrier?->name ?? '-' }}</div>
        </div>
        <div>
            <span class="text-slate-400 dark:text-slate-500">Rute</span>
            <div class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->origin?->city }} → {{ $shipment->destination?->city }}</div>
        </div>
        <div>
            <span class="text-slate-400 dark:text-slate-500">Kargo</span>
            <div class="font-semibold text-slate-700 dark:text-slate-200 mt-0.5">{{ $shipment->cargo_type }}</div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('operator.shipments.update', $shipment) }}">
    @csrf
    @method('PUT')
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
            <i data-lucide="edit-3" class="w-4 h-4 text-cyan-500"></i>
            Perbarui Status & Lokasi
        </h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Status Pengiriman <span class="text-rose-500">*</span></label>
                <select name="status" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    @php $statuses = ['CREATED' => 'Dibuat', 'BOOKED' => 'Dipesan', 'PICKED_UP' => 'Diambil', 'IN_TRANSIT' => 'Dalam Perjalanan', 'DELIVERED' => 'Terkirim']; @endphp
                    @foreach($statuses as $val => $label)
                        <option value="{{ $val }}" {{ (old('status', $shipment->status) === $val) ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('status')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Lokasi Saat Ini <span class="text-rose-500">*</span></label>
                <select name="current_location_id" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    <option value="">— Pilih Lokasi —</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ (old('current_location_id', $shipment->current_location_id) == $loc->id) ? 'selected' : '' }}>{{ $loc->name }} ({{ $loc->country }})</option>
                    @endforeach
                </select>
                @error('current_location_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Waktu Berangkat Aktual</label>
                <input type="datetime-local" name="actual_departure" value="{{ old('actual_departure', $shipment->actual_departure ? \Carbon\Carbon::parse($shipment->actual_departure)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Waktu Tiba Aktual</label>
                <input type="datetime-local" name="actual_arrival" value="{{ old('actual_arrival', $shipment->actual_arrival ? \Carbon\Carbon::parse($shipment->actual_arrival)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">ETA Terkini <span class="text-rose-500">*</span></label>
                <input type="datetime-local" name="estimated_time_arrival" value="{{ old('estimated_time_arrival', $shipment->estimated_time_arrival ? \Carbon\Carbon::parse($shipment->estimated_time_arrival)->format('Y-m-d\TH:i') : '') }}" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                @error('estimated_time_arrival')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="col-span-2">
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1.5">Catatan Pembaruan</label>
                <textarea name="notes" rows="2" placeholder="Alasan perubahan status atau catatan kondisi kargo..." class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 resize-none">{{ old('notes', $shipment->notes) }}</textarea>
            </div>
        </div>
        <div class="flex items-center gap-3 mt-5 pt-4 border-t border-slate-100 dark:border-slate-800">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-semibold text-sm transition-colors shadow-sm">
                <i data-lucide="save" class="w-4 h-4"></i>
                Simpan Perubahan
            </button>
            <a href="{{ route('operator.shipments.show', $shipment) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                Batal
            </a>
        </div>
    </div>
</form>
@endsection

