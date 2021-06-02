@extends('layout', ['title'=>'Объекты'])

@section('content')

    <div class="col-12 text-center mt-5">
        <h1 class="h1">Поиск</h1>
    </div>

    @if(isset($objs))
        <ol>
            @foreach($objs as $obj)
                <li><a href="{{route('objects.composit', $obj->id)}}">{{$obj->name}} (№{{$obj->nomerZayavki}})</a> </li>
            @endforeach
        </ol>
    @endif

    @if(isset($empty))
        {{$empty}}
    @endif

    @if(isset($noFound))
        {{$noFound}}
    @endif

@endsection
