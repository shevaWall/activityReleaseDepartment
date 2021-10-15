<div class="row pointer" data-note-id="{{$note->id}}" data-order-id="{{$note->order_id}}" draggable="true">
    <div class="col-12 bg-note my-1">
        <div class="note-text-wrapper p-2" ondblclick="toggleChangeBlockNoteText(this)">
            <p class="m-0"><small class="text-muted">{{$note->created_at->format('d.m.y')}}</small></p>
            <span class="deleteNote" onclick="ajaxDeleteNote({{$note->id}}, '{{csrf_token()}}')">X</span>
{{--            <span class="noteText">{!!nl2br($note->name, false)!!}</span>--}}
            <span class="noteText">{{$note->name}}</span>
            <textarea class="textareaNoteText d-none w-100" type="text" name="noteText">{{($note->name) }}</textarea>
            <div class="row noteBtns d-none">
                <div class="col-6">
                    <div class="btn btn-danger" onclick="toggleChangeBlockNoteText($(this).parents('.note-text-wrapper'))">Отмена</div>
                </div>
                <div class="col-6 text-end">
                    <div class="btn btn-success" onclick="ajaxChangeBlockNoteText(this)">Сохранить</div>
                </div>
            </div>
        </div>
    </div>
</div>
