<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Accessory;
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

    public string $category = 'accessory';

    // Post fields
    public string $listing_type = 'sell';
    public string $title = '';
    public string $description = '';
    public ?float $price = null;
    public ?float $buy_min_price = null;
    public ?float $buy_max_price = null;
    public bool $is_negotiable = false;
    public ?string $post_condition = null;
    public ?string $location = null;
    public ?string $expires_at = null;

    public $uploaded_primary_photo = null;
    public $uploaded_other_photos = [];
    public $primary_photo = null;
    public $other_photos = [];

    // Accessory fields
    public ?string $accessory_category = null;
    public ?string $brand = null;
    public ?string $model = null;
    public ?string $compatible_with = null;
    public ?string $mount_type = null;
    public ?string $size = null;
    public ?string $color = null;
    public ?string $material = null;
    public ?string $sku = null;
    public ?string $upc = null;
    public ?string $package_includes = null;
    public ?string $condition = null;
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

    #[Computed]
    public function accessoryCategoryOptions(): array
    {
        return [
            'optic' => 'Optic/Scope',
            'holster' => 'Holster',
            'light' => 'Light/Laser',
            'sling' => 'Sling',
            'bag' => 'Bag/Case',
            'magazine' => 'Magazine',
            'grip' => 'Grip',
            'stock' => 'Stock',
            'barrel' => 'Barrel',
            'trigger' => 'Trigger',
            'sight' => 'Sight',
            'suppressor' => 'Suppressor',
            'other' => 'Other',
        ];
    }

    #[Computed]
    public function mountTypeOptions(): array
    {
        return [
            'picatinny' => 'Picatinny',
            'mlok' => 'M-LOK',
            'keymod' => 'KeyMod',
            'weaver' => 'Weaver',
            'none' => 'None',
            'other' => 'Other',
        ];
    }

    private function photoRules(string $field): array
    {
        return [$field => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240']];
    }

    public function removePhoto($num)
    {
        $photo_num = $num + 2;
        Storage::disk('public')->delete($this->post->{"p_$photo_num"});
        $this->post->update(["p_$photo_num" => null]);
        $this->uploaded_other_photos[$num] = null;
        Flux::toast('Photo removed.', variant: 'success');
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
            $this->other_photos = array_values($this->other_photos);
        }
    }

    public function rules(): array
    {
        return array_merge([
            'category' => ['required', Rule::in(['accessory'])],
            'listing_type' => ['required', Rule::in(['buy','sell'])],
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'price' => [Rule::requiredIf(fn() => $this->listing_type === 'sell'), 'nullable', 'numeric', 'min:0'],
            'buy_min_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
            'buy_max_price' => [Rule::requiredIf(fn() => $this->listing_type === 'buy'), 'nullable', 'numeric', 'min:0'],
            'is_negotiable' => ['boolean'],
            'post_condition' => ['required', 'string', 'max:50'],
            'location' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],

            'accessory_category' => ['nullable', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'compatible_with' => ['nullable', 'string', 'max:255'],
            'mount_type' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', 'string', 'max:255'],
            'color' => ['nullable', 'string', 'max:255'],
            'material' => ['nullable', 'string', 'max:255'],
            'sku' => ['nullable', 'string', 'max:255'],
            'upc' => ['nullable', 'string', 'max:255'],
            'package_includes' => ['nullable', 'string'],
            'condition' => ['nullable', Rule::in(['new','like_new','used','for_parts'])],
            'notes' => ['nullable', 'string'],
            'other_photos' => ['nullable', 'array', 'max:9'],
            'other_photos.*' => ['file', 'mimes:jpg,jpeg,png,gif', 'max:5080'],
        ]);
    }

    public function mount(Post $post): void
    {
        // dd('aw');
        $this->post = $post;

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

        $accessory = $post->accessory ?? new Accessory();
        $this->accessory_category = $accessory->category;
        $this->brand = $accessory->brand;
        $this->model = $accessory->model;
        $this->compatible_with = $accessory->compatible_with;
        $this->mount_type = $accessory->mount_type;
        $this->size = $accessory->size;
        $this->color = $accessory->color;
        $this->material = $accessory->material;
        $this->sku = $accessory->sku;
        $this->upc = $accessory->upc;
        $this->package_includes = $accessory->package_includes;
        $this->condition = $accessory->condition;
        $this->notes = $accessory->notes;
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
        if (!$userId || $userId !== $this->post->user_id) {
            abort(403);
        }

        DB::transaction(function () {
            $dir = "posts/{$this->post->uuid}";

            $p1 = $this->storePhotoIfPresent($this->primary_photo, $dir) ?? $this->uploaded_primary_photo;

            $other_photos_paths = [];
            $other_photos_count = count($this->other_photos ?? []);

            if ($other_photos_count >= 1) {
                foreach ($this->other_photos as $index => $photo) {
                    if ($index >= 9) {
                        break;
                    }

                    $storedPath = $this->storePhotoIfPresent($photo, $dir);
                    $other_photos_paths[] = $storedPath;
                }
            }

            // Merge with existing uploaded photos
            $final_other_photos = [];
            for ($i = 0; $i < 9; $i++) {
                $final_other_photos[] = $other_photos_paths[$i] ?? $this->uploaded_other_photos[$i] ?? null;
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
                'expires_at' => $this->expires_at,
                'p_1' => $p1,
                'p_2' => $final_other_photos[0],
                'p_3' => $final_other_photos[1],
                'p_4' => $final_other_photos[2],
                'p_5' => $final_other_photos[3],
                'p_6' => $final_other_photos[4],
                'p_7' => $final_other_photos[5],
                'p_8' => $final_other_photos[6],
                'p_9' => $final_other_photos[7],
                'p_10' => $final_other_photos[8],
            ]);

            $this->post->accessory()->updateOrCreate(
                ['post_id' => $this->post->id],
                [
                    'category' => $this->accessory_category,
                    'brand' => $this->brand,
                    'model' => $this->model,
                    'compatible_with' => $this->compatible_with,
                    'mount_type' => $this->mount_type,
                    'size' => $this->size,
                    'color' => $this->color,
                    'material' => $this->material,
                    'sku' => $this->sku,
                    'upc' => $this->upc,
                    'package_includes' => $this->package_includes,
                    'condition' => $this->condition,
                    'notes' => $this->notes,
                ]
            );

            Flux::toast('Accessory post updated successfully.', variant: 'success');
        });
    }
};
