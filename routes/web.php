<?php

use App\Http\Controllers\CompositController;
use App\Http\Controllers\CountPdfController;
use App\Http\Controllers\PrintableObjectController;
use App\Http\Controllers\SearchController;
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

    // выводит объекты по статусам (в работе, на паузе, сданы, удалены, все)
    Route::get("withStatus/{status_id}", [PrintableObjectController::class, "withStatus"])
            ->name('withStatus');

    // форма добавление и обработчик для нового объекта
    Route::get("add", [PrintableObjectController::class, "addNewObjectForm"])
        ->name('show_form');
    Route::post("add", [PrintableObjectController::class, "addNewObjectSubmit"])
        ->name('submit_form');

    // аякс измененение статуса объекта на "удалён"
    Route::get('delete/{object_id}', [PrintableObjectController::class, "ajaxChangeObjectStatus"])
        ->name('deleteObject');

    // отображение настроек объекта
    Route::get("edit/{id}", [PrintableObjectController::class, "showObjectSettings"])
        ->name('showObjectSettings');

    // навсегда удаляет объект из БД
    Route::get("remove/{id}", [PrintableObjectController::class, "removeObject"])
            ->name('removeObject');

    // аякс изменение статуса объетк
    Route::get("changeObjectStatus/{object_id}/{status_id}", [PrintableObjectController::class, "ajaxChangeObjectStatus"])
            ->name('ajaxChangeObjectStatus');

    // отображение разделов (состава) объекта
    Route::get("composit/{object_id}", [CompositController::class, "index"])
            ->name('composit');

    // отображение сводной таблицы подсчитанных страниц
    Route::get("{object_id}/showPaperConsumption", [PrintableObjectController::class, "showPaperConsumption"])
            ->name('paperConsumption');

});

Route::group([
//    'middleware'=>  '',
    'prefix'    =>  'search',
    'as'        =>  'search.',
], function(){
    Route::get("/", [SearchController::class, "index"])
        ->name('index');
});

/**
 * роуты для работы с составами объектов
 */
Route::group([
//    'middleware'=>  '',
    'prefix'    =>  'composit',
    'as'        =>  'composit.',
], function(){
        // аякс добавление раздела (состава)
        Route::post("ajaxAddComposit", [CompositController::class, "ajaxAddComposit"])
                ->name('ajaxAddComposit');

        // аякс удаление раздела (состава)
        Route::get("ajaxDeleteComposit/{composit_id}", [CompositController::class, "ajaxDeleteComposit"])
                ->name('ajaxDeleteComposit');

        // аякс изменение статуса (готов/не готов) раздела (соства)
        Route::get("ajaxChangeCompositStatus/{composit_id}", [CompositController::class, "ajaxChangeCompositStatus"])
                ->name('ajaxChangeCompositStatus');

        Route::post('ajaxRenameComposit/{composit_id}', [CompositController::class, "ajaxRenameComposit"]);
});

/**
 * роуты для работы с подсчетом страниц по форматам в ПДФ
 */
Route::group([
//    'middleware'=>  '',
    'prefix'    =>  'countPdf',
    'as'        =>  'countPdf.',
], function(){
    // аякс загрузка файла и инициализация рассчетов форматов
    Route::post('ajaxCountPdf/{composit_id}', [CountPdfController::class, "ajaxLoadFile"])
            ->name('/ajaxCountPdf');

    // аякс получить подсчитанные форматы
    Route::get("/ajaxGetCountedPdf/{composit_id}", [CountPdfController::class, "ajaxGetCountedPdf"])
            ->name('ajaxGetCountedPdf');

    // очищает все подсчитанные форматы в объекте
    Route::get("clearAll/{object_id}", [CountPdfController::class, "clearAll"])
            ->name('clearAll');

    // очищает список подсчитанных форматов у определенного раздела (состава)
    Route::get("ajaxDropCounted/{composit_id}", [CountPdfController::class, "ajaxDropCounted"]);

    // вывод заглушки о неправильном расширении файла
    Route::get("ajaxBadExtension", [CountPdfController::class, "ajaxBadExtension"]);

    // для дебага
    Route::get("test", [CountPdfController::class, "test"]);
});
