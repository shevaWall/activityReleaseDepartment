<?php

namespace Database\Seeders;

use App\Models\Blocknotes;
use App\Models\WarehouseTransactions;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            CompositGroupSeeder::class,
            StatusSeeder::class,
            BlocknotesSeeder::class,
            PrintableObjectSeeder::class,
            CompositSeeder::class,
            CountPdfsSeeder::class,
            WarehouseSeeder::class,
        ]);
    }
}
