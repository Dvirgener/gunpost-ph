<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Other;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Flux\Flux;

new class extends Component {
    use WithFileUploads;

    public Post $post;

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
    public $uploaded_primary_photo = null;

    public $uploaded_other_photos = []; // for loop in blade to avoid copy/paste for photo_2..photo_10

    public $primary_photo = null; // for wire:model of primary photo upload
    public $other_photos = []; // for wire:model of other photo uploads (keys 0..8 for photo_2..photo_10)

    // Other
    public ?string $weapon_type = null;
    public ?string $subcategory = null;
    public ?string $intended_use = null;
    public ?string $brand = null;
    public ?string $model = null;
    public ?string $variant = null;
    public ?string $country_of_origin = null;
    public ?string $blade_type = null;
    public ?string $edge_type = null;
    public ?string $steel_type = null;
    public ?string $finish = null;
    public  $full_tang = false;
    public ?float $overall_length = null;
    public ?float $blade_length = null;
    public ?float $head_length = null;
    public ?float $handle_length = null;
    public string $length_unit = 'cm';
    public ?float $weight = null;
    public string $weight_unit = 'kg';
    public ?string $handle_material = null;
    public ?string $handle_color = null;
    public ?string $grip_texture = null;
    public  $is_folding = false;
    public ?string $opening_mechanism = null;
    public ?string $lock_type = null;
    public  $includes_sheath = false;
    public ?string $sheath_type = null;
    public ?string $carry_type = null;
    public ?string $condition = null;
    public  $has_box = false;
    public  $has_receipt = false;
    public ?string $package_includes = null;
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
        // 10MB max (Livewire "max" is KB)
        return [$field => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240']];
    }

    public function removePhoto($num){
        $photo_num = $num + 2; // because photo_1 maps to p_1 etc
        Storage::disk('public')->delete($this->post->{"p_$photo_num"});
        $this->post->update(["p_$photo_num" => null]);
        $this->uploaded_other_photos[$num] = null; // update uploaded_other_photos array for UI
        Flux::toast("Photo removed.", variant: 'success');
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

    public function rules(): array
    {
        return array_merge(
            [
                // Post (required)
                'category' => ['required', Rule::in(['others'])],
                'listing_type' => ['required', Rule::in(['buy', 'sell'])],
                'title' => ['required', 'string', 'min:5', 'max:255'],
                'description' => ['required', 'string', 'min:20'],
                'price' => [Rule::requiredIf(fn() => $this->listing_type === 'sell'), 'nullable', 'numeric', 'min:0'],
                'buy_min_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
                'buy_max_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
                'is_negotiable' => ['boolean'],
                'post_condition' => ['required', 'string', 'max:50'],
                'location' => ['nullable', 'string', 'max:255'],
                'expires_at' => ['nullable', 'date'],

                // Other
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
                'full_tang' => ['boolean'],
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
                'other_photos' => ['nullable', 'array', 'max:9'],
                'other_photos.*' => ['file', 'mimes:jpg,jpeg,png,gif', 'max:5080'],
            ],
        );
    }

    public function mount(Post $post): void
    {
        $this->post = $post;

        // populate basic post fields
        $this->listing_type = $post->listing_type;
        $this->title = $post->title;
        $this->description = $post->description;
        $this->price = $post->price;
        $this->buy_min_price = $post->buy_min_price;
        $this->buy_max_price = $post->buy_max_price;
        $this->is_negotiable = $post->is_negotiable;
        $this->post_condition = $post->condition;
        $this->location = $post->location;
        $this->expires_at = $post->expires_at?->format('Y-m-d');

        $this->uploaded_primary_photo = $post->p_1;

        $this->uploaded_other_photos = [
            $post->p_2,
            $post->p_3,
            $post->p_4,
            $post->p_5,
            $post->p_6,
            $post->p_7,
            $post->p_8,
            $post->p_9,
            $post->p_10,
        ];

        $other = $post->other ?? new Other();
        $this->weapon_type = $other->weapon_type;
        $this->subcategory = $other->subcategory;
        $this->intended_use = $other->intended_use;
        $this->brand = $other->brand;
        $this->model = $other->model;
        $this->variant = $other->variant;
        $this->country_of_origin = $other->country_of_origin;
        $this->blade_type = $other->blade_type;
        $this->edge_type = $other->edge_type;
        $this->steel_type = $other->steel_type;
        $this->finish = $other->finish;
        $this->full_tang = $other->full_tang;
        $this->overall_length = $other->overall_length;
        $this->blade_length = $other->blade_length;
        $this->head_length = $other->head_length;
        $this->handle_length = $other->handle_length;
        $this->length_unit = $other->length_unit ?: 'cm';
        $this->weight = $other->weight;
        $this->weight_unit = $other->weight_unit ?: 'kg';
        $this->handle_material = $other->handle_material;
        $this->handle_color = $other->handle_color;
        $this->grip_texture = $other->grip_texture;
        $this->is_folding = $other->is_folding;
        $this->opening_mechanism = $other->opening_mechanism;
        $this->lock_type = $other->lock_type;
        $this->includes_sheath = $other->includes_sheath;
        $this->sheath_type = $other->sheath_type;
        $this->carry_type = $other->carry_type;
        $this->condition = $other->condition;
        $this->has_box = $other->has_box;
        $this->has_receipt = $other->has_receipt;
        $this->package_includes = $other->package_includes;
        $this->notes = $other->notes;
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
        if (!$userId || $userId !== $this->post->user_id) {
            abort(403);
        }

        DB::transaction(function () {

            $dir = "posts/{$this->post->uuid}";

            // MAIN PHOTO:
            if($this->primary_photo){

                // If user uploaded a new primary photo, store it and update p_1
                $p1 = $this->storePhotoIfPresent($this->primary_photo, $dir);

                // Delete old primary photo if exists
                if ($this->uploaded_primary_photo) {
                    Storage::disk('public')->delete($this->uploaded_primary_photo);
                }
            }else{
                // No new upload, keep existing primary photo (if any)
                $p1 = $this->uploaded_primary_photo;
            }

            // OTHER PHOTOS UPLOADING LOGIC:
            $existingPhotosCount = count(array_filter($this->uploaded_other_photos));

            if ($existingPhotosCount > 0) {

                // If there are existing photos, we need to ensure that the new uploads fill in the first available slots (p_2..p_10)
                $photoIndex = 0;

                for ($i = 2; $i <= 9; $i++) {

                    if (!$this->post->{"p_$i"}) {

                        // This slot is empty, try to fill it with an uploaded photo
                        if ($photoIndex < count($this->other_photos) && $this->other_photos[$photoIndex]) {
                            $this->post->update(["p_$i" => $this->storePhotoIfPresent($this->other_photos[$photoIndex], $dir)]);
                            $photoIndex++;
                        }
                    }
                }

            }else{

                $other_photos_paths = [];
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

                $this->post->update([
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

            }

            $this->post->update([
                'listing_type' => $this->listing_type,
                'title' => $this->title,
                'description' => $this->description,
                'price' => $this->price,
                'buy_min_price' => $this->buy_min_price,
                'buy_max_price' => $this->buy_max_price,
                'is_negotiable' => $this->is_negotiable,
                'condition' => $this->post_condition,
                'location' => $this->location,
                'expires_at' => $this->expires_at ? \Carbon\Carbon::parse($this->expires_at) : null,
                'p_1' => $p1,
            ]);

            $other = $this->post->other ?? new Other(['post_id' => $this->post->id]);
            $other->fill([
                'weapon_type' => $this->weapon_type,
                'subcategory' => $this->subcategory,
                'intended_use' => $this->intended_use,
                'brand' => $this->brand,
                'model' => $this->model,
                'variant' => $this->variant,
                'country_of_origin' => $this->country_of_origin,
                'blade_type' => $this->blade_type,
                'edge_type' => $this->edge_type,
                'steel_type' => $this->steel_type,
                'finish' => $this->finish,
                'full_tang' => $this->full_tang,
                'overall_length' => $this->overall_length,
                'blade_length' => $this->blade_length,
                'head_length' => $this->head_length,
                'handle_length' => $this->handle_length,
                'length_unit' => $this->length_unit,
                'weight' => $this->weight,
                'weight_unit' => $this->weight_unit,
                'handle_material' => $this->handle_material,
                'handle_color' => $this->handle_color,
                'grip_texture' => $this->grip_texture,
                'is_folding' => $this->is_folding,
                'opening_mechanism' => $this->opening_mechanism,
                'lock_type' => $this->lock_type,
                'includes_sheath' => $this->includes_sheath,
                'sheath_type' => $this->sheath_type,
                'carry_type' => $this->carry_type,
                'condition' => $this->condition,
                'has_box' => $this->has_box,
                'has_receipt' => $this->has_receipt,
                'package_includes' => $this->package_includes,
                'notes' => $this->notes,
            ]);
            $other->save();

            Flux::toast('Post updated.', variant: 'success');
            return redirect()->back();
        });
    }
};
