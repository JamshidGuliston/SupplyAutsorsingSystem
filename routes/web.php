<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TechnologController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\ApiControllers\TelegramController;
use App\Http\Controllers\HomeController;
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

Route::any('telegrambot', [TelegramController::class, 'telegrambot']);

Route::get('/', function () {
    return redirect()->route('technolog.home');
});

Route::get('/showmenu/{kid}/{did}/{aid}', [TestController::class, 'showmenu']);
// ommaga ochiq bot orqali taxminiy menyuni ko'rish
Route::get('/nextdaymenuPDF/{kid}/{aid}', [TestController::class, 'nextdaymenuPDF']);
Route::get('/nextdaysecondmenuPDF/{kid}', [TestController::class, 'nextdaysecondmenuPDF']);
Route::get('/nextnakladnoyPDF/{kid}', [TestController::class, 'nextnakladnoyPDF']);
Route::get('/gow', [TestController::class, 'addchilds']);

// ommaga ochiq bot orqali haqiqiy menyuni ko'rish
Route::get('/activmenuPDF/{day}/{kid}/{aid}', [TestController::class, 'activmenuPDF']);
Route::get('/activsecondmenuPDF/{day}/{kid}', [TestController::class, 'activsecondmenuPDF']);
Route::get('/activnakladPDF/{day}/{kid}', [TestController::class, 'activnakladPDF']);
// ommaga ochiq shoplar uchun
Route::get('nextdayshoppdf/{id}', [TestController::class, 'nextdayshoppdf'])->name('technolog.nextdayshoppdf');

Route::get('tempclear', [TestController::class, 'tempclear']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});

Auth::routes();

$globalroutes =  function () {
    Route::get('getbotusers', [TechnologController::class, 'getbotusers']);
};

