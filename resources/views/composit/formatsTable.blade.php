<table class="table formatsTable table-hover">
    <thead>
    <tr>
        <td>Формат</td>
        <td>Цветной</td>
        <td>Черно-белый</td>
    </tr>
    </thead>
    <tbody>
            @if(!is_null($composit->formats))
                @foreach($composit->formats->formats as $k=>$format)
                <tr>
                    <td>{{$k}}</td>
                    <td>
                        @if(isset($format->Colored))
                            {{$format->Colored}}
                        @endif
                    </td>
                    <td>
                        @if(isset($format->BW))
                            {{$format->BW}}
                        @endif
                    </td>
                </tr>
                @endforeach
            @endif
    </tbody>
</table>
