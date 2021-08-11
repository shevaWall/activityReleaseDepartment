<?php

namespace Database\Seeders;

use App\Models\Blocknotes;
use Illuminate\Database\Seeder;

class BlocknotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Blocknotes::create([
            'name' => "Это блок заметок! \nСюда можно добавлять любые заметки в ходе рабочего процесса. \nЗаметки можно добавлять, удалять или менять порядок их отображения.\nПопробуйте!",
            'order_id' => 1
        ]);
    }
}
