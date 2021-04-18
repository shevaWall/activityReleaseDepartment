<tr id="compositId_{{$composit->id}}">
    <th scope="row"></th>
    <td>{{$composit->name}}</td>
    <td>
        <p class="m-0 pointer {{ ($composit->completed == "Готов") ? 'completed' : 'uncompleted' }}"
           onclick="ajaxCompositChangeStatus(this)">
            {{$composit->completed}}
        </p>
    </td>
    <td>
        <p class="ajaxDeleteComposit pointer"
           onclick="ajaxCompositRefresh(this)">
            <img src="/storage/restore.svg" alt="Сбросить" title="Сбросить"/>
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
        <div class="text-center error_pdf d-none">
                <span>ошибка</span>
        </div>
    </td>
    <td>
        <input id="countPdf_{{$composit->id}}" type="file" name="pdf" accept=".pdf">
        <input type="button" value="Подсчитать" onclick="ajaxCountFormats($(this).siblings('#countPdf_{{$composit->id}}'))">
    </td>
</tr>
