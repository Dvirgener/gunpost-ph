<?php

namespace App\Models\tickets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
       protected $table = 'ticket_attachments';
    protected static function booted()
    {
        static::deleting(function ($attachment) {
            Storage::disk('public')->delete($attachment->file_path);
        });
    }
    protected $fillable = [
        'ticket_id',
        'file_path',
        'file_name',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
