<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Item;
use App\Models\User;

class TransactionCompletedNotification extends Notification implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable;

    protected $itemId;
    protected $buyerId;

    /**
     * Create a new notification instance.
     *
     * 【変更】コンストラクタで受け取る引数の型をシンプルにする
     * @param int $itemId  購入された商品のID
     * @param int $buyerId 購入者のID
     * @return void
     */
    public function __construct(int $itemId, int $buyerId)
    {
        // 【変更】プロパティにIDをセット
        $this->itemId = $itemId;
        $this->buyerId = $buyerId;
    }

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
     * @param  mixed  $notifiable (通知の受信者 = 出品者)
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // 1. 保存しておいたIDを使って、データベースから最新のモデル情報を取得
        $item = Item::find($this->itemId);
        $buyer = User::find($this->buyerId);

        // 念のため、モデルが見つからない場合の安全対策
        $itemName = $item ? $item->item_name : '（商品情報取得エラー）';
        $buyerName = $buyer ? $buyer->username : '（購入者情報取得エラー）';

        // 2. 出品者のマイページへのURLを動的に生成
        $loginUrl = url('/login'); 

        return (new MailMessage)
                    ->subject('【取引完了】あなたが出品した商品が購入されました') // 件名を少し変更
                    ->greeting($notifiable->username . '様、取引が完了しました。') // 【変更】出品者の名前
                    ->line('あなたが出品した以下の商品について、購入者からの評価が入力され、取引が完了しました。')
                    ->line('商品名: ' . $itemName) // 【変更】再取得した情報を使う
                    ->line('購入者: ' . $buyerName . '様') // 【変更】再取得した情報を使う
                    ->action('マイページで確認する', $loginUrl) // 【変更】出品者用のURLを使う
                    ->line('ご利用いただきありがとうございました。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
