@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
        <h2>プロフィール設定</h2>
        <div class="profile-pic">
            <input type="file" id="imageUpload" accept="image/*" style="display: none;" onchange="previewImage(event)" />
            <label for="imageUpload" class="image-placeholder"></label>
            <span class="image-label" onclick="document.getElementById('imageUpload').click();">画像を選択する</span>
            <div class="image-preview-container">
                <img id="imagePreview" src="" alt="選択した画像のプレビュー" style="display: none;" />
            </div>
        </div>
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf

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