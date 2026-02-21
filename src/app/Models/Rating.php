<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'evaluator_id',
        'evaluated_id',
        'rating',
        'comment',
    ];

    // 評価者が誰か (Userモデルとのリレーション)
    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    // 被評価者が誰か (Userモデルとのリレーション)
    public function evaluated()
    {
        return $this->belongsTo(User::class, 'evaluated_id');
    }

    // どの商品についての評価か (Itemモデルとのリレーション)
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
