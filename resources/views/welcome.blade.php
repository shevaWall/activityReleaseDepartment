{{--
идеи для текста и функционала
https://a2is.ru/catalog/programmy-dlya-tipografii/asystem
https://a2is.ru/catalog/programmy-dlya-tipografii/ws-tipografiya--}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl"
          crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.6.3.slim.js"
            integrity="sha256-DKU1CmJ8kBuEwumaLuh9Tl/6ZB6jzGOBV/5YpNE2BWc="
            crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.6.0/dist/umd/popper.min.js"
            integrity="sha384-KsvD1yqQ1/1+IA7gi3P0tyJcT3vR+NdBTt13hSJ2lnve8agRGXTTyNaBYmCR/Nwi"
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js"
            integrity="sha384-nsg8ua9HAw1y0W1btsyWgBklPnCUAFLuTMS2G72MMONqmOymq585AcH49TLBQObG"
            crossorigin="anonymous"></script>

    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <script src="{{asset('js/scripts.js')}}"></script>
</head>
<body>
<div class="container">
    <x-guest-header-menu></x-guest-header-menu>

    <div class="row my-5">
        <div class="col-12 col-md-6 my-auto">
            <p class="h2">Увеличьте продуктивность вашей команды</p>
            <p class="text-muted">Новый инструмент по проектному менеджменту печати помогает в планировании, коммуникации и
                управлении временем</p>

            <form action="#">
                <div class="input-group mb-3">
                    <input type="text"
                           class="form-control"
                           placeholder="mail@example.com"
                           name="email">
                    <button class="btn btn-outline-dark"
                            type="button">Попробовать</button>
                </div>
            </form>
        </div>
        <div class="col-12 col-md-6">
            <img src="{{asset('images/welcomePage/planning_a_job.svg')}}" alt="">
        </div>
    </div>

    <div class="row">
        <div class="col-3"></div>
        <div class="col-3"></div>
        <div class="col-3"></div>
        <div class="col-3"></div>
    </div>

</div>
</body>
</html>
