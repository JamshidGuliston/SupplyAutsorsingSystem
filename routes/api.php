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
