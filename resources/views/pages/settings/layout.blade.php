<div class="flex-1 min-h-0 flex flex-col md:flex-row gap-2 overflow-y-scroll md:overflow-y-hidden">
    <div class="me-10 pb-4 w-1/3">
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

    <div class="w-full flex flex-col max-md:pt-6">
        <div class="flex-1 min-h-0 md:overflow-y-scroll">
            <flux:heading>{{ $heading ?? '' }}</flux:heading>
            <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

            <div class="my-5 md:w-full h-200 ps-5 pe-10 py-5">
                {{ $slot }}
            </div>
        </div>

    </div>

</div>
