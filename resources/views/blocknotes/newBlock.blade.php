<div class="row" data-note-id="{{$note->id}}">
    <div class="col-12 bg-note my-1">
        <div class="note-text p-2">
            <span class="deleteNote" onclick="ajaxDeleteNote({{$note->id}}, '{{csrf_token()}}')">X</span>
            {{$note->name}}
        </div>
    </div>
</div>
