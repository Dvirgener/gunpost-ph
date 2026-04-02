<?php

namespace App\Models\tickets;

use Illuminate\Database\Eloquent\Model;
use App\Models\user\User;

class TicketMessage extends Model
{
        protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $table = 'ticket_messages';

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