Route::group(['prefix' => 'storage', 'middleware' => ['isStorage', 'auth']], function () {
    Route::get('home', [StorageController::class, 'index'])->name('storage.home');
    Route::get('addproductform', [StorageController::class, 'addproductform'])->name('storage.addproductform');
    Route::post('addproducts', [StorageController::class, 'addproducts'])->name('storage.addproducts');
    Route::get('addedproducts', [StorageController::class, 'addedproducts'])->name('storage.addedproducts');
    Route::get('orders', [StorageController::class, 'orders'])->name('storage.orders');
    Route::get('getdoc', [StorageController::class, 'getdoc'])->name('storage.getdoc');
    Route::get('controlpassword', [StorageController::class, 'controlpassword']);
    Route::get('document/{id}', [StorageController::class, 'document']);

    Route::get('addmultisklad', [StorageController::class, 'addmultisklad'])->name('storage.addmultisklad');
    Route::post('newordersklad', [StorageController::class, 'newordersklad'])->name('storage.newordersklad');
    Route::get('onedaymulti/{id}', [StorageController::class, 'onedaymulti'])->name('storage.onedaymulti');
    Route::get('orderskladpdf/{id}', [TechnologController::class, 'orderskladpdf'])->name('technolog.orderskladpdf');
    Route::get('orderitem/{id}', [StorageController::class, 'orderitem'])->name('storage.orderitem');
    Route::get('getproduct', [StorageController::class, 'getproduct']);
    Route::get('editproduct', [StorageController::class, 'editproduct']);
    Route::get('deleteid', [StorageController::class, 'deleteid']);

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
    Route::post('editage', [TechnologController::class, 'editage']);
    Route::post('activagecountedit', [TechnologController::class, 'activagecountedit']);
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
    Route::get('allproducts', [TechnologController::class, 'allproducts'])->name('technolog.allproducts');
    Route::get('settingsproduct/{id}', [TechnologController::class, 'settingsproduct'])->name('technolog.settingsproduct');
    Route::post('updateproduct', [TechnologController::class, 'updateproduct'])->name('updateproduct');
    Route::get('shops', [TechnologController::class, 'shops'])->name('technolog.shops');
    Route::get('shopsettings/{id}', [TechnologController::class, 'shopsettings'])->name('technolog.shopsettings');
    Route::post('updateshop', [TechnologController::class, 'updateshop'])->name('updateshop');
    Route::get('addshop', [TechnologController::class, 'addshop'])->name('addshop');
    Route::post('createshop', [TechnologController::class, 'createshop'])->name('createshop');
    
    Route::get('food', [TechnologController::class, 'food'])->name('food');
    Route::get('foodsettings/{id}', [TechnologController::class, 'foodsettings'])->name('foodsettings');
    Route::post('updatefood', [TechnologController::class, 'updatefood'])->name('updatefood');
    Route::get('fooditem/{id}', [TechnologController::class, 'fooditem'])->name('fooditem');
    Route::get('addfooditem', [TechnologController::class, 'addfooditem'])->name('addfooditem');
    Route::post('addproductfood', [TechnologController::class, 'addproductfood'])->name('technolog.addproductfood');
    Route::get('addfood', [TechnologController::class, 'addfood'])->name('technolog.addfood');
    Route::post('createfood', [TechnologController::class, 'createfood'])->name('createfood');
    Route::post('editproductfood', [TechnologController::class, 'editproductfood'])->name('technolog.editproductfood');
    Route::get('deleteproductfood', [TechnologController::class, 'deleteproductfood'])->name('technolog.deleteproductfood');
    Route::get('menus/{id}', [TechnologController::class, 'menus'])->name('technolog.menus');
    Route::get('seasons', [TechnologController::class, 'seasons'])->name('technolog.seasons');
    Route::get('addtitlemenu/{id}', [TechnologController::class, 'addtitlemenu'])->name('technolog.addtitlemenu');
    Route::post('createmenu', [TechnologController::class, 'createmenu'])->name('technolog.createmenu');
    Route::get('menuitem/{id}', [TechnologController::class, 'menuitem'])->name('technolog.menuitem');
    Route::get('getfood', [TechnologController::class, 'getfood'])->name('technolog.getfood');
    Route::get('getfoodcomposition', [TechnologController::class, 'getfoodcomposition'])->name('technolog.getfoodcomposition');
    Route::post('createmenucomposition', [TechnologController::class, 'createmenucomposition'])->name('technolog.createmenucomposition');
    Route::get('getmenuproduct', [TechnologController::class, 'getmenuproduct'])->name('technolog.getmenuproduct');
    Route::post('editemenuproduct', [TechnologController::class, 'editemenuproduct'])->name('technolog.editemenuproduct');
    Route::post('deletemenufood', [TechnologController::class, 'deletemenufood'])->name('technolog.deletemenufood');
    Route::get('getfoodnametoday', [TechnologController::class, 'getfoodnametoday'])->name('technolog.getfoodnametoday');
    Route::post('todaynextdaymenu', [TechnologController::class, 'todaynextdaymenu'])->name('technolog.todaynextdaymenu');
    Route::post('copymenuitem', [TechnologController::class, 'copymenuitem'])->name('technolog.copymenuitem');
    Route::post('deletetitlemenuid', [TechnologController::class, 'deletetitlemenuid'])->name('technolog.deletetitlemenuid');
    // telegram
    Route::get('sendtoallgarden', [TelegramController::class, 'sendtoallgarden'])->name('technolog.sendtoallgarden');
    Route::post('deletepeople', [TechnologController::class, 'deletepeople'])->name('technolog.deletepeople');
    Route::get('sendtoonegarden/{id}', [TelegramController::class, 'sendtoonegarden'])->name('technolog.sendtoonegarden');
    Route::get('nextsendmenutoonegarden/{id}', [TelegramController::class, 'nextsendmenutoonegarden'])->name('technolog.nextsendmenutoonegarden');
    Route::get('nextsendmenutoallgarden', [TelegramController::class, 'nextsendmenutoallgarden'])->name('technolog.nextsendmenutoallgarden');
    Route::get('activsendmenutoallgardens/{dayid}', [TelegramController::class, 'activsendmenutoallgardens'])->name('technolog.activsendmenutoallgardens');
    Route::get('activsendmenutoonegarden/{dayid}/{gid}', [TelegramController::class, 'activsendmenutoonegarden'])->name('technolog.activsendmenutoonegarden');
    Route::get('sendordertooneshop/{id}', [TelegramController::class, 'sendordertooneshop'])->name('technolog.sendordertooneshop');
    // mayda skladlar
    Route::get('minusmultistorage/{id}', [TechnologController::class, 'minusmultistorage'])->name('technolog.minusmultistorage');
    Route::get('plusmultistorage/{id}', [TechnologController::class, 'plusmultistorage'])->name('technolog.plusmultistorage');
    Route::get('getmodproduct/{id}', [TechnologController::class, 'getmodproduct'])->name('technolog.getmodproduct');
    // end telegram
    Route::post('editnextworkers', [TechnologController::class, 'editnextworkers'])->name('technolog.editnextworkers');
    Route::post('editnextcheldren', [TechnologController::class, 'editnextcheldren'])->name('technolog.editnextcheldren');
    Route::post('editnextmenu', [TechnologController::class, 'editnextmenu'])->name('technolog.editnextmenu');
    Route::get('fornextmenuselect', [TechnologController::class, 'fornextmenuselect'])->name('technolog.fornextmenuselect');
    Route::get('nextdelivershop/{id}', [TechnologController::class, 'nextdelivershop'])->name('technolog.nextdelivershop');
    Route::get('nextdayshoppdf/{id}', [TechnologController::class, 'nextdayshoppdf'])->name('technolog.nextdayshoppdf');
    // sklad
    Route::get('addshopproduct/{id}', [TechnologController::class, 'addshopproduct'])->name('technolog.addshopproduct');
    Route::post('productshoptogarden', [TechnologController::class, 'productshoptogarden'])->name('technolog.productshoptogarden');
    Route::get('orderskladpdf/{id}', [TechnologController::class, 'orderskladpdf'])->name('technolog.orderskladpdf');
    // chef
    Route::get('allchefs', [TechnologController::class, 'allchefs'])->name('technolog.allchefs');
    Route::get('addchef', [TechnologController::class, 'addchef'])->name('technolog.addchef');
    Route::post('createchef', [TechnologController::class, 'createchef'])->name('technolog.createchef');
    Route::get('chefsettings', [TechnologController::class, 'chefsettings'])->name('technolog.chefsettings');
    Route::get('chefgetproducts', [TechnologController::class, 'chefgetproducts'])->name('technolog.chefgetproducts');
    Route::post('chefeditproductw', [TechnologController::class, 'chefeditproductw'])->name('technolog.chefeditproductw');
    
    Route::get('createnextdaypdf', [TestController::class, 'createnextdaypdf'])->name('technolog.createnextdaypdf');
    Route::get('createnewdaypdf/{id}', [TestController::class, 'createnewdaypdf'])->name('technolog.createnewdaypdf');
    
    
});

Route::group(['prefix' => 'chef', 'middleware' => ['isChef', 'auth']], function () {
    Route::get('home', [ChefController::class, 'index'])->name('chef.home');
    Route::post('sendnumbers', [ChefController::class, 'sendnumbers'])->name('chef.sendnumbers');
    Route::post('minusproducts', [ChefController::class, 'minusproducts'])->name('chef.minusproducts');
});

Route::group(['prefix' => 'accountant', 'middleware' => ['isAccountant', 'auth']], function () {
    Route::get('home', [AccountantController::class, 'index'])->name('accountant.home');
    Route::get('costs', [AccountantController::class, 'costs'])->name('accountant.costs');
});

Route::get('/minusp', [TestController::class, 'minusproduct']);
Route::get('/modproducts', [TestController::class, 'modproducts']);
Route::get('/deletemod', [TestController::class, 'deletemod']);
