<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\CodeCleaner\ReturnTypePass;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'condition_id', 'description', 'price',
    ];

    public function order()
    {
        return $this->hasOne(Order::class);
    }
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
    public function condition()
    {
        return $this->belongsTo(Condition::class);
    }
    // 出品アイテムが複数のユーザのお気に入りになる場合のリレーション
    public function favoriteBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
    // 出品アイテムが複数のユーザがコメントを投稿する場合のリレーション
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
