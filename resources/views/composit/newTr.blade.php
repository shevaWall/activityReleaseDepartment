<tr id="compositId_{{$composit->id}}" data-composit-id="{{$composit->id}}"
    ondrop="dndReDrop(this, '{{csrf_token()}}')"
    ondragover="stopPreventDef()"
    ondragenter="dndReDragenter(this)"
    @class([
        'bg-warning' => !isset($composit->formats)
    ])
    >
    <th scope="row"></th>
    <td class="dropZone text-center d-none"
        colspan="10"
        ondragleave="dndReDragleave(this)">Отпусти файл</td>
    <td ondblclick="dblclick_renameComposit(this)" class="cursorRenameComposit pointer">{{$composit->name}}</td>
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
    <td class="newTableHere">
        @include('composit.formatsTable')
        <div class="text-center">
            <div class="spinner-border d-none" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>
        </div>
    </td>
    <td>
        <p class="ajaxDeleteComposit pointer"
           onclick="ajaxDeleteComposit(this)">
            <img src="/images/trash.svg" alt="Удалить" title="Удалить"/>
        </p>
    </td>
</tr>
