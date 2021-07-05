@extends('layout', ['title'=>$object->name])

@section('content')
    <div class="row">
        <div class="col-1 my-auto text-center">
            <a href="{{route('objects.composit', $object->id)}}">
                <img class="img-fluid" src="/images/backArrow.svg" alt="Назад" title="Назад">
            </a>
        </div>
        <div class="col-11 my-auto mx-auto text-center">
            <h1 class="h1">{{$object->name}}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-auto">В настроках заявки установлены следующие настройки:</div>
        <div class="col-auto">
            ПД: @php ((!is_null($object->count_pd) ? print_r ($object->count_pd) : print_r(1) )) @endphp экз.
        </div>
        <div class="col-auto">
            РД: @php ((!is_null($object->count_rd) ? print_r ($object->count_rd) : print_r(1) )) @endphp экз.
        </div>
        <div class="col-auto">
            ИИ: @php ((!is_null($object->count_ii) ? print_r ($object->count_ii) : print_r(1) )) @endphp экз.
        </div>
    </div>

    <div class="col-12 text-center h3 mt-5">Сводная таблица</div>
    <div class="row justify-content-end">
        <div class="col-auto">
            <div class="btn border-warning" onclick="showTotalPaperConsumption(this)">Показать итого</div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-hover tablePaperConsumption">
            <thead>
            <tr>
                <td>Формат</td>
                <td>Цветных</td>
                <td>Черно-белых</td>
                <td class="toggleTotal d-none">Итого</td>
            </tr>
            </thead>
            <tbody>
            @foreach($formats as $formatName => $format)
                <tr>
                    <td>{{$formatName}}</td>
                    <td>
                        @if(isset($format['Colored']))
                            {{$format['Colored']}}
                        @endif
                    </td>
                    <td>
                        @if(isset($format['BW']))
                            {{$format['BW']}}
                        @endif
                    </td>
                    <td class="toggleTotal d-none">
                        {{$format['total']}}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
