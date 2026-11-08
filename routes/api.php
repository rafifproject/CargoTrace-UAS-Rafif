<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CarrierApiController;
use App\Http\Controllers\Api\ExceptionApiController;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Controllers\Api\ShipmentApiController;
use Illuminate\Support\Facades\Route;

// Public Auth Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected API Endpoints (Sanctum Authenticated)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Shipments
    Route::get('/shipments', [ShipmentApiController::class, 'index']);
    Route::post('/shipments', [ShipmentApiController::class, 'store']);
    Route::get('/shipments/{shipment}', [ShipmentApiController::class, 'show']);
    Route::match(['put', 'patch'], '/shipments/{shipment}', [ShipmentApiController::class, 'update']);
    Route::delete('/shipments/{shipment}', [ShipmentApiController::class, 'destroy']);

    // Tracking
    Route::get('/shipments/{shipment}/tracking', [ShipmentApiController::class, 'tracking']);
    Route::post('/shipments/{shipment}/tracking', [ShipmentApiController::class, 'addTracking']);

    // Exceptions
    Route::get('/shipments/{shipment}/exceptions', [ShipmentApiController::class, 'exceptions']);
    Route::post('/shipments/{shipment}/exceptions', [ShipmentApiController::class, 'addException']);
    Route::patch('/exceptions/{exception}', [ExceptionApiController::class, 'update']);

    // Carriers & Locations
    Route::get('/carriers', [CarrierApiController::class, 'index']);
    Route::post('/carriers', [CarrierApiController::class, 'store']);
    Route::get('/locations', [LocationApiController::class, 'index']);
    Route::post('/locations', [LocationApiController::class, 'store']);
});
