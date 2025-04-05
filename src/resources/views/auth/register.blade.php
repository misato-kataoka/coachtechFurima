<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
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
            <h1>会員登録</h1>
            <form action="/register" method="GET">
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username">
                </div>
                <div class="form__error">
                    @error('username')
                    {{ $message }}
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email">
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password">
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">確認用パスワード</label>
                    <input type="password" id="password_confirmation" name="password_confirmation">
                </div>
                <button type="submit" class="submit-btn">登録する</button>
            </form>
            <p class="login-link">
                <a href="/login">ログインはこちら</a>
            </p>
        </div>
    </main>
</body>
</html>