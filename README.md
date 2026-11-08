# CargoTrace – Global Shipment Visibility & Tracking Platform

![CargoTrace](public/images/logo.png)

> **Enterprise Global Logistics Tracking & Supply Chain Intelligence System**  
> Developed for **Web Programming 2 (UAS)** using Laravel 13, MySQL 8, Leaflet.js, Chart.js, Tailwind CSS, and REST API.

---

## 🌟 Key Highlights

- **22 Complete Modules & Screens**: Full matching against the official UI/UX design specifications.
- **Global Shipment Visibility**: Real-world international routes, major seaport/airport coordinates, delay severity algorithms, and interactive Leaflet.js global maps.
- **Strict RBAC & Security**: Role-based access control protecting **Administrator**, **Logistics Operator**, and **Customer** roles via Laravel Middleware and Policies.
- **Internal REST API**: 16+ JSON endpoints secured by **Laravel Sanctum** with an exported **Postman Collection** (`CargoTrace_API_Collection.json`).
- **Global Intelligence Integrations**:
  - **Weather Monitoring** (Open-Meteo API)
  - **Currency & Exchange Rates** (ExchangeRate-API)
  - **Macroeconomic Indicators** (World Bank & UN Comtrade)
  - **Global Logistics News** with PHP Lexicon-based sentiment analysis
  - **Global Port Directory** (World Port Index reference)
  - **Country Comparison** engine
  - **Supply Chain Weighted Risk Intelligence** ($0.35 \times \text{Weather} + 0.25 \times \text{Currency} + 0.20 \times \text{Inflation} + 0.20 \times \text{News Sentiment}$)
- **Design System**: Deep Navy / Electric Blue enterprise dashboard with **one-click global Dark / Light mode toggle** and Indonesian language by default.

---

## 🚀 Quick Start Guide

### Prerequisites
- PHP 8.3+ or 8.5+
- Composer 2.8+
- MySQL 8.0+ (Laragon or standalone MySQL)
- Node.js & NPM

### Setup Instructions

1. **Clone repository & install dependencies**:
   ```bash
   composer install
   npm install
   npm run build
   ```

2. **Configure Environment (`.env`)**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Ensure MySQL database settings:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cargotrace
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. **Run Migrations & Seed Realistic International Data**:
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Serve Application**:
   ```bash
   php artisan serve
   ```
   Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## 👥 Demo Credentials

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@cargotrace.id` | `password` | Full monitoring, user management, carrier/location CRUD, audit logs, system settings |
| **Logistics Operator** | `operator@cargotrace.id` | `password` | Operational manifest updates, append-only tracking events, exception handling |
| **Customer** | `customer@globalmart.com` | `password` | Own shipments monitoring, real-time cargo ETA & tracking timeline |

*(Demo account autofill buttons are available directly on the login screen for instant presentation)*

---

## 🧪 Automated Testing Suite

All features and business rules are verified with PHPUnit / Pest:
```bash
php artisan test
```

### Verified Test Cases (26 Tests Passed):
- `AuthenticationTest`: Login, invalid credentials, session regeneration, and public registration role isolation.
- `RbacAuthorizationTest`: Admin routes protection, operator/customer 403 authorization guard.
- `ShipmentModelTest`: Database seeding verification, automatic delay calculation, chronological append-only events.
- `OperatorWorkflowTest`: Complete operator operational workflow, shipment creation with auto-first event, append-only checkpoints, exception lifecycle (report & resolve), and profile settings.
- `CargoTraceApiTest`: Sanctum bearer tokens, JSON schema compliance, append-only tracking events, exception resolution.
- `GlobalIntelligenceServiceTest`: Mocked weather responses, mathematical weighted risk formula validation.

---

## 📑 Postman Collection

Import `CargoTrace_API_Collection.json` into Postman to test all endpoints:
- `POST /api/login` & `POST /api/register`
- `GET /api/user`
- `GET /api/shipments`, `POST /api/shipments`, `GET /api/shipments/{id}`
- `GET /api/shipments/{id}/tracking`, `POST /api/shipments/{id}/tracking`
- `GET /api/shipments/{id}/exceptions`, `POST /api/shipments/{id}/exceptions`, `PATCH /api/exceptions/{id}`
- `GET /api/carriers`, `POST /api/carriers`
- `GET /api/locations`, `POST /api/locations`

---

## ⚖️ License & Attribution
Developed for academic assessment (UAS Web Programming 2). All external services (Open-Meteo, ExchangeRate-API, OpenStreetMap, World Bank) are used under their respective open data licenses with graceful offline fallback mechanisms.
