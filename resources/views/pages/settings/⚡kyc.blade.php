<?php

use App\Models\user\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Flux\Flux;

new class extends Component {
    use WithFileUploads;

    // Verification record fields
    public string $kyc_status = 'pending';
    public ?string $submitted_at = null;
    public ?string $reviewed_at = null;
    public ?string $kyc_notes = null;

    public ?string $government_id_type = null;
    public ?string $government_id_number = null;

    public ?string $government_id_front_path = null;
    public ?string $government_id_back_path = null;
    public ?string $selfie_with_id_path = null;

    // Upload temp fields
    public $government_id_front = null;
    public $government_id_back = null;
    public $selfie_with_id = null;

    #[On('profile-updated')]
    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user()->load('verification');

        $verification = $user->verification; // relationship (see model note below)

        if ($verification) {
            $this->kyc_status = $verification->kyc_status ?? 'pending';
            $this->submitted_at = optional($verification->submitted_at)->toDateTimeString();
            $this->reviewed_at = optional($verification->reviewed_at)->toDateTimeString();
            $this->kyc_notes = $verification->kyc_notes;

            $this->government_id_type = $verification->government_id_type;
            $this->government_id_number = $verification->government_id_number;

            $this->government_id_front_path = $verification->government_id_front_path;
            $this->government_id_back_path = $verification->government_id_back_path;
            $this->selfie_with_id_path = $verification->selfie_with_id_path;
        } else {
            // default states
            $this->kyc_status = 'pending';
        }
    }

    #[Computed]
    public function canEdit(): bool
    {
        // allow edits while pending/rejected; lock when approved
        return in_array($this->kyc_status, ['pending', 'rejected'], true);
    }

    #[Computed]
    public function statusBadgeVariant(): string
    {
        return match ($this->kyc_status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning', // pending
        };
    }

    public function submitKyc(): void
    {
        /** @var User $user */
        $user = Auth::user()->load('verification');

        if (!$this->canEdit) {
            Flux::toast(variant: 'warning', text: 'Your KYC is already approved and can no longer be edited.');
            return;
        }

        $rules = [
            'government_id_type' => ['required', 'string', 'max:50'],
            'government_id_number' => ['required', 'string', 'max:80'],

            // Require files if not yet uploaded
            'government_id_front' => [$this->government_id_front_path ? 'nullable' : 'required', 'image', 'max:4096'],
            'government_id_back' => [$this->government_id_back_path ? 'nullable' : 'required', 'image', 'max:4096'],
            'selfie_with_id' => [$this->selfie_with_id_path ? 'nullable' : 'required', 'image', 'max:4096'],
        ];

        $validated = $this->validate($rules);

        // Ensure record exists
        $verification = $user->verification()->firstOrCreate(['user_id' => $user->id], ['kyc_status' => 'pending']);

        // Upload new files (and delete old if replaced)
        if ($this->government_id_front) {
            if ($verification->government_id_front_path) {
                Storage::disk('public')->delete($verification->government_id_front_path);
            }
            $verification->government_id_front_path = $this->storeKycFile($this->government_id_front, 'government_id_front');
        }

        if ($this->government_id_back) {
            if ($verification->government_id_back_path) {
                Storage::disk('public')->delete($verification->government_id_back_path);
            }
            $verification->government_id_back_path = $this->storeKycFile($this->government_id_back, 'government_id_back');
        }

        if ($this->selfie_with_id) {
            if ($verification->selfie_with_id_path) {
                Storage::disk('public')->delete($verification->selfie_with_id_path);
            }
            $verification->selfie_with_id_path = $this->storeKycFile($this->selfie_with_id, 'selfie_with_id');
        }

        // Update fields
        $verification->government_id_type = $validated['government_id_type'];
        $verification->government_id_number = $validated['government_id_number'];

        // When submitting, keep status as pending; set submitted_at
        $verification->kyc_status = 'pending';
        $verification->submitted_at = now();
        $verification->kyc_notes = null; // optional: clear old notes on resubmit

        $verification->save();

        // Sync paths back to UI
        $this->government_id_front_path = $verification->government_id_front_path;
        $this->government_id_back_path = $verification->government_id_back_path;
        $this->selfie_with_id_path = $verification->selfie_with_id_path;

        $this->kyc_status = $verification->kyc_status;
        $this->submitted_at = optional($verification->submitted_at)->toDateTimeString();

        // Reset temp uploads
        $this->government_id_front = null;
        $this->government_id_back = null;
        $this->selfie_with_id = null;

        $this->dispatch('profile-updated');
        Flux::toast(variant: 'success', text: 'KYC submitted! Your verification is now pending review.');
    }

    public function deleteKycFile(string $field): void
    {
        /** @var User $user */
        $user = Auth::user()->load('verification');

        if (!$this->canEdit) {
            Flux::toast(variant: 'warning', text: 'Approved KYC files can no longer be removed.');
            return;
        }

        $allowed = ['government_id_front_path', 'government_id_back_path', 'selfie_with_id_path'];

        if (!in_array($field, $allowed, true)) {
            return;
        }

        $verification = $user->verification;

        if (!$verification) {
            return;
        }

        $old = $verification->{$field};

        if ($old) {
            Storage::disk('public')->delete($old);
            // IMPORTANT: update via relationship/builder to guarantee DB update
            $user->verification()->updateOrCreate(['user_id' => $user->id], [$field => null]);

            // refresh UI
            $this->{$field} = null;

            $this->dispatch('profile-updated');
            Flux::toast(variant: 'success', text: 'File removed.');
        }
    }

    private function storeKycFile($file, string $type): string
    {
        return $file->store('users/' . Auth::id() . '/kyc/' . $type, 'public');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <flux:heading class="sr-only">{{ __('KYC Verification') }}</flux:heading>

    <x-pages::settings.layout :heading="__('KYC Verification')" :subheading="__('Submit your identity documents for review')">

        {{-- Compliance notice --}}
        <div class="space-y-3 rounded-lg border p-4">
            <flux:heading size="md">{{ __('Why KYC is required before posting') }}</flux:heading>

            <flux:text class="text-sm opacity-90">
                {{ __('GunPost PH requires identity verification (KYC) before allowing users to publish listings to help prevent fraud, impersonation, and unlawful transactions, and to support safe, accountable marketplace operations. This process also helps us comply with applicable Philippine regulations and platform risk controls by ensuring we can verify who is posting listings, respond to law-enforcement or regulator requests when legally required, and enforce bans against repeat offenders.') }}
            </flux:text>

            <flux:text class="text-sm opacity-90">
                {{ __('Your documents are used only for verification and internal compliance. We may restrict posting privileges until your KYC status is approved.') }}
            </flux:text>

            <div class="flex items-center gap-3">
                <flux:badge
                    color="{{ auth()->user()->verification->kyc_status == 'verified' ? 'green' : (auth()->user()->verification->kyc_status == 'rejected' ? 'red' : 'blue') }}"
                    size="xs" class="ms-2">
                    {{ strtoupper(auth()->user()->verification->kyc_status) }}
                </flux:badge>

                @if ($submitted_at)
                    <flux:text class="text-sm opacity-75">
                        {{ __('Submitted:') }} {{ $submitted_at }}
                    </flux:text>
                @endif

                @if ($reviewed_at)
                    <flux:text class="text-sm opacity-75">
                        {{ __('Reviewed:') }} {{ $reviewed_at }}
                    </flux:text>
                @endif
            </div>

            @if ($kyc_status === 'rejected' && $kyc_notes)
                <div class="rounded-md border p-3">
                    <flux:text class="text-sm">
                        <span class="font-medium">{{ __('Reviewer Notes:') }}</span>
                        {{ $kyc_notes }}
                    </flux:text>
                </div>
            @endif
        </div>

        <form wire:submit="submitKyc" enctype="multipart/form-data" class="my-6 w-full space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="government_id_type" :label="__('Government ID Type')" type="text"
                    placeholder="e.g., Driver’s License, Passport, UMID" :disabled="!$this->canEdit" required />

                <flux:input wire:model="government_id_number" :label="__('Government ID Number')" type="text"
                    placeholder="Enter your ID number" :disabled="!$this->canEdit" required />
            </div>

            <flux:separator />

            <div class="space-y-6">
                <flux:heading size="md">{{ __('Upload Required Documents') }}</flux:heading>
                <flux:text class="text-sm opacity-80">
                    {{ __('Accepted: JPG/PNG. Max 4MB each. Make sure text is readable and the selfie is clear.') }}
                </flux:text>

                {{-- Government ID Front --}}
                <div class="space-y-3">
                    <flux:input wire:model="government_id_front" :label="__('Government ID (Front)')" type="file"
                        accept=".jpg,.jpeg,.png" :disabled="!$this->canEdit" />

                    @if ($government_id_front)
                        <img src="{{ $government_id_front->temporaryUrl() }}"
                            class="w-full max-w-md rounded-md object-cover" alt="">
                    @endif

                    @if ($government_id_front_path)
                        <div class="relative w-full max-w-md">
                            <img src="{{ url('storage/' . $government_id_front_path) }}"
                                class="w-full rounded-md object-cover" alt="">
                            @if ($this->canEdit)
                                <button wire:click="deleteKycFile('government_id_front_path')" type="button"
                                    class="absolute top-2 right-2">
                                    <flux:icon name="x-circle" class="w-7 h-7 text-red-500 hover:cursor-pointer" />
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <flux:separator />

                {{-- Government ID Back --}}
                <div class="space-y-3">
                    <flux:input wire:model="government_id_back" :label="__('Government ID (Back)')" type="file"
                        accept=".jpg,.jpeg,.png" :disabled="!$this->canEdit" />

                    @if ($government_id_back)
                        <img src="{{ $government_id_back->temporaryUrl() }}"
                            class="w-full max-w-md rounded-md object-cover" alt="">
                    @endif

                    @if ($government_id_back_path)
                        <div class="relative w-full max-w-md">
                            <img src="{{ url('storage/' . $government_id_back_path) }}"
                                class="w-full rounded-md object-cover" alt="">
                            @if ($this->canEdit)
                                <button wire:click="deleteKycFile('government_id_back_path')" type="button"
                                    class="absolute top-2 right-2">
                                    <flux:icon name="x-circle" class="w-7 h-7 text-red-500 hover:cursor-pointer" />
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <flux:separator />

                {{-- Selfie with ID --}}
                <div class="space-y-3">
                    <flux:input wire:model="selfie_with_id" :label="__('Selfie Holding Your ID')" type="file"
                        accept=".jpg,.jpeg,.png" :disabled="!$this->canEdit" />

                    @if ($selfie_with_id)
                        <img src="{{ $selfie_with_id->temporaryUrl() }}"
                            class="w-full max-w-md rounded-md object-cover" alt="">
                    @endif

                    @if ($selfie_with_id_path)
                        <div class="relative w-full max-w-md">
                            <img src="{{ url('storage/' . $selfie_with_id_path) }}"
                                class="w-full rounded-md object-cover" alt="">
                            @if ($this->canEdit)
                                <button wire:click="deleteKycFile('selfie_with_id_path')" type="button"
                                    class="absolute top-2 right-2">
                                    <flux:icon name="x-circle" class="w-7 h-7 text-red-500 hover:cursor-pointer" />
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-4">
                <flux:button variant="primary" type="submit" class="w-full md:w-auto" :disabled="!$this->canEdit">
                    {{ $kyc_status === 'rejected' ? __('Re-submit KYC') : __('Submit KYC') }}
                </flux:button>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('Updated.') }}
                </x-action-message>
            </div>

            @if (!$this->canEdit)
                <flux:text class="text-sm opacity-70">
                    {{ __('Your KYC is approved. Editing is locked. Contact support if you need changes.') }}
                </flux:text>
            @endif
        </form>

    </x-pages::settings.layout>
</section>
