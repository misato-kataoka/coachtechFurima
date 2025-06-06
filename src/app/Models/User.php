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
        'profile_pic'
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

    public function items()
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes', 'user_id', 'item_id')->withPivot('id')->withTimestamps();
    }

    public function purchasedItems()
    {
        return $this->hasMany(Item::class, 'buyer_id');
    }

    public function getProfilePicAttribute($value)
    {
        if ($value) {
            return asset('storage/' . $value);
    }

        return null;
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

}
