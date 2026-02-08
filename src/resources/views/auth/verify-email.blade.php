<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>メールアドレスの確認</title>
    <style>
        body { font-family: sans-serif; text-align: center; padding-top: 50px; }
        .card { max-width: 500px; margin: auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .message { margin-bottom: 20px; }
        .button { padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; border: none; cursor: pointer; }
        .session-message { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>ご登録ありがとうございます！</h2>
        <p class="message">
            ご登録いただいたメールアドレスに、確認用のリンクを送信しました。<br>
            メールボックスをご確認の上、リンクをクリックして認証を完了してください。
        </p>

        @if (session('message'))
            <div class="session-message">
                {{ session('message') }}
            </div>
        @endif

        <p>メールが届かない場合は、以下のボタンから再送信できます。</p>
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="button">認証メールを再送信する</button>
        </form>
    </div>
</body>
</html>