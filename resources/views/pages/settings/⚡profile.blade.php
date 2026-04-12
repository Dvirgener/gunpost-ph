<?php

use App\Concerns\ProfileValidationRules;
use App\Models\user\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Component;
use Flux\Flux;

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

    // File Variables
    public $picture = null; // temporary property for Livewire file upload
    public $logo = null;
    public $dti_sec_reg = null;
    public $business_permit = null;

    // Address Fields
    public $region = '';
    public $province = '';
    public $city = '';

    public $filteredProvinces = [];
    public $filteredCities = [];

    public $regions = [];
    public $provinces = [];
    public $cities = [];

    public function updatedRegion($value)
    {
        $this->filteredProvinces = collect($this->provinces)->where('region', $value)->values()->all();
        $this->province = ''; // Reset selected province
        $this->filteredCities = []; // Reset cities when region changes
    }

    public function updatedProvince($value)
    {
        $this->filteredCities = collect($this->cities)->where('province', $value)->values()->all();
        $this->city = ''; // Reset selected city
    }

    // personal_profiles fields
    public array $personal = [
        'date_of_birth' => null,
        'gender' => null,
        'bio' => null,
        'address_line_1' => null,
        'address_line_2' => null,
        'city' => null,
        'province' => null,
        'region' => null,
        'country' => 'Philippines',
    ];

    // corporate_profiles fields
    public array $corporate = [
        'company_name' => null,
        'business_type' => null,
        'address_line_1' => null,
        'address_line_2' => null,
        'city' => null,
        'province' => null,
        'region' => null,
        'country' => 'Philippines',
        'business_email' => null,
        'business_phone' => null,
        'website' => null,

        // paths are in the table, but not implemented as uploads here (yet)
        'logo_path' => null,
        'dti_sec_reg_path' => null,
        'business_permit_path' => null,
    ];

    #[On('profile-updated')]
    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->account_type = $user->account_type ?? 'personal';

        $this->first_name = (string) ($user->first_name ?? '');
        $this->last_name = (string) ($user->last_name ?? '');
        $this->company_name = $user->company_name;

        $this->email = (string) ($user->email ?? '');
        $this->phone = $user->phone;
        $this->avatar_path = $user->avatar_path;

        $this->regions = json_decode(file_get_contents(base_path('data/regions.json')), true);
        $this->provinces = json_decode(file_get_contents(base_path('data/provinces.json')), true);
        $this->cities = json_decode(file_get_contents(base_path('data/cities.json')), true);

        if ($this->isPersonalAccount) {
            $profile = $user->personalProfile;

            if ($profile) {
                $this->personal = array_merge($this->personal, [
                    'date_of_birth' => optional($profile->date_of_birth)->format('Y-m-d') ?? $profile->date_of_birth,
                    'gender' => $profile->gender,
                    'bio' => $profile->bio,
                    'address_line_1' => $profile->address_line_1,
                    'address_line_2' => $profile->address_line_2,
                    'city' => $profile->city,
                    'province' => $profile->province,
                    'region' => $profile->region,
                    'country' => $profile->country ?: 'Philippines',
                ]);
            }

            if ($this->personal['region']) {
                $this->region = $this->personal['region'];
                $this->province = $this->personal['province'];
                $this->city = $this->personal['city'];

                $this->filteredProvinces = collect($this->provinces)->where('region', $profile->region)->values()->all();
                $this->filteredCities = collect($this->cities)->where('province', $profile->province)->values()->all();
            }
        }

        if ($this->isCorporateAccount) {
            $profile = $user->corporateProfile;

            if ($profile) {
                $this->corporate = array_merge($this->corporate, [
                    'company_name' => $profile->company_name,
                    'business_type' => $profile->business_type,
                    'address_line_1' => $profile->address_line_1,
                    'address_line_2' => $profile->address_line_2,
                    'city' => $profile->city,
                    'province' => $profile->province,
                    'region' => $profile->region,
                    'country' => $profile->country ?: 'Philippines',
                    'business_email' => $profile->business_email,
                    'business_phone' => $profile->business_phone,
                    'website' => $profile->website,

                    'logo_path' => $profile->logo_path,
                    'dti_sec_reg_path' => $profile->dti_sec_reg_path,
                    'business_permit_path' => $profile->business_permit_path,
                ]);

                if ($this->corporate['region']) {
                    $this->region = $this->corporate['region'];
                    $this->province = $this->corporate['province'];
                    $this->city = $this->corporate['city'];

                    $this->filteredProvinces = collect($this->provinces)->where('region', $profile->region)->values()->all();
                    $this->filteredCities = collect($this->cities)->where('province', $profile->province)->values()->all();
                }
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
            'last_name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email:rfc,dns', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
        ];

        if ($this->picture) {
            $rules['picture'] = ['image', 'max:2048']; // max 2MB, adjust as needed
            if ($user->avatar_path) {
                // if user already has a picture, we can optionally delete the old one here
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $this->uploadFiles($this->picture, 'avatar');
        }

        if ($this->logo) {
            $rules['logo'] = ['image', 'max:2048'];
            if ($user->corporateProfile?->logo_path) {
                Storage::disk('public')->delete($user->corporateProfile->logo_path);
            }
            $this->corporate['logo_path'] = $this->uploadFiles($this->logo, 'logo_path');
        }

        if ($this->dti_sec_reg) {
            $rules['dti_sec_reg'] = ['image', 'max:2048'];
            if ($user->corporateProfile?->dti_sec_reg_path) {
                Storage::disk('public')->delete($user->corporateProfile->dti_sec_reg_path);
            }
            $this->corporate['dti_sec_reg_path'] = $this->uploadFiles($this->dti_sec_reg, 'dti_sec_reg_path');
        }

        if ($this->business_permit) {
            $rules['business_permit'] = ['image', 'max:2048'];
            if ($user->corporateProfile?->business_permit_path) {
                Storage::disk('public')->delete($user->corporateProfile->business_permit_path);
            }
            $this->corporate['business_permit_path'] = $this->uploadFiles($this->business_permit, 'business_permit_path');
        }

        if ($this->isCorporateAccount) {
            $this->corporate['region'] = $this->region ? $this->region : null; // set to null if empty
            $this->corporate['province'] = $this->province ? $this->province : null;
            $this->corporate['city'] = $this->city ? $this->city : null;

            $rules = array_merge($rules, [
                // Corporate must have a company name (both in users + corporate_profiles)
                'company_name' => ['required', 'string', 'max:150'],

                'corporate.company_name' => ['required', 'string', 'max:150'],
                'corporate.business_type' => ['nullable', 'string', 'max:80'],

                'corporate.address_line_1' => ['nullable', 'string', 'max:255'],
                'corporate.address_line_2' => ['nullable', 'string', 'max:255'],
                'corporate.city' => ['nullable', 'string', 'max:255'],
                'corporate.province' => ['nullable', 'string', 'max:255'],
                'corporate.region' => ['nullable', 'string', 'max:255'],
                'corporate.country' => ['nullable', 'string', 'max:80'],

                'corporate.business_email' => ['nullable', 'email:rfc,dns', 'max:255'],
                'corporate.business_phone' => ['nullable', 'string', 'max:30'],
                'corporate.website' => ['nullable', 'url', 'max:255'],
            ]);
        }

        if ($this->isPersonalAccount) {
            $this->personal['region'] = $this->region ? $this->region : null; // set to null if empty
            $this->personal['province'] = $this->province ? $this->province : null;
            $this->personal['city'] = $this->city ? $this->city : null;

            $rules = array_merge($rules, [
                'personal.date_of_birth' => ['nullable', 'date'],
                'personal.gender' => ['nullable', 'string', 'max:20'],
                'personal.bio' => ['nullable', 'string'],

                'personal.address_line_1' => ['nullable', 'string', 'max:255'],
                'personal.address_line_2' => ['nullable', 'string', 'max:255'],
                'personal.city' => ['nullable', 'string', 'max:255'],
                'personal.province' => ['nullable', 'string', 'max:255'],
                'personal.region' => ['nullable', 'string', 'max:255'],
                'personal.country' => ['nullable', 'string', 'max:80'],
            ]);
        }

        $validated = $this->validate($rules);

        // Update users table
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($this->isCorporateAccount) {
            $user->company_name = $validated['company_name'] ?? $this->company_name;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $this->picture = null; // reset the temporary picture property after upload
        $this->logo = null;
        $this->dti_sec_reg = null;
        $this->business_permit = null;

        $user->save();

        // Update the correct profile table
        if ($this->isPersonalAccount) {
            $user->personalProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'date_of_birth' => $validated['personal']['date_of_birth'] ?? null,
                    'gender' => $validated['personal']['gender'] ?? null,
                    'bio' => $validated['personal']['bio'] ?? null,

                    'address_line_1' => $validated['personal']['address_line_1'] ?? null,
                    'address_line_2' => $validated['personal']['address_line_2'] ?? null,
                    'city' => $validated['personal']['city'] ?? null,
                    'province' => $validated['personal']['province'] ?? null,
                    'region' => $validated['personal']['region'] ?? null,
                    'country' => $validated['personal']['country'] ?? 'Philippines',
                ],
            );
        }

        if ($this->isCorporateAccount) {
            // keep users.company_name + corporate_profiles.company_name in sync
            $company = $validated['corporate']['company_name'] ?? ($validated['company_name'] ?? $user->company_name);

            $user->corporateProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'company_name' => $company,
                    'business_type' => $validated['corporate']['business_type'] ?? null,

                    'address_line_1' => $validated['corporate']['address_line_1'] ?? null,
                    'address_line_2' => $validated['corporate']['address_line_2'] ?? null,
                    'city' => $validated['corporate']['city'] ?? null,
                    'province' => $validated['corporate']['province'] ?? null,
                    'region' => $validated['corporate']['region'] ?? null,
                    'country' => $validated['corporate']['country'] ?? 'Philippines',

                    'business_email' => $validated['corporate']['business_email'] ?? null,
                    'business_phone' => $validated['corporate']['business_phone'] ?? null,
                    'website' => $validated['corporate']['website'] ?? null,
                    'logo_path' => $this->corporate['logo_path'] ?? null,
                    'dti_sec_reg_path' => $this->corporate['dti_sec_reg_path'] ?? null,
                    'business_permit_path' => $this->corporate['business_permit_path'] ?? null,
                ],
            );

            // if company name changed via corporate fields, also update users.company_name
            if ($user->company_name !== $company) {
                $user->company_name = $company;
                $user->save();
            }
        }

        // Use first_name + last_name for the toast/event
        $this->dispatch('profile-updated', name: trim($user->first_name . ' ' . $user->last_name));
        Flux::toast(variant: 'success', text: 'Your changes have been saved.');
    }

    private function uploadFiles($file, $field)
    {
        switch ($field) {
            case 'avatar':
                $path = $file->store('users/' . Auth::user()->id . '/avatar', 'public');
                break;

            case 'logo_path':
                $path = $file->store('users/' . Auth::user()->id . '/logo', 'public');
                break;

            case 'dti_sec_reg_path':
                $path = $file->store('users/' . Auth::user()->id . '/dti', 'public');
                break;

            case 'business_permit_path':
                $path = $file->store('users/' . Auth::user()->id . '/business_permit', 'public');
                break;
        }

        return $path;
    }

    public function deletePicture($field): void
    {
        /** @var User $user */
        $user = Auth::user();

        switch ($field) {
            case 'avatar':
                if ($user->avatar_path) {
                    Storage::disk('public')->delete($user->avatar_path);
                    $user->avatar_path = null;
                    $user->save();
                }
                break;

            case 'logo_path':
                if ($user->corporateProfile?->logo_path) {
                    Storage::disk('public')->delete($user->corporateProfile->logo_path);
                    $user->corporateProfile->logo_path = null;
                }
                break;

            case 'dti_sec_reg_path':
                if ($user->corporateProfile?->dti_sec_reg_path) {
                    Storage::disk('public')->delete($user->corporateProfile->dti_sec_reg_path);
                    $user->corporateProfile->dti_sec_reg_path = null;
                }
                break;

            case 'business_permit_path':
                if ($user->corporateProfile?->business_permit_path) {
                    Storage::disk('public')->delete($user->corporateProfile->business_permit_path);
                    $user->corporateProfile->business_permit_path = null;
                }
                break;
        }
        $user->corporateProfile?->save();
        $this->dispatch('profile-updated');
        Flux::toast(variant: 'success', text: 'Image Deleted.');
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
        return Auth::user() instanceof MustVerifyEmail && !Auth::user()->hasVerifiedEmail();
    }

    #[Computed]
    public function showDeleteUser(): bool
    {
        return !Auth::user() instanceof MustVerifyEmail || (Auth::user() instanceof MustVerifyEmail && Auth::user()->hasVerifiedEmail());
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

<section class="w-full h-full flex flex-col">

    <div class="shrink-0">
        @include('partials.settings-heading')
    </div>
    <div class="shrink-0">
        <flux:heading class="sr-only">{{ __('Profile Settings') }}</flux:heading>
    </div>

        <x-pages::settings.layout :heading="__('Profile')" :subheading="__('Update your details based on account type')">

            {{-- This is the form for setting upload --}}
            <form wire:submit="updateProfileInformation" enctype="multipart/form-data" class="my-6 w-full space-y-8">

                {{-- Account type (display only) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="account_type" :label="__('Account Type')" type="text" disabled />
                </div>

                <div class="col-span-3 flex flex-col md:flex-row gap-6 pt-0 items-center">
                    <!-- Picture -->
                    <flux:input wire:model="picture" :label="__('Update Profile Picture')" type="file" autofocus
                        accept=".jpg,.jpeg,.png" class="" />

                    @if ($picture)
                        <img src="{{ $picture->temporaryUrl() }}" alt="" class="w-32 h-32 rounded-md object-cover">
                    @endif

                    {{-- Checking if user picture exist --}}
                    @if (Auth::user()->avatar_path)
                        <img src="{{ url('storage/' . Auth::user()->avatar_path) }}" alt=""
                            class="w-32 h-32 rounded-md object-cover">
                    @else
                        <img src="{{ asset('/blank_image.png') }}" alt="" class="w-32 h-32 rounded-md object-cover">
                    @endif

                </div>

                {{-- Core user details --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="first_name" :label="__('First Name')" type="text" required
                        autocomplete="given-name" />
                    <flux:input wire:model="last_name" :label="__('Last Name')" type="text" required
                        autocomplete="family-name" />
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
                            <flux:input wire:model="personal.gender" :label="__('Gender')" type="text"
                                placeholder="Male / Female / Prefer not to say" />
                        </div>

                        <div>
                            <flux:textarea wire:model="personal.bio" :label="__('Bio')" rows="4" />
                        </div>

                        <flux:heading size="md">{{ __('Address') }}</flux:heading>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:input wire:model="personal.address_line_1" :label="__('Address Line 1')" type="text" />
                            <flux:input wire:model="personal.address_line_2" :label="__('Address Line 2')"
                                type="text" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <flux:input wire:model="personal.country" :label="__('Country')" type="text" />

                            <flux:select wire:model.live="region" placeholder="Choose Region..." label="Region">
                                @foreach ($regions as $region)
                                    <flux:select.option value="{{ $region['key'] }}">{{ $region['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <flux:select wire:model.live="province" placeholder="Choose Province..." label="Province"
                                class="">
                                @foreach ($this->filteredProvinces as $province)
                                    <flux:select.option value="{{ $province['key'] }}">{{ $province['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:select wire:model="city" placeholder="Choose a City..." label="City">
                                @foreach ($this->filteredCities as $city)
                                    <flux:select.option value="{{ $city['name'] }}">{{ $city['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                        </div>
                    </div>
                @endif

                {{-- CORPORATE PROFILE --}}
                @if ($this->isCorporateAccount)
                    <div class="space-y-6">
                        <flux:heading size="lg">{{ __('Corporate Profile') }}</flux:heading>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- users.company_name --}}
                            <flux:input wire:model="company_name" :label="__('Company Name (Account)')" type="text"
                                required />

                            {{-- corporate_profiles.company_name --}}
                            <flux:input wire:model="corporate.company_name" :label="__('Company Name (Business Profile)')"
                                type="text" required />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:input wire:model="corporate.business_type" :label="__('Business Type')" type="text"
                                placeholder="Gun store / Dealer / Distributor / etc" />
                            <flux:input wire:model="corporate.website" :label="__('Website')" type="url"
                                placeholder="https://..." />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:input wire:model="corporate.business_email" :label="__('Business Email')"
                                type="email" />
                            <flux:input wire:model="corporate.business_phone" :label="__('Business Phone')"
                                type="text" />
                        </div>

                        <flux:heading size="md">{{ __('Business Address') }}</flux:heading>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <flux:input wire:model="corporate.address_line_1" :label="__('Address Line 1')"
                                type="text" />
                            <flux:input wire:model="corporate.address_line_2" :label="__('Address Line 2')"
                                type="text" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <flux:input wire:model="personal.country" :label="__('Country')" type="text" />

                            <flux:select wire:model.live="region" placeholder="Choose Region..." label="Region">
                                @foreach ($regions as $region)
                                    <flux:select.option value="{{ $region['key'] }}">{{ $region['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <flux:select wire:model.live="province" placeholder="Choose Province..." label="Province"
                                class="">
                                @foreach ($this->filteredProvinces as $province)
                                    <flux:select.option value="{{ $province['key'] }}">{{ $province['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                            <flux:select wire:model="city" placeholder="Choose a City..." label="City">
                                @foreach ($this->filteredCities as $city)
                                    <flux:select.option value="{{ $city['name'] }}">{{ $city['name'] }}
                                    </flux:select.option>
                                @endforeach
                            </flux:select>

                        </div>

                        {{-- Document paths (optional / display only for now) --}}
                        <div class="w-max space-y-5">
                            <flux:heading size="md">{{ __('Business Documents') }}</flux:heading>

                            <div class="flex flex-col gap-5 items-start">

                                <flux:input wire:model="logo" :label="__('Corporate Logo')" type="file" autofocus
                                    accept=".jpg,.jpeg,.png" class="" />

                                @if ($corporate['logo_path'])
                                    <div class="relative">
                                        <img src="{{ url('storage/' . $corporate['logo_path']) }}" alt=""
                                            class="w-full h-50 rounded-md object-cover">

                                        <button wire:click="deletePicture('logo_path')" type="button"
                                            class="absolute top-1 right-1  text-white rounded-full w-6 h-6 flex items-center justify-center">
                                            <flux:icon name="x-circle"
                                                class="w-6 h-6 text-red-500 hover:cursor-pointer" />
                                        </button>
                                    </div>
                                @else
                                    <img src="{{ asset('/nd_available.png') }}" alt=""
                                        class="w-full h-50 rounded-md object-cover">
                                @endif
                            </div>
                            <flux:separator />
                            <div class="flex flex-col gap-5 items-start">
                                <flux:input wire:model="dti_sec_reg" :label="__('DTI/SEC Registration')" type="file"
                                    autofocus accept=".jpg,.jpeg,.png" class="" />

                                @if ($corporate['dti_sec_reg_path'])
                                    <div class="relative">
                                        <img src="{{ url('storage/' . $corporate['dti_sec_reg_path']) }}" alt=""
                                            class="w-full h-50 rounded-md object-cover">

                                        <button wire:click="deletePicture('dti_sec_reg_path')" type="button"
                                            class="absolute top-1 right-1  text-white rounded-full w-6 h-6 flex items-center justify-center">
                                            <flux:icon name="x-circle"
                                                class="w-6 h-6 text-red-500 hover:cursor-pointer" />
                                        </button>
                                    </div>
                                @else
                                    <img src="{{ asset('/nd_available.png') }}" alt=""
                                        class="w-full h-50 rounded-md object-cover">
                                @endif
                            </div>
                            <flux:separator />
                            <div class="flex flex-col gap-5 items-start">
                                <flux:input wire:model="business_permit" :label="__('Business Permit')" type="file"
                                    autofocus accept=".jpg,.jpeg,.png" class="" />

                                @if ($corporate['business_permit_path'])
                                    <div class="relative">
                                        <img src="{{ url('storage/' . $corporate['business_permit_path']) }}"
                                            alt="" class="w-full h-50 rounded-md object-cover">

                                        <button wire:click="deletePicture('business_permit_path')" type="button"
                                            class="absolute top-1 right-1  text-white rounded-full w-6 h-6 flex items-center justify-center">
                                            <flux:icon name="x-circle"
                                                class="w-6 h-6 text-red-500 hover:cursor-pointer" />
                                        </button>
                                    </div>
                                @else
                                    <img src="{{ asset('/nd_available.png') }}" alt=""
                                        class="w-full h-50 rounded-md object-cover">
                                @endif
                            </div>

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
