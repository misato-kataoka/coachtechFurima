@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<h2>{{ isset($user) ? 'プロフィール修正' : 'プロフィール登録' }}</h2>
<form action="{{ isset($user) ? route('address.update', ['item_id' => $item_id]) : route('address.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
<div class="profile-pic">
    <label for="imageUpload" class="image-placeholder">
        <img id="imagePreview" src="" alt="選択した画像のプレビュー" style="display: none;" />
        
    </label>
    <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="previewImage(event)" />
    <span class="image-label" onclick="document.getElementById('imageUpload').click();">画像を選択する</span>
</div>

    <label for="username">ユーザー名</label>
        <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" />
            <div class="form__error">
                @error('username')
                {{ $message }}
                @enderror
            </div>

    <label for="post_code">郵便番号</label>
        <input type="text" id="post_code" name="post_code" value="{{ old('post_code', $user->post_code) }}" />
            <div class="form__error">
                @error('post_code')
                {{ $message }}
                @enderror
            </div>

    <label for="address">住所</label>
        <input type="text" id="address" name="address" value="{{ old('address',$user->address) }}" />
            <div class="form__error">
                @error('address')
                {{ $message }}
                @enderror
            </div>

    <label for="building_name">建物名</label>
        <input type="text" id="building_name" name="building_name" value="{{ old('building',$user->building) }}" />
            <div class="form__error">
                @error('building')
                {{ $message }}
                @enderror
            </div>

    <button type="submit">{{ isset($user) ? '更新する' : '登録する' }}</button>
</form>
@endsection

@section('javascript')
<script>
    function previewImage(event) {
        // 画像ファイルが選択された場合
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const imagePreview = document.getElementById('imagePreview');
                imagePreview.src = e.target.result; // 画像のデータURLを設定
                imagePreview.style.display = 'block'; // 画像を表示
            }
            reader.readAsDataURL(input.files[0]); // 画像ファイルを読み込む
        }
    }
</script>
@endsection