<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExceptionController;
use App\Http\Controllers\GlobalIntelligenceController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Operator\OperatorDashboardController;
use App\Http\Controllers\Operator\OperatorExceptionController;
use App\Http\Controllers\Operator\OperatorIntelligenceController;
use App\Http\Controllers\Operator\OperatorNotificationController;
use App\Http\Controllers\Operator\OperatorProfileController;
use App\Http\Controllers\Operator\OperatorShipmentController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }
    return auth()->user()->isOperator()
        ? redirect()->route('operator.dashboard')
        : redirect()->route('admin.dashboard');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Administrator Routes
Route::middleware(['auth', 'role:ADMINISTRATOR'])->prefix('admin')->name('admin.')->group(function () {
    // 01. Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 02 - 05. Shipments, Tracking, Map
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/map', [ShipmentController::class, 'map'])->name('shipments.map');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/tracking', [ShipmentController::class, 'tracking'])->name('tracking.index');

    // 06. Exceptions
    Route::get('/exceptions', [ExceptionController::class, 'index'])->name('exceptions.index');
    Route::patch('/exceptions/{exception}/resolve', [ExceptionController::class, 'resolve'])->name('exceptions.resolve');

    // 07. Carrier Management
    Route::resource('carriers', CarrierController::class)->except(['create', 'show', 'edit']);

    // 08. Location Management & Geocoding
    Route::get('/locations/geocode', [LocationController::class, 'geocode'])->name('locations.geocode');
    Route::resource('locations', LocationController::class)->except(['create', 'show', 'edit']);

    // 09. User & Role Management
    Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

    // 10. Operational Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // 11. System Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    // Administrasi: Audit Logs & Notifications
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Global Intelligence Modul A - G
    Route::prefix('intelligence')->name('intelligence.')->group(function () {
        Route::get('/weather', [GlobalIntelligenceController::class, 'weather'])->name('weather');
        Route::get('/currency', [GlobalIntelligenceController::class, 'currency'])->name('currency');
        Route::get('/economy', [GlobalIntelligenceController::class, 'economy'])->name('economy');
        Route::get('/news', [GlobalIntelligenceController::class, 'news'])->name('news');
        Route::get('/ports', [GlobalIntelligenceController::class, 'ports'])->name('ports');
        Route::get('/comparison', [GlobalIntelligenceController::class, 'comparison'])->name('comparison');
        Route::get('/risk', [GlobalIntelligenceController::class, 'risk'])->name('risk');
    });
});

// =====================================================================
// LOGISTICS OPERATOR ROUTES
// =====================================================================
Route::middleware(['auth', 'role:LOGISTICS_OPERATOR'])->prefix('operator')->name('operator.')->group(function () {

    // 02. Dasbor Operasional
    Route::get('/dashboard', [OperatorDashboardController::class, 'index'])->name('dashboard');

    // 03-07. Pengelolaan Pengiriman
    Route::get('/shipments', [OperatorShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/create', [OperatorShipmentController::class, 'create'])->name('shipments.create');
    Route::post('/shipments', [OperatorShipmentController::class, 'store'])->name('shipments.store');
    Route::get('/shipments/map', [OperatorShipmentController::class, 'map'])->name('shipments.map');
    Route::get('/shipments/{shipment}', [OperatorShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{shipment}/edit', [OperatorShipmentController::class, 'edit'])->name('shipments.edit');
    Route::put('/shipments/{shipment}', [OperatorShipmentController::class, 'update'])->name('shipments.update');
    Route::post('/shipments/{shipment}/tracking', [OperatorShipmentController::class, 'addTrackingEvent'])->name('shipments.tracking.store');

    // 06. Riwayat Pelacakan
    Route::get('/tracking', [OperatorShipmentController::class, 'tracking'])->name('tracking.index');

    // 08. Manajemen Pengecualian
    Route::get('/exceptions', [OperatorExceptionController::class, 'index'])->name('exceptions.index');
    Route::post('/exceptions', [OperatorExceptionController::class, 'store'])->name('exceptions.store');
    Route::patch('/exceptions/{exception}/resolve', [OperatorExceptionController::class, 'resolve'])->name('exceptions.resolve');

    // 09-14. Intelijen Global
    Route::prefix('intelligence')->name('intelligence.')->group(function () {
        Route::get('/weather', [OperatorIntelligenceController::class, 'weather'])->name('weather');
        Route::get('/currency', [OperatorIntelligenceController::class, 'currency'])->name('currency');
        Route::get('/economy', [OperatorIntelligenceController::class, 'economy'])->name('economy');
        Route::get('/news', [OperatorIntelligenceController::class, 'news'])->name('news');
        Route::get('/ports', [OperatorIntelligenceController::class, 'ports'])->name('ports');
        Route::get('/comparison', [OperatorIntelligenceController::class, 'comparison'])->name('comparison');
        Route::get('/risk', [OperatorIntelligenceController::class, 'risk'])->name('risk');
    });

    // 15. Pusat Notifikasi
    Route::get('/notifications', [OperatorNotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [OperatorNotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [OperatorNotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // 16. Profil Akun
    Route::get('/profile', [OperatorProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [OperatorProfileController::class, 'update'])->name('profile.update');
});
