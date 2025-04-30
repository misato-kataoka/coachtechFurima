<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    protected $table = 'conditions';

    protected $fillable = [
        'condition',
    ];

    public function itemCategoryConditions()  
    {  
        return $this->hasMany(ItemCategoryCondition::class);  
    }

    //リレーション: コンディションは複数のアイテムとの関連を持つ (多対多)
    //item_category_conditionテーブルを経由
    public function items()
    {
        return $this->belongsToMany(
            Item::class,
            'item_category_condition',
            'condition_id',
            'item_id'
        );
    }
}