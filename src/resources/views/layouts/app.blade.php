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

{{-- app.blade.php --}}
<body>
    <header class="header">
        <div class="header__inner">

            {{--   ロゴグループ  --}}
            <div class="header__logo">
                <a href="/">
                    <img src="{{ asset('image/logo.png') }}" alt="COACHTECH Logo" />
                </a>
            </div>

            <div class="header__utility-group">
            {{--   検索グループ（ログイン時のみ表示）  --}}
                @auth
                <div class="header__search">
                    <form action="{{ route('item.search') }}" method="GET">
                        <input type="text" name="query" placeholder="なにをお探しですか？" value="{{ request('query') }}">

                        <button type="submit" class="header__search-button">検索</button>
                    </form>
                </div>
                @endauth

            {{--   ナビゲーショングループ  --}}
                <nav class="header__nav">
                    @if (Auth::check())
                        {{-- ログインしている場合のナビゲーション --}}
                        <form action="{{ route('logout') }}" method="post">
                            @csrf
                            <button type="submit" class="header__nav-item">ログアウト</button>
                        </form>
                        <a class="header__nav-item" href="/mypage">マイページ</a>
                        <a class="header__nav-item header__nav-item--button" href="{{ route('items.create') }}">出品</a>
                    @else
                        {{-- ログインしていない場合のナビゲーション --}}
                        <a class="header__nav-item" href="{{ route('login') }}">ログイン</a>
                        <a class="header__nav-item" href="{{ route('register') }}">会員登録</a>
                    @endif
                </nav>
            </div>

        </div>
    </header>

    <main>
    @yield('content')
    </main>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('js')
</body>

</html>