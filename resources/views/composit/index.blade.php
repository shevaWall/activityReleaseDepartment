@extends('layout', ['title'=>'Состав объекта'])

@section('content')
    <div class="row">
        <div class="col-12">
            <h1 class="text-center">
                {{$object->name}}
            </h1>
        </div>
        <div class="col-4">
            <div class="h2 text-center">ПД</div>
            <table class="table numeratedTable">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Название раздела</th>
                        <th scope="col">Статус</th>
                    </tr>
                </thead>
                <tbody>
                    @php $counter = 1; @endphp
                    @foreach($composits as $k => $composit)
                        @if($composit->compositGroup_id == 1)
                            <tr>
                                <th scope="row">{{$counter++}}</th>
                                <td>{{$composit->name}}</td>
                                <td>{{$composit->completed}}</td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-4">
            <div class="h2 text-center">РД</div>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название раздела</th>
                    <th scope="col">Статус</th>
                </tr>
                </thead>
                <tbody>
                @php $counter = 1; @endphp
                @foreach($composits as $k => $composit)
                    @if($composit->compositGroup_id == 2)
                        <tr>
                            <th scope="row">{{$counter++}}</th>
                            <td>{{$composit->name}}</td>
                            <td>{{$composit->completed}}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-4">
            <div class="h2 text-center">ИИ</div>
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Название раздела</th>
                    <th scope="col">Статус</th>
                </tr>
                </thead>
                <tbody>
                @php $counter = 1; @endphp
                @foreach($composits as $k => $composit)
                    @if($composit->compositGroup_id == 3)
                        <tr>
                            <th scope="row">{{$counter++}}</th>
                            <td>{{$composit->name}}</td>
                            <td>
                                <a href="{{route('composit.updateStatus', [$composit->id])}}">
                                    {{$composit->completed}}
                                </a>
                            </td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
