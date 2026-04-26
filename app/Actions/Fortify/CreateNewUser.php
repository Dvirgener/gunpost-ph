<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\user\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */

    public function create(array $input): User
    {

        $input['terms_accepted'] = isset($input['terms_accepted']) && $input['terms_accepted'] ? true : false;
        $validated = Validator::make($input, [
            // account type
            'account_type' => ['required', 'in:personal,corporate'],

            // personal
            'first_name' => ['string', 'max:80', ],
            'last_name'  => ['string', 'max:80', ],

            // corporate
            'company_name' => ['nullable', 'string', 'max:150', 'required_if:account_type,corporate'],

            // common
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],

            // password
            'password' => ['required', 'string', 'confirmed', $this->passwordRules()],

            // Terms
            'terms_accepted' => ['boolean'],
        ])->validate();

        $user = User::create([
            'account_type'  => $validated['account_type'],
            'first_name'    => $validated['first_name'] ?? null,
            'last_name'     => $validated['last_name'] ?? null,
            'company_name'  => $validated['company_name'] ?? null,
            'phone'         => $validated['phone'] ?? null,
            'email'         => $validated['email'],
            'terms_accepted'=> $validated['terms_accepted'],
            'password'     => Hash::make($validated['password']), // IMPORTANT
        ]);

        if($user->account_type === 'personal') {
            $user->personalProfile()->create([]);
        } else {
            $user->corporateProfile()->create([]);
        }

        $user->verification()->create([
            'kyc_status' => 'pending',
        ]);

        return $user;
    }
}
