<div class="">
    <div class="flex justify-between items-center mb-4 w-full">
        <div>
            <h2 class="text-2xl font-bold  mb-2 px-4">
                {{ $owner->fullName() }}
            </h2>
        </div>
        <div>
            @auth
                @if (auth()->user()->id == $owner->id)
                    <flux:link href="{{ route('profile.edit') }}" variant="subtle" icon="cog">
                        <flux:icon.cog-6-tooth />
                    </flux:link>
                @else
                    <flux:modal.trigger name="sendMessageModal">
                        <flux:button variant="outline" color="red" icon="chat-bubble-left">Send Message</flux:button>
                    </flux:modal.trigger>
                @endif
            @endauth
        </div>

    </div>
    <div class="">


        <div class="space-y-3  px-4">
            <div class="md:flex justify-start">
                {{-- * This is where the user profile picture is --}}

                <div class="flex justify-center md:justify-start items-center relative rounded w-full md:w-70 h-max">
                    <img src="{{ $owner->avatar_path ? url('storage/' . $owner->avatar_path) : asset('/blank_image.png') }}"
                        alt="" class="w-48 h-48 shadow-xl border">
                </div>

                <div class="w-full">
                    <div class="hidden md:block">
                        <div class="flex flex-col justify-start items-start ms-4 sm:text-xs ">
                            @auth
                                <p class="text-gray-600 dark:text-gray-300  mb-2 text-sm">
                                    <span class="text-black dark:text-white font-bold">Email Address : </span>
                                    {{ $owner->email }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-300  mb-2 text-sm">
                                    <span class="text-black dark:text-white font-bold">Contact Number : </span>
                                    {{ $owner->phone ?? '-' }}
                                </p>
                                <div class="text-gray-600 dark:text-gray-300 mb-2 text-sm flex gap-2 items-center">
                                    <span class="text-black dark:text-white font-bold">Post Credits :</span>
                                    <span class="font-bold">{{ $owner->post_credits }}</span>
                                    @if ($owner->isMe())
                                        @if ($owner->post_credits <= 0)
                                            <flux:badge size="sm" color="red">No post Credits (Purchase credits to
                                                publish posts)
                                            </flux:badge>
                                        @elseif ($owner->post_credits <= 3)
                                            <flux:badge size="sm" color="orange">Low Credits
                                            </flux:badge>
                                        @endif
                                    @endif

                                </div>

                                <div class="flex gap-3 items-center mb-2">
                                    <p class="font-bold text-sm">
                                        KYC Status :
                                    </p>
                                    @if ($owner->isAdmin())
                                        <flux:badge size="sm" color="green">Admin</flux:badge>
                                    @else
                                        @switch($owner->verification->kyc_status)
                                            @case('pending')
                                                <flux:badge size="sm" color="yellow">Pending</flux:badge>
                                            @break

                                            @case('verified')
                                                <flux:badge size="sm" color="green">Verified</flux:badge>
                                            @break

                                            @case('rejected')
                                                <flux:badge size="sm" color="red">Rejected</flux:badge>
                                            @break

                                            @default
                                                <flux:badge size="sm" color="green">Admin</flux:badge>
                                        @endswitch
                                    @endif

                                </div>

                                <div class="">

                                    <!-- Rating -->
                                    {{-- <flux:text class="flex flex-col gap-2 font-bold">
                                        <flux:heading class="font-bold!">User Rating : 4.8 / 5.0</flux:heading>
                                        <span>⭐⭐⭐⭐⭐</span>
                                    </flux:text> --}}

                                </div>

                            @endauth
                        </div>
                    </div>
                    <div class="block md:hidden">
                        <div class="flex flex-col justify-center items-center sm:text-xs ">
                            @auth

                                <div>
                                    <span class="text-black dark:text-white text-sm font-bold">Email Address </span>
                                </div>

                                <p class="text-gray-600 dark:text-gray-300  mb-2 text-sm">
                                    {{ $owner->email }}
                                </p>

                                <div>
                                    <span class="text-black dark:text-white font-bold text-sm">Contact Number</span>
                                </div>

                                <p class="text-gray-600 dark:text-gray-300  mb-2 text-sm">
                                    {{ $owner->phone ?? '-' }}
                                </p>

                                <div>
                                    <span class="text-black dark:text-white font-bold text-sm">Post Credits</span>
                                </div>

                                <div
                                    class="text-gray-600 dark:text-gray-300 mb-2 text-sm flex flex-col gap-2 items-center justify-center">
                                    <div>
                                        <span class="font-bold">{{ $owner->post_credits }}</span>
                                    </div>


                                    @if ($owner->post_credits <= 0)
                                        <flux:badge size="sm" color="red">No post Credits (Purchase credits to
                                            publish posts)
                                        </flux:badge>
                                    @elseif ($owner->post_credits <= 3)
                                        <flux:badge size="sm" color="orange">Low Credits
                                        </flux:badge>
                                    @endif
                                </div>

                                <div class="flex gap-3 items-center mb-2">
                                    <p class="font-bold text-sm">
                                        KYC Status :
                                    </p>
                                    @switch($owner->verification->kyc_status)
                                        @case('pending')
                                            <flux:badge size="sm" color="yellow">Pending</flux:badge>
                                        @break

                                        @case('verified')
                                            <flux:badge size="sm" color="green">Verified</flux:badge>
                                        @break

                                        @case('rejected')
                                            <flux:badge size="sm" color="red">Rejected</flux:badge>
                                        @break

                                        @default
                                    @endswitch
                                </div>

                                <div class="w-full  text-center">
                                    <!-- Rating -->
                                    {{-- <flux:text class="flex flex-col gap-1 font-bold text-center w-full justify-center">
                                        <flux:heading class="font-bold!">User Rating : 4.8 / 5.0</flux:heading>
                                        <span class="w-full">⭐⭐⭐⭐⭐</span>
                                    </flux:text> --}}
                                </div>

                            @endauth
                        </div>
                    </div>

                </div>
            </div>



            <flux:separator class="my-5" />

            <div class="space-y-4 p-4 rounded bg-slate-50 dark:bg-slate-800">
                <flux:heading size="lg">{{ __('Account Details') }}</flux:heading>

                @if ($owner->account_type === 'personal')
                    <div class="mt-4">
                        @php
                            $owner->personalProfile()->firstOrCreate(); // Ensure personal profile exists
                            $profile = $owner->personalProfile;

                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold">{{ __('Date of Birth') }}:</span>
                                <span
                                    class="ml-1">{{ optional($profile)->date_of_birth ? \Illuminate\Support\Carbon::parse(optional($profile)->date_of_birth)->format('F j, Y') : '-' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">{{ __('Joined:') }}:</span>
                                <span class="ml-1">{{ $owner->created_at->format('F j, Y') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">{{ __('Gender') }}:</span>
                                <span class="ml-1">{{ optional($profile)->gender ?? '-' }}</span>
                            </div>
                            <div class="md:col-span-2">
                                <span class="font-semibold">{{ __('Bio') }}:</span>
                                <span class="ml-1">{{ optional($profile)->bio ?? '-' }}</span>
                            </div>
                            <div class="md:col-span-2">
                                <span class="font-semibold">{{ __('Address') }}:</span>
                                <span class="ml-1">
                                    {{ $profile->address_line_1 ?? '' }}
                                    {{ $profile->address_line_2 ? ' ' . $profile->address_line_2 : '' }}
                                    {{ $profile->city ? ', ' . $profile->city . ' City' : '' }}
                                    {{ $profile->province ? ', ' . $profile->province : '' }}
                                    {{ $profile->region ? ', Region ' . $profile->region : '' }}
                                    {{ $profile->country ? ', ' . $profile->country : '' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @elseif($owner->account_type === 'corporate')
                    <div class="mt-4">
                        @php
                            $owner->corporateProfile()->firstOrCreate(); // Ensure corporate profile exists
                            $profile = $owner->corporateProfile;
                        @endphp
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-semibold">{{ __('Business Name') }}:</span>
                                <span
                                    class="ml-1">{{ optional($profile)->company_name ?? ($owner->company_name ?? '-') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">{{ __('Business Type') }}:</span>
                                <span class="ml-1">{{ optional($profile)->business_type ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">{{ __('Business Email') }}:</span>
                                <span class="ml-1">{{ optional($profile)->business_email ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">{{ __('Business Phone') }}:</span>
                                <span class="ml-1">{{ optional($profile)->business_phone ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">{{ __('Website') }}:</span>
                                <span class="ml-1">
                                    @if (optional($profile)->website)
                                        <a href="{{ optional($profile)->website }}" target="_blank"
                                            class="text-blue-600">{{ optional($profile)->website }}</a>
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="md:col-span-2">
                                <span class="font-semibold">{{ __('Address') }}:</span>
                                <span class="ml-1">
                                    {{ $profile->address_line_1 ?? '' }}
                                    {{ $profile->address_line_2 ? ' ' . $profile->address_line_2 : '' }}
                                    {{ $profile->city ? ', ' . $profile->city . ' City' : '' }}
                                    {{ $profile->province ? ', ' . $profile->province : '' }}
                                    {{ $profile->region ? ', Region ' . $profile->region : '' }}
                                    {{ $profile->country ? ', ' . $profile->country : '' }}
                                </span>
                            </div>
                            <div class="md:col-span-2">
                                <div class="flex flex-wrap gap-2">
                                    @if (optional($profile)->logo_path)
                                        <a href="{{ asset('storage/' . optional($profile)->logo_path) }}"
                                            target="_blank" class="text-blue-600">{{ __('View logo') }}</a>
                                    @endif
                                    @if (optional($profile)->dti_sec_reg_path)
                                        <a href="{{ asset('storage/' . optional($profile)->dti_sec_reg_path) }}"
                                            target="_blank" class="text-blue-600">{{ __('View DTI/SEC') }}</a>
                                    @endif
                                    @if (optional($profile)->business_permit_path)
                                        <a href="{{ asset('storage/' . optional($profile)->business_permit_path) }}"
                                            target="_blank"
                                            class="text-blue-600">{{ __('View business permit') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <flux:separator class="my-5" />

        </div>
    </div>

    {{-- Modal for Sending the User a Message --}}

    <flux:modal name="sendMessageModal" class="md:w-96">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Send Message</flux:heading>
                <flux:text class="mt-2">Send a message to this user.</flux:text>
            </div>
            <form action="" wire:submit.prevent="sendMessage" class="space-y-4">
                <flux:textarea label="Message" placeholder="Your message" wire:model="message" />
                <div class="flex">
                    <flux:spacer />
                    <flux:button type="submit" variant="primary">Send Message</flux:button>
                </div>

            </form>

        </div>
    </flux:modal>
</div>
