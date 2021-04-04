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
    <td class="newTableHere">
        @include('composit.formatsTable')
        <div class="text-center">
            <div class="spinner-border d-none" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
    </td>
    <td>
        <input id="countPdf" type="file" name="pdf" accept=".pdf">
        <input type="button" value="Подсчитать" onclick="ajaxCountFormats($(this).siblings('#countPdf'))">
    </td>
</tr>
