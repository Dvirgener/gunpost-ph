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

    // ===================================================================================== AMMO PROPERTIES =====================================================================================
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
                'post_condition' => ['required', 'string', 'max:50'],
                'primary_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
                'other_photos' => ['nullable', 'array', 'max:9'],
                'other_photos.*' => ['file', 'mimes:jpg,jpeg,png,gif', 'max:5080'],

                // Ammunition
                'brand' => ['required', 'string', 'max:255'],
                'product_line' => ['required', 'string', 'max:255'],
                'caliber' => ['required', 'string', 'max:255'],
                'bullet_type' => ['required', 'string', 'max:255'],
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
            ]
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

           $p1 = $this->storePhotoIfPresent($this->primary_photo, $dir);

            $other_photos_count = count($this->other_photos ?? []);

            if ($other_photos_count >= 1) {
                foreach ($this->other_photos as $index => $photo) {
                    if ($index >= 9) {
                        break;
                    } // limit to 9 additional photos

                    $storedPath = $this->storePhotoIfPresent($photo, $dir);
                    $other_photos_paths[] = $storedPath;
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

            Flux::toast('Ammunition post submitted for review. Add more post', variant: 'success');
            $this->reset();
        });
    }
};
