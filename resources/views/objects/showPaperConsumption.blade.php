@extends('layout', ['title'=>$object->name])

@section('content')
    <div class="row">
        <div class="col-12 my-auto mx-auto text-center">
            <h1 class="h1">{{$object->name}}</h1>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <td>Формат</td>
                    <td>Цветных</td>
                    <td>Черно-белых</td>
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
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
