@component('mail::message')
# こんにちは {{ $user->name }} さん！

あなたのメールアドレスを確認するために、以下のボタンをクリックしてください。

@component('mail::button', ['url' => $url])
メールアドレスを確認
@endcomponent

ありがとうございます。<br>
{{ config('app.name') }}
@endcomponent