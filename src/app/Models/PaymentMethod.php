<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $table = 'payment_methods';

    protected $fillable = [
        'name',
    ];

     //リレーション: この支払い方法を利用した注文
    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_method_id');
    }
}