@extends('layout', ['title'=>"$object->name"])

@section('content')
    <div class="row my-5">
        <h1 class="text-center">Настройки объекта</h1>
        <form action="{{route('objects.submit_form')}}" method="post" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$object->id}}">
            <div class="row mb-3">
                <label for="inputObjectName" class="col-sm-2 col-form-label">Название</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputObjectName" name="name" value="{{$object->name}}">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCipher" class="col-sm-2 col-form-label">Шифр объекта</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCipher" name="cipher" value="{{$object->cipher}}">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputFile" class="col-sm-2 col-form-label">Скан заявки</label>
                <div class="col-sm-10">
                    <input class="form-control" type="file" id="inputFile" name="scan_img" value="{{$object->scan_img}}">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputObjectOwner" class="col-sm-2 col-form-label">Чей объект</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputObjectOwner" name="object_owner" value="{{$object->object_owner}}">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCountPD" class="col-sm-2 col-form-label">Кол-во экз ПД</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCountPD" name="count_pd" value="{{$object->count_pd}}">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCountRD" class="col-sm-2 col-form-label">Кол-во экз РД</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCountRD" name="count_rd" value="{{$object->count_rd}}">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCountII" class="col-sm-2 col-form-label">Кол-во экз ИИ</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCountII" name="count_ii" value="{{$object->count_ii}}">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
{{--                        todo: придумать как ставить чекбокс--}}
                        <input class="form-check-input" type="checkbox" id="inputOriginalDoc" name="original_documents" {{($object->original_documents == 1) ? 'checked=checked' : ''}}">
                        <label class="form-check-label" for="inputOriginalDoc">
                            Наличие оригиналов документов
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputDeadline" class="col-sm-2 col-form-label">Срок сдачи</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="inputDeadline" name="deadline" value="{{$object->deadline}}">
                </div>
            </div>


            <button type="submit" class="btn btn-primary">Сохранить</button>

            <a href="{{route('objects.deleteObject', [$object->id])}}">Удалить</a>
        </form>
    </div>
@endsection
