<script>
/**
 * Master Data Lengkap (38 Provinsi Indonesia + Provinsi/State Global)
 * + Live CountriesNow API (/api/geo/provinces & /api/geo/province-cities)
 * + Searchable Filterable Combobox UI untuk Provinsi, Kota, dan Daerah
 */
window.GEO_PROVINCES_URL = @json(route('geo.provinces'));
window.GEO_PROVINCE_CITIES_URL = @json(route('geo.province_cities'));

window.fallbackProvincesMap = {
    'Indonesia': {
        'Aceh': ['Banda Aceh', 'Sabang', 'Langsa', 'Lhokseumawe', 'Subulussalam', 'Aceh Besar', 'Meulaboh'],
        'Sumatera Utara': ['Medan', 'Binjai', 'Pematangsiantar', 'Tebing Tinggi', 'Tanjungbalai', 'Sibolga', 'Padangsidimpuan', 'Deli Serdang'],
        'Sumatera Barat': ['Padang', 'Bukittinggi', 'Payakumbuh', 'Pariaman', 'Padang Panjang', 'Solok', 'Sawahlunto'],
        'Riau': ['Pekanbaru', 'Dumai', 'Bengkalis', 'Siak', 'Kampar', 'Indragiri Hilir'],
        'Kepulauan Riau': ['Batam', 'Tanjungpinang', 'Bintan', 'Karimun', 'Natuna'],
        'Jambi': ['Jambi', 'Sungai Penuh', 'Muara Bungo', 'Kuala Tungkal'],
        'Sumatera Selatan': ['Palembang', 'Prabumulih', 'Lubuklinggau', 'Pagar Alam', 'Baturaja', 'Muara Enim'],
        'Kepulauan Bangka Belitung': ['Pangkalpinang', 'Tanjung Pandan (Belitung)', 'Sungailiat'],
        'Bengkulu': ['Bengkulu', 'Curup', 'Manna', 'Argamakmur'],
        'Lampung': ['Bandar Lampung', 'Metro', 'Kalianda', 'Kotabumi', 'Pringsewu'],
        'DKI Jakarta': ['Jakarta Selatan', 'Jakarta Pusat', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara', 'Kepulauan Seribu'],
        'Jawa Barat': ['Bandung', 'Depok', 'Bekasi', 'Bogor', 'Cimahi', 'Cirebon', 'Karawang', 'Sukabumi', 'Tasikmalaya', 'Banjar', 'Garut', 'Purwakarta', 'Subang'],
        'Banten': ['Tangerang', 'Tangerang Selatan', 'Serang', 'Cilegon', 'Pandeglang', 'Lebak'],
        'Jawa Tengah': ['Semarang', 'Surakarta (Solo)', 'Magelang', 'Pekalongan', 'Tegal', 'Salatiga', 'Cilacap', 'Purwokerto', 'Kudus'],
        'DI Yogyakarta': ['Yogyakarta', 'Sleman', 'Bantul', 'Kulon Progo', 'Gunungkidul'],
        'Jawa Timur': ['Surabaya', 'Malang', 'Sidoarjo', 'Gresik', 'Banyuwangi', 'Kediri', 'Madiun', 'Mojokerto', 'Pasuruan', 'Probolinggo', 'Blitar', 'Batu', 'Jember'],
        'Bali': ['Denpasar (Bali)', 'Badung', 'Gianyar', 'Tabanan', 'Buleleng (Singaraja)', 'Klungkung', 'Karangasem'],
        'Nusa Tenggara Barat': ['Mataram', 'Bima', 'Sumbawa Besar', 'Praya'],
        'Nusa Tenggara Timur': ['Kupang', 'Labuan Bajo', 'Maumere', 'Ende', 'Waingapu'],
        'Kalimantan Barat': ['Pontianak', 'Singkawang', 'Ketapang', 'Sintang', 'Sambas'],
        'Kalimantan Tengah': ['Palangka Raya', 'Sampit', 'Pangkalan Bun', 'Kuala Kapuas'],
        'Kalimantan Selatan': ['Banjarmasin', 'Banjarbaru', 'Martapura', 'Kotabaru'],
        'Kalimantan Timur': ['Balikpapan', 'Samarinda', 'Bontang', 'Tenggarong', 'Sangatta'],
        'Kalimantan Utara': ['Tarakan', 'Tanjung Selor', 'Nunukan', 'Malinau'],
        'Sulawesi Utara': ['Manado', 'Bitung', 'Tomohon', 'Kotamobagu', 'Minahasa'],
        'Gorontalo': ['Gorontalo', 'Limboto', 'Pohuwato'],
        'Sulawesi Tengah': ['Palu', 'Luwuk', 'Poso', 'Tolitoli'],
        'Sulawesi Barat': ['Mamuju', 'Majene', 'Polewali Mandar'],
        'Sulawesi Selatan': ['Makassar', 'Parepare', 'Palopo', 'Gowa', 'Maros', 'Bone'],
        'Sulawesi Tenggara': ['Kendari', 'Baubau', 'Kolaka', 'Konawe'],
        'Maluku': ['Ambon', 'Tual', 'Masohi'],
        'Maluku Utara': ['Ternate', 'Tidore Kepulauan', 'Sofifi', 'Tobelo'],
        'Papua': ['Jayapura', 'Biak', 'Sarmi'],
        'Papua Barat': ['Manokwari', 'Fakfak', 'Kaimana'],
        'Papua Selatan': ['Merauke', 'Boven Digoel'],
        'Papua Tengah': ['Nabire', 'Timika (Mimika)'],
        'Papua Pegunungan': ['Wamena (Jayawijaya)'],
        'Papua Barat Daya': ['Sorong', 'Raja Ampat']
    },
    'Malaysia': {
        'Kuala Lumpur': ['Kuala Lumpur'],
        'Putrajaya': ['Putrajaya'],
        'Labuan': ['Victoria (Labuan)'],
        'Selangor': ['Port Klang', 'Shah Alam', 'Petaling Jaya', 'Subang Jaya', 'Klang'],
        'Pulau Pinang': ['George Town (Penang)', 'Butterworth', 'Bayan Lepas'],
        'Johor': ['Johor Bahru', 'Pasir Gudang', 'Batu Pahat', 'Muar'],
        'Perak': ['Ipoh', 'Taiping', 'Lumut'],
        'Melaka': ['Melaka', 'Ayer Keroh'],
        'Negeri Sembilan': ['Seremban', 'Port Dickson'],
        'Pahang': ['Kuantan', 'Bentong'],
        'Kedah': ['Alor Setar', 'Sungai Petani', 'Langkawi'],
        'Kelantan': ['Kota Bharu'],
        'Terengganu': ['Kuala Terengganu', 'Kemaman'],
        'Perlis': ['Kangar'],
        'Sabah': ['Kota Kinabalu', 'Sandakan', 'Tawau'],
        'Sarawak': ['Kuching', 'Miri', 'Bintulu', 'Sibu']
    },
    'Singapura': {
        'Central Region': ['Singapore', 'Marina Bay', 'Orchard', 'Tanjong Pagar'],
        'East Region': ['Changi', 'Tampines', 'Bedok'],
        'West Region': ['Jurong', 'Tuas'],
        'North Region': ['Woodlands', 'Yishun'],
        'North-East Region': ['Ang Mo Kio', 'Punggol', 'Sengkang']
    },
    'Singapore': {
        'Central Region': ['Singapore', 'Marina Bay', 'Orchard', 'Tanjong Pagar'],
        'East Region': ['Changi', 'Tampines', 'Bedok'],
        'West Region': ['Jurong', 'Tuas'],
        'North Region': ['Woodlands', 'Yishun'],
        'North-East Region': ['Ang Mo Kio', 'Punggol', 'Sengkang']
    }
};

window.extraDistrictsMap = {
    'Jakarta Selatan': ['Kebayoran Baru (Senopati)', 'Setiabudi (Kuningan)', 'Kemang', 'Cilandak', 'Pondok Indah', 'Tebet', 'Senayan'],
    'Jakarta Pusat': ['Menteng', 'Gambir', 'Tanah Abang', 'Kemayoran', 'Senen'],
    'Jakarta Barat': ['Kebon Jeruk', 'Grogol Petamburan', 'Palmerah', 'Kalideres', 'Cengkareng'],
    'Jakarta Timur': ['Cakung', 'Jatinegara', 'Matraman', 'Duren Sawit', 'Pulo Gadung'],
    'Jakarta Utara': ['Tanjung Priok', 'Kelapa Gading', 'Pantai Indah Kapuk (PIK)', 'Penjaringan', 'Koja'],
    'Depok': ['Margonda (Beji)', 'Cinere', 'Sawangan', 'Sukmajaya', 'Cimanggis', 'Pancoran Mas'],
    'Bekasi': ['Bekasi Barat', 'Bekasi Selatan (Summarecon)', 'Bekasi Timur', 'Cikarang Industri', 'Pondok Gede'],
    'Bogor': ['Bogor Tengah', 'Bogor Selatan', 'Bogor Utara', 'Sentul City', 'Cibinong'],
    'Tangerang': ['Karawaci', 'Cipondoh', 'Ciledug', 'Batuceper (Soekarno-Hatta)', 'Alam Sutera'],
    'Tangerang Selatan': ['BSD City (Serpong)', 'Bintaro (Pondok Aren)', 'Ciputat', 'Pamulang'],
    'Cilegon': ['Pelabuhan Merak', 'Cilegon Industri', 'Ciwandan'],
    'Makassar': ['Pelabuhan Makassar (Soekarno-Hatta)', 'Panakkukang', 'Tamalanrea', 'Ujung Pandang', 'Rappocini', 'Makassar New Port'],
    'Palembang': ['Ilir Timur I', 'Ilir Barat I', 'Seberang Ulu I', 'Pelabuhan Boom Baru', 'Alang-Alang Lebar'],
    'Balikpapan': ['Balikpapan Kota', 'Balikpapan Selatan', 'Balikpapan Barat (Pelabuhan Semayang)', 'Sepinggan'],
    'Batam': ['Batam Center', 'Nagoya (Lubuk Baja)', 'Batu Ampar Port', 'Sekupang'],
    'Malang': ['Klojen', 'Lowokwaru', 'Blimbing', 'Sukun'],
    'Sleman': ['Depok (Sleman)', 'Mlati', 'Ngaglik', 'Kalasan']
};

/**
 * Searchable Dropdown Helper (Ketik untuk memfilter pilihan secara instan)
 */
window._searchableWrappers = window._searchableWrappers || {};

window.initSearchableSelect = function (selectId, searchPlaceholder) {
    const select = document.getElementById(selectId);
    if (!select) return;

    if (!window._searchableWrappers[selectId]) {
        select.classList.add('hidden');

        const wrapper = document.createElement('div');
        wrapper.className = 'relative w-full';
        select.parentNode.insertBefore(wrapper, select.nextSibling);

        const trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500 flex items-center justify-between gap-2 text-left';
        trigger.innerHTML = `<span class="truncate" data-label>— Pilih —</span><svg class="w-3.5 h-3.5 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>`;

        const panel = document.createElement('div');
        panel.className = 'hidden absolute left-0 right-0 mt-1 z-50 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-[#0D1733] shadow-xl overflow-hidden';

        const searchWrap = document.createElement('div');
        searchWrap.className = 'p-2 border-b border-slate-100 dark:border-slate-800';
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = searchPlaceholder || 'Ketik untuk mencari...';
        searchInput.className = 'w-full px-2.5 py-1.5 text-xs rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-cyan-500';
        searchWrap.appendChild(searchInput);

        const list = document.createElement('div');
        list.className = 'max-h-52 overflow-y-auto py-1';

        panel.appendChild(searchWrap);
        panel.appendChild(list);
        wrapper.appendChild(trigger);
        wrapper.appendChild(panel);

        const renderOptions = (filterText = '') => {
            const q = filterText.toLowerCase().trim();
            list.innerHTML = '';
            let matchCount = 0;

            Array.from(select.options).forEach(opt => {
                if (!opt.value) return;
                if (q && !opt.text.toLowerCase().includes(q)) return;

                matchCount++;
                const item = document.createElement('button');
                item.type = 'button';
                const isSelected = opt.value === select.value;
                item.className = `w-full text-left px-3 py-1.5 text-xs transition-colors flex items-center justify-between ${
                    isSelected
                        ? 'bg-cyan-500/15 text-cyan-600 dark:text-cyan-400 font-bold'
                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800'
                }`;
                item.textContent = opt.text;
                item.addEventListener('click', () => {
                    select.value = opt.value;
                    panel.classList.add('hidden');
                    window.syncSearchableSelect(selectId);
                    select.dispatchEvent(new Event('change'));
                });
                list.appendChild(item);
            });

            if (matchCount === 0) {
                const empty = document.createElement('div');
                empty.className = 'px-3 py-2 text-[11px] text-slate-400 italic';
                empty.textContent = 'Tidak ada hasil yang cocok';
                list.appendChild(empty);
            }
        };

        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = panel.classList.contains('hidden');
            // Tutup panel lain
            Object.values(window._searchableWrappers).forEach(w => w.panel.classList.add('hidden'));
            if (isHidden) {
                searchInput.value = '';
                renderOptions('');
                panel.classList.remove('hidden');
                setTimeout(() => searchInput.focus(), 20);
            }
        });

        searchInput.addEventListener('input', () => renderOptions(searchInput.value));
        searchInput.addEventListener('click', (e) => e.stopPropagation());

        document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });

        window._searchableWrappers[selectId] = { select, trigger, panel, renderOptions };
    }

    window.syncSearchableSelect(selectId);
};

