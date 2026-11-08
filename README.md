# CargoTrace – Global Shipment Visibility & Tracking Platform

> **Enterprise Global Logistics Tracking & Supply Chain Risk Intelligence System**  
> Proyek Akhir / **Ujian Akhir Semester (UAS) Pemrograman Web 2**  
> Dibangun dengan **Laravel 13**, **PHP 8.5**, **MySQL 8**, **REST API (Sanctum)**, **Leaflet.js**, **Chart.js**, dan **Tailwind CSS**.

---

## 🌟 Ringkasan Sistem & Arsitektur

CargoTrace dirancang untuk memantau pengiriman barang global secara multi-moda (maritim, udara, dan darat) lintas benua, mengintegrasikan data cuaca riil, indikator makroekonomi, kurs mata uang, analisis sentimen berita logistik, dan direktori 40+ pelabuhan dunia.

Aplikasi ini mengimplementasikan **Role-Based Access Control (RBAC)** dengan 3 hak akses terpisah:
1. **Administrator** (22 Halaman): Pengawasan sistem, analitik global, audit log, manajemen user, carrier, lokasi, dan konfigurasi platform.
2. **Logistics Operator** (18 Halaman): Operasional pengiriman, pencatatan tracking event (*append-only*), pelaporan & penyelesaian *exceptions*, pemantauan cuaca, dan intelijen risiko.
3. **Customer Portal** (13 Halaman): Akses mandiri (*self-service*) berorientasi pelanggan dengan proteksi **Anti-IDOR**, tracking stepper, estimasi waktu tiba (ETA), notifikasi, dan profil kargo milik sendiri.

---

## 🌐 Integrasi Real-Time Public APIs (Sesuai Ketentuan UAS)

CargoTrace mengintegrasikan 5 sumber data publik langsung dari internet dengan mekanisme caching cerdas dan *graceful fallback*:

| Sumber API | Endpoint Eksternal | Penggunaan di CargoTrace | Cache TTL |
| :--- | :--- | :--- | :--- |
| **Open-Meteo API** | `https://api.open-meteo.com/v1/forecast` | Cuaca maritim, temperatur, kelembapan, kecepatan angin, curah hujan, & peringatan badai | 15 Menit |
| **World Bank API** | `https://api.worldbank.org/v2/country/{code}/indicator/...` | PDB/GDP nominal, tingkat inflasi, dan jumlah populasi negara | 1 Jam |
| **REST Countries API** | `https://restcountries.com/v3.1/alpha/{code}` | Profil negara, kode mata uang, region, dan bahasa resmi | 1 Jam |
| **ExchangeRate-API** | `https://open.er-api.com/v6/latest/{base}` | Kurs valuta asing langsung (USD, EUR, GBP, IDR, SGD, CNY, JPY) | 30 Menit |
| **World Port Index Dataset** | Built-in Comprehensive Dataset | Direktori 40+ pelabuhan peti kemas & hub logistik utama dunia | Terindeks di DB |
| **Lexicon Sentiment Analysis** | Algoritma PHP Native | Analisis sentimen berita logistik global (Positif, Netral, Negatif) | Real-time |

### Model Perhitungan Risiko Rantai Pasok Terbobot (Risk Score Formula):
$$\text{Skor Risiko} = (0.35 \times \text{Cuaca}) + (0.25 \times \text{Volatilitas Kurs}) + (0.20 \times \text{Tingkat Inflasi}) + (0.20 \times \text{Sentimen Berita})$$

---

## 📡 5 Public Intelligence REST Endpoints Wajib

Sesuai spesifikasi proyek akhir, berikut 5 endpoint publik yang dapat diakses langsung oleh sistem luar maupun frontend:

| Method | Endpoint | Keterangan |
| :--- | :--- | :--- |
| `GET` | `/api/countries?code=ID` | Profil makroekonomi negara dari World Bank & REST Countries |
| `GET` | `/api/risk` | Indeks risiko rantai pasok global hasil kalkulasi formula terbobot |
| `GET` | `/api/ports` | Direktori lengkap pelabuhan peti kemas dunia (dapat difilter via `?search=`) |
| `GET` | `/api/news` | Berita logistik terkini disertai skor sentimen leksikon positif/negatif |
| `GET` | `/api/currency?base=USD` | Kurs valuta asing langsung dari ExchangeRate-API |

---

## 👥 Akun Demo & Hak Akses

