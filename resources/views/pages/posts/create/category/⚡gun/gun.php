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

new class extends Component {
    use WithFileUploads;

    public string $category = 'gun';

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





    // ===================================================================================== GUN PROPERTIES =====================================================================================

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
            'rifle' => 'Rifle',
            'shotgun' => 'Shotgun',
            'pcc' => 'PCC',
            'smg' => 'SMG',
            'sniper' => 'Sniper',
            'other' => 'Other',
        ];
    }



    public function rules(): array
    {
        return array_merge([

            // POST RULES (required)
            'category' => ['required', Rule::in(['gun'])],
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

            // Gun
            'manufacturer' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'variant' => ['nullable', 'string', 'max:255'],
            'series' => ['nullable', 'string', 'max:255'],
            'country_of_origin' => ['nullable', 'string', 'max:255'],
            'platform' => ['required', Rule::in(['handgun', 'rifle', 'shotgun', 'pcc', 'smg', 'sniper', 'other'])],
            'type' => ['required', 'string', 'max:255'],
            'action' => ['required', 'string', 'max:255'],
            'caliber' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'barrel_length' => ['nullable', 'numeric', 'min:0'],
            'overall_length' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'weight_unit' => ['required', Rule::in(['kg', 'lb'])],
            'frame_material' => ['nullable', 'string', 'max:255'],
            'slide_material' => ['nullable', 'string', 'max:255'],
            'barrel_material' => ['nullable', 'string', 'max:255'],
            'finish' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'grip_type' => ['nullable', 'string', 'max:255'],
            'stock_type' => ['nullable', 'string', 'max:255'],
            'handguard_type' => ['nullable', 'string', 'max:255'],
            'rail_type' => ['nullable', 'string', 'max:255'],
            'sight_type' => ['nullable', 'string', 'max:255'],
            'optic_ready' => ['boolean'],
            'optic_mount_pattern' => ['nullable', 'string', 'max:255'],
            'threaded_barrel' => ['boolean'],
            'thread_pitch' => ['nullable', 'string', 'max:255'],
            'muzzle_device_included' => ['boolean'],
            'muzzle_device_type' => ['nullable', 'string', 'max:255'],
            'trigger_type' => ['nullable', 'string', 'max:255'],
            'trigger_pull' => ['nullable', 'numeric', 'min:0'],
            'trigger_pull_unit' => ['required', Rule::in(['lb', 'kg'])],
            'has_manual_safety' => ['boolean'],
            'has_firing_pin_safety' => ['boolean'],
            'sku' => ['nullable', 'string', 'max:255'],
            'upc' => ['nullable', 'string', 'max:255'],
            'gun_condition' => ['nullable', Rule::in(['new', 'like_new', 'used', 'refurbished', 'for_parts'])],
            'round_count_estimate' => ['nullable', 'integer', 'min:0'],
            'has_box' => ['boolean'],
            'has_receipt' => ['boolean'],
            'has_documents' => ['boolean'],
            'document_notes' => ['nullable', 'string'],
            'included_magazines' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'included_accessories' => ['nullable', 'string'],
            'notes' => ['nullable', 'string']
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
                'category' => 'gun',
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

            Gun::create([
                'post_id' => $post->id,

                'manufacturer' => $this->manufacturer,
                'model' => $this->model,
                'variant' => $this->variant,
                'series' => $this->series,
                'country_of_origin' => $this->country_of_origin,

                'platform' => $this->platform,
                'type' => $this->type,
                'action' => $this->action,

                'caliber' => $this->caliber,
                'capacity' => $this->capacity,
                'barrel_length' => $this->barrel_length,
                'overall_length' => $this->overall_length,
                'height' => $this->height,
                'width' => $this->width,
                'weight' => $this->weight,
                'weight_unit' => $this->weight_unit,

                'frame_material' => $this->frame_material,
                'slide_material' => $this->slide_material,
                'barrel_material' => $this->barrel_material,
                'finish' => $this->finish,
                'color' => $this->color,
                'grip_type' => $this->grip_type,
                'stock_type' => $this->stock_type,
                'handguard_type' => $this->handguard_type,
                'rail_type' => $this->rail_type,

                'sight_type' => $this->sight_type,
                'optic_ready' => $this->optic_ready,
                'optic_mount_pattern' => $this->optic_mount_pattern,

                'threaded_barrel' => $this->threaded_barrel,
                'thread_pitch' => $this->thread_pitch,
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

            Flux::toast('Gun post submitted for review. Add more post', variant: 'success');
            $this->reset();
        });
    }
};
