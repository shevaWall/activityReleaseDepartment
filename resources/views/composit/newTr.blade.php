<tr>
    <th scope="row"></th>
    <td>{{$composit->name}}</td>
    <td>
        <p class="m-0 pointer {{ ($composit->completed == "Готов") ? 'completed' : 'uncompleted' }}"
           id="compositId_{{$composit->id}}"
           onclick="ajaxCompositChangeStatus(this)">
            {{$composit->completed}}
        </p>
    </td>
    <td>
        <p class="ajaxDeleteComposit pointer"
           onclick="ajaxDeleteComposit(this)">
            <img src="/storage/trash.svg" alt="Удалить" title="Удалить"/>
        </p>
    </td>
</tr>
