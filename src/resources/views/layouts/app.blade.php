<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <div class="header-utilities">
                <a class="header__logo" href="/">
                    <img src="{{ asset('image/logo.png') }}" alt="COACHTECH Logo" />
                </a>
                <nav>
                <ul class="header-nav">
                    @if (Auth::check())
                    <div class="search">
                        <input type="text" placeholder="なにをお探しですか？">
                    </div>
                    <nav>
                        <form class="form" action="/logout" method="post">
                            @csrf
                            <button class="header-nav__button">ログアウト</button>
                        </form>
                        <a class="header-nav__link" href="/mypage">マイページ</a>
                        <a href="#">出品</a>
                    </nav>
                    @endif
                </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
    @yield('content')
    </main>
</body>

</html>