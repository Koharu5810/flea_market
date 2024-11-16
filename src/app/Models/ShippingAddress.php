<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'postal_code',
        'address',
        'building',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
