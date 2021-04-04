@extends('layout', ['title'=>'Состав объекта'])

@section('content')
    <div class="row">
        <div class="col-1 my-auto text-center">
            <a href="{{route('objects.index')}}">
                <img class="img-fluid" src="/storage/backArrow.svg" alt="Назад" title="Назад">
            </a>
        </div>

        <div class="col-11 my-auto">
            <h1 class="text-center">
                {{$object->name}}
            </h1>
        </div>
        @foreach($compositGroups as $compositGroup)
            <div class="col-12">
                <div class="h2 text-center">{{$compositGroup->name}} - <span id="compositGroupPersents_{{$compositGroup->id}}">{{$persents["$compositGroup->id"]}}</span>%</div>
                <form action="{{route('composit.ajaxAddComposit')}}" method="post">
                    <table class="table numeratedTable">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Название раздела</th>
                                <th scope="col" colspan="2">Статус</th>
                                <th scope="col" colspan="2">Форматы</th>
                            </tr>
                        </thead>
                        <tbody id="compositGroup_{{$compositGroup->id}}">
                        @foreach($composits as $k => $composit)
                            @if($composit->compositGroup_id == $compositGroup->id)
                                @include('composit.newTr', $composit)
                            @endif
                        @endforeach
                        <tr>
                            <th scope="row"></th>
                            <td>
                                @csrf
                                <input type="hidden" name="compositGroup_id" value="{{$compositGroup->id}}">
                                <input type="hidden" name="object_id" value="{{$object->id}}">
                                <input type="text" name="name">
                            </td>
                            <td class="text-center">
                                <input type="submit" class="ajaxSend" value="+"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        @endforeach
    </div>

    <div id="response">

    </div>
@endsection
