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
        <a class="ajaxDeleteComposit" href="{{route('composit.ajaxDeleteComposit', $composit->id)}}">
            <img src="/storage/trash.svg" alt="Удалить" title="Удалить">
        </a>
    </td>
</tr>
