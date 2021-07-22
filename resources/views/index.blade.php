@extends('layout', ['title' => 'Отдел выпуска'])

@section('content')
    <div class="row">
        <div class="col-6 latestAddedObjects py-5">
            <div class="row card text-dark">
                <div class="card-body">
                    <h5 class="card-title text-center">Последние добавленные заявки</h5>
                    @if($latestObjects->count() != 0)
                        <ul class="list-group list-group-flush">
                            @foreach($latestObjects as $latestObject)
                                <li class="list-group-item">
                                    <a href="{{route('objects.composit', $latestObject->id)}}">
                                        <small class="text-muted">{{$latestObject->created_at->format('d.m.y')}}</small>
                                        {{$latestObject->name}}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <div class="text-end"><a href="{{route('objects.withStatus', 5)}}">Посмотреть все &rarr;</a>
                        </div>
                    @else
                        <div class="text-center">Пока нет объектов - пора добавить</div>
                        <div class="text-center">
                            <div class="btn btn-link"><a href="{{route('objects.submit_form')}}">добавить</a></div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-6 notes py-5">
            <div class="row card text-dark">
                <div class="card-body">
                    <h5 class="card-title text-center">Заметки</h5>
                    @if(isset($notes) && count($notes) > 0)
                        <div class="note-list">
                            @foreach($notes as $note)
                                @include('blocknotes.newBlock', $note)
                            @endforeach
                        </div>
                    @else
                        <div class="text-center">Пока здесь нет записей</div>
                    @endif
                    <form action="/" method="post" id="notesForm" class="mt-3">
                        @csrf
                        <div class="col-12 text-center">
                            <textarea class="form-control" id="newNote" type="text" name="name"
                                      placeholder="Ввести текст заметки"></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
