<?php

namespace App\Http\Controllers;

use App\Models\WarehouseTransactions;
use Illuminate\Http\Request;

class WarehouseTransactionsController extends Controller
{

    /**
     * @param bool $type - тип операции, false - списание, true - пополнение
     * @param string $description - описание операции
     * @param $transactionInfo - обычно это массив информации, содержащий в себе информацию о названии материала (name),
     * количество до транзакции (before) и количество после транзакции (after). Например
     * $transactionInfo = [
        0 => [
        'name'  => $warehouseItem->material,
        'before'=> $quantityBefore,
        'after' => $request->quantity,
        ]
        ];
     * @param int|null $printableObject_id - необязательное, id связанного объекта(заявки) печати
     */
    static function makeTransaction(bool $type, string $description, $transactionInfo, int $printableObject_id = null){
        return WarehouseTransactions::create([
            'type_of_operation' => $type,
            'description'       => $description,
            'transaction'       => $transactionInfo,
        ]);
    }


    // аякс подгрузка транзакций при прокрутке страницы
    public function ajaxMoreTransaction(int $lastTransactionId){
        $transactions = WarehouseTransactions::where('id','<', $lastTransactionId)->orderBy('id', 'desc')->take(3)->get();

        return view('warehouseTransaction.ajaxMoreTransactions')
            ->with('transactions', $transactions)
            ->with('count', $transactions->count())
            ;
    }
}
