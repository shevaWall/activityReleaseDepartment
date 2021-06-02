<tr id="compositId_{{$composit->id}}">
    <th scope="row"></th>
    <td ondblclick="dblclick_renameComposit(this)" class="cursorRenameComposit">{{$composit->name}}</td>
    <td class="renameComposit d-none">
        <input type="text" value="{{$composit->name}}">
        <span class="completeRenaming" onclick="completeRenameComposit(this)"></span>
    </td>
    <td>
        <p class="m-0 pointer {{ ($composit->completed == "Готов") ? 'completed' : 'uncompleted' }}"
           onclick="ajaxCompositChangeStatus(this)">
            {{$composit->completed}}
        </p>
    </td>
    <td>
        <p class="ajaxDeleteComposit pointer"
           onclick="ajaxCompositRefresh(this)">
            <img src="/images/restore.svg" alt="Сбросить" title="Сбросить"/>
        </p>
    </td>
    <td>
        <p class="ajaxDeleteComposit pointer"
           onclick="ajaxDeleteComposit(this)">
            <img src="/images/trash.svg" alt="Удалить" title="Удалить"/>
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
    <td class="col-4 align-middle">
        <input class="d-none"
               id="countPdf_{{$composit->id}}"
               type="file"
               name="pdf"
               accept=".pdf"
               onchange="ajaxCountFormats($(this).siblings('#dropZone_{{$composit->id}}'), $(this).prop('files')[0])">
        <div id="dropZone_{{$composit->id}}"
             class="py-5 text-center"
             onclick="openFileExplorer(this)"
             ondragenter="dndDragenter(this)"
             ondragleave="dndDragleave(this)"
             ondrop="dndDrop(this)"
             ondragover="stopPreventDef()">
            Для загрузки, перетащите файл сюда или нажмите здесь.
        </div>
    </td>
</tr>
