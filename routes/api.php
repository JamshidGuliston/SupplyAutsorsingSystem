<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiControllers\OrderProductController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

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
