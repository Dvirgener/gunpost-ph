<?php

use App\Models\posts\Post;
use App\Models\posts\categories\Airsoft;
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

    public $primary_photo = null;
    public $other_photos = null;

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
            'primary_photo' => ['required', 'file', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
            'other_photos' => ['nullable', 'array', 'max:9'],
            'other_photos.*' => ['file', 'mimes:jpg,jpeg,png,gif', 'max:5080'],

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
                    }

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
                'category' => 'airsoft',
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

            Airsoft::create([
                'post_id' => $post->id,
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

            Flux::toast('Airsoft post submitted for review. Add more post', variant: 'success');
            $this->reset();
        });
    }
};
