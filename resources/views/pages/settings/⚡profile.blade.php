<?php

use App\Concerns\ProfileValidationRules;
use App\Models\user\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use Livewire\Component;

new class extends Component {
    use ProfileValidationRules, WithFileUploads;


    // users table fields (editable on this page)
    public string $account_type = 'personal';
    public string $first_name = '';
    public string $last_name = '';
    public ?string $company_name = null; // users.company_name (for corporate)
    public string $email = '';
    public ?string $phone = null;
    public ?string $avatar_path = null;
    public $picture = null; // temporary property for Livewire file upload

    // personal_profiles fields
    public array $personal = [
        'date_of_birth'   => null,
        'gender'          => null,
        'bio'             => null,
        'address_line_1'  => null,
        'address_line_2'  => null,
        'city'            => null,
        'province'        => null,
        'region'          => null,
        'country'         => 'Philippines',
    ];

    // corporate_profiles fields
    public array $corporate = [
        'company_name'         => null,
        'business_type'        => null,
        'address_line_1'       => null,
        'address_line_2'       => null,
        'city'                 => null,
        'province'             => null,
        'region'               => null,
        'country'              => 'Philippines',
        'business_email'       => null,
        'business_phone'       => null,
        'website'              => null,

        // paths are in the table, but not implemented as uploads here (yet)
        'logo_path'            => null,
        'dti_sec_reg_path'     => null,
        'business_permit_path' => null,
    ];

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->account_type = $user->account_type ?? 'personal';

        $this->first_name = (string) ($user->first_name ?? '');
        $this->last_name  = (string) ($user->last_name ?? '');
        $this->company_name = $user->company_name;

        $this->email = (string) ($user->email ?? '');
        $this->phone = $user->phone;
        $this->avatar_path = $user->avatar_path;

        if ($this->isPersonalAccount) {
            $profile = $user->personalProfile;

            if ($profile) {
                $this->personal = array_merge($this->personal, [
                    'date_of_birth'  => optional($profile->date_of_birth)->format('Y-m-d') ?? $profile->date_of_birth,
                    'gender'         => $profile->gender,
                    'bio'            => $profile->bio,
                    'address_line_1' => $profile->address_line_1,
                    'address_line_2' => $profile->address_line_2,
                    'city'           => $profile->city,
                    'province'       => $profile->province,
                    'region'         => $profile->region,
                    'country'        => $profile->country ?: 'Philippines',

                ]);
            }
        }

        if ($this->isCorporateAccount) {
            $profile = $user->corporateProfile;

            if ($profile) {
                $this->corporate = array_merge($this->corporate, [
                    'company_name'         => $profile->company_name,
                    'business_type'        => $profile->business_type,
                    'address_line_1'       => $profile->address_line_1,
                    'address_line_2'       => $profile->address_line_2,
                    'city'                 => $profile->city,
                    'province'             => $profile->province,
                    'region'               => $profile->region,
                    'country'              => $profile->country ?: 'Philippines',
                    'business_email'       => $profile->business_email,
                    'business_phone'       => $profile->business_phone,
                    'website'              => $profile->website,

                    'logo_path'            => $profile->logo_path,
                    'dti_sec_reg_path'     => $profile->dti_sec_reg_path,
                    'business_permit_path' => $profile->business_permit_path,
                ]);
            } else {
                // keep the users.company_name visible as default
                $this->corporate['company_name'] = $this->company_name;
            }
        }
    }

    public function updateProfileInformation(): void
    {
        /** @var User $user */
        $user = Auth::user();

        // Base user rules (you can also move these into ProfileValidationRules if you prefer)
        $rules = [
            'first_name' => ['required', 'string', 'max:80'],
            'last_name'  => ['required', 'string', 'max:80'],
            'email'      => [
                'required',
                'email:rfc,dns',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone'      => ['nullable', 'string', 'max:30'],
        ];

        if($this->picture){
            $rules['picture'] = ['image', 'max:2048']; // max 2MB, adjust as needed
            if($user->avatar_path){
                // if user already has a picture, we can optionally delete the old one here
                Storage::disk('public')->delete($user->avatar_path);
            }

            $user->avatar_path = $this->uploadFiles($this->picture, 'avatar');

        }

        if ($this->isCorporateAccount) {
            $rules = array_merge($rules, [
                // Corporate must have a company name (both in users + corporate_profiles)
                'company_name'               => ['required', 'string', 'max:150'],

                'corporate.company_name'     => ['required', 'string', 'max:150'],
                'corporate.business_type'    => ['nullable', 'string', 'max:80'],

                'corporate.address_line_1'   => ['nullable', 'string', 'max:255'],
                'corporate.address_line_2'   => ['nullable', 'string', 'max:255'],
                'corporate.city'             => ['nullable', 'string', 'max:255'],
                'corporate.province'         => ['nullable', 'string', 'max:255'],
                'corporate.region'           => ['nullable', 'string', 'max:255'],
                'corporate.country'          => ['nullable', 'string', 'max:80'],

                'corporate.business_email'   => ['nullable', 'email:rfc,dns', 'max:255'],
                'corporate.business_phone'   => ['nullable', 'string', 'max:30'],
                'corporate.website'          => ['nullable', 'url', 'max:255'],
            ]);
        }

        if ($this->isPersonalAccount) {
            $rules = array_merge($rules, [
                'personal.date_of_birth'    => ['nullable', 'date'],
                'personal.gender'           => ['nullable', 'string', 'max:20'],
                'personal.bio'              => ['nullable', 'string'],

                'personal.address_line_1'   => ['nullable', 'string', 'max:255'],
                'personal.address_line_2'   => ['nullable', 'string', 'max:255'],
                'personal.city'             => ['nullable', 'string', 'max:255'],
                'personal.province'         => ['nullable', 'string', 'max:255'],
                'personal.region'           => ['nullable', 'string', 'max:255'],
                'personal.country'          => ['nullable', 'string', 'max:80'],
            ]);
        }

        $validated = $this->validate($rules);

        // Update users table
        $user->first_name = $validated['first_name'];
        $user->last_name  = $validated['last_name'];
        $user->email      = $validated['email'];
        $user->phone      = $validated['phone'] ?? null;

        if ($this->isCorporateAccount) {
            $user->company_name = $validated['company_name'] ?? $this->company_name;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $this->picture = null; // reset the temporary picture property after upload
        $user->save();

        // Update the correct profile table
        if ($this->isPersonalAccount) {
            $user->personalProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth'  => $validated['personal']['date_of_birth'] ?? null,
                    'gender'         => $validated['personal']['gender'] ?? null,
                    'bio'            => $validated['personal']['bio'] ?? null,

                    'address_line_1' => $validated['personal']['address_line_1'] ?? null,
                    'address_line_2' => $validated['personal']['address_line_2'] ?? null,
                    'city'           => $validated['personal']['city'] ?? null,
                    'province'       => $validated['personal']['province'] ?? null,
                    'region'         => $validated['personal']['region'] ?? null,
                    'country'        => $validated['personal']['country'] ?? 'Philippines',
                ]
            );
        }

        if ($this->isCorporateAccount) {
            // keep users.company_name + corporate_profiles.company_name in sync
            $company = $validated['corporate']['company_name'] ?? $validated['company_name'] ?? $user->company_name;

            $user->corporateProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name'   => $company,
                    'business_type'  => $validated['corporate']['business_type'] ?? null,

                    'address_line_1' => $validated['corporate']['address_line_1'] ?? null,
                    'address_line_2' => $validated['corporate']['address_line_2'] ?? null,
                    'city'           => $validated['corporate']['city'] ?? null,
                    'province'       => $validated['corporate']['province'] ?? null,
                    'region'         => $validated['corporate']['region'] ?? null,
                    'country'        => $validated['corporate']['country'] ?? 'Philippines',

                    'business_email' => $validated['corporate']['business_email'] ?? null,
                    'business_phone' => $validated['corporate']['business_phone'] ?? null,
                    'website'        => $validated['corporate']['website'] ?? null,
                ]
            );

            // if company name changed via corporate fields, also update users.company_name
            if ($user->company_name !== $company) {
                $user->company_name = $company;
                $user->save();
            }
        }

        // Use first_name + last_name for the toast/event
        $this->dispatch('profile-updated', name: trim($user->first_name.' '.$user->last_name));
    }


    private function uploadFiles($file, $field){

        switch($field){

            case 'avatar':
                $path = $file->store('users/'.Auth::user()->id.'/avatar', 'public');
                break;

            case 'logo_path':
                $path = $file->store('users/'.Auth::user()->id.'/logo', 'public');
                break;

            case 'dti_sec_reg_path':
                $path = $file->store('users/'.Auth::user()->id.'/dti', 'public');
                break;

            case 'business_permit_path':
                $path = $file->store('users/'.Auth::user()->id.'/business_permit', 'public');
                break;

        }

        return $path;

    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();
        Session::flash('status', 'verification-link-sent');
    }

    #[Computed]
    public function hasUnverifiedEmail(): bool
    {
        return Auth::user() instanceof MustVerifyEmail && ! Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return ! Auth::user() instanceof MustVerifyEmail
            || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
    }

    #[Computed]
    public function isPersonalAccount(): bool
    {
        return ($this->account_type ?? 'personal') === 'personal';
    }

    #[Computed]
    public function isCorporateAccount(): bool
    {
        return ($this->account_type ?? 'personal') === 'corporate';
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>

    <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your details based on account type')">

        {{-- This is the form for setting upload --}}
        <form wire:submit="updateProfileInformation" enctype="multipart/form-data" class="my-6 w-full space-y-8">

            {{-- Account type (display only) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input
                    wire:model="account_type"
                    :label="__('Account Type')"
                    type="text"
                    disabled
                />
            </div>

                <div class="col-span-3 flex gap-6 pt-0 items-center">
                    <!-- Picture -->
                    <flux:input wire:model="picture" :label="__('Update Profile Picture')" type="file" autofocus
                        accept=".jpg,.jpeg,.png" class="" />

                    @if ($picture)
                    <img src="{{ $picture->temporaryUrl() }}" alt="" class="w-32 h-32 rounded-md object-cover">
                    @endif

                    {{-- Checking if user picture exist --}}
                    @if(Auth::user()->avatar_path)
                    <img src="{{ url('storage/' . Auth::user()->avatar_path)}}" alt=""
                        class="w-32 h-32 rounded-md object-cover">
                    @else
                    <img src="{{ asset('/blank_image.png') }}" alt="" class="w-32 h-32 rounded-md object-cover">
                    @endif

                </div>

            {{-- Core user details --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="first_name" :label="__('First Name')" type="text" required autocomplete="given-name" />
                <flux:input wire:model="last_name" :label="__('Last Name')" type="text" required autocomplete="family-name" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />
                <flux:input wire:model="phone" :label="__('Phone')" type="text" autocomplete="tel" />
            </div>

            {{-- Email verification --}}
            @if ($this->hasUnverifiedEmail)
                <div>
                    <flux:text class="mt-2">
                        {{ __('Your email address is unverified.') }}

                        <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                            {{ __('Click here to re-send the verification email.') }}
                        </flux:link>
                    </flux:text>

                    @if (session('status') === 'verification-link-sent')
                        <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </flux:text>
                    @endif
                </div>
            @endif

            {{-- PERSONAL PROFILE --}}
            @if ($this->isPersonalAccount)
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Personal Profile') }}</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="personal.date_of_birth" :label="__('Date of Birth')" type="date" />
                        <flux:input wire:model="personal.gender" :label="__('Gender')" type="text" placeholder="Male / Female / Prefer not to say" />
                    </div>

                    <div>
                        <flux:textarea wire:model="personal.bio" :label="__('Bio')" rows="4" />
                    </div>

                    <flux:heading size="md">{{ __('Address') }}</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="personal.address_line_1" :label="__('Address Line 1')" type="text" />
                        <flux:input wire:model="personal.address_line_2" :label="__('Address Line 2')" type="text" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="personal.city" :label="__('City')" type="text" />
                        <flux:input wire:model="personal.province" :label="__('Province')" type="text" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="personal.region" :label="__('Region')" type="text" />
                        <flux:input wire:model="personal.country" :label="__('Country')" type="text" />
                    </div>
                </div>
            @endif

            {{-- CORPORATE PROFILE --}}
            @if ($this->isCorporateAccount)
                <div class="space-y-6">
                    <flux:heading size="lg">{{ __('Corporate Profile') }}</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- users.company_name --}}
                        <flux:input wire:model="company_name" :label="__('Company Name (Account)')" type="text" required />

                        {{-- corporate_profiles.company_name --}}
                        <flux:input wire:model="corporate.company_name" :label="__('Company Name (Business Profile)')" type="text" required />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="corporate.business_type" :label="__('Business Type')" type="text" placeholder="Gun store / Dealer / Distributor / etc" />
                        <flux:input wire:model="corporate.website" :label="__('Website')" type="url" placeholder="https://..." />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="corporate.business_email" :label="__('Business Email')" type="email" />
                        <flux:input wire:model="corporate.business_phone" :label="__('Business Phone')" type="text" />
                    </div>

                    <flux:heading size="md">{{ __('Business Address') }}</flux:heading>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="corporate.address_line_1" :label="__('Address Line 1')" type="text" />
                        <flux:input wire:model="corporate.address_line_2" :label="__('Address Line 2')" type="text" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="corporate.city" :label="__('City')" type="text" />
                        <flux:input wire:model="corporate.province" :label="__('Province')" type="text" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <flux:input wire:model="corporate.region" :label="__('Region')" type="text" />
                        <flux:input wire:model="corporate.country" :label="__('Country')" type="text" />
                    </div>

                    {{-- Document paths (optional / display only for now) --}}
                    <div class="space-y-4">
                        <flux:heading size="sm">{{ __('Documents (paths)') }}</flux:heading>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <flux:input wire:model="corporate.logo_path" :label="__('Logo Path')" type="text" disabled />
                            <flux:input wire:model="corporate.dti_sec_reg_path" :label="__('DTI/SEC Reg Path')" type="text" disabled />
                            <flux:input wire:model="corporate.business_permit_path" :label="__('Business Permit Path')" type="text" disabled />
                        </div>
                        <flux:text class="text-sm opacity-80">
                            {{ __('Uploads are not implemented in this form yet. We can add Livewire file uploads next (logo, DTI/SEC, permit).') }}
                        </flux:text>
                    </div>
                </div>
            @endif

            {{-- Save --}}
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="update-profile-button">
                        {{ __('Save') }}
                    </flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
        </form>

        @if ($this->showDeleteUser)
            <livewire:pages::settings.delete-user-form />
        @endif
    </x-pages::settings.layout>
</section>
