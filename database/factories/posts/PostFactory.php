<?php

namespace Database\Factories\posts;

use App\Models\posts\categories\Accessory;
use App\Models\posts\categories\Airsoft;
use App\Models\posts\categories\Ammunition;
use App\Models\posts\categories\Gun;
use App\Models\posts\categories\Other;
use App\Models\posts\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    protected $model = Post::class;

    private const CATEGORIES = ['gun', 'ammunition', 'airsoft', 'accessory', 'other'];
    private const LISTING_TYPES = ['buy', 'sell'];
    private const STATUSES = ['pending', 'approved', 'rejected', 'expired'];

    private function picsum(string $seed, int $w = 1200, int $h = 800): string
    {
        return "https://picsum.photos/seed/{$seed}/{$w}/{$h}";
    }

    private function picsumImages(string $seedBase, int $w = 1200, int $h = 800): array
    {
        $images = [];
        for ($i = 1; $i <= 10; $i++) {
            $images["p_{$i}"] = $this->picsum($seedBase . "-{$i}", $w, $h);
        }
        return $images;
    }

    public function definition(): array
    {
        $title = rtrim($this->faker->sentence(5), '.');
        $category = $this->faker->randomElement(self::CATEGORIES);
        $status = $this->faker->randomElement(self::STATUSES);

        // stable seed so each post has consistent images
        $seedBase = Str::slug($title) . '-' . $category . '-' . $this->faker->unique()->numberBetween(1000, 999999);

        return array_merge([
            'user_id' => 1,
            'category' => $category,
            'listing_type' => $this->faker->randomElement(self::LISTING_TYPES),

            'title' => $title,
            'description' => $this->faker->paragraphs(3, true),

            'price' => $this->faker->boolean(85) ? $this->faker->randomFloat(2, 500, 250000) : null,
            'is_negotiable' => $this->faker->boolean(35),

            'condition' => $this->faker->randomElement(['new', 'like_new', 'used', 'refurbished', 'for_parts']),
            'location' => $this->faker->randomElement([
                'Davao City', 'Cebu City', 'Quezon City', 'Makati', 'Taguig', 'Pasig', 'Baguio', 'Iloilo City',
            ]),

            'status' => $status,
            'approved_at' => $status === 'approved' ? now()->subDays($this->faker->numberBetween(0, 20)) : null,
            'approved_by' => $status === 'approved' ? 1 : null,
            'rejection_reason' => $status === 'rejected' ? $this->faker->sentence(10) : null,

            'is_featured' => $this->faker->boolean(10),
            'views' => $this->faker->numberBetween(0, 5000),
            'expires_at' => $this->faker->optional(0.7)->dateTimeBetween('+7 days', '+60 days'),
        ], $this->picsumImages($seedBase));
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Post $post) {
            match ($post->category) {
                'gun' => Gun::factory()->create(['post_id' => $post->id]),
                'ammunition' => Ammunition::factory()->create(['post_id' => $post->id]),
                'airsoft' => Airsoft::factory()->create(['post_id' => $post->id]),
                'accessory' => Accessory::factory()->create(['post_id' => $post->id]),
                'other' => Other::factory()->create(['post_id' => $post->id]),
                default => null,
            };
        });
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => 1,
            'rejection_reason' => null,
        ]);
    }
}
