<?php

namespace App\Models\user;

use App\Models\user\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalProfile extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date_of_birth',
        'gender',
        'bio',
        'address_line_1',
        'address_line_2',
        'city',
        'province',
        'region',
        'country',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
