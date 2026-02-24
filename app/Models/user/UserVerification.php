<?php

namespace App\Models\user;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reviewed_by',
        'kyc_status',
        'submitted_at',
        'reviewed_at',
        'kyc_notes',
        'government_id_type',
        'government_id_number',
        'government_id_front_path',
        'government_id_back_path',
        'selfie_with_id_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
