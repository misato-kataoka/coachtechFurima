<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemCategoryCondition extends Model
{
    use HasFactory;

    protected $table = 'item_category_condition';

    protected $fillable = [
        'item_id',
        'category_id',
        'condition_id',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function condition()
    {
        return $this->belongsTo(Condition::class, 'condition_id');
    }
}