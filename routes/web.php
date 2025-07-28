<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\TechnologController;
use App\Http\Controllers\ChefController;
use App\Http\Controllers\AccountantController;
use App\Http\Controllers\ApiControllers\TelegramController;
use App\Http\Controllers\BossController;
use App\Http\Controllers\CasherController;
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

// Route::any('telegrambot', [TelegramController::class, 'telegrambot']);

Route::get('/', function () {
    return redirect()->route('technolog.home');
});

Route::get('/showmenu/{kid}/{did}/{aid}', [TestController::class, 'showmenu']);
// ommaga ochiq bot orqali taxminiy menyuni ko'rish
Route::get('/nextdaymenuPDF/{kid}/{aid}', [TestController::class, 'nextdaymenuPDF']);
Route::get('/nextdaysecondmenuPDF/{kid}', [TestController::class, 'nextdaysecondmenuPDF']);
Route::get('/nextdaysomenakladnoyPDF/{kid}', [TestController::class, 'nextdaysomenakladnoyPDF']);
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
    Route::get('home/{year}/{id}', [StorageController::class, 'index'])->name('storage.home');
    Route::get('addproductform', [StorageController::class, 'addproductform'])->name('storage.addproductform');
    Route::post('addproducts', [StorageController::class, 'addproducts'])->name('storage.addproducts');
    Route::post('addr_products', [StorageController::class, 'addr_products'])->name('storage.addr_products');
    Route::post('addproduct', [StorageController::class, 'addproduct'])->name('storage.addproduct');
    Route::get('addedproducts/{year}/{id}', [StorageController::class, 'addedproducts'])->name('storage.addedproducts');
    Route::get('orders', [StorageController::class, 'orders'])->name('storage.orders');
    Route::get('getdoc', [StorageController::class, 'getdoc'])->name('storage.getdoc');
    Route::get('dostcontrolpassword', [StorageController::class, 'dostcontrolpassword']);
    Route::get('document/{id}', [StorageController::class, 'document']);
    Route::get('intakingsmallbasepdf/{day}/{kid}', [StorageController::class, 'intakingsmallbasepdf']);
    Route::get('report', [StorageController::class, 'report'])->name('storage.report');
    Route::get('backcontrolpassword', [StorageController::class, 'backcontrolpassword']);
    Route::get('allreport', [StorageController::class, 'allreport'])->name('storage.allreport');
    Route::get('increasedreport', [StorageController::class, 'increasedreport'])->name('storage.increasedreport');

    Route::get('addmultisklad', [StorageController::class, 'addmultisklad'])->name('storage.addmultisklad');
    Route::post('newordersklad', [StorageController::class, 'newordersklad'])->name('storage.newordersklad');
    Route::get('onedaymulti/{id}', [StorageController::class, 'onedaymulti'])->name('storage.onedaymulti');
    Route::get('orderskladpdf/{id}', [TechnologController::class, 'orderskladpdf'])->name('technolog.orderskladpdf');
    Route::get('orderitem/{id}', [StorageController::class, 'orderitem'])->name('storage.orderitem');
    Route::get('getproduct', [StorageController::class, 'getproduct']);
    Route::get('editproduct', [StorageController::class, 'editproduct']);
    Route::get('deleteid', [StorageController::class, 'deleteid']);
    Route::get('getworkerfoods', [StorageController::class, 'getworkerfoods'])->name('storage.getworkerfoods');
    Route::get('onedaysvod/{id}', [StorageController::class, 'ordersvodpdf'])->name('storage.onedaysvod');
    Route::get('ingroup/{id}', [StorageController::class, 'ingroup'])->name('storage.ingroup');
    Route::get('deleteproduct', [StorageController::class, 'deleteproduct'])->name('storage.deleteproduct');
    Route::get('takecategories', [StorageController::class, 'takecategories'])->name('storage.takecategories');
    Route::post('add_takecategory', [StorageController::class, 'add_takecategory'])->name('storage.add_takecategory');
    Route::post('update_takecategory', [StorageController::class, 'update_takecategory'])->name('storage.update_takecategory');
    Route::post('delete_takecategory', [StorageController::class, 'delete_takecategory'])->name('storage.delete_takecategory');
    Route::post('deleteorder', [StorageController::class, 'deleteorder'])->name('storage.deleteorder');
    Route::get('debts', [StorageController::class, 'debts'])->name('storage.debts');
    Route::post('editedebts', [StorageController::class, 'editedebts'])->name('storage.editedebts');
    Route::post('deletedebt', [StorageController::class, 'deletedebt'])->name('storage.deletedebt');
    Route::get('shopdebts', [StorageController::class, 'shopdebts'])->name('storage.shopdebts');
    Route::get('payreport', [StorageController::class, 'payreport'])->name('storage.payreport');
    Route::post('createpay', [StorageController::class, 'createpay'])->name('storage.createpay');
    Route::get('selectreport/{id}/{b}/{e}', [StorageController::class, 'selectreport'])->name('storage.selectreport');
    Route::get('takinglargebase', [StorageController::class, 'takinglargebase'])->name('storage.takinglargebase');
    Route::post('addtakinglargebase', [StorageController::class, 'addtakinglargebase'])->name('storage.addtakinglargebase');
    Route::get('editetakinglargebase', [StorageController::class, 'editetakinglargebase'])->name('storage.editetakinglargebase');
    Route::get('deletetakinglargebase', [StorageController::class, 'deletetakinglargebase'])->name('storage.deletetakinglargebase');
    Route::get('intakinglargebase/{id}', [StorageController::class, 'intakinglargebase'])->name('storage.intakinglargebase');
    Route::post('addintakinglargebase', [StorageController::class, 'addintakinglargebase'])->name('storage.addintakinglargebase');
    Route::post('deleteintakinglargebase', [StorageController::class, 'deleteintakinglargebase'])->name('storage.deleteintakinglargebase');
    Route::get('takinglargebase', [StorageController::class, 'takinglargebase'])->name('storage.takinglargebase');
    Route::get('takingsmallbase', [StorageController::class, 'takingsmallbase'])->name('storage.takingsmallbase');
    Route::post('addtakingsmallbase', [StorageController::class, 'addtakingsmallbase'])->name('storage.addtakingsmallbase');
    Route::post('deletetakingsmallbase', [StorageController::class, 'deletetakingsmallbase'])->name('storage.deletetakingsmallbase');
    Route::get('intakingsmallbase/{id}/{kid}/{day}', [StorageController::class, 'intakingsmallbase'])->name('storage.intakingsmallbase');
    Route::post('addintakingsmallbase', [StorageController::class, 'addintakingsmallbase'])->name('storage.addintakingsmallbase');
    Route::post('editegroup', [StorageController::class, 'editegroup'])->name('storage.editegroup');
    
    Route::get('changesome', [StorageController::class, 'changesome']);

    Route::post('confirmorder', [ChefController::class, 'right'])->name('storage.confirmorder');
});

