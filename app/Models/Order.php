<?php

namespace App\Models;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'package',
        'quantity',
        'credits',
        'status',
        'ticket_id',
        'payment_method',
        'user_id',
        'amount',
        'confirmed_by_id',
        'confirmed_at',
        'image_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmer()
    {
        return $this->hasOne(User::class, 'id', 'confirmed_by_id');
    }
}
