@extends('layout', ['title'=>'Объекты'])

@section('content')
    @if (isset($objects))
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название</th>
                    <th scope="col">Готовность</th>
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
                        <td>
                            {{$object->composits['persents']}} %
                        </td>
                        <td>
                            <select class="form-select" id="objectStatusId_{{$object->id}}" onchange="ajaxChangeObjectStatus(this)">
                                @foreach($statuses as $status)
                                    <option
                                        value="{{$status->id}}"
                                        {{($object->status->name == $status->name) ? 'selected' : ""}}
                                    >{{$status->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>{{$object->deadline}}</td>
                        <td class="text-center my-auto">
                            <a href="{{route('objects.showObjectSettings', [$object->id])}}">
                                <img class="img-fluid" style="width:32px;" src="/storage/pencil.svg" alt="Редактировать">
                            </a>
                        </td>
                        @if(!request()->is('objects/withStatus/4'))
                            <td class="text-center my-auto">
                                <a href="{{route('objects.ajaxChangeObjectStatus', [$object->id, 4])}}">
                                    <img class="img-fluid" style="width:32px;" src="/storage/delete.svg" alt="Удалить">
                                </a>
                            </td>
                        @else
                            <td class="text-center my-auto">
                                <a href="{{route('objects.removeObject', [$object->id, $object->status_id])}}">
                                    <img class="img-fluid" style="width:32px;" src="/storage/trash.svg" alt="Удалить навсегда" title="Удалить навсегда">
                                </a>
                            </td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif

<div class="row">
    <div class="col-6">
        <a href="{{route('objects.show_form')}}" class="btn btn-primary">Добавить объект</a>
    </div>
</div>
@endsection
