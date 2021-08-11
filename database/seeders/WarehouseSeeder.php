<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::create([
            'material' => "Бумага А4",
            'quantity' => 2.215,
        ]);
        Warehouse::create([
            'material' => "Бумага А3",
            'quantity' => 4.215,
        ]);
        Warehouse::create([
            'material' => "Рулонная бумага 841",
            'quantity' => 1.215,
        ]);
        Warehouse::create([
            'material' => "CD",
            'quantity' => 99,
        ]);
    }
}
