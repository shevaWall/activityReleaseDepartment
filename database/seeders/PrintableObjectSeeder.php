<?php

namespace Database\Seeders;

use App\Models\PrintableObject;
use Illuminate\Database\Seeder;

class PrintableObjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PrintableObject::create([
            'name' => "Здесь название объекта",
            'description' => "Здесь краткое (или наоборот, подробное) описание. Обычно сюда пишем откуда печатаем",
            'nomerZayavki' => 0,
            'object_owner' => 'Здесь указываем чей объект. Или от кого пришла заявка',
            'status_id' => 1,
        ]);
    }
}
