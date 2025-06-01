<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'post_code',
        'address',
        'building',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //リレーション: あるユーザーはいくつものItemを出品できる (1対多)
    public function items()
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    //リレーション: あるユーザーはいくつものLike情報を持つ (1対多)
    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')->withPivot('id');
    }

    //リレーション: 購入者としての注文 (1対多)
    public function buyerOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    //リレーション: 販売者としての注文 (1対多)
    public function sellerOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }
}
