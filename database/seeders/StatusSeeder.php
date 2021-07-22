<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::create([
            'name'=>"В работе"
        ]);
        Status::create([
            'name'=>"Сданы"
        ]);
        Status::create([
            'name'=>"На паузе"
        ]);
        Status::create([
            'name'=>"Удалённые"
        ]);
        Status::create([
            'name'=>"Все"
        ]);
    }
}
