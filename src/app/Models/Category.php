<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'category_name',
    ];

    //リレーション: カテゴリは複数のアイテムとの関連を持つ (多対多)
    // item_category_conditionテーブルを経由
    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            'item_category_condition',
            'category_id',
            'item_id'
        );
    }
}