@extends('layout', ['title'=>'Склад отдела выпуска'])

@section('content')
    <div class="row">
        <div class="col-12 h2 text-center mt-3">Склад отдела выпуска</div>


        <div class="col-6 py-5">
            <div class="card text-dark">
                <div class="card-body">
                    <h5 class="card-title text-center">Актуальное состояние склада</h5>
                    <table class="table table-striped table-hover warehouseActual">
                        <thead>
                        <tr>
                            <th scope="col">Материал</th>
                            <th scope="col" colspan="2">Количество</th>
                        </tr>
                        </thead>
                        <tbody>
                        @csrf
                        @foreach($warehouse_items as $warehouse_item)
                            @include('warehouse.newTr', $warehouse_item)
                        @endforeach

                        @if($warehouse_items->count() == 0)
                            <div class="text-center">Склад пустой! Надо срочно пополнять!</div>
                        @endif
                        <tr>
                            <td colspan="3">
                                <span class="addCircleBtn mx-auto" onclick="addWarehouseItem(this)">+</span>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 py-5">
            <div class="card text-dark">
                <div class="card-body">
                    <h5 class="card-title text-center">Последнии транзакции склада</h5>
                    <table class="table table-striped table-hover transactionsTable">
                        <thead>
                        <tr>
                            <th scope="col">Тип операции</th>
                            <th scope="col">Описание</th>
                            <th scope="col" class="text-center">Транзакция</th>
                            <th scope="col">Дата</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                @include('warehouseTransaction.newTr', $transactions)
                            @endforeach
                        </tbody>
                    </table>
                    @if($transactions_count >= 15)
                    <div class="col-auto text-center ajaxMoreTransactions">
                        <a href="#">Должно загрузиться само! Молимся...</a>
                    </div>
                    @endif
                    <div class="response">

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
