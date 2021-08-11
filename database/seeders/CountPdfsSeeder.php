<?php

namespace Database\Seeders;

use App\Models\CountPdf;
use Illuminate\Database\Seeder;

class CountPdfsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CountPdf::create([
            'composit_id' => 1,
            'formats' => '{"419 * 1279":{"count":1,"Colored":1},"\u04102":{"count":1,"Colored":1},"\u04102*3":{"count":2,"Colored":2},"\u04102*4":{"count":6,"Colored":6},"\u04103":{"count":10,"Colored":2,"BW":8},"\u04103*3":{"count":2,"Colored":2},"\u04103*4":{"count":18,"Colored":18},"\u04103*5":{"count":35,"Colored":35},"\u04103*6":{"count":5,"Colored":5},"\u04103*7":{"count":2,"Colored":2},"\u04104":{"count":3,"Colored":3},"\u04104*4":{"count":1,"Colored":1}}',
        ]);
        CountPdf::create([
           'composit_id' => 2,
           'formats' => '{"\u04103":{"count":1,"Colored":1},"\u04103*3":{"count":1,"Colored":1},"\u04104":{"count":210,"Colored":149,"BW":61},"\u04104*4":{"count":1,"Colored":1}}',
        ]);
    }
}
