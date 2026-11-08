@extends('layouts.admin')

@section('title', 'Direktori Pelabuhan Global')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Direktori Pelabuhan Global (Global Port Directory)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Direktori referensi titik simpul pelabuhan dunia berbasis standard World Port Index (Bukan live vessel tracker)</p>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.intelligence.ports') }}" class="flex gap-3">
            <div class="relative flex-1">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Kode Port, Nama Pelabuhan, Kota, atau Negara..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
            </div>
            <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg">Cari Pelabuhan</button>
        </form>
    </div>

    <!-- Map of Ports -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm p-5">
        <h2 class="text-sm font-bold text-slate-900 dark:text-white mb-2 flex items-center gap-2">
            <i data-lucide="anchor" class="w-4 h-4 text-cyan-500"></i>
            <span>Peta Sebaran Pelabuhan Utama Dunia</span>
        </h2>
        <div id="portsMap" class="w-full h-80 rounded-xl border border-slate-200 dark:border-slate-800 z-10"></div>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Kode Pelabuhan</th>
                        <th class="py-3 px-4">Nama Pelabuhan</th>
                        <th class="py-3 px-4">Negara</th>
                        <th class="py-3 px-4">Kota</th>
                        <th class="py-3 px-4">Koordinat</th>
                        <th class="py-3 px-4">Kapasitas Tahunan</th>
                        <th class="py-3 px-4">Kategori Terminal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($allPorts as $port)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                            <td class="py-3.5 px-4 font-mono font-bold text-cyan-600 dark:text-cyan-400">
                                {{ $port['code'] }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $port['name'] }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $port['country'] }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $port['city'] }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-400">
                                {{ $port['lat'] }}, {{ $port['lng'] }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-800 dark:text-white">
                                {{ $port['capacity'] }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                                    {{ $port['type'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada pelabuhan yang cocok.</td>
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
        const map = L.map('portsMap', { center: [20, 30], zoom: 2 });
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; World Port Index'
        }).addTo(map);

        const ports = {!! json_encode(array_values($allPorts)) !!};
        ports.forEach(p => {
            L.circleMarker([p.lat, p.lng], {
                radius: 6,
                fillColor: '#00A8E8',
                color: '#fff',
                weight: 1.5,
                fillOpacity: 1
            }).bindPopup(`<b>${p.name} (${p.code})</b><br>${p.city}, ${p.country}<br>Kapasitas: ${p.capacity}`).addTo(map);
        });
    });
</script>
@endpush