Route::group(['prefix' => 'technolog', 'middleware' => ['isTechnolog', 'auth']], function () {
    Route::get('tabassum/{start}/{end}', [TechnologController::class, 'tabassum'])->name('tabassum');
    Route::get('funtest', [TechnologController::class, 'funtest']);
    Route::get('asdf', [TechnologController::class, 'asdf'] );
    Route::get('home', [TechnologController::class, 'index'])->name('technolog.home');
    Route::post('newday', [TechnologController::class, 'newday'])->name('technolog.newday');
    Route::get('sendmenu/{day}', [TechnologController::class, 'sendmenu'])->name('technolog.sendmenu');
    Route::get('showdate/{year}/{month}/{day}', [TechnologController::class, 'showdate'])->name('technolog.showdate');
    Route::get('settings/{id}', [TechnologController::class, 'settings'])->name('technolog.settings');
    Route::post('updategarden', [TechnologController::class, 'updategarden'])->name('updategarden');
    Route::get('ageranges/{id}', [TechnologController::class, 'ageranges']);
    Route::get('gageranges/{id}', [TechnologController::class, 'gageranges']);
    Route::get('addage/{bogid}/{ageid}/{qiymati}', [TechnologController::class, 'addage']);
    Route::post('nextdayaddgarden', [TechnologController::class, 'nextdayaddgarden']);
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
    Route::get('reportinout', [TechnologController::class, 'reportinout'])->name('technolog.reportinout');
    Route::get('reportinoutpdf', [TechnologController::class, 'reportinoutpdf'])->name('technolog.reportinoutpdf');
    
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
    Route::get('minusmultistorage/{id}/{monthid}', [TechnologController::class, 'minusmultistorage'])->name('technolog.minusmultistorage');
    Route::post('editminusproduct', [TechnologController::class, 'editminusproduct'])->name('technolog.editminusproduct');
    Route::post('plusmultimodadd', [TechnologController::class, 'plusmultimodadd'])->name('technolog.plusmultimodadd');
    Route::get('plusmultistorage/{id}/{monthid}', [TechnologController::class, 'plusmultistorage'])->name('technolog.plusmultistorage');
    Route::post('deleteweights', [TechnologController::class, 'deleteweights'])->name('technolog.deleteweights');
    Route::get('weightcurrent/{kind}/{yearid}/{monthid}', [TechnologController::class, 'weightcurrent'])->name('technolog.weightcurrent');
    Route::get('weightsdocument/{group_id}', [TechnologController::class, 'weightsdocument'])->name('technolog.weightsdocument');
    Route::get('monthlyweights/{kindid}/{monthid}', [TechnologController::class, 'monthlyweights'])->name('technolog.monthlyweights');
    Route::get('getmodproduct/{id}', [TechnologController::class, 'getmodproduct'])->name('technolog.getmodproduct');
    Route::get('getweightproducts', [TechnologController::class, 'getweightproducts'])->name('technolog.getweightproducts');
    Route::post('addingweights', [TechnologController::class, 'addingweights'])->name('technolog.addingweights');
    Route::post('editegroup', [TechnologController::class, 'editegroup'])->name('technolog.editegroup');
    // end telegram
    Route::post('editnextworkers', [TechnologController::class, 'editnextworkers'])->name('technolog.editnextworkers');
    Route::post('editnextcheldren', [TechnologController::class, 'editnextcheldren'])->name('technolog.editnextcheldren');
    Route::post('editnextmenu', [TechnologController::class, 'editnextmenu'])->name('technolog.editnextmenu');
    Route::get('fornextmenuselect', [TechnologController::class, 'fornextmenuselect'])->name('technolog.fornextmenuselect');
    Route::get('nextdelivershop/{id}', [TechnologController::class, 'nextdelivershop'])->name('technolog.nextdelivershop');
    Route::get('nextdayshoppdf/{id}', [TechnologController::class, 'nextdayshoppdf'])->name('technolog.nextdayshoppdf');
    // sklad
    Route::get('orderskladpdf/{id}', [TechnologController::class, 'orderskladpdf'])->name('technolog.orderskladpdf');
    // chef
    Route::get('allchefs', [TechnologController::class, 'allchefs'])->name('technolog.allchefs');
    Route::get('addchef', [TechnologController::class, 'addchef'])->name('technolog.addchef');
    Route::post('createchef', [TechnologController::class, 'createchef'])->name('technolog.createchef');
    Route::get('chefsettings', [TechnologController::class, 'chefsettings'])->name('technolog.chefsettings');
    Route::post('updatechef', [TechnologController::class, 'updatechef'])->name('updatechef');
    Route::get('chefgetproducts', [TechnologController::class, 'chefgetproducts'])->name('technolog.chefgetproducts');
    Route::post('chefeditproductw', [TechnologController::class, 'chefeditproductw'])->name('technolog.chefeditproductw');
    
    Route::get('createnextdaypdf', [TestController::class, 'createnextdaypdf'])->name('technolog.createnextdaypdf');
    Route::get('createnewdaypdf/{id}', [TestController::class, 'createnewdaypdf'])->name('technolog.createnewdaypdf');
    Route::delete('deletegarden', [TechnologController::class, 'deleteGarden'])->name('deletegarden');
    
    Route::get('finding/{id}', [TechnologController::class, 'finding']);
    
    Route::get('updatemanu', [TechnologController::class, 'updatemanu']);
    Route::post('editactivemanu', [TechnologController::class, 'editactivemanu'])->name('technolog.editactivemanu');
    Route::get('getactivemenuproducts', [TechnologController::class, 'getactivemenuproducts']);
    
    Route::get('pagecreateproduct', [TechnologController::class, 'pageCreateProduct']);
    Route::post('createproduct', [TechnologController::class, 'createproduct'])->name('createproduct');
    
    // Muassasalar (Bog'chalar) boshqaruvi
    Route::get('muassasalar', [TechnologController::class, 'muassasalar'])->name('technolog.muassasalar');
    Route::get('addmuassasa', [TechnologController::class, 'addmuassasa'])->name('technolog.addmuassasa');
    Route::post('createmuassasa', [TechnologController::class, 'createmuassasa'])->name('technolog.createmuassasa');
    Route::get('editmuassasa/{id}', [TechnologController::class, 'editmuassasa'])->name('technolog.editmuassasa');
    Route::post('updatemuassasa', [TechnologController::class, 'updatemuassasa'])->name('technolog.updatemuassasa');
    Route::delete('deletemuassasa', [TechnologController::class, 'deletemuassasa'])->name('technolog.deletemuassasa');
});

