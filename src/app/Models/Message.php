<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    public function chatRoom()
    {
        return $this->belongsTo(ChatRoom::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function readers()
    {
        return $this->belongsToMany(User::class, 'message_reads')
                    ->withTimestamps();  // 既読時間の記録
    }
}
