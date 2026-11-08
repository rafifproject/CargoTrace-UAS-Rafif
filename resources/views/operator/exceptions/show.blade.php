@extends('layouts.operator')

@section('title', 'Detail Pengecualian – EX-' . str_pad($exception->id, 5, '0', STR_PAD_LEFT))

@section('content')
{{-- Breadcrumbs & Header --}}
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <div class="flex items-center gap-2 text-xs text-slate-400 dark:text-slate-500 mb-1">
            <a href="{{ route('operator.exceptions.index') }}" class="hover:text-cyan-500">Manajemen Pengecualian</a>
            <i data-lucide="chevron-right" class="w-3 h-3"></i>
            <span class="font-mono text-slate-700 dark:text-slate-300">EX-{{ str_pad($exception->id, 5, '0', STR_PAD_LEFT) }}</span>
        </div>
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-6 h-6 text-rose-500"></i>
                Detail Pengecualian
                <span class="font-mono text-lg">EX-{{ str_pad($exception->id, 5, '0', STR_PAD_LEFT) }}</span>
            </h1>
            @php
                $statusColors = [
                    'OPEN' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300',
                    'INVESTIGATING' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                    'RESOLVED' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                ];
                $statusLabels = ['OPEN' => 'Terbuka', 'INVESTIGATING' => 'Diselidiki', 'RESOLVED' => 'Selesai'];
                $sevColors = [
                    'LOW' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300',
                    'MEDIUM' => 'bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300',
                    'HIGH' => 'bg-orange-100 text-orange-700 dark:bg-orange-950/60 dark:text-orange-300',
                    'CRITICAL' => 'bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-300',
                ];
            @endphp
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusColors[$exception->status] ?? 'bg-slate-100' }}">
                {{ $statusLabels[$exception->status] ?? $exception->status }}
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $sevColors[$exception->severity] ?? 'bg-slate-100' }}">
                {{ $exception->severity }}
            </span>
        </div>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ route('operator.exceptions.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Kembali
        </a>
        @if($exception->status !== 'RESOLVED')
            <button onclick="document.getElementById('resolve-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-colors shadow-sm">
                <i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Selesaikan
            </button>
        @endif
    </div>
</div>

@if(session('status'))
    <div class="mb-5 flex items-center gap-3 p-4 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 rounded-xl text-sm">
        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
        {{ session('status') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom Kiri: Info Utama Pengecualian --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Card Informasi Pengecualian --}}
        <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-500"></i>
                <h2 class="text-sm font-bold text-slate-700 dark:text-white">Informasi Pengecualian</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">ID Pengecualian</div>
                    <div class="font-mono font-bold text-base text-slate-800 dark:text-white">EX-{{ str_pad($exception->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Jenis Masalah</div>
                    <div class="font-mono text-sm font-semibold text-rose-600 dark:text-rose-400">{{ $exception->exception_type }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Tingkat Keparahan</div>
                    <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-bold {{ $sevColors[$exception->severity] ?? 'bg-slate-100' }}">
                        {{ $exception->severity }}
                    </span>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Status Saat Ini</div>
                    <span class="inline-flex items-center gap-1.5 text-sm font-semibold {{ $exception->status === 'RESOLVED' ? 'text-emerald-600 dark:text-emerald-400' : ($exception->status === 'INVESTIGATING' ? 'text-amber-500' : 'text-rose-500') }}">
                        @if($exception->status === 'RESOLVED')
                            <i data-lucide="check-circle" class="w-4 h-4"></i> Selesai
                        @elseif($exception->status === 'INVESTIGATING')
                            <i data-lucide="loader" class="w-4 h-4 animate-spin"></i> Sedang Diselidiki
                        @else
                            <i data-lucide="alert-circle" class="w-4 h-4"></i> Terbuka
                        @endif
                    </span>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Dilaporkan Pada</div>
                    <div class="text-sm text-slate-700 dark:text-slate-200">{{ $exception->created_at->format('d M Y, H:i') }} WIB</div>
                </div>
                @if($exception->resolved_at)
                <div>
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Diselesaikan Pada</div>
                    <div class="text-sm text-slate-700 dark:text-slate-200">{{ $exception->resolved_at->format('d M Y, H:i') }} WIB</div>
                </div>
                @endif
                <div class="md:col-span-2">
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Deskripsi Masalah</div>
                    <div class="p-4 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 rounded-xl text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                        {{ $exception->description }}
                    </div>
                </div>
                @if($exception->resolution_notes)
                <div class="md:col-span-2">
                    <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Catatan Penyelesaian</div>
                    <div class="p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-xl text-sm text-slate-700 dark:text-slate-200 leading-relaxed">
                        {{ $exception->resolution_notes }}
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Card Informasi Pengiriman Terkait --}}
        <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-cyan-500"></i>
                <h2 class="text-sm font-bold text-slate-700 dark:text-white">Pengiriman Terkait</h2>
            </div>
            <div class="p-6">
                @if($exception->shipment)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">No. Pengiriman</div>
                        <a href="{{ route('operator.shipments.show', $exception->shipment) }}"
                            class="font-mono font-bold text-base text-cyan-600 dark:text-cyan-400 hover:underline">
                            {{ $exception->shipment->shipment_number }}
                        </a>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Status Pengiriman</div>
                        @php
                            $shipStatusMap = ['CREATED'=>['Dibuat','slate'],'BOOKED'=>['Dipesan','amber'],'PICKED_UP'=>['Diambil','purple'],'IN_TRANSIT'=>['Dalam Perjalanan','blue'],'DELIVERED'=>['Terkirim','emerald'],'CANCELLED'=>['Dibatalkan','rose']];
                            [$sl, $sc] = $shipStatusMap[$exception->shipment->status] ?? [$exception->shipment->status, 'slate'];
                        @endphp
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-{{ $sc }}-100 text-{{ $sc }}-700 dark:bg-{{ $sc }}-900/40 dark:text-{{ $sc }}-300">{{ $sl }}</span>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Carrier</div>
                        <div class="text-sm text-slate-700 dark:text-slate-200">{{ $exception->shipment->carrier?->name ?? '-' }}</div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Rute Asal</div>
                        <div class="text-sm text-slate-700 dark:text-slate-200">
                            {{ $exception->shipment->origin?->name ?? '-' }}
                            <span class="text-xs text-slate-400">({{ $exception->shipment->origin?->country ?? '' }})</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">Rute Tujuan</div>
                        <div class="text-sm text-slate-700 dark:text-slate-200">
                            {{ $exception->shipment->destination?->name ?? '-' }}
                            <span class="text-xs text-slate-400">({{ $exception->shipment->destination?->country ?? '' }})</span>
                        </div>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">ETA</div>
                        <div class="text-sm text-slate-700 dark:text-slate-200">
                            {{ $exception->shipment->estimated_time_arrival?->format('d M Y') ?? '-' }}
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('operator.shipments.show', $exception->shipment) }}"
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-cyan-600 dark:text-cyan-400 hover:underline">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        Buka Detail Pengiriman Lengkap
                    </a>
                </div>
                @else
                    <p class="text-sm text-slate-400">Data pengiriman tidak tersedia.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- Kolom Kanan: Timeline & Petugas --}}
    <div class="space-y-5">

        {{-- Card Timeline Status --}}
        <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
                <h2 class="text-sm font-bold text-slate-700 dark:text-white">Timeline Penanganan</h2>
            </div>
            <div class="p-5">
                <ol class="relative border-l border-slate-200 dark:border-slate-700 ml-2 space-y-5">
                    {{-- Dibuka --}}
                    <li class="ml-5">
                        <span class="absolute -left-2 flex items-center justify-center w-4 h-4 bg-rose-100 dark:bg-rose-900/40 rounded-full ring-4 ring-white dark:ring-[#0A1128]">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                        </span>
                        <div class="text-xs font-bold text-slate-700 dark:text-slate-200">Pengecualian Dilaporkan</div>
                        <div class="text-xs text-slate-400">{{ $exception->created_at->format('d M Y, H:i') }}</div>
                        @if($exception->reporter)
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">oleh: <span class="font-semibold">{{ $exception->reporter->name }}</span></div>
                        @endif
                    </li>
                    {{-- Investigasi --}}
                    <li class="ml-5">
                        <span class="absolute -left-2 flex items-center justify-center w-4 h-4 {{ $exception->status !== 'OPEN' ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-slate-100 dark:bg-slate-800' }} rounded-full ring-4 ring-white dark:ring-[#0A1128]">
                            <span class="w-2 h-2 rounded-full {{ $exception->status !== 'OPEN' ? 'bg-amber-500' : 'bg-slate-300' }}"></span>
                        </span>
                        <div class="text-xs font-bold {{ $exception->status !== 'OPEN' ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400' }}">Diselidiki</div>
                        <div class="text-xs text-slate-400">{{ $exception->status !== 'OPEN' ? 'Dalam proses penanganan' : 'Belum dimulai' }}</div>
                    </li>
                    {{-- Selesai --}}
                    <li class="ml-5">
                        <span class="absolute -left-2 flex items-center justify-center w-4 h-4 {{ $exception->status === 'RESOLVED' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-slate-100 dark:bg-slate-800' }} rounded-full ring-4 ring-white dark:ring-[#0A1128]">
                            <span class="w-2 h-2 rounded-full {{ $exception->status === 'RESOLVED' ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                        </span>
                        <div class="text-xs font-bold {{ $exception->status === 'RESOLVED' ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400' }}">Selesai</div>
                        @if($exception->resolved_at)
                        <div class="text-xs text-slate-400">{{ $exception->resolved_at->format('d M Y, H:i') }}</div>
                        @if($exception->resolver)
                        <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">oleh: <span class="font-semibold">{{ $exception->resolver->name }}</span></div>
                        @endif
                        @else
                            <div class="text-xs text-slate-400">Belum diselesaikan</div>
                        @endif
                    </li>
                </ol>
            </div>
        </div>

        {{-- Card Petugas Penanggung Jawab --}}
        <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <i data-lucide="users" class="w-4 h-4 text-cyan-500"></i>
                <h2 class="text-sm font-bold text-slate-700 dark:text-white">Petugas</h2>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Pelapor</div>
                    @if($exception->reporter)
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center shrink-0">
                            <span class="text-sm font-bold text-rose-600 dark:text-rose-400">{{ strtoupper(substr($exception->reporter->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $exception->reporter->name }}</div>
                            <div class="text-xs text-slate-400">{{ $exception->reporter->role }}</div>
                        </div>
                    </div>
                    @else
                        <p class="text-xs text-slate-400">Sistem (auto-generated)</p>
                    @endif
                </div>
                @if($exception->resolver)
                <div class="border-t border-slate-100 dark:border-slate-800 pt-4">
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Penyelesai</div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center shrink-0">
                            <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ strtoupper(substr($exception->resolver->name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $exception->resolver->name }}</div>
                            <div class="text-xs text-slate-400">{{ $exception->resolver->role }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Aksi Cepat --}}
        @if($exception->status !== 'RESOLVED')
        <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/50 rounded-2xl p-5">
            <div class="flex items-start gap-3 mb-4">
                <i data-lucide="alert-octagon" class="w-5 h-5 text-rose-500 shrink-0 mt-0.5"></i>
                <div>
                    <div class="text-sm font-bold text-rose-700 dark:text-rose-300">Pengecualian Aktif</div>
                    <div class="text-xs text-rose-600/80 dark:text-rose-400/80 mt-0.5">Masalah ini memerlukan tindakan penanganan segera.</div>
                </div>
            </div>
            <button onclick="document.getElementById('resolve-modal').classList.remove('hidden')"
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition-colors shadow-sm">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Selesaikan Pengecualian Ini
            </button>
        </div>
        @else
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 rounded-2xl p-5 flex items-center gap-3">
            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <div>
                <div class="text-sm font-bold text-emerald-700 dark:text-emerald-300">Pengecualian Selesai</div>
                <div class="text-xs text-emerald-600/80 dark:text-emerald-400/80 mt-0.5">Masalah ini sudah berhasil ditangani.</div>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Modal Selesaikan Pengecualian --}}
@if($exception->status !== 'RESOLVED')
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
        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
            Penyelesaian untuk pengecualian
            <span class="font-mono font-bold text-slate-800 dark:text-white">EX-{{ str_pad($exception->id, 5, '0', STR_PAD_LEFT) }}</span>
            pada kargo
            <span class="font-mono font-bold text-cyan-600 dark:text-cyan-400">{{ $exception->shipment?->shipment_number ?? '-' }}</span>:
        </p>
        <form method="POST" action="{{ route('operator.exceptions.resolve', $exception) }}" class="space-y-4">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-1">
                    Catatan Tindakan Penyelesaian <span class="text-rose-500">*</span>
                </label>
                <textarea name="resolution_notes" rows="4" required
                    placeholder="Tuliskan langkah mitigasi atau hasil penyelesaian yang telah dilakukan..."
                    class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 resize-none focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('resolve-modal').classList.add('hidden')"
                    class="px-4 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-lg shadow-sm">
                    Konfirmasi Selesai
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

