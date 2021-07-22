<?php

namespace Database\Seeders;

use App\Models\Composit;
use App\Models\CompositGroup;
use Illuminate\Database\Seeder;

class CompositGroupSeeder extends Seeder
{
    /**
     * Заполняем таблицу CompositGroup названиями разделов (ПД/РД/ИИ)
     *
     * @return void
     */
    public function run()
    {
        //
        CompositGroup::create([
           'name' => "ПД"
        ]);
        CompositGroup::create([
            'name' => "РД"
        ]);
        CompositGroup::create([
            'name' => "ИИ"
        ]);
    }
}
