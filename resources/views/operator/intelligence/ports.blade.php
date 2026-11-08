@extends('layouts.operator')

@section('title', 'Direktori Pelabuhan Global')

@section('content')
<div class="mb-6 flex flex-wrap items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Direktori Pelabuhan Global</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Basis data pelabuhan kontainer utama dunia, kode UN/LOCODE, dan koordinat navigasi.</p>
    </div>

    {{-- Search Form --}}
    <form method="GET" action="{{ route('operator.intelligence.ports') }}" class="flex items-center gap-2">
        <div class="relative">
            <i data-lucide="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, kota, kode..." class="pl-9 pr-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-[#0A1128] text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 shadow-sm w-64">
        </div>
        <button type="submit" class="px-3 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold">Cari</button>
        @if(request('search'))
            <a href="{{ route('operator.intelligence.ports') }}" class="px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-xs">Reset</a>
        @endif
    </form>
</div>

{{-- Ports Table & Mini Map (Mockup 13) --}}
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    {{-- Mini Map --}}
    <div class="lg:col-span-2 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 p-4 shadow-sm">
        <h3 class="text-xs font-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-1.5 uppercase tracking-wider">
            <i data-lucide="map-pin" class="w-4 h-4 text-cyan-500"></i>
            Sebaran Hub Maritim
        </h3>
        <div id="ports-map" style="height: 480px; width: 100%; border-radius: 12px; background: #0A1128;"></div>
    </div>

    {{-- Ports Table --}}
    <div class="lg:col-span-3 bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-800">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-slate-500 dark:text-slate-400">Kode</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Nama Pelabuhan</th>
                        <th class="text-left px-3 py-3 font-semibold text-slate-500 dark:text-slate-400">Negara</th>
                        <th class="text-right px-4 py-3 font-semibold text-slate-500 dark:text-slate-400">Koordinat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($allPorts as $port)
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-900/40 transition-colors">
                        <td class="px-4 py-3 font-mono font-bold text-cyan-600 dark:text-cyan-400">{{ $port['code'] ?? 'IDTPP' }}</td>
                        <td class="px-3 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ $port['name'] }}</td>
                        <td class="px-3 py-3 text-slate-600 dark:text-slate-400">{{ $port['city'] }}, {{ $port['country'] }}</td>
                        <td class="px-4 py-3 text-right font-mono text-[11px] text-slate-400">
                            {{ round($port['lat'], 3) }}, {{ round($port['lng'], 3) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-slate-400">Tidak ada pelabuhan yang sesuai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const map = L.map('ports-map').setView([10, 115], 3);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '© OSM' }).addTo(map);

    const ports = @json(array_values($allPorts));
    const bounds = [];

    ports.forEach(p => {
        if (p.lat && p.lng) {
            bounds.push([p.lat, p.lng]);
            L.circleMarker([p.lat, p.lng], {
                radius: 6,
                color: '#06b6d4',
                fillColor: '#0284c7',
                fillOpacity: 0.9,
                weight: 2
            }).addTo(map).bindPopup(`<b>${p.name}</b><br>${p.city}, ${p.country}<br><span class="font-mono text-cyan-600">${p.code}</span>`);
        }
    });

    if (bounds.length > 0) {
        map.fitBounds(bounds, { padding: [30, 30] });
    }
});
</script>
@endpush

