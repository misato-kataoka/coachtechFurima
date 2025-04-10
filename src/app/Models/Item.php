<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';

    protected $fillable = [
        'user_id',
        'image',
        'item_name',
        'brand',
        'price',
        'description',
    ];

    public function getImageAttribute($value)
    {
        return asset('storage/images/' . $value);
    }

    //リレーション: アイテムは1人のユーザーに属する (多対1)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //リレーション: 多対多（中間テーブルで複数のカテゴリ・コンディションと関連づけ）
    // item_category_conditionテーブルを経由
    public function categories()
    {
        return $this->belongsToMany(
            Category::class,
            'item_category_condition',
            'item_id',
            'category_id'
        );
    }

    public function conditions()
    {
        return $this->belongsToMany(
            Condition::class,
            'item_category_condition',
            'item_id',
            'condition_id'
        );
    }

    //リレーション: アイテムは複数のいいねを持つ (1対多)
    public function likes()
    {
        return $this->hasMany(Like::class, 'item_id');
    }

    //リレーション: アイテムはいくつかの注文を持つ (1対多)
    public function orders()
    {
        return $this->hasMany(Order::class, 'item_id');
    }
}