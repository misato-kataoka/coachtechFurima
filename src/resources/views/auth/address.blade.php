@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')

<div class="profile-form-container">
    <h2>{{ isset($user) ? 'プロフィール設定' : 'プロフィール登録' }}</h2>
    <form action="{{ isset($user) ? route('address.update') : route('address.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="profile-pic">
            <div class="image-placeholder">
                <img id="imagePreview"
                     src="{{ isset($user) && $user->profile_pic ? asset('storage/' . $user->profile_pic) : '' }}"
                     alt="プロフィール画像プレビュー" />
            </div>
            <input type="file" id="imageUpload" name="image" accept="image/*" />
            <button type="button" class="select-image-button" onclick="document.getElementById('imageUpload').click();">画像を選択する</button>
        </div>

        <label for="username">ユーザー名</label>
            <input type="text" id="username" name="username" value="{{ old('username', $user->username ?? $username_from_session ??'') }}" />
                <div class="form__error">
                    @error('username')
                    {{ $message }}
                    @enderror
                </div>

        <label for="post_code">郵便番号</label>
            <input type="text" id="post_code" name="post_code" value="{{ old('post_code', $user->post_code ?? '') }}" />
                <div class="form__error">
                    @error('post_code')
                    {{ $message }}
                    @enderror
                </div>

        <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address',$user->address ?? '') }}" />
                <div class="form__error">
                    @error('address')
                    {{ $message }}
                    @enderror
                </div>

        <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building',$user->building ?? '') }}" />
                <div class="form__error">
                    @error('building')
                    {{ $message }}
                    @enderror
                </div>

        <button type="submit">{{ isset($user) ? '更新する' : '登録する' }}</button>
    </form>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- プレビュー処理の定義 ---
        function previewImage(event) {
            const input = event.target;
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imagePreview = document.getElementById('imagePreview');
                    imagePreview.src = e.target.result;
                    // 画像がない場合に備えて、表示をblockにする
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // --- イベントリスナーの登録 ---
        const imageUpload = document.getElementById('imageUpload');
        imageUpload.addEventListener('change', previewImage);

        // --- ページ読み込み時の画像表示制御 ---
        const imagePreview = document.getElementById('imagePreview');
        if (!imagePreview.getAttribute('src')) {
            imagePreview.style.display = 'none';
        }
    });
</script>
@endsection