window.syncSearchableSelect = function (selectId) {
    const entry = window._searchableWrappers[selectId];
    if (!entry) return;
    const { select, trigger } = entry;
    const labelEl = trigger.querySelector('[data-label]');
    const selectedOpt = select.options[select.selectedIndex];
    if (labelEl) {
        labelEl.textContent = (selectedOpt && selectedOpt.text) ? selectedOpt.text : '— Pilih —';
    }
};

window.findProvinceMapForCountry = function (country) {
    if (!country) return null;
    const cleanCountry = country.toLowerCase().trim();
    for (const [k, v] of Object.entries(window.fallbackProvincesMap)) {
        const kLower = k.toLowerCase();
        if (cleanCountry === kLower || cleanCountry.includes(kLower) || kLower.includes(cleanCountry)) {
            return v;
        }
    }
    return null;
};

window.populateProvincesAndCascade = async function (country) {
    if (!country) return;
    if (typeof updateDestinationNodesByCountry === 'function') {
        updateDestinationNodesByCountry(country);
    }

    const provinceSelect = document.getElementById('delivery_province');
    if (!provinceSelect) return;

    const provinceMap = window.findProvinceMapForCountry(country);

    if (provinceMap) {
        const provinces = Object.keys(provinceMap);
        let opts = '<option value="">— Pilih Provinsi / State —</option>';
        provinces.forEach((prov, idx) => {
            opts += `<option value="${prov}" ${idx === 0 ? 'selected' : ''}>${prov}</option>`;
        });
        provinceSelect.innerHTML = opts;
        provinceSelect.value = provinces[0];
        window.initSearchableSelect('delivery_province', 'Ketik nama provinsi...');
        onProvinceSelected(provinces[0]);
        return;
    }

    // Negara lain: ambil seluruh provinsi/state dari CountriesNow API
    provinceSelect.innerHTML = `<option value="">Memuat seluruh provinsi ${country}...</option>`;
    window.initSearchableSelect('delivery_province', 'Ketik nama provinsi / state...');

    try {
        const res = await fetch(`${window.GEO_PROVINCES_URL}?country=${encodeURIComponent(country)}`);
        if (res.ok) {
            const json = await res.json();
            if (json.data && Array.isArray(json.data) && json.data.length > 0) {
                const provinces = json.data;
                let opts = '<option value="">— Pilih Provinsi / State —</option>';
                provinces.forEach((prov, idx) => {
                    opts += `<option value="${prov}" ${idx === 0 ? 'selected' : ''}>${prov}</option>`;
                });
                provinceSelect.innerHTML = opts;
                provinceSelect.value = provinces[0];
                window.initSearchableSelect('delivery_province', 'Ketik nama provinsi / state...');
                onProvinceSelected(provinces[0]);
                return;
            }
        }
    } catch (e) {
        console.warn('Live provinces fetch warning:', e);
    }

    // Fallback jika negara tidak memiliki data provinsi di API
    const defaultProv = `Wilayah Utama (${country})`;
    provinceSelect.innerHTML = `<option value="${defaultProv}" selected>${defaultProv}</option>`;
    window.initSearchableSelect('delivery_province', 'Ketik nama provinsi / state...');
    onProvinceSelected(defaultProv);
};

