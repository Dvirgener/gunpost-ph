<?php

namespace App\Models\user;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CorporateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_line_1',
        'address_line_2',
        'city',
        'province',
        'region',
        'country',
        'company_name',
        'business_type',
        'business_email',
        'business_phone',
        'website',
        'logo_path',
        'dti_sec_reg_path',
        'business_permit_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
