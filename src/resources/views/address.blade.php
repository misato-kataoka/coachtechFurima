<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>プロフィール設定</title>
    <link rel="stylesheet" href="mypage.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>COACHTECH</h1>
        </div>
        <div class="search">
            <input type="text" placeholder="なにをお探しですか？">
        </div>
        <nav>
            <a href="#">ログアウト</a>
            <a href="#">マイページ</a>
            <a href="#">出品</a>
        </nav>
    </header>
    <main>
        <h2>プロフィール設定</h2>
        <div class="profile-pic">
            <div class="image-placeholder">画像を選択する</div>
        </div>
        <form>
            <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" value="{{ old('username') }}" />
                <div class="form__error">
                    @error('username')
                    {{ $message }}
                    @enderror
                </div>

            <label for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" />
                <div class="form__error">
                    @error('postal_code')
                    {{ $message }}
                    @enderror
                </div>

            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address') }}" />
                <div class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </div>

            <label for="building_name">建物名</label>
            <input type="text" id="building_name" name="building_name" value="{{ old('building_name') }}" />
                <div class="form__error">
                    @error('building_name')
                    {{ $message }}
                    @enderror
                </div>

            <button type="submit">更新する</button>
        </form>
    </main>
</body>
</html>