window.onProvinceSelected = async function (province) {
    const countryEl = document.getElementById('delivery_country');
    const citySelect = document.getElementById('delivery_city');
    const districtSelect = document.getElementById('delivery_district');
    const spinner = document.getElementById('city-spinner');
    if (!countryEl || !citySelect) return;

    const country = countryEl.value;
    window.syncSearchableSelect('delivery_province');

    const provinceMap = window.findProvinceMapForCountry(country);
    const localCities = (provinceMap && provinceMap[province]) ? provinceMap[province] : [];

    if (localCities.length > 0) {
        let opts = '<option value="">— Pilih Kota Tujuan —</option>';
        localCities.forEach((city, idx) => {
            opts += `<option value="${city}" ${idx === 0 ? 'selected' : ''}>${city}</option>`;
        });
        citySelect.innerHTML = opts;
        citySelect.value = localCities[0];
        window.initSearchableSelect('delivery_city', 'Ketik nama kota...');
        onCitySelected(localCities[0]);
        return;
    }

    // Fetch live cities for this province via CountriesNow API
    citySelect.innerHTML = `<option value="">Memuat kota di ${province}...</option>`;
    window.initSearchableSelect('delivery_city', 'Ketik nama kota...');
    if (spinner) spinner.classList.remove('hidden');

    try {
        const res = await fetch(`${window.GEO_PROVINCE_CITIES_URL}?country=${encodeURIComponent(country)}&province=${encodeURIComponent(province)}`);
        if (res.ok) {
            const json = await res.json();
            if (json.data && Array.isArray(json.data) && json.data.length > 0) {
                const citiesList = json.data;
                let opts = '<option value="">— Pilih Kota Tujuan —</option>';
                citiesList.forEach((city, idx) => {
                    opts += `<option value="${city}" ${idx === 0 ? 'selected' : ''}>${city}</option>`;
                });
                citySelect.innerHTML = opts;
                citySelect.value = citiesList[0];
                window.initSearchableSelect('delivery_city', 'Ketik nama kota...');
                onCitySelected(citiesList[0]);
                return;
            }
        }
    } catch (e) {
        console.warn('Province cities fetch warning:', e);
    } finally {
        if (spinner) spinner.classList.add('hidden');
    }

    // Fallback ke Geoapify / fallbackCitiesMap jika province-cities kosong
    try {
        const res = await fetch(`${GEO_CITIES_URL}?country=${encodeURIComponent(country)}`);
        if (res.ok) {
            const json = await res.json();
            if (json.data && Array.isArray(json.data) && json.data.length > 0) {
                const citiesList = json.data;
                let opts = '<option value="">— Pilih Kota Tujuan —</option>';
                citiesList.forEach((city, idx) => {
                    opts += `<option value="${city}" ${idx === 0 ? 'selected' : ''}>${city}</option>`;
                });
                citySelect.innerHTML = opts;
                citySelect.value = citiesList[0];
                window.initSearchableSelect('delivery_city', 'Ketik nama kota...');
                onCitySelected(citiesList[0]);
                return;
            }
        }
    } catch (e) {
        console.warn('Fallback cities fetch warning:', e);
    }

    if (districtSelect) {
        districtSelect.innerHTML = '<option value="">— Pilih Daerah / Kecamatan —</option>';
        window.initSearchableSelect('delivery_district', 'Ketik nama daerah / kecamatan...');
    }
};

// Sinkronkan searchable dropdown kota & daerah setelah onCitySelected selesai
document.addEventListener('DOMContentLoaded', () => {
    window.initSearchableSelect('delivery_province', 'Ketik nama provinsi / state...');
    window.initSearchableSelect('delivery_city', 'Ketik nama kota...');
    window.initSearchableSelect('delivery_district', 'Ketik nama daerah / kecamatan...');

    // Observe options changes on city & district selects to keep searchable UI synced
    ['delivery_city', 'delivery_district'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            new MutationObserver(() => {
                window.syncSearchableSelect(id);
            }).observe(el, { childList: true, attributes: true, attributeFilter: ['value'] });
        }
    });
});
</script>
