@foreach($transactions as $transaction)
    @include('warehouseTransaction.newTr', ['transactions' => $transaction])
@endforeach

@if($count < 3)
    <script>
        $('.ajaxMoreTransactions').remove();
    </script>
@endif
