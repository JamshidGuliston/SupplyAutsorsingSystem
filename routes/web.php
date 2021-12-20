<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
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
use Dompdf\Dompdf;

Route::get('/', [TestController::class, 'index']);

Route::get('/controller', [TestController::class, 'start']);

Route::get('/newday', [TestController::class, 'menustart']);

Route::get('/showmenu/{kid}/{did}/{aid}', [TestController::class, 'showmenu']);

Route::get('/downloadPDF/{kid}/{did}/{aid}', [TestController::class, 'downloadPDF']);

Route::get('/pdf', function(){
    $dompdf = new Dompdf();
    $dompdf->loadHtml('hello world');

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream('demo.pdf', ['Attachment'=>0]);

});

Route::get('/gow', [TestController::class, 'addchilds']);

Route::get('/cron', [TestController::class, 'tomorrowdate']);

Route::group(['prefix' => 'admin'], function () {
    Voyager::routes();
});
