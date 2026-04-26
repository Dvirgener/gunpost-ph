<?php

namespace App\Models;

use App\Models\user\User;
use App\Models\posts\Post;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
   // UUID settings
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
    protected $fillable = ['id', 'type', 'post_id', 'initiator_id'];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function mainComponentMessages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'desc');
    }

    public function initiator()
    {
        return $this->hasOne(User::class, 'id', 'initiator_id');
    }


    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user', 'conversation_id', relatedPivotKey: 'user_id');
    }

    /** Is this user part of the conversation? */
    public function isParticipant(User $user): bool
    {
        $sellerId = $this->post->user_id;
        return $user->id === $sellerId || $user->id === $this->non_owner_id;
    }

    public function unreadCountFor($userId)
    {
        return $this->messages()
            ->whereJsonDoesntContain('read_by', $userId)
            ->count();
    }

    public function scopeBetweenUsers($query, $type, $userA, $userB)
    {
        return $query->where('type', $type)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userA))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userB));
    }
}
