<tr data-transaction-id="{{$transaction->id}}" class="{{($transaction->type_of_operation == true) ? 'bg-success text-white' : 'bg-danger text-white'}}">
    <td>{{($transaction->type_of_operation == true) ? 'Пополнение' : 'Списание'}}</td>
    <td>{{$transaction->description}}</td>
    <td>
        <table class="text-white mx-auto">
            <thead>
            <tr>
                <th>Было</th>
                <th></th>
                <th>Стало</th>
            </tr>
            </thead>
            <tbody>
            @foreach($transaction->transaction as $transactionInfo)
                <tr class="text-center">
                    <td colspan="3">{{$transactionInfo['name']}}</td>
                </tr>
                <tr>
                    <td class="text-center">{{$transactionInfo['before']}}</td>
                    <td>-></td>
                    <td class="text-center">{{$transactionInfo['after']}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </td>
    <td>{{$transaction->created_at}}</td>
</tr>
<script>
    $(document).ready(function(){
       let tr = $('.warehouseActual').find('tr[data-warehouse-item = 0]');
        @if (isset($warehouseItemId) && !empty($warehouseItemId))
            if({{$warehouseItemId}} !== '')
                $(tr).attr('data-warehouse-item', {{$warehouseItemId}});
       @endif
    });
</script>

