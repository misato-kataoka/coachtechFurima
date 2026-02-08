<?php

namespace App\Notifications;

//use Illuminate\Bus\Queueable;
//use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Lang;

class VerifyEmailWithUser extends VerifyEmail
{
    //use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    //public function __construct()
    //{
        //
    //}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {

        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject(Lang::get('メールアドレスの認証'))

            // テンプレートで {{ $user->name }} と書く代わりに、
            // ここで挨拶文を生成し、テンプレート内の `$greeting` 変数に渡します。
            ->greeting(Lang::get('こんにちは、:username さん！', ['username' => $notifiable->username])) // `->name` はモデルのカラム名に合わせてください

            ->line(Lang::get('あなたのメールアドレスを認証するために、以下のボタンをクリックしてください。'))
            ->action(Lang::get('メールアドレスを認証する'), $verificationUrl)
            ->line(Lang::get('このメールに心当たりがない場合は、お手数ですがこのメールを破棄してください。'));
    }

    

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