| Peran (Role) | Email | Password | Cakupan Akses |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.id` | `password` | Pengawasan penuh, CRUD user, carrier, lokasi, audit logs, setting |
| **Logistics Operator** | `operator@cargotrace.id` | `password` | Create & update shipment, append-only tracking, manajemen pengecualian |
| **Customer** | `customer@globalmart.com` | `password` | Hanya melihat pengiriman miliknya sendiri, ETA, timeline, peta, profil |

> **Catatan Ujian/Presentasi**: Halaman login menyediakan tombol *Quick-Fill* sekali klik untuk masing-masing peran di atas.

---

## 🚀 Panduan Instalasi & Menjalankan Proyek

### Prasyarat
- PHP 8.3 atau PHP 8.5
- Composer 2.8+
- MySQL 8.0+ (Laragon / XAMPP / Standalone)
- Node.js & NPM

### Langkah-Langkah

1. **Clone Repositori & Install Dependencies**:
   ```bash
   git clone https://github.com/rafifproject/CargoTrace-UAS-Rafif.git
   cd CargoTrace
   composer install
   npm install
   npm run build
   ```

2. **Konfigurasi Environment (`.env`)**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Pastikan pengaturan database di file `.env` sudah benar:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cargotrace
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Migrasi Database & Seeding Data Global**:
   ```bash
   php artisan migrate:fresh --seed
   ```
   *Seeder akan mengisi 40+ pelabuhan dunia, 9 carrier internasional, 15+ pengiriman lintas samudera, event tracking append-only, dan akun untuk ketiga role.*

4. **Jalankan Aplikasi**:
   ```bash
   php artisan serve
   ```
   Buka browser di: [http://localhost:8000](http://localhost:8000)

---

## 🧪 Pengujian Otomatis (Automated Testing Suite)

Seluruh logika bisnis, keamanan Anti-IDOR, RBAC, dan integrasi API dilindungi oleh automated feature tests:

```bash
php artisan test
```

### Hasil Pengujian: **32 Tests Passed (100%)**
- `AuthenticationTest`: Render halaman login mockup, autentikasi multi-role, registrasi customer publik.
- `RbacAuthorizationTest`: Isolasi route middleware, penolakan akses lintas role (HTTP 403).
- `ShipmentModelTest`: Seeding database, kalkulasi otomatis keterlambatan (*delay*), riwayat event *append-only*.
- `OperatorWorkflowTest`: Pembuatan pengiriman, event tracking otomatis, siklus pelaporan dan penyelesaian *exception*, pembaruan profil operator.
- `CustomerWorkflowTest`: **Anti-IDOR Protection** (Customer A membuka shipment Customer B ditolak HTTP 404), penolakan akses ke admin/operator (HTTP 403), update profil mandiri.
- `CargoTraceApiTest`: Autentikasi Sanctum token, schema JSON, event tracking API, penyelesaian exception via API, dan pengujian **5 Public Intelligence Endpoints** (`/api/countries`, `/api/risk`, `/api/ports`, `/api/news`, `/api/currency`).
- `GlobalIntelligenceServiceTest`: Mocking data cuaca Open-Meteo, kalkulasi formula terbobot Supply Chain Risk Index.

---

## 📑 Postman Collection

File Postman collection tersedia di root proyek: `CargoTrace_API_Collection.json`.
Endpoints yang tercakup:
- **Autentikasi**: `POST /api/login`, `POST /api/register`, `POST /api/logout`, `GET /api/user`
- **Operasional**: `GET /api/shipments`, `POST /api/shipments`, `GET /api/shipments/{id}`, `PUT /api/shipments/{id}`
- **Tracking (Append-Only)**: `GET /api/shipments/{id}/tracking`, `POST /api/shipments/{id}/tracking`
- **Pengecualian**: `GET /api/shipments/{id}/exceptions`, `POST /api/shipments/{id}/exceptions`, `PATCH /api/exceptions/{id}`
- **Master Data**: `GET|POST /api/carriers`, `GET|POST /api/locations`
- **Intelijen Global**: `GET /api/countries`, `GET /api/risk`, `GET /api/ports`, `GET /api/news`, `GET /api/currency`

---

## ⚖️ Lisensi & Hak Cipta
Dikembangkan untuk penilaian Ujian Akhir Semester (UAS) mata kuliah Pemrograman Web 2. Seluruh API publik (Open-Meteo, ExchangeRate-API, World Bank, REST Countries) digunakan sesuai dengan lisensi *open data* masing-masing.
