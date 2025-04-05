<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
    <title>会員登録</title>
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <a class="header__logo" href="/">
            COACHTECH
            </a>
        </div>
    </header>

    <main>
        <div class="container">
            <h1>ログイン</h1>
            <form action="/login" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="text" id="email" name="email">
                </div>
                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password">
                </div>
                <button type="submit" class="submit-btn">ログインする</button>
            </form>
            <p class="register-link">
                <a href="/register">会員登録はこちら</a>
            </p>
        </div>
    </main>
</body>
</html>