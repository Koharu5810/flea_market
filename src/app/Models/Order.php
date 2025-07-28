<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'item_id',
        'address_id',
        'payment_method',
        'purchased_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);  // 購入者
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function address()
    {
        return $this->belongsTo(UserAddress::class);
    }
    public function chatRoom()
    {
        return $this->hasOne(ChatRoom::class);
    }

        protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->uuid)) {
                $order->uuid = (string) Str::uuid();
            }
        });
    }
}
