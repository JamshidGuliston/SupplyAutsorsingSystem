<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\Controller;

use App\Http\Controllers\StorageController;
use App\Http\Controllers\TechnologController;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/wel', function(){
    return view('welcome');
});

Route::get('/hello', [Controller::class, 'hello']);

Route::get('/', [TestController::class, 'index']);

Route::get('/controller', [TestController::class, 'start']);

Route::post('/newday', [TestController::class, 'menustart']);

Route::get('/showmenu/{kid}/{did}/{aid}', [TestController::class, 'showmenu']);

Route::get('/downloadPDF/{kid}/{did}/{aid}', [TestController::class, 'downloadPDF']);

Route::get('/gow', [TestController::class, 'addchilds']);

Route::get('/cron', [TestController::class, 'tomorrowdate']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['prefix'=>'storage', 'middleware'=>['isStorage','auth']], function(){
    Route::get('home', [StorageController::class, 'index'])->name('storage.home');
});

Route::group(['prefix'=>'technolog', 'middleware'=>['isTechnolog','auth']], function(){
    Route::get('home', [TechnologController::class, 'index'])->name('technolog.home');
});