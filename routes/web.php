<?php

use App\Http\Controllers\BlocknotesController;
use App\Http\Controllers\CompositController;
use App\Http\Controllers\CountPdfController;
use App\Http\Controllers\IndexPageController;
use App\Http\Controllers\PrintableObjectController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarehouseTransactionsController;
use App\Models\PrintableObject;
use App\Models\Status;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

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


Route::get("/", [IndexPageController::class, "index"])
    ->name('index')
    ->breadcrumbs(function (Trail $trail) {
        return $trail->push('Главная', route('index'));
    });


/**
 * роуты для работы с объектами
 */
Route::group([
//    'middleware'=>  '',
    'prefix' => 'objects',
    'as' => 'objects.',
], function () {
    Route::get("/", [PrintableObjectController::class, "index"])
        ->name('index')
        ->breadcrumbs(function (Trail $trail) {
            return $trail->parent('index')
                ->push('Все заявки', route('objects.index'));
        });

    // выводит заявки по статусам (в работе, на паузе, сданы, удалены, все)
    // todo: придумать как сделать роут
    Route::get("withStatus/{Status}", [PrintableObjectController::class, "withStatus"])
        ->name('withStatus')
        ->breadcrumbs(function (Trail $trail, Status $Status) {
            return $trail->parent('index')
                ->push("Заявки -> $Status->name", route('objects.withStatus', $Status->id));
        });

    // форма добавление и обработчик для новой заявки
    Route::get("add", [PrintableObjectController::class, "addNewObjectForm"])
        ->name('show_form')
        ->breadcrumbs(function (Trail $trail) {
            return $trail->parent('objects.index')
                ->push('Добавить новую заявку', route('objects.show_form'));
        });
    Route::post("add", [PrintableObjectController::class, "addNewObjectSubmit"])
        ->name('submit_form');

    // аякс измененение статуса заявки на "удалён"
    Route::get('delete/{object_id}', [PrintableObjectController::class, "ajaxChangeObjectStatus"])
        ->name('deleteObject');

    // навсегда удаляет объект из БД
    Route::get("{PrintableObject}-{Status}/remove", [PrintableObjectController::class, "removeObject"])
        ->name('removeObject');

    // аякс изменение статуса заявки
    Route::get("changeObjectStatus/{PrintableObject}/{Status}", [PrintableObjectController::class, "ajaxChangeObjectStatus"])
        ->name('ajaxChangeObjectStatus');

    // отображение состава раздела
    Route::get("{PrintableObject}", [CompositController::class, "index"])
        ->name('composit')
        ->breadcrumbs(function (Trail $trail, PrintableObject $PrintableObject) {
            return $trail->parent('objects.withStatus', Status::find($PrintableObject->status_id))
                ->push($PrintableObject->name, route('objects.composit', $PrintableObject->id));
        });

    // отображение настроек заявки
    Route::get("{PrintableObject}/edit", [PrintableObjectController::class, "showObjectSettings"])
        ->name('showObjectSettings')
        ->breadcrumbs(function (Trail $trail, PrintableObject $PrintableObject) {
            return $trail->parent('objects.composit', PrintableObject::find($PrintableObject->id))
                ->push("Настройки", route('objects.showObjectSettings', $PrintableObject->id));
        });

    // отображение сводной таблицы подсчитанных страниц
    Route::get("{PrintableObject}/showPaperConsumption", [PrintableObjectController::class, "showPaperConsumption"])
        ->name('paperConsumption')
        ->breadcrumbs(function (Trail $trail, PrintableObject $PrintableObject) {
            return $trail->parent('objects.composit', PrintableObject::find($PrintableObject->id))
                ->push('Расход материалов', route('objects.paperConsumption', $PrintableObject->id));
        });

});

Route::group([
//    'middleware'=>  '',
    'prefix' => 'search',
    'as' => 'search.',
], function () {
    Route::get("/", [SearchController::class, "index"])
        ->name('index')
        ->breadcrumbs(function (Trail $trail) {
            return $trail->parent('index')
                ->push('Поиск', route('search.index'));
        });
});

/**
 * роуты для работы с составами объектов
 */
Route::group([
//    'middleware'=>  '',
    'prefix' => 'composit',
    'as' => 'composit.',
], function () {
    // аякс добавление раздела (состава)
    Route::post("ajaxAddComposit", [CompositController::class, "ajaxAddComposit"])
        ->name('ajaxAddComposit');

    // аякс удаление раздела (состава)
    Route::get("ajaxDeleteComposit/{Composit}", [CompositController::class, "ajaxDeleteComposit"])
        ->name('ajaxDeleteComposit');

    // аякс изменение статуса (готов/не готов) раздела (соства)
    Route::get("ajaxChangeCompositStatus/{Composit}", [CompositController::class, "ajaxChangeCompositStatus"])
        ->name('ajaxChangeCompositStatus');

    Route::post('ajaxRenameComposit/{Composit}', [CompositController::class, "ajaxRenameComposit"]);
});

/**
 * роуты для работы с подсчетом страниц по форматам в ПДФ
 */
Route::group([
//    'middleware'=>  '',
'prefix' => 'countPdf',
    'as' => 'countPdf.',
], function () {
    // аякс загрузка файла и инициализация рассчетов форматов
    Route::post('ajaxCountPdf/{composit_id}', [CountPdfController::class, "ajaxLoadFile"])
        ->name('/ajaxCountPdf');

    // аякс получить подсчитанные форматы
    Route::get("/ajaxGetCountedPdf/{composit_id}", [CountPdfController::class, "ajaxGetCountedPdf"])
        ->name('ajaxGetCountedPdf');

    // очищает все подсчитанные форматы в объекте
    Route::get("clearAll/{ajaxDropCounted}", [CountPdfController::class, "clearAll"])
        ->name('clearAll');

    // аякс очищает список подсчитанных форматов у определенного раздела (состава)
    Route::get("ajaxDropCounted/{Composit}", [CountPdfController::class, "ajaxDropCounted"]);

    // для дебага
    Route::get("test", [CountPdfController::class, "test"]);
});

Route::group([
//    'middleware'=>  '',
    'prefix' => 'warehouse',
    'as' => 'warehouse.',
], function () {
    Route::get("/", [WarehouseController::class, "index"])
        ->name('index')
        ->breadcrumbs(function (Trail $trail) {
            return $trail->parent('index')
                ->push('Склад', route('warehouse.index'));
        });

    Route::any("updateWarehouseActualData", [WarehouseController::class, "ajaxUpdateWarehouseActualData"])
        ->name('updateWarehouseActualData');

    Route::get("ajaxAddNewTr", [WarehouseController::class, "ajaxAddNewTr"]);
    Route::get("ajaxDeleteItem/{Warehouse}", [WarehouseController::class, "ajaxDeleteItem"]);

    Route::get("ajaxMoreTransaction/{WarehouseTransactions}", [WarehouseTransactionsController::class, "ajaxMoreTransaction"]);
});

Route::group([
//    'middleware'=>  '',
    'prefix' => 'blocknotes',
    'as' => 'blocknotes.',
], function () {
    Route::post("addNote", [BlocknotesController::class, "ajaxAddNote"]);
    Route::post('deleteNote', [BlocknotesController::class, "ajaxDeleteNote"]);
    Route::post('changeOrdersId', [BlocknotesController::class, "ajaxChangeOrdersId"]);
});