Route::group(['prefix' => 'chef', 'middleware' => ['isChef', 'auth']], function () {
    Route::get('home', [ChefController::class, 'index'])->name('chef.home');
    Route::post('sendnumbers', [ChefController::class, 'sendnumbers'])->name('chef.sendnumbers');
    Route::post('minusproducts', [ChefController::class, 'minusproducts'])->name('chef.minusproducts');
    Route::post('right', [ChefController::class, 'right'])->name('chef.right');
});

Route::group(['prefix' => 'accountant', 'middleware' => ['isAccountant', 'auth']], function () {
    Route::get('home', [AccountantController::class, 'index'])->name('accountant.home');
    Route::get('costs', [AccountantController::class, 'costs'])->name('accountant.costs');
    Route::get('reports', [AccountantController::class, 'reports'])->name('accountant.reports');
    Route::get('kindreport/{id}', [AccountantController::class, 'kindreport'])->name('accountant.kindreport');
    Route::get('bycosts/{id}', [AccountantController::class, 'bycosts'])->name('accountant.bycosts');
    Route::post('pluscosts', [AccountantController::class, 'pluscosts'])->name('accountant.pluscosts');
    Route::post('editcost', [AccountantController::class, 'editcost'])->name('accountant.editcost');
    Route::post('editallcosts', [AccountantController::class, 'editallcosts'])->name('accountant.editallcosts');
    // hisobot
    Route::get('narxselect/{region_id}', [AccountantController::class, 'narxselect'])->name('accountant.narxselect');
    Route::get('nakapit/{id}/{ageid}/{start}/{end}/{costid}/{nds}/{ust}', [AccountantController::class, 'nakapit'])->name('accountant.nakapit');
    Route::get('nakapitexcel/{id}/{ageid}/{start}/{end}/{costid}/{nds}/{ust}', [AccountantController::class, 'nakapitexcel'])->name('accountant.nakapitexcel');
    Route::get('schotfaktur/{id}/{ageid}/{start}/{end}/{costid}/{nds}/{ust}', [AccountantController::class, 'schotfaktur'])->name('accountant.schotfaktur');
    Route::get('allschotfaktur/{id}/{start}/{end}/{costid}/{nds}/{ust}', [AccountantController::class, 'allschotfaktur'])->name('accountant.allschotfaktur');
    Route::get('schotfakturexcel/{id}/{ageid}/{start}/{end}/{costid}/{nds}/{ust}', [AccountantController::class, 'schotfakturexcel'])->name('accountant.schotfakturexcel');
    Route::get('norm/{id}/{ageid}/{start}/{end}/{costid}', [AccountantController::class, 'norm'])->name('accountant.norm');
    Route::get('normexcel/{id}/{ageid}/{start}/{end}/{costid}', [AccountantController::class, 'normexcel'])->name('accountant.normexcel');
    Route::get('svod', [AccountantController::class, 'svod'])->name('accountant.svod');
    // hisobot ishchi xodimlar 
    Route::get('reportsworker', [AccountantController::class, 'reportsworker'])->name('accountant.reportsworker');
    Route::get('kindreportworker/{id}', [AccountantController::class, 'kindreportworker'])->name('accountant.kindreportworker');
    Route::get('nakapitworker/{id}/{ageid}/{start}/{end}/{costid}', [AccountantController::class, 'nakapitworker'])->name('accountant.nakapitworker');
    Route::get('schotfakturworker/{id}/{ageid}/{start}/{end}/{costid}', [AccountantController::class, 'schotfakturworker'])->name('accountant.schotfakturworker');

    Route::get('svodworkers', [AccountantController::class, 'svodworkers'])->name('accountant.svodworkers');
    // Daromad
    Route::get('income/{id}', [AccountantController::class, 'income'])->name('accountant.income');
    Route::get('bigbase', [AccountantController::class, 'bigbase']);
    Route::get('multibase', [AccountantController::class, 'multibase']);
    Route::get('getmodproduct/{id}', [AccountantController::class, 'getmodproduct']);
    // mods of products
    Route::get('modsofproducts', [AccountantController::class, 'modsofproducts']);
    Route::get('getingcosts', [AccountantController::class, 'getingcosts']);
    Route::get('getreportlargebase', [AccountantController::class, 'getreportlargebase']);

});

