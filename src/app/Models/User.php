<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Cashier\Billable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Billable;  // BillableはLaravelCashierに関するトレイト

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'username',
        'profile_image',
        'email',
        'password',
    ];
    protected $guarded = [];

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
    /**
     * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany favorites()
     */

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user_address() {
        return $this->hasOne(UserAddress::class, 'user_id', 'id');
    }
    public function orders() {
        return $this->hasMany(Order::class);
    }
    public function items() {
        return $this->hasMany(Item::class);
    }
    public function favorites() {
        return $this->belongsToMany(Item::class, 'favorites', 'user_id', 'item_id')
                    ->withTimestamps();
    }
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    protected static function boot() {
        parent::boot();

        // モデルが作成されるときにUUIDを生成
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    public function setPasswordAttribute($value)
    {
        if (Hash::needsRehash($value)) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }

    public function getProfileImageUrlAttribute() {
        return $this->profile_image
            ? asset('storage/' . $this->profile_image)
            : null;
    }
}
