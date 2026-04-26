<?php

namespace App\Models\tickets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\user\User;

class Ticket extends Model
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

    protected $fillable = [
        'user_id',
        'category',
        'subject',
        'message',
        'status',
        'priority',
        'resolved_at',
    ];

    public function ticketAttachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function ticketMessages()
    {
        return $this->hasMany(TicketMessage::class)->orderBy('created_at', 'desc');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
