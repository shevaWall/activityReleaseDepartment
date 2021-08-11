<?php

namespace Database\Seeders;

use App\Models\Composit;
use Illuminate\Database\Seeder;

class CompositSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Composit::create([
            'object_id' => 1,
            'compositGroup_id' => 1,
            'name' => "Том документации",
        ]);
        Composit::create([
            'object_id' => 1,
            'compositGroup_id' => 2,
            'name' => "Том документации из другого раздела",
            'completed' => 1,
        ]);
    }
}
