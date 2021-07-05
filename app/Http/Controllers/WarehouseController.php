<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseTransactions;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index(){
        $transactions = WarehouseTransactions::all()->sortByDesc('created_at')->take(15);

        return view('warehouse.index')
            ->with('warehouse_items', Warehouse::all())
            ->with('transactions', $transactions)
            ->with('transactions_count', $transactions->count())
            ;
    }

//    todo: сделать свой request, если какое-то поле не проходит по требованиям
    public function ajaxUpdateWarehouseActualData(Request $request){
        $transactionType = true; // false - списание, true - пополнение
        $newWarehouseItemId = '';

        if($request->id != 0){
            $warehouseItem = Warehouse::findOrFail($request->id);

            // записываем последнее значение количества материала, перед перезаписью
            $quantityBefore = $warehouseItem->quantity;

            ((float)$warehouseItem->quantity > (float)$request->quantity) ? $transactionType = false : $transactionType = true;
            $warehouseItem->material = $request->material;
            $warehouseItem->quantity = $request->quantity;

            $warehouseItem->save();

            $transactionDescription = 'Ручное исправление';
        }else{
            // а тут принудительно ставим в ноль, потому что до этого не было такого материала
            $quantityBefore = 0;
            $warehouseItem = Warehouse::create([
                'material' => $request->material,
                'quantity' => $request->quantity,
            ]);

            $transactionDescription = 'Добавление нового материала';
            $newWarehouseItemId = $warehouseItem->id;
        }
        $transactionInfo = [
            0 => [
                'name'  => $warehouseItem->material,
                'before'=> $quantityBefore,
                'after' => $request->quantity,
            ]
        ];

        $WarehouseTransaction = WarehouseTransactionsController::makeTransaction($transactionType, $transactionDescription, $transactionInfo);

        return view('warehouseTransaction.newTr')
            ->with('transaction', $WarehouseTransaction)
            ->with('warehouseItemId', $newWarehouseItemId)
            ;
    }

//    ajax добавление нового материала в актуальный склад
    public function ajaxAddNewTr(){
        return view('warehouse.newTr');
    }

    public function ajaxDeleteItem(int $warehouseItem_id){
        $deleteWarehouseItem = Warehouse::findOrFail($warehouseItem_id);

        $transactionInfo = [
            0 => [
                'name'  => $deleteWarehouseItem->material,
                'before'=> $deleteWarehouseItem->quantity,
                'after' => 0,
            ]
        ];

        $transaction = WarehouseTransactionsController::makeTransaction(0, 'Удаление материала со склада', $transactionInfo);
        $deleteWarehouseItem->delete();

        return view('warehouseTransaction.newTr')
            ->with('transaction', $transaction);
    }
}
