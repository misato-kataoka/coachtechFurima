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