Route::group(['prefix' => 'casher', 'middleware' => ['isChasher', 'auth']], function () {
    Route::get('home', [CasherController::class, 'index'])->name('casher.home');
    Route::get('cashes', [CasherController::class, 'costs'])->name('casher.costs');
    Route::get('costs', [CasherController::class, 'costs'])->name('casher.costs');
    Route::get('allcosts', [CasherController::class, 'allcosts'])->name('casher.allcosts');
    Route::get('report', [CasherController::class, 'report'])->name('casher.report');
    Route::post('createcost', [CasherController::class, 'createcost'])->name('casher.createcost');
    Route::post('deletecost', [CasherController::class, 'deletecost'])->name('casher.deletecost');
    Route::post('editecost', [CasherController::class, 'editecost'])->name('casher.editecost');
    Route::post('allcreatecost', [CasherController::class, 'allcreatecost'])->name('casher.allcreatecost');
    Route::post('alldeletecost', [CasherController::class, 'alldeletecost'])->name('casher.alldeletecost');
    Route::post('alleditecost', [CasherController::class, 'alleditecost'])->name('casher.alleditecost');
    Route::post('createcash', [CasherController::class, 'createcash'])->name('casher.createcash');
    Route::post('deletecash', [CasherController::class, 'deletecash'])->name('casher.deletecash');
    Route::get('selectallcost/{id}', [CasherController::class, 'selectallcost'])->name('casher.selectallcost');
    Route::get('selectreport/{type}/{id}/{b}/{e}', [CasherController::class, 'selectreport'])->name('casher.selectallcost');
    
});

Route::group(['prefix' => 'boss', 'middleware' => ['isBoss', 'auth']], function () {
    Route::get('home', [BossController::class, 'index'])->name('boss.home', ['yearid'=>0, 'monthid'=>0]);
    Route::get('cashe', [BossController::class, 'cashe'])->name('boss.cashe');
    Route::post('accepted', [BossController::class, 'accepted'])->name('boss.accepted');
    Route::get('report', [BossController::class, 'report'])->name('boss.report');
    Route::get('incomereport', [BossController::class, 'incomereport'])->name('boss.incomereport');
    Route::get('showincome', [BossController::class, 'showincome'])->name('boss.showincome');
    Route::get('selectallcost/{id}', [CasherController::class, 'selectallcost'])->name('casher.selectallcost');
    Route::get('selectreport/{type}/{id}/{b}/{e}', [CasherController::class, 'selectreport'])->name('casher.selectallcost');
    
});


Route::get('/minusp', [TestController::class, 'minusproduct']);
Route::get('/modproducts', [TestController::class, 'modproducts']);
Route::get('/deletemod', [TestController::class, 'deletemod']);
