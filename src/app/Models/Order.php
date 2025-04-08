<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'buyer_id',
        'seller_id',
        'item_id',
        'payment_method_id',
        'status',
    ];

    //リレーション: 購入者 (多対1)
    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    //リレーション: 販売者 (多対1)
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    //リレーション: 注文されたアイテム (多対1)
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    //リレーション: 支払い方法 (多対1)
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}