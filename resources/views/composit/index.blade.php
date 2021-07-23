@extends('layout', ['title'=>'Состав объекта'])

@section('content')
    <div class="row">
        <div class="col-11 my-auto mx-auto">
            <h1 class="text-center">
                <a href="{{route('objects.showObjectSettings', $object->id)}}">{{$object->name}}</a>
            </h1>
        </div>

        <div class="col-auto mx-auto">
            <a class="btn btn-danger" href="{{route('countPdf.clearAll', $object->id)}}">Очистить все форматы</a>
        </div>

        <div class="col-auto mx-auto">
            <a class="btn btn-success" href="{{route('objects.paperConsumption', $object->id)}}">Вывести общий расход бумаги</a>
        </div>

        @foreach($compositGroups as $compositGroup)
            <div class="col-12 my-5">
                <div class="h2 text-center">{{$compositGroup->name}} - <span
                        id="compositGroupPersents_{{$compositGroup->id}}">{{$persents["$compositGroup->id"]}}</span>%
                </div>
                <table class="table numeratedTable">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Название раздела</th>
                        <th scope="col">Статус</th>
                        <th scope="col" colspan="2">Форматы</th>
                    </tr>
                    </thead>
                    <tbody id="compositGroup_{{$compositGroup->id}}">
                    @foreach($composits as $k => $composit)
                        @if($composit->compositGroup_id == $compositGroup->id)
                            @include('composit.newTr', $composit)
                        @endif
                    @endforeach
                    </tbody>
                </table>

                <div class="fieldForDropWrapper py-5"
                     data-object-id="{{$object->id}}"
                     data-composit-group-id="{{$compositGroup->id}}"
                     ondrop="dndDropMany(this, '{{csrf_token()}}')"
                     ondragover="stopPreventDef()"
                     ondragenter="dndDragenter(this)"
                     ondragleave="dndDragleave(this)">
                    <div class="fieldForDropText text-center">Перетащите файлы сюда</div>
                    <div class="fieldForDropCount text-center d-none">Обработка <span id="current">1</span> из <span id="all"></span> </div>
                </div>
            </div>
        @endforeach
    </div>

    <div id="response">

    </div>
@endsection
