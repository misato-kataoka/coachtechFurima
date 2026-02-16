<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'message',
        'image_path',
    ];

    //このチャットを投稿したユーザーを取得
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //このチャットが属する商品を取得
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
