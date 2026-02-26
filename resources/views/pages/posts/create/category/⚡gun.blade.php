<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Gun;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Flux\Flux;

new class extends Component
{
    use WithFileUploads;

    public string $category = 'gun';

    // Post (required ones marked in UI with *)
    public string $listing_type = 'sell'; // * buy|sell
    public string $title = '';            // *
    public string $description = '';      // *
    public ?float $price = null;          // * if sell
    public ?float $buy_min_price = null; // * if buy
    public ?float $buy_max_price = null; // * if buy
    public bool $is_negotiable = false;
    public ?string $post_condition = null;
    public ?string $location = null;
    public ?string $expires_at = null;

    /**
     * File uploads (each maps to posts.p_1..p_10)
     * NOTE: we keep p_1..p_10 in DB as string paths (set during save).
     */
    public $photo_1 = null;
    public $photo_2 = null;
    public $photo_3 = null;
    public $photo_4 = null;
    public $photo_5 = null;
    public $photo_6 = null;
    public $photo_7 = null;
    public $photo_8 = null;
    public $photo_9 = null;
    public $photo_10 = null;

    // Gun
    public ?string $manufacturer = null;
    public ?string $model = null;
    public ?string $variant = null;
    public ?string $series = null;
    public ?string $country_of_origin = null;

    public ?string $platform = null;
    public ?string $type = null;
    public ?string $action = null;

    public ?string $caliber = null;
    public ?int $capacity = null;
    public ?float $barrel_length = null;
    public ?float $overall_length = null;
    public ?float $height = null;
    public ?float $width = null;
    public ?float $weight = null;
    public string $weight_unit = 'kg';

    public ?string $frame_material = null;
    public ?string $slide_material = null;
    public ?string $barrel_material = null;
    public ?string $finish = null;
    public ?string $color = null;
    public ?string $grip_type = null;
    public ?string $stock_type = null;
    public ?string $handguard_type = null;
    public ?string $rail_type = null;

    public ?string $sight_type = null;
    public bool $optic_ready = false;
    public ?string $optic_mount_pattern = null;

    public bool $threaded_barrel = false;
    public ?string $thread_pitch = null;
    public bool $muzzle_device_included = false;
    public ?string $muzzle_device_type = null;

    public ?string $trigger_type = null;
    public ?float $trigger_pull = null;
    public string $trigger_pull_unit = 'lb';
    public bool $has_manual_safety = false;
    public bool $has_firing_pin_safety = false;

    public ?string $sku = null;
    public ?string $upc = null;

    public ?string $gun_condition = null;
    public ?int $round_count_estimate = null;
    public bool $has_box = false;
    public bool $has_receipt = false;
    public bool $has_documents = false;
    public ?string $document_notes = null;

    public ?int $included_magazines = null;
    public ?string $included_accessories = null;

    public ?string $notes = null;

    #[Computed]
    public function platformOptions(): array
    {
        return [
            'handgun' => 'Handgun',
            'rifle'   => 'Rifle',
            'shotgun' => 'Shotgun',
            'pcc'     => 'PCC',
            'smg'     => 'SMG',
            'sniper'  => 'Sniper',
            'other'   => 'Other',
        ];
    }

    #[Computed]
    public function listingTypeOptions(): array
    {
        return ['sell' => 'Sell', 'buy' => 'Buy'];
    }

    #[Computed]
    public function postConditionOptions(): array
    {
        return [
            'new'         => 'New',
            'used'        => 'Used',
            'like_new'    => 'Like New',
            'refurbished' => 'Refurbished',
            'for_parts'   => 'For Parts',
        ];
    }

    private function photoRules(string $field): array
    {
        // 10MB max (Livewire "max" is KB)
        return [$field => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240']];
    }

    public function rules(): array
    {
        return array_merge([
            // Post (required)
            'category'      => ['required', Rule::in(['gun'])],
            'listing_type'  => ['required', Rule::in(['buy', 'sell'])],
            'title'         => ['required', 'string', 'min:5', 'max:255'],
            'description'   => ['required', 'string', 'min:20'],
            'price'         => [Rule::requiredIf(fn () => $this->listing_type === 'sell'), 'nullable', 'numeric', 'min:0'],
            'buy_min_price' => [Rule::requiredIf(fn () => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
            'buy_max_price' => [Rule::requiredIf(fn () => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
            'is_negotiable' => ['boolean'],
            'post_condition'=> ['nullable', 'string', 'max:50'],
            'location'      => ['nullable', 'string', 'max:255'],
            'expires_at'    => ['nullable', 'date'],

            // Gun
            'manufacturer' => ['nullable', 'string', 'max:255'],
            'model'        => ['nullable', 'string', 'max:255'],
            'variant'      => ['nullable', 'string', 'max:255'],
            'series'       => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],

            'platform'     => ['nullable', Rule::in(['handgun','rifle','shotgun','pcc','smg','sniper','other'])],
            'type'         => ['nullable', 'string', 'max:255'],
            'action'       => ['nullable', 'string', 'max:255'],

            'caliber'      => ['nullable', 'string', 'max:255'],
            'capacity'     => ['nullable', 'integer', 'min:0', 'max:9999'],
            'barrel_length'=> ['nullable', 'numeric', 'min:0'],
            'overall_length'=>['nullable', 'numeric', 'min:0'],
            'height'       => ['nullable', 'numeric', 'min:0'],
            'width'        => ['nullable', 'numeric', 'min:0'],
            'weight'       => ['nullable', 'numeric', 'min:0'],
            'weight_unit'  => ['required', Rule::in(['kg', 'lb'])],

            'frame_material'=> ['nullable', 'string', 'max:255'],
            'slide_material'=> ['nullable', 'string', 'max:255'],
            'barrel_material'=>['nullable', 'string', 'max:255'],
            'finish'       => ['nullable', 'string', 'max:255'],
            'color'        => ['nullable', 'string', 'max:255'],
            'grip_type'    => ['nullable', 'string', 'max:255'],
            'stock_type'   => ['nullable', 'string', 'max:255'],
            'handguard_type'=>['nullable', 'string', 'max:255'],
            'rail_type'    => ['nullable', 'string', 'max:255'],

            'sight_type'   => ['nullable', 'string', 'max:255'],
            'optic_ready'  => ['boolean'],
            'optic_mount_pattern' => ['nullable', 'string', 'max:255'],

            'threaded_barrel' => ['boolean'],
            'thread_pitch'    => ['nullable', 'string', 'max:255'],
            'muzzle_device_included' => ['boolean'],
            'muzzle_device_type' => ['nullable', 'string', 'max:255'],

            'trigger_type' => ['nullable', 'string', 'max:255'],
            'trigger_pull' => ['nullable', 'numeric', 'min:0'],
            'trigger_pull_unit' => ['required', Rule::in(['lb', 'kg'])],
            'has_manual_safety' => ['boolean'],
            'has_firing_pin_safety' => ['boolean'],

            'sku'          => ['nullable', 'string', 'max:255'],
            'upc'          => ['nullable', 'string', 'max:255'],

            'gun_condition'=> ['nullable', Rule::in(['new','like_new','used','refurbished','for_parts'])],
            'round_count_estimate' => ['nullable', 'integer', 'min:0'],
            'has_box'      => ['boolean'],
            'has_receipt'  => ['boolean'],
            'has_documents'=> ['boolean'],
            'document_notes'=>['nullable', 'string'],

            'included_magazines' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'included_accessories'=>['nullable', 'string'],
            'notes'        => ['nullable', 'string'],
        ],
            // Photos
            $this->photoRules('photo_1'),
            $this->photoRules('photo_2'),
            $this->photoRules('photo_3'),
            $this->photoRules('photo_4'),
            $this->photoRules('photo_5'),
            $this->photoRules('photo_6'),
            $this->photoRules('photo_7'),
            $this->photoRules('photo_8'),
            $this->photoRules('photo_9'),
            $this->photoRules('photo_10'),
        );
    }

    protected function makeUniqueSlug(string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (Post::query()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function storePhotoIfPresent($file, string $dir): ?string
    {
        if (! $file) return null;

        // Stored path example: posts/<uuid>/photo_1_abc123.jpg
        return $file->storePublicly($dir, 'public');
    }

    public function save(): void
    {
        $this->validate();

        $userId = Auth::id();
        if (! $userId) abort(403);

        DB::transaction(function () use ($userId) {

            $uuid = (string) Str::uuid();
            $slug = $this->makeUniqueSlug($this->title);
            $dir  = "posts/{$uuid}";

            // Save uploads first (so we can write paths into p_1..p_10)
            $p1  = $this->storePhotoIfPresent($this->photo_1, $dir);
            $p2  = $this->storePhotoIfPresent($this->photo_2, $dir);
            $p3  = $this->storePhotoIfPresent($this->photo_3, $dir);
            $p4  = $this->storePhotoIfPresent($this->photo_4, $dir);
            $p5  = $this->storePhotoIfPresent($this->photo_5, $dir);
            $p6  = $this->storePhotoIfPresent($this->photo_6, $dir);
            $p7  = $this->storePhotoIfPresent($this->photo_7, $dir);
            $p8  = $this->storePhotoIfPresent($this->photo_8, $dir);
            $p9  = $this->storePhotoIfPresent($this->photo_9, $dir);
            $p10 = $this->storePhotoIfPresent($this->photo_10, $dir);

            $post = Post::create([
                'uuid'          => $uuid,
                'user_id'       => $userId,
                'category'      => 'gun',
                'listing_type'  => $this->listing_type,
                'title'         => $this->title,
                'slug'          => $slug,
                'description'   => $this->description,
                'price'         => $this->price,
                'buy_min_price' => $this->buy_min_price,
                'buy_max_price' => $this->buy_max_price,
                'is_negotiable' => $this->is_negotiable,
                'condition'     => $this->post_condition,
                'location'      => $this->location,
                'status'        => 'pending',
                'expires_at'    => $this->expires_at ? \Carbon\Carbon::parse($this->expires_at) : null,

                // picture paths
                'p_1'  => $p1,
                'p_2'  => $p2,
                'p_3'  => $p3,
                'p_4'  => $p4,
                'p_5'  => $p5,
                'p_6'  => $p6,
                'p_7'  => $p7,
                'p_8'  => $p8,
                'p_9'  => $p9,
                'p_10' => $p10,
            ]);

            Gun::create([
                'post_id' => $post->id,

                'manufacturer' => $this->manufacturer,
                'model'        => $this->model,
                'variant'      => $this->variant,
                'series'       => $this->series,
                'country_of_origin' => $this->country_of_origin,

                'platform'     => $this->platform,
                'type'         => $this->type,
                'action'       => $this->action,

                'caliber'      => $this->caliber,
                'capacity'     => $this->capacity,
                'barrel_length'=> $this->barrel_length,
                'overall_length'=> $this->overall_length,
                'height'       => $this->height,
                'width'        => $this->width,
                'weight'       => $this->weight,
                'weight_unit'  => $this->weight_unit,

                'frame_material'=> $this->frame_material,
                'slide_material'=> $this->slide_material,
                'barrel_material'=> $this->barrel_material,
                'finish'       => $this->finish,
                'color'        => $this->color,
                'grip_type'    => $this->grip_type,
                'stock_type'   => $this->stock_type,
                'handguard_type'=> $this->handguard_type,
                'rail_type'    => $this->rail_type,

                'sight_type'   => $this->sight_type,
                'optic_ready'  => $this->optic_ready,
                'optic_mount_pattern' => $this->optic_mount_pattern,

                'threaded_barrel' => $this->threaded_barrel,
                'thread_pitch'    => $this->thread_pitch,
                'muzzle_device_included' => $this->muzzle_device_included,
                'muzzle_device_type' => $this->muzzle_device_type,

                'trigger_type' => $this->trigger_type,
                'trigger_pull' => $this->trigger_pull,
                'trigger_pull_unit' => $this->trigger_pull_unit,
                'has_manual_safety' => $this->has_manual_safety,
                'has_firing_pin_safety' => $this->has_firing_pin_safety,

                'sku' => $this->sku,
                'upc' => $this->upc,

                'condition' => $this->gun_condition,
                'round_count_estimate' => $this->round_count_estimate,
                'has_box' => $this->has_box,
                'has_receipt' => $this->has_receipt,
                'has_documents' => $this->has_documents,
                'document_notes' => $this->document_notes,

                'included_magazines' => $this->included_magazines,
                'included_accessories' => $this->included_accessories,

                'notes' => $this->notes,
            ]);

            Flux::toast('Gun post submitted for review.', variant: 'success');
            // $this->redirectRoute('posts.show', ['uuid' => $post->uuid], navigate: true);
            return redirect()->route('home');
        });
    }
};
?>

<div class="max-w-5xl mx-auto space-y-6">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Create Gun Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>

            <flux:badge color="blue">Category: Gun</flux:badge>
        </div>
    </flux:card>

    <form wire:submit="save" class="space-y-6">
        <flux:accordion>

            {{-- Photos (p_1..p_10) --}}
            <flux:accordion.item>
                <flux:accordion.heading>Photos</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Repeat for each slot --}}
                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_1" label="Photo 1">
                                <flux:file-upload.dropzone
                                    heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB"
                                    inline
                                />
                            </flux:file-upload>
                            <flux:error name="photo_1"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_2" label="Photo 2">
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_2"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_3" label="Photo 3">
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_3"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_4" label="Photo 4">
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_4"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_5" label="Photo 5">
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_5"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_6" label="Photo 6">
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_6"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_7" label="Photo 7" >
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_7"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_8" label="Photo 8" >
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_8"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_9" label="Photo 9" >
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_9"/>
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_10" label="Photo 10" >
                                <flux:file-upload.dropzone heading="Drop file or click to browse" text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_10"/>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Photos (p_1..p_10) --}}


            {{-- Listing --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Listing details</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Listing type <span class="text-red-500">*</span></flux:label>
                            <flux:select wire:model.live="listing_type">
                                @foreach($this->listingTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="listing_type"/>
                        </flux:field>

                        {{-- <flux:field>
                            <flux:label>Location</flux:label>
                            <flux:input wire:model.live="location" placeholder="City / Province (optional)"/>
                            <flux:error name="location"/>
                        </flux:field> --}}

                        <flux:field class="md:col-span-2">
                            <flux:label>Title <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="title" placeholder="e.g., Glock 19 Gen 5 w/ extras"/>
                            <flux:error name="title"/>
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Description <span class="text-red-500">*</span></flux:label>
                            <flux:textarea wire:model.live="description" rows="6" placeholder="Condition, inclusions, history, meetup/shipping notes..."/>
                            <flux:error name="description"/>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                General condition
                                <span class="text-red-500">*</span>
                            </flux:label>

                            <flux:select wire:model.live="post_condition">
                                <option value="">— Optional —</option>
                                @foreach($this->postConditionOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="post_condition"/>
                        </flux:field>

                        @if($this->listing_type === 'sell')
                        <flux:field>
                            <flux:label>
                                Price
                                <span class="text-red-500">*</span>
                            </flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="price" placeholder="0.00"/>
                            <flux:error name="price"/>
                        </flux:field>
                        @elseif($this->listing_type === 'buy')

                        <flux:field>
                            <flux:label>
                                Budget range (min)
                                    <span class="text-red-500">*</span>
                            </flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="buy_min_price" placeholder="0.00"/>
                            <flux:error name="buy_min_price"/>
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                Budget range (max)
                                    <span class="text-red-500">*</span>
                            </flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="buy_max_price" placeholder="0.00"/>
                            <flux:error name="buy_max_price"/>
                        </flux:field>

                        @endif





                        <div class="flex items-center gap-3 mt-7">
                            <flux:switch wire:model.live="is_negotiable" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Negotiable</div>
                                <div class="text-xs text-zinc-500">Allow offers on this listing.</div>
                            </div>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Listing --}}


            {{-- 2) Gun identification --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Gun identification</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field><flux:label>Manufacturer</flux:label><flux:input wire:model.live="manufacturer" placeholder="e.g., Glock"/><flux:error name="manufacturer"/></flux:field>
                        <flux:field><flux:label>Model</flux:label><flux:input wire:model.live="model" placeholder="e.g., 19"/><flux:error name="model"/></flux:field>
                        <flux:field><flux:label>Variant</flux:label><flux:input wire:model.live="variant" placeholder="e.g., Gen 5 / MOS"/><flux:error name="variant"/></flux:field>

                        <flux:field><flux:label>Series</flux:label><flux:input wire:model.live="series" placeholder="Optional"/><flux:error name="series"/></flux:field>
                        <flux:field><flux:label>Country of origin</flux:label><flux:input wire:model.live="country_of_origin" placeholder="Optional"/><flux:error name="country_of_origin"/></flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 2) Gun identification --}}

            {{-- 3) Classification --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Classification</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Platform</flux:label>
                            <flux:select wire:model.live="platform">
                                <option value="">— Optional —</option>
                                @foreach($this->platformOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="platform"/>
                        </flux:field>

                        <flux:field><flux:label>Type</flux:label><flux:input wire:model.live="type" placeholder="e.g., 1911, AR-15"/><flux:error name="type"/></flux:field>
                        <flux:field><flux:label>Action</flux:label><flux:input wire:model.live="action" placeholder="e.g., semi-auto, bolt, pump"/><flux:error name="action"/></flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 3) Classification --}}

            {{-- 4) Core specs --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Core specs</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <flux:field><flux:label>Caliber</flux:label><flux:input wire:model.live="caliber" placeholder="e.g., 9mm"/><flux:error name="caliber"/></flux:field>
                        <flux:field><flux:label>Capacity</flux:label><flux:input type="number" wire:model.live="capacity" placeholder="e.g., 15"/><flux:error name="capacity"/></flux:field>
                        <flux:field><flux:label>Barrel length</flux:label><flux:input type="number" step="0.01" wire:model.live="barrel_length" placeholder="e.g., 4.02"/><flux:error name="barrel_length"/></flux:field>
                        <flux:field><flux:label>Overall length</flux:label><flux:input type="number" step="0.01" wire:model.live="overall_length"/><flux:error name="overall_length"/></flux:field>

                        <flux:field><flux:label>Height</flux:label><flux:input type="number" step="0.01" wire:model.live="height"/><flux:error name="height"/></flux:field>
                        <flux:field><flux:label>Width</flux:label><flux:input type="number" step="0.01" wire:model.live="width"/><flux:error name="width"/></flux:field>
                        <flux:field><flux:label>Weight</flux:label><flux:input type="number" step="0.001" wire:model.live="weight"/><flux:error name="weight"/></flux:field>
                        <flux:field>
                            <flux:label>Weight unit</flux:label>
                            <flux:select wire:model.live="weight_unit">
                                <option value="kg">kg</option>
                                <option value="lb">lb</option>
                            </flux:select>
                            <flux:error name="weight_unit"/>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 4) Core specs --}}

            {{-- 5) Materials & finish --}}
            <flux:accordion.item>
                <flux:accordion.heading>Materials & finish</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field><flux:label>Frame material</flux:label><flux:input wire:model.live="frame_material"/><flux:error name="frame_material"/></flux:field>
                        <flux:field><flux:label>Slide material</flux:label><flux:input wire:model.live="slide_material"/><flux:error name="slide_material"/></flux:field>
                        <flux:field><flux:label>Barrel material</flux:label><flux:input wire:model.live="barrel_material"/><flux:error name="barrel_material"/></flux:field>

                        <flux:field><flux:label>Finish</flux:label><flux:input wire:model.live="finish"/><flux:error name="finish"/></flux:field>
                        <flux:field><flux:label>Color</flux:label><flux:input wire:model.live="color"/><flux:error name="color"/></flux:field>
                        <flux:field><flux:label>Grip type</flux:label><flux:input wire:model.live="grip_type"/><flux:error name="grip_type"/></flux:field>

                        <flux:field><flux:label>Stock type</flux:label><flux:input wire:model.live="stock_type"/><flux:error name="stock_type"/></flux:field>
                        <flux:field><flux:label>Handguard type</flux:label><flux:input wire:model.live="handguard_type"/><flux:error name="handguard_type"/></flux:field>
                        <flux:field>
                            <flux:label>Rail type</flux:label>
                            <flux:select wire:model.live="rail_type">
                                <option value="">— Optional —</option>
                                <option value="picatinny">Picatinny</option>
                                <option value="mlok">M-LOK</option>
                                <option value="keymod">KeyMod</option>
                                <option value="none">None</option>
                            </flux:select>
                            <flux:error name="rail_type"/>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 5) Materials & finish --}}

            {{-- 6) Sights & optics --}}
            <flux:accordion.item>
                <flux:accordion.heading>Sights & optics</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field><flux:label>Sight type</flux:label><flux:input wire:model.live="sight_type"/><flux:error name="sight_type"/></flux:field>

                        <div class="flex items-center gap-3 mt-7">
                            <flux:switch wire:model.live="optic_ready" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Optic ready</div>
                                <div class="text-xs text-zinc-500">Slide/cut ready for optics.</div>
                            </div>
                        </div>

                        <flux:field>
                            <flux:label>Optic mount pattern</flux:label>
                            <flux:input wire:model.live="optic_mount_pattern" placeholder="e.g., RMR, MOS"/>
                            <flux:error name="optic_mount_pattern"/>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 6) Sights & optics --}}

            {{-- 7) Barrel & muzzle --}}
            <flux:accordion.item>
                <flux:accordion.heading>Barrel & muzzle</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="threaded_barrel" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Threaded barrel</div>
                                <div class="text-xs text-zinc-500">Barrel has threads.</div>
                            </div>
                        </div>

                        <flux:field>
                            <flux:label>Thread pitch</flux:label>
                            <flux:input wire:model.live="thread_pitch" placeholder="e.g., 1/2x28"/>
                            <flux:error name="thread_pitch"/>
                        </flux:field>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="muzzle_device_included" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Muzzle device included</div>
                                <div class="text-xs text-zinc-500">Comp/flash hider included.</div>
                            </div>
                        </div>

                        <flux:field class="md:col-span-3">
                            <flux:label>Muzzle device type</flux:label>
                            <flux:input wire:model.live="muzzle_device_type" placeholder="e.g., compensator, flash hider"/>
                            <flux:error name="muzzle_device_type"/>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 7) Barrel & muzzle --}}

            {{-- 8) Safety & trigger --}}
            <flux:accordion.item>
                <flux:accordion.heading>Safety & trigger</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <flux:field class="md:col-span-2">
                            <flux:label>Trigger type</flux:label>
                            <flux:input wire:model.live="trigger_type"/>
                            <flux:error name="trigger_type"/>
                        </flux:field>

                        <flux:field>
                            <flux:label>Trigger pull</flux:label>
                            <flux:input type="number" step="0.01" wire:model.live="trigger_pull"/>
                            <flux:error name="trigger_pull"/>
                        </flux:field>

                        <flux:field>
                            <flux:label>Unit</flux:label>
                            <flux:select wire:model.live="trigger_pull_unit">
                                <option value="lb">lb</option>
                                <option value="kg">kg</option>
                            </flux:select>
                            <flux:error name="trigger_pull_unit"/>
                        </flux:field>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="has_manual_safety" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Manual safety</div>
                                <div class="text-xs text-zinc-500">Has a manual safety.</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="has_firing_pin_safety" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Firing pin safety</div>
                                <div class="text-xs text-zinc-500">Has firing pin safety.</div>
                            </div>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 8) Safety & trigger --}}

            {{-- 9) Metadata & inclusions --}}
            <flux:accordion.item>
                <flux:accordion.heading>Metadata & inclusions</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field><flux:label>SKU</flux:label><flux:input wire:model.live="sku"/><flux:error name="sku"/></flux:field>
                        <flux:field><flux:label>UPC</flux:label><flux:input wire:model.live="upc"/><flux:error name="upc"/></flux:field>

                        <flux:field>
                            <flux:label>Gun condition</flux:label>
                            <flux:select wire:model.live="gun_condition">
                                <option value="">— Optional —</option>
                                <option value="new">New</option>
                                <option value="like_new">Like New</option>
                                <option value="used">Used</option>
                                <option value="refurbished">Refurbished</option>
                                <option value="for_parts">For Parts</option>
                            </flux:select>
                            <flux:error name="gun_condition"/>
                        </flux:field>

                        <flux:field>
                            <flux:label>Round count estimate</flux:label>
                            <flux:input type="number" wire:model.live="round_count_estimate"/>
                            <flux:error name="round_count_estimate"/>
                        </flux:field>

                        <flux:field>
                            <flux:label>Included magazines</flux:label>
                            <flux:input type="number" wire:model.live="included_magazines"/>
                            <flux:error name="included_magazines"/>
                        </flux:field>

                        <div class="flex flex-wrap items-center gap-4 md:col-span-3">
                            <label class="flex items-center gap-2">
                                <flux:checkbox wire:model.live="has_box" />
                                <span class="text-sm">Has box</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <flux:checkbox wire:model.live="has_receipt" />
                                <span class="text-sm">Has receipt</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <flux:checkbox wire:model.live="has_documents" />
                                <span class="text-sm">Has documents (flag only)</span>
                            </label>
                        </div>

                        <flux:field class="md:col-span-3">
                            <flux:label>Document notes</flux:label>
                            <flux:textarea wire:model.live="document_notes" rows="3"/>
                            <flux:error name="document_notes"/>
                        </flux:field>

                        <flux:field class="md:col-span-3">
                            <flux:label>Included accessories</flux:label>
                            <flux:textarea wire:model.live="included_accessories" rows="3" placeholder="case, sling, optic, etc."/>
                            <flux:error name="included_accessories"/>
                        </flux:field>

                        <flux:field class="md:col-span-3">
                            <flux:label>Extra notes</flux:label>
                            <flux:textarea wire:model.live="notes" rows="3"/>
                            <flux:error name="notes"/>
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- 9) Metadata & inclusions --}}

        </flux:accordion>

        <div class="flex items-center justify-end gap-3">
            <flux:button type="submit" variant="primary">Submit for review</flux:button>
        </div>
    </form>
</div>
