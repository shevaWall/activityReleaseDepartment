<?php

use App\Http\Controllers\CompositController;
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

/**
 * роуты для работы с объектами
 */
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

    Route::get('delete/{id}', [PrintableObjectController::class, "StatusDeletedObject"])
        ->name('deleteObject');

    Route::get("edit/{id}", [PrintableObjectController::class, "showObjectSettings"])
        ->name('showObjectSettings');

    Route::get("deleted", [PrintableObjectController::class, "deletedObjects"])
            ->name('deleted');

    Route::get("restore/{id}", [PrintableObjectController::class, "restoreObject"])
            ->name('restoreObject');
    Route::get("remove/{id}", [PrintableObjectController::class, "removeObject"])
            ->name('removeObject');


    Route::get("composit/{object_id}", [CompositController::class, "index"])
            ->name('composit');
});

Route::group([
//    'middleware'=>  '',
    'prefix'    =>  'composit',
    'as'        =>  'composit.',
], function(){
        Route::get("updateStatus/{id}", [CompositController::class, "updateStatus"])
                ->name('updateStatus');
});

/*Route::group([
//    'middleware'=>  '',
    'prefix'    =>  'settings',
    'as'        =>  'settings.',
], function(){
    Route::get("/", [SettingsController::class, "index"]);
});*/

