<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiControllers\OrderProductController;

// Route::middleware("auth:sanctum")->get("/user", function (Request $request) {
//     return $request->user();
// });

// Order Product API Routes
Route::prefix("order-products")->group(function () {
    // Barcha order_productlarni olish (delete bo''lganlarni ham)
    // Route::get("/all", [OrderProductController::class, "getOrderProductsWithStructures"]);

    // Faqat faol order_productlarni olish
    Route::get("/active", [OrderProductController::class, "getActiveOrderProductsWithStructures"]);
});

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\DeviceController;
use App\Http\Controllers\Api\V1\Chef\AttendanceController;
use App\Http\Controllers\Api\V1\Chef\LocationEventController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('login', [LoginController::class, 'login'])->middleware('throttle:5,1');
        Route::post('logout', [LogoutController::class, 'logout'])->middleware('auth:sanctum');
        Route::post('device', [DeviceController::class, 'register'])->middleware('auth:sanctum');
    });

    Route::prefix('chef')->middleware(['auth:sanctum'])->group(function () {
        Route::prefix('attendance')->middleware('throttle:30,1')->group(function () {
            Route::post('check-in', [AttendanceController::class, 'checkIn']);
            Route::post('check-out', [AttendanceController::class, 'checkOut']);
            Route::post('replace', [AttendanceController::class, 'replace']);
            Route::get('today', [AttendanceController::class, 'today']);
        });
        Route::post('location-events', [LocationEventController::class, 'store'])
            ->middleware('throttle:60,1');
    });
});
