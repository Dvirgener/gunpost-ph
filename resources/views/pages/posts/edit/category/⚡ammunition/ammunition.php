<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Ammunition;
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
    public $uploaded_primary_photo = null;

    public $uploaded_other_photos = []; // for loop in blade to avoid copy/paste for photo_2..photo_10

    public $primary_photo = null; // for wire:model of primary photo upload
    public $other_photos = []; // for wire:model of other photo uploads (keys 0..8 for photo_2..photo_10)

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

    #[Computed]
    public function conditionOptions(): array
    {
        return [
            'factory_new' => 'Factory New',
            'sealed' => 'Sealed',
            'opened' => 'Opened',
            'mixed' => 'Mixed',
            'other' => 'Other',
        ];
    }

    #[Computed]
    public function bulletTypeOptions(): array
    {
        return [
            'FMJ' => 'Full Metal Jacket (FMJ)',
            'JHP' => 'Jacketed Hollow Point (JHP)',
            'HP' => 'Hollow Point (HP)',
            'SP' => 'Soft Point (SP)',
            'AP' => 'Armor Piercing (AP)',
            'SWC' => 'Semi-Wadcutter (SWC)',
            'WC' => 'Wadcutter (WC)',
            'RN' => 'Round Nose (RN)',
            'BT' => 'Boat Tail (BT)',
            'other' => 'Other',
        ];
    }

    #[Computed]
    public function caseMaterialOptions(): array
    {
        return [
            'brass' => 'Brass',
            'steel' => 'Steel',
            'aluminum' => 'Aluminum',
            'other' => 'Other',
        ];
    }

    #[Computed]
    public function primerTypeOptions(): array
    {
        return [
            'boxer' => 'Boxer',
            'berdan' => 'Berdan',
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
                'category' => ['required', Rule::in(['ammunition'])],
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

        $ammunition = $post->ammunition ?? new Ammunition();
        $this->brand = $ammunition->brand;
        $this->product_line = $ammunition->product_line;
        $this->caliber = $ammunition->caliber;
        $this->bullet_type = $ammunition->bullet_type;
        $this->grain = $ammunition->grain;
        $this->case_material = $ammunition->case_material;
        $this->primer_type = $ammunition->primer_type;
        $this->corrosive = $ammunition->corrosive;
        $this->total_rounds = $ammunition->total_rounds;
        $this->boxes = $ammunition->boxes;
        $this->rounds_per_box = $ammunition->rounds_per_box;
        $this->lot_number = $ammunition->lot_number;
        $this->sku = $ammunition->sku;
        $this->upc = $ammunition->upc;
        $this->condition = $ammunition->condition;
        $this->reloads = $ammunition->reloads;
        $this->notes = $ammunition->notes;
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

            $ammunition = $this->post->ammunition ?? new Ammunition(['post_id' => $this->post->id]);
            $ammunition->fill([
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
            $ammunition->save();

            Flux::toast('Ammunition post updated.', variant: 'success');
            return redirect()->back();
        });
    }
};
