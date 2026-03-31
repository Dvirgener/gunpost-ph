<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Airsoft;
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

    public string $category = 'airsoft';

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

    // Airsoft fields
    public ?string $brand = null;
    public ?string $model = null;
    public ?string $series = null;
    public ?string $platform = null;
    public ?string $power_source = null;
    public ?string $compatibility_platform = null;
    public ?string $gearbox_version = null;
    public ?int $fps = null;
    public ?float $joule = null;
    public ?string $color = null;
    public ?string $body_material = null;
    public bool $metal_body = false;
    public bool $blowback = false;
    public ?string $battery_type = null;
    public ?string $battery_connector = null;
    public ?string $gas_type = null;
    public bool $includes_magazines = false;
    public ?int $magazine_count = null;
    public ?string $magazine_type = null;
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
    public function platformOptions(): array
    {
        return [
            'pistol' => 'Pistol',
            'rifle' => 'Rifle',
            'smg' => 'SMG',
            'sniper' => 'Sniper',
            'shotgun' => 'Shotgun',
            'lmg' => 'LMG',
            'other' => 'Other',
        ];
    }

    #[Computed]
    public function powerSourceOptions(): array
    {
        return [
            'aeg' => 'AEG',
            'gbb' => 'GBB',
            'spring' => 'Spring',
            'hpa' => 'HPA',
            'co2' => 'CO2',
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
            'category' => ['required', Rule::in(['airsoft'])],
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

            'brand' => ['required', 'string', 'max:255'],
            'model' => ['required', 'string', 'max:255'],
            'series' => ['nullable', 'string', 'max:255'],
            'platform' => ['required', Rule::in(['pistol','rifle','smg','sniper','shotgun','lmg','other'])],
            'power_source' => ['required', Rule::in(['aeg','gbb','spring','hpa','co2'])],
            'compatibility_platform' => ['nullable', 'string', 'max:255'],
            'gearbox_version' => ['nullable', 'string', 'max:255'],
            'fps' => ['nullable', 'integer', 'min:0'],
            'joule' => ['nullable', 'numeric', 'min:0'],
            'color' => ['nullable', 'string', 'max:255'],
            'body_material' => ['nullable', 'string', 'max:255'],
            'metal_body' => ['boolean'],
            'blowback' => ['boolean'],
            'battery_type' => ['nullable', 'string', 'max:255'],
            'battery_connector' => ['nullable', 'string', 'max:255'],
            'gas_type' => ['nullable', 'string', 'max:255'],
            'includes_magazines' => ['boolean'],
            'magazine_count' => ['nullable', 'integer', 'min:0'],
            'magazine_type' => ['nullable', 'string', 'max:255'],
            'package_includes' => ['nullable', 'string'],
            'condition' => ['nullable', Rule::in(['new','like_new','used','for_parts'])],
            'notes' => ['nullable', 'string'],
            'other_photos' => ['nullable', 'array', 'max:9'],
            'other_photos.*' => ['file', 'mimes:jpg,jpeg,png,gif', 'max:5080'],
        ]);
    }

    public function mount(Post $post): void
    {
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

        $airsoft = $post->airsoft ?? new Airsoft();
        $this->brand = $airsoft->brand;
        $this->model = $airsoft->model;
        $this->series = $airsoft->series;
        $this->platform = $airsoft->platform;
        $this->power_source = $airsoft->power_source;
        $this->compatibility_platform = $airsoft->compatibility_platform;
        $this->gearbox_version = $airsoft->gearbox_version;
        $this->fps = $airsoft->fps;
        $this->joule = $airsoft->joule;
        $this->color = $airsoft->color;
        $this->body_material = $airsoft->body_material;
        $this->metal_body = $airsoft->metal_body;
        $this->blowback = $airsoft->blowback;
        $this->battery_type = $airsoft->battery_type;
        $this->battery_connector = $airsoft->battery_connector;
        $this->gas_type = $airsoft->gas_type;
        $this->includes_magazines = $airsoft->includes_magazines;
        $this->magazine_count = $airsoft->magazine_count;
        $this->magazine_type = $airsoft->magazine_type;
        $this->package_includes = $airsoft->package_includes;
        $this->condition = $airsoft->condition;
        $this->notes = $airsoft->notes;
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

            if ($this->primary_photo) {
                $p1 = $this->storePhotoIfPresent($this->primary_photo, $dir);
                if ($this->uploaded_primary_photo) {
                    Storage::disk('public')->delete($this->uploaded_primary_photo);
                }
            } else {
                $p1 = $this->uploaded_primary_photo;
            }

            $existingPhotosCount = count(array_filter($this->uploaded_other_photos));

            if ($existingPhotosCount > 0) {
                $photoIndex = 0;
                for ($i = 2; $i <= 9; $i++) {
                    if (!$this->post->{"p_$i"}) {
                        if ($photoIndex < count($this->other_photos) && $this->other_photos[$photoIndex]) {
                            $this->post->update(["p_$i" => $this->storePhotoIfPresent($this->other_photos[$photoIndex], $dir)]);
                            $photoIndex++;
                        }
                    }
                }
            } else {
                $other_photos_paths = [];
                $other_photos_count = count($this->other_photos ?? []);

                if ($other_photos_count >= 1) {
                    foreach ($this->other_photos as $index => $photo) {
                        if ($index >= 9) {
                            break;
                        }
                        $other_photos_paths[] = $this->storePhotoIfPresent($photo, $dir);
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

            $airsoft = $this->post->airsoft ?? new Airsoft(['post_id' => $this->post->id]);
            $airsoft->fill([
                'brand' => $this->brand,
                'model' => $this->model,
                'series' => $this->series,
                'platform' => $this->platform,
                'power_source' => $this->power_source,
                'compatibility_platform' => $this->compatibility_platform,
                'gearbox_version' => $this->gearbox_version,
                'fps' => $this->fps,
                'joule' => $this->joule,
                'color' => $this->color,
                'body_material' => $this->body_material,
                'metal_body' => $this->metal_body,
                'blowback' => $this->blowback,
                'battery_type' => $this->battery_type,
                'battery_connector' => $this->battery_connector,
                'gas_type' => $this->gas_type,
                'includes_magazines' => $this->includes_magazines,
                'magazine_count' => $this->magazine_count,
                'magazine_type' => $this->magazine_type,
                'package_includes' => $this->package_includes,
                'condition' => $this->condition,
                'notes' => $this->notes,
            ]);
            $airsoft->save();

            Flux::toast('Airsoft post updated.', variant: 'success');
            return redirect()->back();
        });
    }
};
