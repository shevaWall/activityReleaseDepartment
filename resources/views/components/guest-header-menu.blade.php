<nav class="navbar sticky-top navbar-expand-lg">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">PDF Page Counter</span>
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavAltMarkup">
            <div class="navbar-nav">
                <a class="nav-link my-auto" href="#">Тарифы</a>
                <a class="nav-link my-auto" href="#">Возможности</a>
                @if (Route::has('login'))
                    @auth
                        <a class="nav-link my-auto" href="{{ url('/dashboard') }}">Dashboard</a>
                    @else
                        <a class="nav-link my-auto" href="{{ route('login') }}">Войти</a>
                    @endauth
                @endif
                @if (Route::has('register'))
                    @auth
                    @else
                        <a class="nav-link my-auto blueWrapper" href="{{ route('register') }}">Регистрация</a>
                    @endauth
                @endif
            </div>
        </div>
    </div>
</nav>

{{--todo: дизайн отсюда https://tilda.cc/tpls/page/?q=basicserviceru--}}
