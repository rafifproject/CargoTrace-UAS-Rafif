@extends('layouts.admin')

@section('title', 'Manajemen Lokasi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">Manajemen Lokasi (Location Management)</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Kelola direktori titik simpul logistik dunia: pelabuhan samudra, bandara kargo, dan kantor pabean</p>
        </div>
        <button type="button" onclick="openCreateModal()" class="flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 shadow-sm transition-colors">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Tambah Lokasi Baru</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm">
        <form method="GET" action="{{ route('admin.locations.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="relative flex-1 w-full">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama Lokasi, Kota, atau Negara..." class="w-full pl-9 pr-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
            </div>
            <select name="location_type" onchange="this.form.submit()" class="w-full sm:w-48 px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-800 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-cyan-500">
                <option value="">Jenis Lokasi: Semua</option>
                <option value="PORT" {{ request('location_type') === 'PORT' ? 'selected' : '' }}>Pelabuhan (Port)</option>
                <option value="AIRPORT" {{ request('location_type') === 'AIRPORT' ? 'selected' : '' }}>Bandara (Airport)</option>
                <option value="WAREHOUSE" {{ request('location_type') === 'WAREHOUSE' ? 'selected' : '' }}>Gudang Logistik</option>
                <option value="CUSTOMS_OFFICE" {{ request('location_type') === 'CUSTOMS_OFFICE' ? 'selected' : '' }}>Kantor Pabean</option>
            </select>
            <button type="submit" class="w-full sm:w-auto px-4 py-1.5 text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500 rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Table -->
    <div class="rounded-2xl bg-white dark:bg-[#0A1128] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-900/60 border-b border-slate-200 dark:border-slate-800 text-slate-400 uppercase font-semibold">
                        <th class="py-3 px-4">Nama Lokasi</th>
                        <th class="py-3 px-4">Kota</th>
                        <th class="py-3 px-4">Negara</th>
                        <th class="py-3 px-4">Jenis</th>
                        <th class="py-3 px-4">Koordinat (Lat, Long)</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($locations as $loc)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/40 transition-colors">
                            <td class="py-3.5 px-4 font-bold text-slate-900 dark:text-white">
                                {{ $loc->name }}
                                @if($loc->address)
                                    <span class="block text-[10px] text-slate-400 truncate max-w-xs">{{ $loc->address }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300 font-medium">
                                {{ $loc->city }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                {{ $loc->country }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                                    {{ $loc->location_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500 dark:text-slate-400">
                                {{ number_format($loc->latitude, 4) }}, {{ number_format($loc->longitude, 4) }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $loc->status === 'ACTIVE' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-slate-500/10 text-slate-400' }}">
                                    {{ $loc->status === 'ACTIVE' ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" onclick='previewLocationMap(@json($loc))' class="p-1 rounded text-slate-500 hover:text-cyan-500" title="Pratinjau Peta">
                                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                                    </button>
                                    <button type="button" onclick='openEditModal(@json($loc))' class="p-1 rounded text-slate-500 hover:text-cyan-500" title="Edit">
                                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                                    </button>
                                    <form action="{{ route('admin.locations.destroy', $loc) }}" method="POST" onsubmit="return confirm('Hapus atau nonaktifkan lokasi ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-slate-500 hover:text-rose-500" title="Hapus">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada lokasi terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($locations->hasPages())
            <div class="p-4 border-t border-slate-200 dark:border-slate-800">
                {{ $locations->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Tambah / Edit Lokasi dengan Fitur Geocoding Nominatim -->
<div id="locationModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 shadow-2xl">
        <h3 id="modalTitle" class="text-base font-bold text-slate-900 dark:text-white">Tambah Lokasi Baru</h3>
        
        <!-- Live Geocoding Search Assistant -->
        <div class="mt-3 p-3 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-xs">
            <label class="block font-semibold text-cyan-400 mb-1">Pencarian Geocoding Cepat (Nominatim OpenStreetMap)</label>
            <div class="flex gap-2">
                <input type="text" id="geoQuery" placeholder="Ketik nama pelabuhan/kota (cth: Port of Tokyo)..." class="flex-1 px-3 py-1 text-xs bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                <button type="button" onclick="searchGeocode()" class="px-3 py-1 bg-cyan-600 text-white rounded-lg font-semibold hover:bg-cyan-500">Cari Koordinat</button>
            </div>
            <div id="geoResults" class="mt-2 space-y-1 max-h-24 overflow-y-auto hidden"></div>
        </div>

        <form id="locationForm" method="POST" action="{{ route('admin.locations.store') }}" class="mt-4 space-y-3">
            @csrf
            <div id="methodContainer"></div>

            <div>
                <label for="loc_name" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Lokasi</label>
                <input type="text" id="loc_name" name="name" required placeholder="Contoh: Port of Singapore" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="loc_city" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Kota</label>
                    <input type="text" id="loc_city" name="city" required placeholder="Singapore" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
                <div>
                    <label for="loc_country" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Negara</label>
                    <input type="text" id="loc_country" name="country" required placeholder="Singapore" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="loc_type" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Jenis Lokasi</label>
                    <select id="loc_type" name="location_type" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                        <option value="PORT">Pelabuhan (Port)</option>
                        <option value="AIRPORT">Bandara (Airport)</option>
                        <option value="WAREHOUSE">Gudang Logistik</option>
                        <option value="CUSTOMS_OFFICE">Kantor Pabean</option>
                    </select>
                </div>
                <div>
                    <label for="loc_status" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Status</label>
                    <select id="loc_status" name="status" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white">
                        <option value="ACTIVE">Aktif</option>
                        <option value="INACTIVE">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="loc_lat" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Latitude</label>
                    <input type="number" step="0.0000001" id="loc_lat" name="latitude" required placeholder="1.2650" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white font-mono">
                </div>
                <div>
                    <label for="loc_lng" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Longitude</label>
                    <input type="number" step="0.0000001" id="loc_lng" name="longitude" required placeholder="103.8190" class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white font-mono">
                </div>
            </div>

            <div>
                <label for="loc_address" class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Alamat Lengkap (Opsional)</label>
                <textarea id="loc_address" name="address" rows="2" placeholder="Alamat fisik area dermaga atau terminal kargo..." class="w-full px-3 py-1.5 text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-200 dark:border-slate-800">
                <button type="button" onclick="closeLocationModal()" class="px-3.5 py-1.5 rounded-lg text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">Batal</button>
                <button type="submit" class="px-4 py-1.5 rounded-lg text-xs font-semibold text-white bg-cyan-600 hover:bg-cyan-500">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Preview Map Lokasi -->
<div id="previewMapModal" class="fixed inset-0 z-50 hidden bg-slate-950/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-[#0A1128] rounded-2xl border border-slate-200 dark:border-slate-800 max-w-xl w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200 dark:border-slate-800">
            <h3 id="previewTitle" class="text-sm font-bold text-slate-900 dark:text-white">Pratinjau Peta Lokasi</h3>
            <button type="button" onclick="closePreviewModal()" class="text-slate-400 hover:text-slate-200"><i data-lucide="x" class="w-4 h-4"></i></button>
        </div>
        <div id="locPreviewMap" class="w-full h-72 rounded-xl mt-4 border border-slate-200 dark:border-slate-800 z-10"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let previewMap = null;

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Lokasi Baru';
        document.getElementById('locationForm').action = "{{ route('admin.locations.store') }}";
        document.getElementById('methodContainer').innerHTML = '';
        document.getElementById('loc_name').value = '';
        document.getElementById('loc_city').value = '';
        document.getElementById('loc_country').value = '';
        document.getElementById('loc_type').value = 'PORT';
        document.getElementById('loc_lat').value = '';
        document.getElementById('loc_lng').value = '';
        document.getElementById('loc_address').value = '';
        document.getElementById('loc_status').value = 'ACTIVE';
        document.getElementById('locationModal').classList.remove('hidden');
    }

    function openEditModal(loc) {
        document.getElementById('modalTitle').textContent = 'Edit Data Lokasi: ' + loc.name;
        document.getElementById('locationForm').action = '/admin/locations/' + loc.id;
        document.getElementById('methodContainer').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('loc_name').value = loc.name;
        document.getElementById('loc_city').value = loc.city;
        document.getElementById('loc_country').value = loc.country;
        document.getElementById('loc_type').value = loc.location_type;
        document.getElementById('loc_lat').value = loc.latitude;
        document.getElementById('loc_lng').value = loc.longitude;
        document.getElementById('loc_address').value = loc.address || '';
        document.getElementById('loc_status').value = loc.status;
        document.getElementById('locationModal').classList.remove('hidden');
    }

    function closeLocationModal() {
        document.getElementById('locationModal').classList.add('hidden');
    }

    async function searchGeocode() {
        const q = document.getElementById('geoQuery').value;
        if (!q) return;

        const resultsEl = document.getElementById('geoResults');
        resultsEl.innerHTML = '<p class="text-[10px] text-slate-400">Mencari koordinat...</p>';
        resultsEl.classList.remove('hidden');

        try {
            const res = await fetch('/admin/locations/geocode?q=' + encodeURIComponent(q));
            const data = await res.json();
            
            resultsEl.innerHTML = '';
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(item => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'w-full text-left p-1.5 rounded bg-slate-800 text-[11px] text-slate-200 hover:bg-cyan-600 transition truncate block';
                    btn.textContent = `${item.display_name} (${parseFloat(item.lat).toFixed(4)}, ${parseFloat(item.lon).toFixed(4)})`;
                    btn.onclick = () => {
                        document.getElementById('loc_lat').value = parseFloat(item.lat).toFixed(7);
                        document.getElementById('loc_lng').value = parseFloat(item.lon).toFixed(7);
                        document.getElementById('loc_address').value = item.display_name;
                        resultsEl.classList.add('hidden');
                    };
                    resultsEl.appendChild(btn);
                });
            } else {
                resultsEl.innerHTML = '<p class="text-[10px] text-rose-400">Lokasi tidak ditemukan di OpenStreetMap.</p>';
            }
        } catch (e) {
            resultsEl.innerHTML = '<p class="text-[10px] text-rose-400">Gagal mengambil koordinat.</p>';
        }
    }

    function previewLocationMap(loc) {
        document.getElementById('previewTitle').textContent = `Peta: ${loc.name} (${loc.city}, ${loc.country})`;
        document.getElementById('previewMapModal').classList.remove('hidden');

        setTimeout(() => {
            if (!previewMap) {
                previewMap = L.map('locPreviewMap');
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(previewMap);
            }
            previewMap.setView([loc.latitude, loc.longitude], 12);
            L.circleMarker([loc.latitude, loc.longitude], {
                radius: 8,
                fillColor: '#00A8E8',
                color: '#fff',
                weight: 2,
                fillOpacity: 1
            }).addTo(previewMap).bindPopup(`<b>${loc.name}</b><br>${loc.location_type}`).openPopup();
        }, 150);
    }

    function closePreviewModal() {
        document.getElementById('previewMapModal').classList.add('hidden');
    }
</script>
@endpush

