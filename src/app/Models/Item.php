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
        'buyer_id',
        'image',
        'item_name',
        'brand',
        'price',
        'description',
        'is_sold',
        'payment_method_id',
    ];

    public function getImageAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
        }
        return null;
    }

    public function getItemNameAttribute($value)
    {
        return $value ?? '未定義';
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }

    public function isOrdered(): bool
    {
        return $this->is_sold && $this->buyer_id !== null;
    }

    public function categoryConditions()
    {
        return $this->hasMany(ItemCategoryCondition::class, 'item_id');
    }

    public function itemCategoryConditions()
    {
        return $this->hasMany(ItemCategoryCondition::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category_condition','item_id', 'category_id');
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

    public function likes()
    {
        return $this->hasMany(Like::class, 'item_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getIsSoldAttribute($value)
    {
        return (bool) $value;
    }
}