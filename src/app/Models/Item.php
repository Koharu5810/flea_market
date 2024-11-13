<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\CodeCleaner\ReturnTypePass;

class Item extends Model
{
    use HasFactory;

    public function order()
    {
        return $this->hasOne(Order::class);
    }
    // 出品アイテムが複数のユーザのお気に入りになる場合のリレーション
    public function favoriteBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }
}
