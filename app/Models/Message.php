<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\user\User;
use App\Models\posts\Post;
use Illuminate\Support\Str;

class Message extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    protected $fillable = ['id', 'conversation_id', 'sender_id', 'receiver_id', 'body', 'read_by', 'is_reply', 'message_id'];

    protected $casts = [
        'read_by' => 'array',   // or 'json'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
    public function sender()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead($id)
    {
        $this->read_by = [...$this->read_by, $id];
        $this->save();
    }

    public function scopeUnreadMessage($id)
    {
        return $this->whereJsonDoesntContain('read_by', $id);
    }

    public function replyTo()
    {
        return $this->belongsTo(Message::class, 'message_id');
    }
}
