<x-layouts::auth>
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account. This is just for the initial registration. You should add details on the settings tab')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf

            <div class="grid md:grid-cols-2 gap-5">

                <div class="space-y-3">
                    {{-- Account Type --}}
                    <div class="grid gap-2">
                        <label class="text-sm font-medium text-zinc-800 dark:text-zinc-200">
                            {{ __('Account type') }}
                        </label>

                        {{-- If you have a flux select component, use it. If not, this native select is fine. --}}
                        <select
                            name="account_type"
                            class="w-full rounded-md border border-zinc-300 bg-white py-2 text-sm text-zinc-900 shadow-sm focus:border-zinc-400 focus:outline-none focus:ring-2 focus:ring-zinc-200 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:focus:ring-zinc-800 pe-5"
                            required
                        >
                            <option value="personal" @selected(old('account_type', 'personal') === 'personal')>
                                {{ __('Personal') }}
                            </option>
                            <option value="corporate" @selected(old('account_type') === 'corporate')>
                                {{ __('Corporate') }}
                            </option>
                        </select>

                        @error('account_type')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <flux:input
                    name="first_name"
                    :label="__('First name')"
                    :value="old('first_name')"
                    type="text"
                    autocomplete="given-name"
                    :placeholder="__('First name')"
                    />

                    <flux:input
                        name="last_name"
                        :label="__('Last name')"
                        :value="old('last_name')"
                        type="text"
                        autocomplete="family-name"
                        :placeholder="__('Last name')"
                    />

                    {{-- Corporate: Company name --}}
                    <flux:input
                        name="company_name"
                        :label="__('Company name')"
                        :value="old('company_name')"
                        type="text"
                        autocomplete="organization"
                        :placeholder="__('Company name')"
                    />


                </div>

                <div class="space-y-3">
                    {{-- Phone --}}
            <flux:input
                name="phone"
                :label="__('Phone')"
                :value="old('phone')"
                type="text"
                autocomplete="tel"
                :placeholder="__('09xxxxxxxxx')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                viewable
            />
                </div>

            </div>


            <div class="flex gap-5 justify-start">
                <flux:field variant="inline">
                    <flux:checkbox name="terms_accepted" />
                        <flux:label>I agree to the terms and conditions</flux:label>
                    <flux:error name="terms_accepted" />
                </flux:field>

                <flux:modal.trigger name="terms-and-conditions">
                    <button type="button" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 hover:cursor-pointer">Terms and conditions</button>
                </flux:modal.trigger>
            </div>






            <flux:modal name="terms-and-conditions" class="md:w-130">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-zinc-900 dark:text-white mb-4">
                            {{ __('Terms and Conditions') }}
                        </h2>
                        <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                            By registering for an account on GunPost PH, you agree to the following terms and conditions. Please
                            read them carefully:
                        </P>
                        <ul class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 text-justify list-decimal list-inside">
                            <li class="mb-2">
                                <strong>Legal Compliance:</strong> You confirm that you are at least 21 years old and legally
                                allowed to own, sell, or purchase
                                firearms in the Republic of the Philippines. All listings, transactions, and communications on
                                this
                                platform must comply with applicable Philippine laws, including but not limited to RA 10591
                                (Comprehensive Firearms and Ammunition Regulation Act) and PNP Firearms and Explosives Office
                                (FEO)
                                guidelines.
                            </li>
                            <li class="mb-2">
                                <strong>Account Types:</strong> GunPost PH allows both Personal and Corporate accounts. You
                                agree to provide accurate and verifiable
                                identification, business permits (if applicable), and all relevant documentation upon
                                registration
                                or when requested for verification.
                            </li>
                            <li class="mb-2">
                                <strong>Listings and Content:</strong> You are solely responsible for the accuracy, legality,
                                and completeness of your listings. Posting
                                illegal, unlicensed, or misrepresented firearms, ammunition, or accessories is strictly
                                prohibited
                                and will result in account suspension and possible legal action.
                            </li>
                            <li class="mb-2">
                                <strong>Transaction Responsibility:</strong> GunPost PH is a listing platform only. We do not
                                facilitate payments, transfers, or ownership
                                verification between users. You acknowledge that all transactions must follow lawful procedures,
                                including securing the necessary PNP permits and conducting face-to-face or regulated transfers
                                as
                                required.
                            </li>
                            <li class="mb-2">
                                <strong>Account Termination:</strong> We reserve the right to suspend or terminate any account
                                found violating these terms, posting
                                fraudulent or illegal content, or attempting to use the platform for non-compliant activities.
                            </li>
                            <li class="mb-2">
                                <strong>Privacy and Data Use:</strong> By registering, you consent to our collection and use of
                                your data in accordance with our Privacy
                                Policy. We do not sell or share your personal information with third parties except as required
                                by
                                law.
                            </li>
                            <li class="mb-2">
                                <strong>Amendments:</strong> GunPost PH may update these terms at any time. Continued use of
                                your account after such changes
                                constitutes acceptance of the new terms.
                                law.
                            </li>

                        </ul>

                    </div>

                </div>
            </flux:modal>





            <div class="flex items-center justify-end">


                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>

        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
