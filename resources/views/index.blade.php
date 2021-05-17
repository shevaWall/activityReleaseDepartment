@extends('layout', ['title' => 'Отдел выпуска'])

@section('content')
    <div class="row">
        <div class="col-6 latestAddedObjects py-5 ">
            <div class="row card text-dark">
                <div class="card-body">
                    <h5 class="card-title text-center">Последние добавленные объекты</h5>
                    <ul class="list-group list-group-flush">
                        @foreach($latestObjects as $latestObject)
                            <li class="list-group-item">
                                <a href="{{route('objects.composit', $latestObject->id)}}"><small class="text-muted">{{$latestObject->created_at->format('d.m.y')}}</small> {{$latestObject->name}}</a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="text-end"><a href="{{route('objects.withStatus', 5)}}">Посмотреть все &rarr;</a></div>
                </div>
            </div>
        </div>
    </div>
@endsection
