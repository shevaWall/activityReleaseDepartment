@extends('layout', ['title'=>'Объекты'])

@section('content')

{{--    todo: настроить отображение всех объектов со статусом "в работе"--}}

    <div class="col-6">
        <a href="{{route('objects.show_form')}}" class="btn btn-primary">Добавить объект</a>
    </div>
    <div class="col-6">

    </div>
@endsection
