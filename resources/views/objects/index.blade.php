@extends('layout', ['title'=>'Объекты'])

@section('content')

{{--    todo: настроить отображение всех объектов со статусом "в работе"--}}

    @if (isset($objects))
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Шифр</th>
                    <th scope="col">Статус</th>
                    <th scope="col">Срок сдачи</th>
                    <th scope="col" colspan="2" class="text-center">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($objects as $k => $object)
                    <tr>
                        <th scope="row">{{++$k}}</th>
                        <td>{{$object->name}}</td>
                        <td>{{$object->cipher}}</td>
                        <td>{{$object->status->name}}</td>
                        <td>{{$object->deadline}}</td>
                        <td class="text-center my-auto">
                            <img class="img-fluid" style="max-width:32px;" src="storage/pencil.svg" alt="Редактировать">
                        </td>
                        <td class="text-center my-auto">
                            <img class="img-fluid" style="max-width:32px;" src="storage/delete.svg" alt="Удалить">
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="col-6">
        <a href="{{route('objects.show_form')}}" class="btn btn-primary">Добавить объект</a>
    </div>
    <div class="col-6">

    </div>
@endsection
