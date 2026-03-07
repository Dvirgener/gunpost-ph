 <?php

use App\Models\posts\Post;
use App\Models\posts\categories\Ammunition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Flux\Flux;

new class extends Component {
    use WithFileUploads;

    public string $category = 'ammunition';

    // Post (required ones marked in UI with *)
    public string $listing_type = 'sell'; // * buy|sell
    public string $title = ''; // *
    public string $description = ''; // *
    public ?float $price = null; // * if sell
    public ?float $buy_min_price = null; // * if buy
    public ?float $buy_max_price = null; // * if buy
    public bool $is_negotiable = false;
    public ?string $post_condition = null;
    public ?string $location = null;
    public ?string $expires_at = null;

    /**
     * File uploads (each maps to posts.p_1..p_10)
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

    // Ammunition
    public ?string $brand = null;
    public ?string $product_line = null;
    public ?string $caliber = null;
    public ?string $bullet_type = null;
    public ?string $grain = null;
    public ?string $case_material = null;
    public ?string $primer_type = null;
    public bool $corrosive = false;

    public ?int $total_rounds = null;
    public ?int $boxes = null;
    public ?int $rounds_per_box = null;

    public ?string $lot_number = null;
    public ?string $sku = null;
    public ?string $upc = null;

    public ?string $condition = null;
    public bool $reloads = false;
    public ?string $notes = null;

    #[Computed]
    public function listingTypeOptions(): array
    {
        return ['sell' => 'Sell', 'buy' => 'Buy'];
    }

    #[Computed]
    public function postConditionOptions(): array
    {
        return [
            'new' => 'New',
            'used' => 'Used',
            'like_new' => 'Like New',
            'refurbished' => 'Refurbished',
            'for_parts' => 'For Parts',
        ];
    }

    private function photoRules(string $field): array
    {
        return [$field => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240']];
    }

    public function rules(): array
    {
        return array_merge(
            [
                // Post (required)
                'category' => ['required', Rule::in(['ammunition'])],
                'listing_type' => ['required', Rule::in(['buy', 'sell'])],
                'title' => ['required', 'string', 'min:5', 'max:255'],
                'description' => ['required', 'string', 'min:20'],
                'price' => [Rule::requiredIf(fn() => $this->listing_type === 'sell'), 'nullable', 'numeric', 'min:0'],
                'buy_min_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
                'buy_max_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
                'is_negotiable' => ['boolean'],
                'post_condition' => ['nullable', 'string', 'max:50'],
                'location' => ['nullable', 'string', 'max:255'],
                'expires_at' => ['nullable', 'date'],

                // Ammunition
                'brand' => ['nullable', 'string', 'max:255'],
                'product_line' => ['nullable', 'string', 'max:255'],
                'caliber' => ['nullable', 'string', 'max:255'],
                'bullet_type' => ['nullable', 'string', 'max:255'],
                'grain' => ['nullable', 'string', 'max:255'],
                'case_material' => ['nullable', 'string', 'max:255'],
                'primer_type' => ['nullable', 'string', 'max:255'],
                'corrosive' => ['boolean'],

                'total_rounds' => ['nullable', 'integer', 'min:0'],
                'boxes' => ['nullable', 'integer', 'min:0'],
                'rounds_per_box' => ['nullable', 'integer', 'min:0'],

                'lot_number' => ['nullable', 'string', 'max:255'],
                'sku' => ['nullable', 'string', 'max:255'],
                'upc' => ['nullable', 'string', 'max:255'],

                'condition' => ['nullable', Rule::in(['factory_new', 'sealed', 'opened', 'mixed', 'other'])],
                'reloads' => ['boolean'],
                'notes' => ['nullable', 'string'],
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
        if (!$file) {
            return null;
        }

        return $file->storePublicly($dir, 'public');
    }

    public function save(): void
    {
        $this->validate();

        $userId = Auth::id();
        if (!$userId) {
            abort(403);
        }

        DB::transaction(function () use ($userId) {
            $uuid = (string) Str::uuid();
            $slug = $this->makeUniqueSlug($this->title);
            $dir = "posts/{$uuid}";

            // Save uploads first (so we can write paths into p_1..p_10)
            $p1 = $this->storePhotoIfPresent($this->photo_1, $dir);
            $p2 = $this->storePhotoIfPresent($this->photo_2, $dir);
            $p3 = $this->storePhotoIfPresent($this->photo_3, $dir);
            $p4 = $this->storePhotoIfPresent($this->photo_4, $dir);
            $p5 = $this->storePhotoIfPresent($this->photo_5, $dir);
            $p6 = $this->storePhotoIfPresent($this->photo_6, $dir);
            $p7 = $this->storePhotoIfPresent($this->photo_7, $dir);
            $p8 = $this->storePhotoIfPresent($this->photo_8, $dir);
            $p9 = $this->storePhotoIfPresent($this->photo_9, $dir);
            $p10 = $this->storePhotoIfPresent($this->photo_10, $dir);

            $post = Post::create([
                'uuid' => $uuid,
                'user_id' => $userId,
                'category' => 'ammunition',
                'listing_type' => $this->listing_type,
                'title' => $this->title,
                'slug' => $slug,
                'description' => $this->description,
                'price' => $this->price,
                'buy_min_price' => $this->buy_min_price,
                'buy_max_price' => $this->buy_max_price,
                'is_negotiable' => $this->is_negotiable,
                'condition' => $this->post_condition,
                'location' => $this->location,
                'status' => 'pending',
                'expires_at' => $this->expires_at ? \Carbon\Carbon::parse($this->expires_at) : null,

                // picture paths
                'p_1' => $p1,
                'p_2' => $p2,
                'p_3' => $p3,
                'p_4' => $p4,
                'p_5' => $p5,
                'p_6' => $p6,
                'p_7' => $p7,
                'p_8' => $p8,
                'p_9' => $p9,
                'p_10' => $p10,
            ]);

            Ammunition::create([
                'post_id' => $post->id,

                'brand' => $this->brand,
                'product_line' => $this->product_line,
                'caliber' => $this->caliber,
                'bullet_type' => $this->bullet_type,
                'grain' => $this->grain,
                'case_material' => $this->case_material,
                'primer_type' => $this->primer_type,
                'corrosive' => $this->corrosive,

                'total_rounds' => $this->total_rounds,
                'boxes' => $this->boxes,
                'rounds_per_box' => $this->rounds_per_box,

                'lot_number' => $this->lot_number,
                'sku' => $this->sku,
                'upc' => $this->upc,

                'condition' => $this->condition,
                'reloads' => $this->reloads,

                'notes' => $this->notes,
            ]);

            Flux::toast('Ammunition post submitted for review.', variant: 'success');
            return redirect()->route('home');
        });
    }
};
?>

<div class="max-w-5xl mx-auto space-y-6">
    <flux:card>
        <div class="flex items-start justify-between gap-4">
            <div class="space-y-1">
                <flux:heading size="lg">Create Ammunition Post</flux:heading>
                <flux:subheading>Fields with <span class="font-semibold">*</span> are required.</flux:subheading>
            </div>

            <flux:badge color="blue">Category: Ammunition</flux:badge>
        </div>
    </flux:card>

    <form action="" wire:submit="save">
        <flux:accordion>


            {{-- Photos (p_1..p_10) --}}
            <flux:accordion.item>
                <flux:accordion.heading>Photos</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Repeat for each slot --}}
                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_1" label="Photo 1">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_1" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_2" label="Photo 2">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_2" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_3" label="Photo 3">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_3" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_4" label="Photo 4">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_4" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_5" label="Photo 5">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_5" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_6" label="Photo 6">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_6" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_7" label="Photo 7">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_7" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_8" label="Photo 8">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_8" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_9" label="Photo 9">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_9" />
                        </div>

                        <div class="space-y-2">
                            <flux:file-upload wire:model="photo_10" label="Photo 10">
                                <flux:file-upload.dropzone heading="Drop file or click to browse"
                                    text="JPG, PNG, GIF up to 10MB" inline />
                            </flux:file-upload>
                            <flux:error name="photo_10" />
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
                                @foreach ($this->listingTypeOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="listing_type" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Title <span class="text-red-500">*</span></flux:label>
                            <flux:input wire:model.live="title" placeholder="e.g., 115gr 9mm FMJ - 500 rounds" />
                            <flux:error name="title" />
                        </flux:field>

                        <flux:field class="md:col-span-2">
                            <flux:label>Description <span class="text-red-500">*</span></flux:label>
                            <flux:textarea wire:model.live="description" rows="6"
                                placeholder="Condition, quantity, special notes, shipping info..." />
                            <flux:error name="description" />
                        </flux:field>

                        <flux:field>
                            <flux:label>
                                General condition
                                <span class="text-red-500">*</span>
                            </flux:label>

                            <flux:select wire:model.live="post_condition">
                                <option value="">— Optional —</option>
                                @foreach ($this->postConditionOptions as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </flux:select>
                            <flux:error name="post_condition" />
                        </flux:field>

                        @if ($this->listing_type === 'sell')
                            <flux:field>
                                <flux:label>
                                    Price
                                    <span class="text-red-500">*</span>
                                </flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="price" placeholder="0.00" />
                                <flux:error name="price" />
                            </flux:field>
                        @elseif($this->listing_type === 'buy')
                            <flux:field>
                                <flux:label>
                                    Budget range (min)
                                    <span class="text-red-500">*</span>
                                </flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="buy_min_price"
                                    placeholder="0.00" />
                                <flux:error name="buy_min_price" />
                            </flux:field>

                            <flux:field>
                                <flux:label>
                                    Budget range (max)
                                    <span class="text-red-500">*</span>
                                </flux:label>
                                <flux:input type="number" step="0.01" wire:model.live="buy_max_price"
                                    placeholder="0.00" />
                                <flux:error name="buy_max_price" />
                            </flux:field>
                        @endif

                        <flux:field>
                            <flux:label>Location</flux:label>
                            <flux:input wire:model.live="location" placeholder="City / Province (optional)" />
                            <flux:error name="location" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Expires at</flux:label>
                            <flux:input wire:model.live="expires_at" type="date" />
                            <flux:error name="expires_at" />
                        </flux:field>

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


            {{-- Ammunition identification --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Ammunition identification</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Brand</flux:label>
                            <flux:input wire:model.live="brand" placeholder="e.g., Federal, Winchester" />
                            <flux:error name="brand" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Product line</flux:label>
                            <flux:input wire:model.live="product_line" placeholder="e.g., American Eagle" />
                            <flux:error name="product_line" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Caliber</flux:label>
                            <flux:input wire:model.live="caliber" placeholder="e.g., 9mm, .45 ACP" />
                            <flux:error name="caliber" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Ammunition identification --}}

            {{-- Ammunition specs --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Ammunition specifications</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Bullet type</flux:label>
                            <flux:input wire:model.live="bullet_type" placeholder="e.g., FMJ, JHP, FMJBT" />
                            <flux:error name="bullet_type" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Grain (weight)</flux:label>
                            <flux:input wire:model.live="grain" placeholder="e.g., 115gr, 147gr" />
                            <flux:error name="grain" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Case material</flux:label>
                            <flux:input wire:model.live="case_material" placeholder="e.g., Brass, Steel" />
                            <flux:error name="case_material" />
                        </flux:field>

                        <flux:field>
                            <flux:label>Primer type</flux:label>
                            <flux:input wire:model.live="primer_type" placeholder="e.g., Boxer, Berdan" />
                            <flux:error name="primer_type" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Ammunition specs --}}

            {{-- Quantity & packaging --}}
            <flux:accordion.item expanded>
                <flux:accordion.heading>Quantity & packaging</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Total rounds</flux:label>
                            <flux:input type="number" wire:model.live="total_rounds" placeholder="e.g., 500" />
                            <flux:error name="total_rounds" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Number of boxes</flux:label>
                            <flux:input type="number" wire:model.live="boxes" placeholder="e.g., 5" />
                            <flux:error name="boxes" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Rounds per box</flux:label>
                            <flux:input type="number" wire:model.live="rounds_per_box" placeholder="e.g., 100" />
                            <flux:error name="rounds_per_box" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Quantity & packaging --}}

            {{-- Lot & metadata --}}
            <flux:accordion.item>
                <flux:accordion.heading>Lot & SKU information</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Lot number</flux:label>
                            <flux:input wire:model.live="lot_number" placeholder="e.g., LOT123456" />
                            <flux:error name="lot_number" />
                        </flux:field>
                        <flux:field>
                            <flux:label>SKU</flux:label>
                            <flux:input wire:model.live="sku" />
                            <flux:error name="sku" />
                        </flux:field>
                        <flux:field>
                            <flux:label>UPC</flux:label>
                            <flux:input wire:model.live="upc" />
                            <flux:error name="upc" />
                        </flux:field>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Lot & metadata --}}

            {{-- Condition & special --}}
            <flux:accordion.item>
                <flux:accordion.heading>Condition & special flags</flux:accordion.heading>
                <flux:accordion.content>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <flux:field>
                            <flux:label>Condition</flux:label>
                            <flux:select wire:model.live="condition">
                                <option value="">— Optional —</option>
                                <option value="factory_new">Factory New</option>
                                <option value="sealed">Sealed</option>
                                <option value="opened">Opened</option>
                                <option value="mixed">Mixed</option>
                                <option value="other">Other</option>
                            </flux:select>
                            <flux:error name="condition" />
                        </flux:field>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="corrosive" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Corrosive</div>
                                <div class="text-xs text-zinc-500">Contains corrosive primers.</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 mt-1">
                            <flux:switch wire:model.live="reloads" />
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Reloads</div>
                                <div class="text-xs text-zinc-500">Reloaded ammunition.</div>
                            </div>
                        </div>
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Condition & special --}}

            {{-- Notes --}}
            <flux:accordion.item>
                <flux:accordion.heading>Additional notes</flux:accordion.heading>
                <flux:accordion.content>
                    <flux:field class="md:col-span-3">
                        <flux:label>Notes</flux:label>
                        <flux:textarea wire:model.live="notes" rows="4"
                            placeholder="Any additional information..." />
                        <flux:error name="notes" />
                    </flux:field>
                </flux:accordion.content>
            </flux:accordion.item>
            {{-- Notes --}}

        </flux:accordion>

        <div class="flex items-center justify-end gap-3">
            <flux:button type="submit" variant="primary">Submit for review</flux:button>
        </div>
    </form>
</div>
