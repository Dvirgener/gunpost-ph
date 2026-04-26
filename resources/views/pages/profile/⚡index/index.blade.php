<div class="p-4 flex flex-col  h-full bg-red-300">
    <div class="flex-1 min-h-0 overflow-y-scroll px-2">
        <div class="my-2 px-4">
            @if (Auth::user()->verification->kyc_status == 'pending' && !auth()->user()->isAdmin())
                <flux:callout icon="exclamation-triangle" color="red" inline>
                    <flux:callout.heading>Your account is not yet verified. Please go to KYC page and verify your
                        account. Thank you.</flux:callout.heading>
                    <x-slot name="actions">
                        <flux:button href="{{ route('kyc.edit') }}">Verify now -></flux:button>
                    </x-slot>
                </flux:callout>
            @endif
        </div>
        <div>
            <livewire:pages::profile.details :user="$profile" />
        </div>
        @if (auth()->user()->isAdmin())


            <div class="px-4">
                @if ($profile->verification->submitted_at)
                    <flux:text>KYC Verification Documents submitted last
                        {{ $profile->verification->submitted_at }}
                    </flux:text>
                    <div>

                        <div class="mb-5 md:flex items-center gap-3 space-y-2">

                            <flux:heading>Goverment ID type Uploaded -
                                ({{ $profile->verification->government_id_type }})
                                with number ({{ $profile->verification->government_id_number }})
                            </flux:heading>

                            @if ($profile->verification->kyc_status === 'pending')
                                <flux:button class="hover:cursor-pointer" wire:click="verifyUser"
                                    wire:confirm="Are you sure you want to verify this user?">Verify User</flux:button>
                            @elseif($profile->verification->kyc_status === 'verified')
                                <flux:badge color="green" size="sm" inset="top bottom">
                                    Verified
                                </flux:badge>
                            @else
                                <flux:badge color="green" size="sm" inset="top bottom">
                                    Admin
                                </flux:badge>
                            @endif

                        </div>

                        <div>
                            <flux:heading size="lg" class="font-mono text-start my-2 font-bold">GOVERNMENT ID FRONT
                            </flux:heading>
                            <img src="{{ url($profile->verification->government_id_front_path) }}" alt="KYC Document"
                                class="w-1/2 h-auto rounded-lg border" />
                        </div>

                        <div>
                            <flux:heading size="lg" class="font-mono text-start my-2 font-bold">GOVERNMENT ID BACK
                            </flux:heading>
                            <img src="{{ url($profile->verification->government_id_back_path) }}" alt="KYC Document"
                                class="w-1/2 h-auto rounded-lg border" />
                        </div>

                        <div>
                            <flux:heading size="lg" class="font-mono text-start my-2 font-bold">SELFIE WITH
                                GOVERNMENT
                                ID
                            </flux:heading>
                            <img src="{{ url($profile->verification->selfie_with_id_path) }}" alt="KYC Document"
                                class="w-1/2 h-auto rounded-lg border" />
                        </div>

                    </div>
                @else
                    <flux:text>KYC Verification Not Yet Submitted. User has not uploaded any KYC documents yet.
                    </flux:text>
                @endif
            </div>
        @endif

        <div class="px-2">
            <livewire:pages::profile.posts :user="$profile" />
        </div>
    </div>








</div>
