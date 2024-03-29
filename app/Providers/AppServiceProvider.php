<?php

namespace App\Providers;

use App\Models\PrintableObject;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!\Schema::hasTable("printable_objects")) {
            \Artisan::call("migrate");
        }

        // выводит в шапке в меню количество объектов по статусам
        $a_PrintableObjectsStatusesCount[5] = 0;

        if (\Schema::hasTable('printable_objects')) {
            $objs = PrintableObject::all();
            $a_PrintableObjectsStatusesCount[5] = 0;
            if ($objs->count() > 0) {
                foreach ($objs as $obj) {
                    if (!isset($a_PrintableObjectsStatusesCount[$obj->status_id])) {
                        $a_PrintableObjectsStatusesCount[$obj->status_id] = 1;
                    } else {
                        $a_PrintableObjectsStatusesCount[$obj->status_id]++;
                    }
                    $a_PrintableObjectsStatusesCount[5]++;
                }
                for ($status_id = 1; $status_id <= 5; $status_id++) {
                    if (!key_exists($status_id, $a_PrintableObjectsStatusesCount))
                        $a_PrintableObjectsStatusesCount[$status_id] = 0;
                }
            } else {
                // заглушечка, если ещё нет ни одного объекта
                for ($i = 0; $i <= 5; $i++) {
                    $a_PrintableObjectsStatusesCount[$i] = 0;
                }
            }
        } else {

        }


        View::share('cntPrntblObjsStatuses', $a_PrintableObjectsStatusesCount);
    }
}
