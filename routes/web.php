<?php

use App\Http\Controllers\PrintableObjectController;
use Illuminate\Support\Facades\Route;

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


Route::get('/', function () {
    return view('index');
})->name('index');

Route::group([
//    'middleware'=>  '',
    'prefix'    =>  'objects',
    'as'        =>  'objects.',
], function(){
    Route::get("/", [PrintableObjectController::class, "index"])
        ->name('index');

    Route::get("add", [PrintableObjectController::class, "addNewObjectForm"])
        ->name('show_form');
    Route::post("add", [PrintableObjectController::class, "addNewObjectSubmit"])
        ->name('submit_form');
});

Route::group([
//    'middleware'=>  '',
    'prefix'    =>  'settings',
    'as'        =>  'settings.',
], function(){
    Route::get("/", [SettingsController::class, "index"]);
});

