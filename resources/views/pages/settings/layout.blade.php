<div class="flex items-start max-md:flex-col">
    <div class="me-10 w-full pb-4 md:w-[220px]">
        <flux:navlist aria-label="{{ __('Settings') }}">
            <flux:navlist.item :href="route('profile.edit')" wire:navigate>{{ __('Profile') }}</flux:navlist.item>
            <flux:navlist.item :href="route('user-password.edit')" wire:navigate>{{ __('Password') }}
            </flux:navlist.item>
            @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
                <flux:navlist.item :href="route('two-factor.show')" wire:navigate>{{ __('Two-Factor Auth') }}
                </flux:navlist.item>
            @endif
            <flux:navlist.item :href="route('appearance.edit')" wire:navigate>{{ __('Appearance') }}</flux:navlist.item>
            <flux:navlist.item :href="route('kyc.edit')" wire:navigate class="">
                <div class="my-2 flex justify-between w-full items-center">
                    <span>KYC</span>
                    <flux:badge
                        color="{{ auth()->user()->verification->kyc_status == 'verified' ? 'green' : (auth()->user()->verification->kyc_status == 'rejected' ? 'red' : 'blue') }}"
                        size="xs" class="ms-2 capitalize">{{ auth()->user()->verification->kyc_status }}</flux:badge>
                </div>

            </flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 md:w-full h-200 ps-5 pe-10 py-5 overflow-auto">
            {{ $slot }}
        </div>
    </div>
</div>
