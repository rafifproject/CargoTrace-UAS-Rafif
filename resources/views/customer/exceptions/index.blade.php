@extends('layouts.customer')

@section('title', 'Informasi Pengecualian')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Pengecualian Pengiriman</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Informasi status kendala rute, penundaan cuaca, atau penanganan khusus pada kargo Anda.</p>
    </div>
</div>

{{-- Banner Status Pengecualian Aktif (Mockup 08) --}}
@if($activeCount > 0)
<div class="mb-6 p-4 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-700 dark:text-amber-400 flex items-center gap-3">
    <div class="p-2 rounded-xl bg-amber-500/20">
        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500"></i>
    </div>
    <div>
        <h3 class="font-bold text-xs">{{ $activeCount }} Pengecualian Aktif</h3>
        <p class="text-xs text-slate-600 dark:text-slate-300">Terdapat gangguan operasional atau cuaca pada kargo Anda yang sedang dalam penanganan tim operator logistik.</p>
    </div>
</div>
@else
<div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 dark:text-emerald-400 flex items-center gap-3">
    <div class="p-2 rounded-xl bg-emerald-500/20">
        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500"></i>
    </div>
    <div>
        <h3 class="font-bold text-xs">Seluruh Pengiriman Berjalan Normal</h3>
        <p class="text-xs text-slate-600 dark:text-slate-300">Tidak ada kendala aktif yang dilaporkan pada kargo kiriman Anda.</p>
    </div>
</div>
@endif

{{-- Exception Cards List (Mockup 08) --}}
<div class="space-y-4">
    @forelse($exceptions as $ex)
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <div class="flex items-center gap-2">
                <span class="font-mono font-bold text-xs text-slate-700 dark:text-slate-200">EX-{{ str_pad($ex->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="text-slate-300 dark:text-slate-700">•</span>
                <a href="{{ route('customer.shipments.show', $ex->shipment_id) }}" class="font-mono text-cyan-600 dark:text-cyan-400 font-bold text-xs hover:underline">
                    {{ $ex->shipment?->shipment_number }}
                </a>
            </div>
            <div class="flex items-center gap-2">
                @php
                    $sevColors = [
                        'LOW' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                        'MEDIUM' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                        'HIGH' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300',
                        'CRITICAL' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                    ];
                @endphp
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $sevColors[$ex->severity] ?? 'bg-slate-100' }}">
                    Tingkat: {{ $ex->severity }}
                </span>
                @if($ex->status === 'RESOLVED')
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">Selesai</span>
                @elseif($ex->status === 'INVESTIGATING')
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-500 border border-amber-500/20">Sedang Diselidiki</span>
                @else
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-500 border border-rose-500/20">Terbuka</span>
                @endif
            </div>
        </div>

        <h3 class="font-bold text-xs text-slate-800 dark:text-slate-100 mb-1">{{ $ex->exception_type }}</h3>
        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">{{ $ex->description }}</p>

        @if($ex->resolution_notes)
            <div class="mt-3 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40 text-xs text-emerald-800 dark:text-emerald-300">
                <span class="font-bold block mb-0.5">Tindakan Penyelesaian Tim Operator:</span>
                <p>{{ $ex->resolution_notes }}</p>
                <span class="text-[10px] text-emerald-600 dark:text-emerald-400 mt-1 block">Diselesaikan pada: {{ $ex->resolved_at?->format('d M Y, H:i') ?? '-' }}</span>
            </div>
        @endif

        <div class="flex items-center justify-between pt-3 mt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] text-slate-400">
            <span>Dilaporkan pada: {{ $ex->created_at->format('d M Y, H:i') }}</span>
            <a href="{{ route('customer.shipments.show', $ex->shipment_id) }}" class="text-cyan-600 dark:text-cyan-400 font-semibold hover:underline">
                Buka Kargo Ini →
            </a>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-12 text-center text-slate-400 shadow-sm">
        <i data-lucide="shield-check" class="w-12 h-12 mx-auto mb-2 text-emerald-500 opacity-60"></i>
        <h3 class="font-bold text-sm text-slate-700 dark:text-slate-200">Tidak Ada Pengecualian</h3>
        <p class="text-xs mt-1">Seluruh kargo kiriman Anda berada dalam jadwal perjalanan normal.</p>
    </div>
    @endforelse

    @if($exceptions->hasPages())
        <div class="pt-4">
            {{ $exceptions->links() }}
        </div>
    @endif
</div>
@endsection
