<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $table = 'likes';

    protected $fillable = [
        'user_id',
        'item_id',
    ];

    //リレーション: いいねは1人のユーザーに属する (多対1)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //リレーション: いいねは1つのアイテムに属する (多対1)
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}