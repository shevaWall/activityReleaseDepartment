@extends('layout', ['title'=>'Добавить объект'])

@section('content')
    <div class="row my-5">
        <h1 class="text-center">Добавление нового объекта</h1>
        <form action="{{route('objects.submit_form')}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
                <label for="inputObjectName" class="col-sm-2 col-form-label">Название</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputObjectName" name="name">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCipher" class="col-sm-2 col-form-label">Шифр объекта</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCipher" name="cipher">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputFile" class="col-sm-2 col-form-label">Скан заявки</label>
                <div class="col-sm-10">
                    <input class="form-control" type="file" id="inputFile" name="scan_img">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputObjectOwner" class="col-sm-2 col-form-label">Чей объект</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputObjectOwner" name="object_owner">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCountPD" class="col-sm-2 col-form-label">Кол-во экз ПД</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCountPD" name="count_pd">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCountRD" class="col-sm-2 col-form-label">Кол-во экз РД</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCountRD" name="count_rd">
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputCountII" class="col-sm-2 col-form-label">Кол-во экз ИИ</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" id="inputCountII" name="count_ii">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-sm-10 offset-sm-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="inputOriginalDoc" name="original_documents">
                        <label class="form-check-label" for="inputOriginalDoc">
                            Наличие оригиналов документов
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <label for="inputDeadline" class="col-sm-2 col-form-label">Срок сдачи</label>
                <div class="col-sm-10">
                    <input type="date" class="form-control" id="inputDeadline" name="deadline">
                </div>
            </div>


            <button type="submit" class="btn btn-primary">Добавить</button>
        </form>
    </div>
@endsection
