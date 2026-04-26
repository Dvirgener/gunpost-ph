<?php

use App\Models\user\CorporateProfile;
use App\Models\user\PersonalProfile;
use App\Models\user\User;

it('shows personal profile details on profile page', function () {
    $user = User::factory()->create([
        'account_type' => 'personal',
        'first_name' => 'Jane',
        'last_name' => 'Doe',
        'company_name' => null,
        'phone' => '+63 912 345 6789',
    ]);

    PersonalProfile::create([
        'user_id' => $user->id,
        'date_of_birth' => '1990-01-01',
        'gender' => 'Female',
        'bio' => 'Personal bio here.',
        'address_line_1' => '123 Main St',
        'address_line_2' => 'Unit 4',
        'city' => 'Manila',
        'province' => 'Metro Manila',
        'region' => 'NCR',
        'country' => 'Philippines',
    ]);

    $this->actingAs($user);

    $this->get(route('profile', $user))
        ->assertOk()
        ->assertSee('Personal Profile')
        ->assertSee('Date of Birth')
        ->assertSee('January 1, 1990')
        ->assertSee('Female')
        ->assertSee('Personal bio here.')
        ->assertSee('123 Main St')
        ->assertSee('Manila');
});

it('shows corporate profile details on profile page', function () {
    $user = User::factory()->create([
        'account_type' => 'corporate',
        'first_name' => 'Acme',
        'last_name' => 'Corp',
        'company_name' => 'Acme Corporation',
        'phone' => '+63 998 765 4321',
    ]);

    CorporateProfile::create([
        'user_id' => $user->id,
        'company_name' => 'Acme Corporation',
        'business_type' => 'Dealer',
        'address_line_1' => '456 Business Rd',
        'address_line_2' => 'Suite 100',
        'city' => 'Quezon City',
        'province' => 'Metro Manila',
        'region' => 'NCR',
        'country' => 'Philippines',
        'business_email' => 'business@acme.test',
        'business_phone' => '+63 998 765 4321',
        'website' => 'https://acme.test',
    ]);

    $this->actingAs($user);

    $this->get(route('profile', $user))
        ->assertOk()
        ->assertSee('Corporate Profile')
        ->assertSee('Acme Corporation')
        ->assertSee('Dealer')
        ->assertSee('business@acme.test')
        ->assertSee('456 Business Rd')
        ->assertSee('Quezon City');
});
