@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('content')
    <div class="container">
        <h1>ログイン</h1>
            <form class="form" action="{{ route('login') }}" method="post">
            @csrf
                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input type="text" id="email" name="email" value="{{ old('email') }}" />

                    <div class="form__error">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input type="password" id="password" name="password" />

                    <div class="form__error">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </div>
                </div>

                <button type="submit" class="submit-btn">ログインする</button>
            </form>
            <p class="register-link">
                <a href="{{ route('register') }}">会員登録はこちら</a>
            </p>
    </div>
@endsection