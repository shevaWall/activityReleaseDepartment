@extends('layout', ['title'=>'Состав объекта'])

@section('content')
    <div class="row">
        <div class="col-1 my-auto text-center">
            <a href="{{route('objects.index')}}">
                <img class="img-fluid" src="/storage/backArrow.svg" alt="Назад" title="Назад">
            </a>
        </div>

        <div class="col-11">
            <h1 class="text-center">
                {{$object->name}}
            </h1>
        </div>
        @foreach($compositGroups as $compositGroup)
            <div class="col-4">
                <div class="h2 text-center">{{$compositGroup->name}} - {{$persents["$compositGroup->id"]}}%</div>
                <form action="{{route('composit.ajaxAddComposit')}}" method="post">
                    <table class="table numeratedTable">
                        <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Название раздела</th>
                            <th scope="col" colspan="2">Статус</th>
                        </tr>
                        </thead>
                        <tbody id="compositGroup_{{$compositGroup->id}}">
                        @foreach($composits as $k => $composit)
                            @if($composit->compositGroup_id == $compositGroup->id)
                                <tr>
                                    <th scope="row"></th>
                                    <td>{{$composit->name}}</td>
                                    <td>
                                        <p class="m-0 pointer {{ ($composit->completed == "Готов") ? 'completed' : 'uncompleted' }}"
                                            id="compositId_{{$composit->id}}"
                                            onclick="ajaxCompositChangeStatus(this)">
                                            {{$composit->completed}}
                                        </p>
                                    </td>
                                    <td>
                                        <a class="ajaxDeleteComposit"
                                           href="{{route('composit.ajaxDeleteComposit', $composit->id)}}">
                                            <img src="/storage/trash.svg" alt="Удалить" title="Удалить"/>
                                        </a>
                                    </td>
                                </tr>
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
