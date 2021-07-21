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
     * 0 => [
     * 'name'  => $warehouseItem->material,
     * 'before'=> $quantityBefore,
     * 'after' => $request->quantity,
     * ]
     * ];
     * @return mixed
     */
    static function makeTransaction(bool $type, string $description, $transactionInfo){
        return WarehouseTransactions::create([
            'type_of_operation' => $type,
            'description'       => $description,
            'transaction'       => $transactionInfo,
        ]);
    }

    /**
     * аякс подгрузка транзакций при прокрутке страницы
     * @param WarehouseTransactions $WarehouseTransactions
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function ajaxMoreTransaction(WarehouseTransactions $WarehouseTransactions){
        $transactions = WarehouseTransactions::where('id','<', $WarehouseTransactions->id)->orderBy('id', 'desc')->take(3)->get();

        return view('warehouseTransaction.ajaxMoreTransactions')
            ->with('transactions', $transactions)
            ->with('count', $transactions->count())
            ;
    }
}
