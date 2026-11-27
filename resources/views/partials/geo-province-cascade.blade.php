<script>
/**
 * Cascading Master Data: Negara -> Provinsi / State -> Kota -> Daerah
 */
window.fallbackProvincesMap = {
    'Indonesia': {
        'DKI Jakarta': ['Jakarta Selatan', 'Jakarta Pusat', 'Jakarta Barat', 'Jakarta Timur', 'Jakarta Utara'],
        'Jawa Barat': ['Bandung', 'Depok', 'Bekasi', 'Bogor', 'Cimahi', 'Cirebon', 'Karawang', 'Sukabumi', 'Tasikmalaya'],
        'Banten': ['Tangerang', 'Tangerang Selatan', 'Serang', 'Cilegon'],
        'Jawa Tengah': ['Semarang', 'Surakarta (Solo)', 'Magelang', 'Pekalongan', 'Tegal', 'Cilacap'],
        'DI Yogyakarta': ['Yogyakarta', 'Sleman', 'Bantul'],
        'Jawa Timur': ['Surabaya', 'Malang', 'Sidoarjo', 'Gresik', 'Banyuwangi', 'Kediri'],
        'Bali': ['Denpasar (Bali)', 'Badung', 'Gianyar', 'Tabanan'],
        'Sumatera Utara': ['Medan', 'Binjai', 'Deli Serdang', 'Pematangsiantar'],
        'Sumatera Selatan': ['Palembang', 'Prabumulih', 'Lubuklinggau'],
        'Riau': ['Pekanbaru', 'Dumai'],
        'Kepulauan Riau': ['Batam', 'Tanjungpinang'],
        'Lampung': ['Bandar Lampung', 'Metro'],
        'Kalimantan Timur': ['Balikpapan', 'Samarinda', 'Bontang'],
        'Kalimantan Selatan': ['Banjarmasin', 'Banjarbaru'],
        'Kalimantan Barat': ['Pontianak', 'Singkawang'],
        'Sulawesi Selatan': ['Makassar', 'Gowa', 'Maros', 'Parepare'],
        'Sulawesi Utara': ['Manado', 'Bitung', 'Tomohon']
    },
    'Malaysia': {
        'Wilayah Persekutuan': ['Kuala Lumpur', 'Putrajaya', 'Labuan'],
        'Selangor': ['Port Klang', 'Shah Alam', 'Petaling Jaya', 'Subang Jaya'],
        'Pulau Pinang': ['George Town (Penang)', 'Butterworth', 'Bayan Lepas'],
        'Johor': ['Johor Bahru', 'Pasir Gudang', 'Batu Pahat'],
        'Sabah': ['Kota Kinabalu', 'Sandakan', 'Tawau'],
        'Sarawak': ['Kuching', 'Miri', 'Bintulu']
    },
    'Singapura': {
        'Central Region': ['Singapore', 'Marina Bay', 'Orchard', 'Tanjong Pagar'],
        'East Region': ['Changi', 'Tampines', 'Bedok'],
        'West Region': ['Jurong', 'Tuas'],
        'North Region': ['Woodlands', 'Yishun']
    },
    'Singapore': {
        'Central Region': ['Singapore', 'Marina Bay', 'Orchard', 'Tanjong Pagar'],
        'East Region': ['Changi', 'Tampines', 'Bedok'],
        'West Region': ['Jurong', 'Tuas'],
        'North Region': ['Woodlands', 'Yishun']
    },
    'Jepang': {
        'Kanto': ['Tokyo', 'Yokohama', 'Kawasaki', 'Chiba'],
        'Kansai': ['Osaka', 'Kobe', 'Kyoto'],
        'Chubu': ['Nagoya', 'Shizuoka'],
        'Kyushu': ['Fukuoka', 'Kitakyushu'],
        'Hokkaido': ['Sapporo']
    },
    'Japan': {
        'Kanto': ['Tokyo', 'Yokohama', 'Kawasaki', 'Chiba'],
        'Kansai': ['Osaka', 'Kobe', 'Kyoto'],
        'Chubu': ['Nagoya', 'Shizuoka'],
        'Kyushu': ['Fukuoka', 'Kitakyushu'],
        'Hokkaido': ['Sapporo']
    },
    'Amerika Serikat': {
        'California': ['Los Angeles', 'San Francisco', 'San Diego', 'Oakland'],
        'New York': ['New York', 'Buffalo'],
        'Texas': ['Houston', 'Dallas', 'Austin'],
        'Florida': ['Miami', 'Jacksonville', 'Tampa'],
        'Washington': ['Seattle', 'Tacoma'],
        'Illinois': ['Chicago']
    },
    'United States': {
        'California': ['Los Angeles', 'San Francisco', 'San Diego', 'Oakland'],
        'New York': ['New York', 'Buffalo'],
        'Texas': ['Houston', 'Dallas', 'Austin'],
        'Florida': ['Miami', 'Jacksonville', 'Tampa'],
        'Washington': ['Seattle', 'Tacoma'],
        'Illinois': ['Chicago']
    },
    'Australia': {
        'New South Wales': ['Sydney', 'Newcastle'],
        'Victoria': ['Melbourne', 'Geelong'],
        'Queensland': ['Brisbane', 'Gold Coast'],
        'Western Australia': ['Perth', 'Fremantle'],
        'South Australia': ['Adelaide']
    },
    'Belanda': {
        'South Holland': ['Rotterdam', 'The Hague'],
        'North Holland': ['Amsterdam', 'Haarlem'],
        'Utrecht': ['Utrecht'],
        'North Brabant': ['Eindhoven', 'Tilburg', 'Breda']
    },
    'Netherlands': {
        'South Holland': ['Rotterdam', 'The Hague'],
        'North Holland': ['Amsterdam', 'Haarlem'],
        'Utrecht': ['Utrecht'],
        'North Brabant': ['Eindhoven', 'Tilburg', 'Breda']
    },
    'Jerman': {
        'Hamburg': ['Hamburg'],
        'Bremen': ['Bremen'],
        'Berlin': ['Berlin'],
        'Hesse': ['Frankfurt'],
        'Bavaria': ['Munich', 'Nuremberg'],
        'North Rhine-Westphalia': ['Cologne', 'Düsseldorf']
    },
    'Germany': {
        'Hamburg': ['Hamburg'],
        'Bremen': ['Bremen'],
        'Berlin': ['Berlin'],
        'Hesse': ['Frankfurt'],
        'Bavaria': ['Munich', 'Nuremberg'],
        'North Rhine-Westphalia': ['Cologne', 'Düsseldorf']
    },
    'Inggris Raya': {
        'Greater London': ['London'],
        'North West England': ['Manchester', 'Liverpool'],
        'West Midlands': ['Birmingham'],
        'South East England': ['Southampton'],
        'Scotland': ['Edinburgh', 'Glasgow']
    },
    'United Kingdom': {
        'Greater London': ['London'],
        'North West England': ['Manchester', 'Liverpool'],
        'West Midlands': ['Birmingham'],
        'South East England': ['Southampton'],
        'Scotland': ['Edinburgh', 'Glasgow']
    },
    'Brazil': {
        'São Paulo': ['Santos', 'São Paulo', 'Campinas'],
        'Rio de Janeiro': ['Rio de Janeiro'],
        'Minas Gerais': ['Belo Horizonte'],
        'Paraná': ['Curitiba', 'Paranaguá'],
        'Rio Grande do Sul': ['Porto Alegre']
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
    const citySelect = document.getElementById('delivery_city');
    const districtSelect = document.getElementById('delivery_district');
    const provinceMap = window.findProvinceMapForCountry(country);

    if (provinceSelect && provinceMap) {
        const provinces = Object.keys(provinceMap);
        let opts = '<option value="">— Pilih Provinsi / State —</option>';
        provinces.forEach((prov, idx) => {
            opts += `<option value="${prov}" ${idx === 0 ? 'selected' : ''}>${prov}</option>`;
        });
        provinceSelect.innerHTML = opts;
        provinceSelect.value = provinces[0];
        onProvinceSelected(provinces[0]);
        return;
    }

    // Fallback for countries without pre-mapped provinces: single province wrapper + Geoapify cities
    if (provinceSelect) {
        const defaultProv = `Wilayah Utama (${country})`;
        provinceSelect.innerHTML = `<option value="${defaultProv}" selected>${defaultProv}</option>`;
    }

    let baseCities = null;
    if (typeof fallbackCitiesMap !== 'undefined') {
        const cleanCountry = country.toLowerCase().trim();
        for (const [k, v] of Object.entries(fallbackCitiesMap)) {
            const kLower = k.toLowerCase();
            if (cleanCountry === kLower || cleanCountry.includes(kLower) || kLower.includes(cleanCountry)) {
                baseCities = v;
                break;
            }
        }
    }

    if (baseCities && baseCities.length > 0 && citySelect) {
        let opts = '<option value="">— Pilih Kota Tujuan —</option>';
        baseCities.forEach((city, idx) => {
            opts += `<option value="${city}" ${idx === 0 ? 'selected' : ''}>${city}</option>`;
        });
        citySelect.innerHTML = opts;
        citySelect.value = baseCities[0];
        onCitySelected(baseCities[0]);
    } else if (citySelect) {
        citySelect.innerHTML = `<option value="">Memuat kota ${country} via Geoapify...</option>`;
        if (districtSelect) districtSelect.innerHTML = '<option value="">— Pilih Daerah Setelah Kota —</option>';
    }

    const spinner = document.getElementById('city-spinner');
    if (spinner) spinner.classList.remove('hidden');
    try {
        const res = await fetch(`${GEO_CITIES_URL}?country=${encodeURIComponent(country)}`);
        if (res.ok) {
            const json = await res.json();
            if (json.data && Array.isArray(json.data) && json.data.length > 0 && citySelect) {
                const citiesList = json.data;
                const selectedNow = citySelect.value;
                let refreshedOpts = '<option value="">— Pilih Kota Tujuan —</option>';
                citiesList.forEach(city => {
                    refreshedOpts += `<option value="${city}" ${city === selectedNow ? 'selected' : ''}>${city}</option>`;
                });
                citySelect.innerHTML = refreshedOpts;
                if (!citySelect.value && citiesList[0]) {
                    citySelect.value = citiesList[0];
                    onCitySelected(citiesList[0]);
                }
            }
        }
    } catch (e) {
        console.warn('Live Geoapify city fetch warning:', e);
    } finally {
        if (spinner) spinner.classList.add('hidden');
    }
};

window.onProvinceSelected = function (province) {
    const countryEl = document.getElementById('delivery_country');
    const citySelect = document.getElementById('delivery_city');
    const districtSelect = document.getElementById('delivery_district');
    if (!countryEl || !citySelect) return;

    const provinceMap = window.findProvinceMapForCountry(countryEl.value);
    const cities = (provinceMap && provinceMap[province]) ? provinceMap[province] : [];

    if (cities.length > 0) {
        let opts = '<option value="">— Pilih Kota Tujuan —</option>';
        cities.forEach((city, idx) => {
            opts += `<option value="${city}" ${idx === 0 ? 'selected' : ''}>${city}</option>`;
        });
        citySelect.innerHTML = opts;
        citySelect.value = cities[0];
        onCitySelected(cities[0]);
    } else {
        citySelect.innerHTML = '<option value="">— Pilih Kota —</option>';
        if (districtSelect) districtSelect.innerHTML = '<option value="">— Pilih Daerah / Kecamatan —</option>';
    }
};
</script>
