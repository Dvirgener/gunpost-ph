<?php

namespace App\Models\user;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'account_type',
        'username',
        'first_name',
        'last_name',
        'company_name',
        'email',
        'phone',
        'password',
        'post_credits',
        'avatar_path',
        'status',
    ];

        /**
     * Create UUID when creating a new User
     *
     *
     */

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = Str::uuid();
            }
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->first_name . ' ' . $this->last_name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }



    /*
    |-------------------------------------------------------------------------------=-------------------
    | Relationships
    |---------------------------------------------------------------------------------------------------
    */

    public function personalProfile()
    {
        return $this->hasOne(PersonalProfile::class);
    }

    public function corporateProfile()
    {
        return $this->hasOne(CorporateProfile::class);
    }

    public function verification()
    {
        return $this->hasOne(Verification::class);
    }

}
