<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TechnologController;
use App\Http\Controllers\ApiControllers\TelegramController;
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

Route::get('/wel', function () {
    return view('welcome');
});

Route::get('/hello', [TestController::class, 'tomany']);
Route::get('/webhook', [TelegramController::class, 'webhook']);

// dashboart test
Route::get('/dash', [TestController::class, 'dash']);

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

$globalroutes =  function () {
    Route::get('getbotusers', [TechnologController::class, 'getbotusers']);
};

Route::group(['prefix' => 'storage', 'middleware' => ['isStorage', 'auth']], function () {
    Route::get('home', [StorageController::class, 'index'])->name('storage.home');
    Route::get('newday', [StorageController::class, 'index'])->name('storage.newday');
    Route::get('orders', [StorageController::class, 'orders'])->name('storage.orders');
    Route::get('getdoc', [StorageController::class, 'getdoc'])->name('storage.getdoc');
    Route::get('controlpassword', [StorageController::class, 'controlpassword']);
    Route::get('getbotusers', [TechnologController::class, 'getbotusers']);
});

Route::group(['prefix' => 'technolog', 'middleware' => ['isTechnolog', 'auth']], function () {
    Route::get('home', [TechnologController::class, 'index'])->name('technolog.home');
    Route::post('newday', [TechnologController::class, 'newday'])->name('technolog.newday');
    Route::get('sendmenu/{day}', [TechnologController::class, 'sendmenu'])->name('technolog.sendmenu');
    Route::get('settings/{id}', [TechnologController::class, 'settings'])->name('technolog.settings');
    Route::post('updategarden', [TechnologController::class, 'updategarden'])->name('updategarden');
    Route::get('ageranges/{id}', [TechnologController::class, 'ageranges']);
    Route::get('addage/{bogid}/{ageid}/{qiymati}', [TechnologController::class, 'addage']);
    Route::get('getage/{bogid}', [TechnologController::class, 'getage']);
    Route::get('editage/{bogid}/{ageid}/{qiymati}', [TechnologController::class, 'editage']);
    Route::get('addproduct', [TechnologController::class, 'addproduct'])->name('technolog.addproduct');
    Route::post('ordername', [TechnologController::class, 'ordername'])->name('technolog.ordername');
    Route::get('orderitem/{id}', [TechnologController::class, 'orderitem'])->name('technolog.orderitem');
    Route::post('plusproduct', [TechnologController::class, 'plusproduct'])->name('technolog.plusproduct');
    Route::get('controlpassword', [TechnologController::class, 'controlpassword']);
    Route::get('getproduct', [TechnologController::class, 'getproduct']);
    Route::get('editproduct', [TechnologController::class, 'editproduct']);
    Route::get('deleteid', [TechnologController::class, 'deleteid']);
    Route::get('getbotusers', [TechnologController::class, 'getbotusers'])->name('technolog.getbotusers');
    Route::post('bindgarden', [TechnologController::class, 'bindgarden'])->name('technolog.bindgarden');
    Route::post('bindshop', [TechnologController::class, 'bindshop'])->name('technolog.bindshop');
});
