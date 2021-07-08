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

    <div id="paperConsumptionTabs">
        <ul>
            <li>
                <a href="#tabs-1">Сводная таблица</a>
            </li>
            <li>
                <a href="#tabs-2">ПД</a>
            </li>
            <li>
                <a href="#tabs-3">РД</a>
            </li>
            <li>
                <a href="#tabs-4">ИИ</a>
            </li>
        </ul>
        <div id="tabs-1">
            <div id="paperConsumptionTabsSubPivotTable">
                <ul>
                    <li>
                        <a href="#pivotTabs-1">Общее</a>
                    </li>
                    <li>
                        <a href="#pivotTabs-2">Для одного экземпляра</a>
                    </li>
                </ul>
                <div id="pivotTabs-1">
                    <div class="col-12 text-center h3 mt-5">Сводная таблица</div>
                    <div class="row justify-content-end">
                        <div class="col-auto">
                            <div class="btn border-warning" onclick="showTotalPaperConsumption(this)">Показать итого
                            </div>
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
                            @foreach($formats['formats'] as $formatName => $format)
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
                </div>
                <div id="pivotTabs-2">
                    <div class="col-12 text-center h3 mt-5">Для одного экземляра</div>
                    {{--<div class="row justify-content-end">
                        <div class="col-auto">
                            <div class="btn border-warning" onclick="showTotalPaperConsumption(this)">Показать итого</div>
                        </div>
                    </div>--}}
                    <div class="table-responsive">
                        <table class="table table-striped table-hover tablePaperConsumptionOnce">
                            <thead>
                            <tr>
                                <td>Формат</td>
                                <td>Цветных</td>
                                <td>Черно-белых</td>
                                {{--                                <td class="toggleTotal d-none">Итого</td>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($formats['formats'] as $formatName => $format)
                                <tr>
                                    <td>{{$formatName}}</td>
                                    <td>
                                        @if(isset($format['Colored_once']))
                                            {{$format['Colored_once']}}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($format['BW_once']))
                                            {{$format['BW_once']}}
                                        @endif
                                    </td>
                                    {{--<td class="toggleTotal d-none">
                                        {{$format['total_once']}}
                                    </td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="tabs-2">
            <div id="paperConsumptionTabsSubPD">
                <ul>
                    <li>
                        <a href="#PDTabs-1">Общее</a>
                    </li>
                    <li>
                        <a href="#PDTabs-2">Для одного экземпляра</a>
                    </li>
                </ul>
                <div id="PDTabs-1">
                    <div class="col-12 text-center h3 mt-5">Общее</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover tablePaperConsumptionOnce">
                            <thead>
                            <tr>
                                <td>Формат</td>
                                <td>Цветных</td>
                                <td>Черно-белых</td>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($formats['1']))
                                @foreach($formats['1'] as $formatName => $format)
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
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="PDTabs-2">
                    <div class="col-12 text-center h3 mt-5">Для одного экземпляра</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover tablePaperConsumptionOnce">
                            <thead>
                            <tr>
                                <td>Формат</td>
                                <td>Цветных</td>
                                <td>Черно-белых</td>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($formats['1']))
                                @foreach($formats['1'] as $formatName => $format)
                                    <tr>
                                        <td>{{$formatName}}</td>
                                        <td>
                                            @if(isset($format['Colored_once']))
                                                {{$format['Colored_once']}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($format['BW_once']))
                                                {{$format['BW_once']}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="tabs-3">
            <div id="paperConsumptionTabsSubRD">
                <ul>
                    <li>
                        <a href="#RDTabs-1">Общее</a>
                    </li>
                    <li>
                        <a href="#RDTabs-2">Для одного экземпляра</a>
                    </li>
                </ul>
                <div id="RDTabs-1">
                    <div class="col-12 text-center h3 mt-5">Общее</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover tablePaperConsumptionOnce">
                            <thead>
                            <tr>
                                <td>Формат</td>
                                <td>Цветных</td>
                                <td>Черно-белых</td>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($formats['2']))
                                @foreach($formats['2'] as $formatName => $format)
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
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="RDTabs-2">
                    <div class="col-12 text-center h3 mt-5">Для одного экземпляра</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover tablePaperConsumptionOnce">
                            <thead>
                            <tr>
                                <td>Формат</td>
                                <td>Цветных</td>
                                <td>Черно-белых</td>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($formats['2']))
                                @foreach($formats['2'] as $formatName => $format)
                                    <tr>
                                        <td>{{$formatName}}</td>
                                        <td>
                                            @if(isset($format['Colored_once']))
                                                {{$format['Colored_once']}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($format['BW_once']))
                                                {{$format['BW_once']}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div id="tabs-4">
            <div id="paperConsumptionTabsSubII">
                <ul>
                    <li>
                        <a href="#IITabs-1">Общее</a>
                    </li>
                    <li>
                        <a href="#IITabs-2">Для одного экземпляра</a>
                    </li>
                </ul>
                <div id="IITabs-1">
                    <div class="col-12 text-center h3 mt-5">Общее</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover tablePaperConsumptionOnce">
                            <thead>
                            <tr>
                                <td>Формат</td>
                                <td>Цветных</td>
                                <td>Черно-белых</td>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($formats['3']))
                                @foreach($formats['3'] as $formatName => $format)
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
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="IITabs-2">
                    <div class="col-12 text-center h3 mt-5">Для одного экземпляра</div>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover tablePaperConsumptionOnce">
                            <thead>
                            <tr>
                                <td>Формат</td>
                                <td>Цветных</td>
                                <td>Черно-белых</td>
                            </tr>
                            </thead>
                            <tbody>
                            @if(isset($formats['3']))
                                @foreach($formats['3'] as $formatName => $format)
                                    <tr>
                                        <td>{{$formatName}}</td>
                                        <td>
                                            @if(isset($format['Colored_once']))
                                                {{$format['Colored_once']}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($format['BW_once']))
                                                {{$format['BW_once']}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
