@extends('layouts.operator')

@section('title', 'Riwayat Pelacakan')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Riwayat Pelacakan</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Timeline riwayat checkpoints dan event pengiriman kargo internasional.</p>
    </div>
</div>

{{-- Shipment Selector Filter --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('operator.tracking.index') }}" class="flex flex-wrap items-end gap-3">
        <div class="flex-1 min-w-64">
            <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Pilih Nomor Pengiriman</label>
            <select name="shipment_id" onchange="this.form.submit()" class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 font-mono">
                <option value="">— Tampilkan Semua Event —</option>
                @foreach($allShipments as $s)
                    <option value="{{ $s->id }}" {{ (request('shipment_id') == $s->id) ? 'selected' : '' }}>
                        {{ $s->shipment_number }} — {{ $s->origin?->city }} → {{ $s->destination?->city }} ({{ $s->status }})
                    </option>
                @endforeach
            </select>
        </div>
        @if(request('shipment_id'))
            <a href="{{ route('operator.tracking.index') }}" class="px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Reset</a>
        @endif
    </form>
</div>

{{-- Vertical Timeline --}}
<div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm">
    <div class="relative pl-6 space-y-6 before:content-[''] before:absolute before:left-2.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-800">
        @forelse($events as $event)
        <div class="relative">
            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-cyan-500 border-2 border-white dark:border-[#0A1128] shadow-sm"></span>
            <div class="bg-slate-50/50 dark:bg-slate-900/30 rounded-xl p-4 border border-slate-100 dark:border-slate-800/80">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-1.5">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('operator.shipments.show', $event->shipment_id) }}" class="font-mono font-bold text-cyan-600 dark:text-cyan-400 hover:underline text-xs">
                            {{ $event->shipment?->shipment_number }}
                        </a>
                        <span class="text-slate-300 dark:text-slate-700">•</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-200 text-xs">{{ $event->event_type }}</span>
                    </div>
                    <span class="text-[11px] text-slate-400 font-mono">{{ \Carbon\Carbon::parse($event->event_time)->format('d M Y, H:i') }}</span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-300">{{ $event->description }}</p>
                <div class="flex flex-wrap items-center gap-4 mt-2 text-[11px] text-slate-400">
                    @if($event->location)
                        <span class="flex items-center gap-1"><i data-lucide="map-pin" class="w-3 h-3 text-cyan-500"></i> {{ $event->location->name }}, {{ $event->location->country }}</span>
                    @endif
                    @if($event->creator)
                        <span class="flex items-center gap-1"><i data-lucide="user" class="w-3 h-3"></i> {{ $event->creator->name }}</span>
                    @endif
                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300">{{ $event->shipment_status_at_event }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="py-12 text-center text-slate-400 text-sm">
            <i data-lucide="git-commit" class="w-10 h-10 mx-auto mb-2 opacity-30"></i>
            <p>Belum ada riwayat event pelacakan.</p>
        </div>
        @endforelse
    </div>
    @if($events->hasPages())
        <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800">
            {{ $events->links() }}
        </div>
    @endif
</div>
@endsection
