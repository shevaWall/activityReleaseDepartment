<?php

namespace Database\Seeders;

use App\Models\WarehouseTransactions;
use Illuminate\Database\Seeder;

class WarehouseTransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WarehouseTransactions::create([
            'type_of_operation' => 1,
            'description' => "Добавлено при инициализации",
            'transaction' => json_encode('[{"name":"\u0411\u0443\u043c\u0430\u0433\u0430 \u04104","before":0,"after":"2.215"}]')
        ]);
        WarehouseTransactions::create([
            'type_of_operation' => 1,
            'description' => "Добавлено при инициализации",
            'transaction' => '[{"name":"\u0411\u0443\u043c\u0430\u0433\u0430 \u04103","before":0,"after":"4.215"}]'
        ]);
        WarehouseTransactions::create([
            'type_of_operation' => 1,
            'description' => "Добавлено при инициализации",
            'transaction' => '[{"name":"\u0420\u0443\u043b\u043e\u043d\u043d\u0430\u044f \u0431\u0443\u043c\u0430\u0433\u0430 841","before":0,"after":"1.215"}]'
        ]);
        WarehouseTransactions::create([
            'type_of_operation' => 1,
            'description' => "Добавлено при инициализации",
            'transaction' => '[{"name":"CD","before":0,"after":"99"}]'
        ]);
    }
}
