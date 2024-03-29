<nav class="navbar navbar-expand-lg navbar-light bg-light mainNavBar">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{route('index')}}">PDF Page Counter</a>
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{request()->routeIs('index') ? 'active' : ''}}" aria-current="page" href="{{route('index')}}">Главная</a>
                </li>
                <li class="nav-item">
                    <div class="btn-group">
                        <a class="nav-link p-0 text-white"
                           aria-current="page"
                           href="{{route('objects.withStatus', 1)}}">
                            <button type="button"
                                    class="btn btn-{{request()->routeIs('objects.*') ? 'success' : 'secondary'}} fixBorderRadius">
                                Заявки
                            </button>
                        </a>
                        <button type="button"
                                class="btn btn-{{request()->routeIs('objects.*') ? 'success' : 'secondary'}} dropdown-toggle dropdown-toggle-split"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item"
                                   href="{{route('objects.index')}}">В работе (<span id="inWorkCount">{{$cntPrntblObjsStatuses[1]}}</span>)</a></li>
                            <li><a class="dropdown-item"
                                   href="{{route('objects.withStatus', 3)}}">На паузе (<span id="inPauseCount">{{$cntPrntblObjsStatuses[3]}}</span>)</a></li>
                            <li><a class="dropdown-item"
                                   href="{{route('objects.withStatus', 2)}}">Сданы (<span id="inCompleteCount">{{$cntPrntblObjsStatuses[2]}}</span>)</a></li>
                            <li><a class="dropdown-item"
                                   href="{{route('objects.withStatus', 4)}}">Удалённые (<span id="inDeleteCount">{{$cntPrntblObjsStatuses[4]}}</span>)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item"
                                   href="{{route('objects.withStatus', 5)}}">Все (<span id="inAllCount">{{$cntPrntblObjsStatuses[5]}}</span>)</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a href="{{route('objects.show_form')}}" class="dropdown-item">Добавить заявку</a>
                            </li>
                        </ul>
                    </div>
                </li>
                {{--<li class="nav-item">
                    <a class="nav-link {{request()->routeIs('settings.index') ? 'active' : ''}}" aria-current="page" href="{{route('settings.index')}}">Настройки</a>
                </li>--}}
            </ul>
            <div class="ui-widget">
                <form action="{{route('search.index')}}" class="d-flex">
                    <input class="form-control me-2"
                           id="searchObject"
                           name="term"
                           type="text"
                           placeholder="Название или № заявки"
                           aria-label="Search">
                </form>
            </div>
        </div>
    </div>
</nav>
