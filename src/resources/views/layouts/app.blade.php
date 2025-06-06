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
                        <form action="{{ route('item.search') }}" method="GET">
                            <input type="text" name="query" placeholder="なにをお探しですか？" value="{{ request('query') }}">
                            <button type="submit">検索</button>
                        </form>
                    </div>
                    <li>
                        <form class="form" action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="header-nav__link" style="background: none; border: none; padding: 0;">ログアウト</button>
                        </form>
                    </li>
                    <li>
                        <a class="header-nav__link" href="/mypage">マイページ</a>
                    </li>
                    <li>
                        <a class="header-nav__button" href="{{ route('items.create') }}">出品</a>
                    </li>
                    @endif
                </ul>
                </nav>
            </div>
        </div>
    </header>

    <main>
    @yield('content')
    </main>

    @yield('javascript')
</body>

</html>