<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Psy\CodeCleaner\ReturnTypePass;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'image', 'item_condition', 'description', 'price', 'brand', 'user_id', 'is_sold', 'address_id',
    ];

    public const CONDITIONS = [
        1 => '良好',
        2 => '目立った傷や汚れなし',
        3 => 'やや傷や汚れあり',
        4 => '状態が悪い',
    ];
    public function getItemConditionTextAttribute() {
        return self::CONDITIONS[$this->item_condition] ?? '';
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
    public function order() {
        return $this->hasOne(Order::class);
    }
    public function categories() {
        return $this->belongsToMany(Category::class, 'item_category');
    }

    // 出品アイテムが複数のユーザのお気に入りになる場合のリレーション
    public function favoriteBy() {
        return $this->belongsToMany(User::class, 'favorites', 'item_id', 'user_id');
    }
    // 商品お気に入り登録
    public function isFavoriteBy(?User $user): bool {
        if (!$user) {
            return false;  // 未認証ユーザにはfalseを返す
        }

        return $this->favoriteBy()->where('user_id', $user->id)->exists();
    }
    // 出品アイテムが複数のユーザがコメントを投稿する場合のリレーション
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    // uuidの生成
    protected static function boot() {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->uuid)) {
                $item->uuid = (string) Str::uuid();
            }
        });
    }
}
