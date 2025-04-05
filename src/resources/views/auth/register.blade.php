@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection

@section('content')
    <div class="container">
        <h1>会員登録</h1>
            <form class="form" action="/register" method="post">
            @csrf
                <div class="form-group">
                    <label for="username">ユーザー名</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" />
                </div>
                <div class="form__error">
                    @error('username')
                    {{ $message }}
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" />
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" />
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">確認用パスワード</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" />
                </div>
                <div class="form__error">
                    @error('password_confirmation')
                    {{ $message }}
                    @enderror
                </div>

                <button type="submit" class="submit-btn">登録する</button>
            </form>

            <p class="login-link">
                <a href="/login">ログインはこちら</a>
            </p>
        </div>
@endsection