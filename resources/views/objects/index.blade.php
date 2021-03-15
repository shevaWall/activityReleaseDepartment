@extends('layout', ['title'=>'Объекты'])

@section('content')
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
                        <td>
                            <a href="{{route('objects.composit', $object->id)}}">
                                {{$object->name}}</td>
                            </a>
                        <td>{{$object->cipher}}</td>
                        <td>{{$object->status->name}}</td>
                        <td>{{$object->deadline}}</td>
                        @if(!request()->routeIs('objects.deleted'))
                            <td class="text-center my-auto">
                                <a href="{{route('objects.showObjectSettings', [$object->id])}}">
                                    <img class="img-fluid" style="max-width:32px;" src="/storage/pencil.svg" alt="Редактировать">
                                </a>
                            </td>
                            <td class="text-center my-auto">
                                <a href="{{route('objects.deleteObject', [$object->id])}}">
                                    <img class="img-fluid" style="max-width:32px;" src="/storage/delete.svg" alt="Удалить">
                                </a>
                            </td>
                        @else
                            <td class="text-center my-auto">
                                <a href="{{route('objects.restoreObject', [$object->id])}}">
                                    <img class="img-fluid" style="max-width:32px;" src="/storage/restore.svg" alt="Восстановить" title="Восстановить">
                                </a>
                            </td>
                            <td class="text-center my-auto">
                                <a href="{{route('objects.removeObject', [$object->id])}}">
                                    <img class="img-fluid" style="max-width:32px;" src="/storage/trash.svg" alt="Удалить навсегда" title="Удалить навсегда">
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!request()->routeIs('objects.deleted'))
    <div class="row">
        <div class="col-6">
            <a href="{{route('objects.show_form')}}" class="btn btn-primary">Добавить объект</a>
        </div>
        <div class="col-6 text-end">
            <a href="{{route('objects.deleted')}}" class="text-secondary">Удаленные объекты</a>
        </div>
    </div>
    @endif
@endsection
