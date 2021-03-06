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
                    <th scope="col" colspan="3" class="text-center">Действия</th>
                </tr>
                </thead>
                <tbody>
                @foreach($objects as $k => $object)
                    <tr>
                        <th scope="row">{{++$k}}</th>
                        <td>
                            <a href="{{route('objects.composit', $object->id)}}">
                                {{$object->name}}
                            </a>
                            @if(strlen($object->description) != 0 && !is_null($object->description ))
                                <p class="text-secondary mb-0">Примечание: {{$object->description}}</p>
                            @endif
                            <p class="text-secondary my-0">Дата создания: {{$object->created_at->format('d.m.y')}}</p>
                            <p class="text-secondary">Последнее изменение: {{$object->updated_at->format('d.m.y')}}</p>
                        </td>
                        <td>
                            {{$object->composits['persents']}} %
                        </td>
                        <td class="fixMinWidth">
                            <select class="form-select" id="objectStatusId_{{$object->id}}"
                                    onchange="ajaxChangeObjectStatus(this)">
                                @foreach($statuses as $status)
                                    <option
                                        value="{{$status->id}}"
                                        {{($object->status->name == $status->name) ? 'selected' : ""}}
                                    >{{$status->name}}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="fixMinWidth">{{$object->deadline}}</td>
                        <td class="text-center my-auto">
                            <a href="{{route('objects.showObjectSettings', [$object->id])}}">
                                <img class="img-fluid" style="width:32px;" src="/images/pencil.svg"
                                     alt="Редактировать">
                            </a>
                        </td>
                        <td class="text-center my-auto">
                            <a href="{{route('objects.paperConsumption', $object->id)}}">
                                <img class="img-fluid" style="width:32px;" src="/images/document-size.svg"
                                     alt="Расход бумаги">
                            </a>
                        </td>
                        @if(!request()->is('objects/withStatus/4'))
                            <td class="text-center my-auto">
                                <a href="{{route('objects.ajaxChangeObjectStatus', [$object->id, 4])}}">
                                    <img class="img-fluid" style="width:32px;" src="/images/delete.svg" alt="Удалить">
                                </a>
                            </td>
                        @else
                            <td class="text-center my-auto">
                                <a href="{{route('objects.removeObject', [$object->id, $object->status_id])}}">
                                    <img class="img-fluid" style="width:32px;" src="/images/trash.svg"
                                         alt="Удалить навсегда" title="Удалить навсегда">
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
