<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{route('index')}}">НаСуете</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('index') ? 'active' : ''}}" aria-current="page" href="{{route('index')}}">Главная</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('objects.index') ? 'active' : ''}}" aria-current="page" href="{{route('objects.index')}}">Объекты</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('settings.index') ? 'active' : ''}}" aria-current="page" href="{{route('settings.index')}}">Настройки</a>
                </li>
            </ul>
            <form class="d-flex">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
        </div>
    </div>
</nav>
