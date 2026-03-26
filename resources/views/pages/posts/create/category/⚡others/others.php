<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Other;
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

    public string $category = 'others';

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
     * NOTE: we keep p_1..p_10 in DB as string paths (set during save).
     */
    public $primary_photo = null;
    public $other_photos = null;

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

    public function removePrimaryPhoto()
    {
        $this->primary_photo->delete();
        $this->primary_photo = null;
    }

    public function removeOtherPhoto(int $index)
    {
        if (isset($this->other_photos[$index])) {
            $this->other_photos[$index]->delete();
            unset($this->other_photos[$index]);
            $this->other_photos = array_values($this->other_photos); // reindex
        }
    }

    // ===================================================================================== OTHERS PROPERTIES =====================================================================================

    // Classification
    public ?string $weapon_type = null;
    public ?string $subcategory = null;
    public ?string $intended_use = null;

    // Identity
    public ?string $brand = null;
    public ?string $model = null;
    public ?string $variant = null;
    public ?string $country_of_origin = null;

    // Blade / Head specs
    public ?string $blade_type = null;
    public ?string $edge_type = null;
    public ?string $steel_type = null;
    public ?string $finish = null;
    public ?bool $full_tang = null;

    // Dimensions
    public ?float $overall_length = null;
    public ?float $blade_length = null;
    public ?float $head_length = null;
    public ?float $handle_length = null;
    public string $length_unit = 'cm';

    // Weight
    public ?float $weight = null;
    public string $weight_unit = 'kg';

    // Handle / grip
    public ?string $handle_material = null;
    public ?string $handle_color = null;
    public ?string $grip_texture = null;

    // Mechanism & lock
    public bool $is_folding = false;
    public ?string $opening_mechanism = null;
    public ?string $lock_type = null;

    // Sheath / scabbard / holster
    public bool $includes_sheath = false;
    public ?string $sheath_type = null;
    public ?string $carry_type = null;

    // Condition & packaging
    public ?string $condition = null;
    public bool $has_box = false;
    public bool $has_receipt = false;

    // Included items / notes
    public ?string $package_includes = null;
    public ?string $notes = null;

    // Commercial Identifiers
    public ?string $sku = null;
    public ?string $upc = null;

    public function rules(): array
    {
        return array_merge([

            // POST RULES (required)
            'category' => ['required', Rule::in(['others'])],
            'listing_type' => ['required', Rule::in(['buy', 'sell'])],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'price' => [Rule::requiredIf(fn() => $this->listing_type === 'sell'), 'nullable', 'numeric', 'min:0'],
            'buy_min_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
            'buy_max_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
            'is_negotiable' => ['boolean'],
            'post_condition' => ['required', 'string', 'max:50'],
            'primary_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
            'other_photos' => ['nullable', 'array', 'max:9'],
            'other_photos.*' => ['file', 'mimes:jpg,jpeg,png,gif', 'max:5080'],

            // Others
            'weapon_type' => ['nullable', Rule::in(['knife', 'sword', 'machete', 'axe', 'tomahawk', 'baton', 'stick', 'tonfa', 'spear', 'other'])],
            'subcategory' => ['nullable', 'string', 'max:255'],
            'intended_use' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'variant' => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'blade_type' => ['nullable', 'string', 'max:255'],
            'edge_type' => ['nullable', Rule::in(['plain', 'serrated', 'combo'])],
            'steel_type' => ['nullable', 'string', 'max:255'],
            'finish' => ['nullable', 'string', 'max:255'],
            'full_tang' => ['nullable', 'boolean'],
            'overall_length' => ['nullable', 'numeric', 'min:0'],
            'blade_length' => ['nullable', 'numeric', 'min:0'],
            'head_length' => ['nullable', 'numeric', 'min:0'],
            'handle_length' => ['nullable', 'numeric', 'min:0'],
            'length_unit' => ['required', Rule::in(['cm', 'in'])],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'weight_unit' => ['required', Rule::in(['kg', 'lb'])],
            'handle_material' => ['nullable', 'string', 'max:255'],
            'handle_color' => ['nullable', 'string', 'max:255'],
            'grip_texture' => ['nullable', 'string', 'max:255'],
            'is_folding' => ['boolean'],
            'opening_mechanism' => ['nullable', 'string', 'max:255'],
            'lock_type' => ['nullable', 'string', 'max:255'],
            'includes_sheath' => ['boolean'],
            'sheath_type' => ['nullable', 'string', 'max:255'],
            'carry_type' => ['nullable', 'string', 'max:255'],
            'condition' => ['nullable', Rule::in(['new', 'like_new', 'used', 'refurbished', 'for_parts'])],
            'has_box' => ['boolean'],
            'has_receipt' => ['boolean'],
            'package_includes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', 'max:255'],
            'upc' => ['nullable', 'string', 'max:255'],
        ]);
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

        // Stored path example: posts/<uuid>/photo_1_abc123.jpg
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

            $p1 = $this->storePhotoIfPresent($this->primary_photo, $dir);

            $other_photos_paths = [];
            $other_photos_count = count($this->other_photos ?? []);

            if ($other_photos_count >= 1) {
                foreach ($this->other_photos as $index => $photo) {
                    if ($index >= 9) {
                        break;
                    } // limit to 9 additional photos

                    $storedPath = $this->storePhotoIfPresent($photo, $dir);
                    if ($storedPath) {
                        $other_photos_paths[] = $storedPath;
                    }
                }
            }

            $p2 = $other_photos_paths[0] ?? null;
            $p3 = $other_photos_paths[1] ?? null;
            $p4 = $other_photos_paths[2] ?? null;
            $p5 = $other_photos_paths[3] ?? null;
            $p6 = $other_photos_paths[4] ?? null;
            $p7 = $other_photos_paths[5] ?? null;
            $p8 = $other_photos_paths[6] ?? null;
            $p9 = $other_photos_paths[7] ?? null;
            $p10 = $other_photos_paths[8] ?? null;

            $post = Post::create([
                'uuid' => $uuid,
                'user_id' => $userId,
                'category' => 'others',
                'listing_type' => $this->listing_type,
                'title' => $this->title,
                'slug' => $slug,
                'description' => $this->description,
                'price' => $this->price,
                'buy_min_price' => $this->buy_min_price,
                'buy_max_price' => $this->buy_max_price,
                'is_negotiable' => $this->is_negotiable,
                'condition' => $this->post_condition,
                'status' => 'pending',

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

            Other::create([
                'post_id' => $post->id,

                // Classification
                'weapon_type' => $this->weapon_type,
                'subcategory' => $this->subcategory,
                'intended_use' => $this->intended_use,

                // Identity
                'brand' => $this->brand,
                'model' => $this->model,
                'variant' => $this->variant,
                'country_of_origin' => $this->country_of_origin,

                // Blade / Head specs
                'blade_type' => $this->blade_type,
                'edge_type' => $this->edge_type,
                'steel_type' => $this->steel_type,
                'finish' => $this->finish,
                'full_tang' => $this->full_tang,

                // Dimensions
                'overall_length' => $this->overall_length,
                'blade_length' => $this->blade_length,
                'head_length' => $this->head_length,
                'handle_length' => $this->handle_length,
                'length_unit' => $this->length_unit,

                // Weight
                'weight' => $this->weight,
                'weight_unit' => $this->weight_unit,

                // Handle / grip
                'handle_material' => $this->handle_material,
                'handle_color' => $this->handle_color,
                'grip_texture' => $this->grip_texture,

                // Mechanism & lock
                'is_folding' => $this->is_folding,
                'opening_mechanism' => $this->opening_mechanism,
                'lock_type' => $this->lock_type,

                // Sheath / scabbard / holster
                'includes_sheath' => $this->includes_sheath,
                'sheath_type' => $this->sheath_type,
                'carry_type' => $this->carry_type,

                // Condition & packaging
                'condition' => $this->condition,
                'has_box' => $this->has_box,
                'has_receipt' => $this->has_receipt,

                // Included items / notes
                'package_includes' => $this->package_includes,
                'notes' => $this->notes,

                // Commercial Identifiers
                'sku' => $this->sku,
                'upc' => $this->upc,
            ]);

            Flux::toast('Others post submitted for review. Add more post', variant: 'success');
            $this->reset();
        });
    }
};
