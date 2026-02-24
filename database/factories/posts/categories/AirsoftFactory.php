<?php

namespace Database\Factories\posts\categories;

use App\Models\posts\categories\Airsoft;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AirsoftFactory extends Factory
{
    protected $model = Airsoft::class;

    private function picsumImages(string $seedBase, int $w = 1200, int $h = 800): array
    {
        $images = [];
        for ($i = 1; $i <= 10; $i++) {
            $images["p_{$i}"] = "https://picsum.photos/seed/{$seedBase}-{$i}/{$w}/{$h}";
        }
        return $images;
    }

    public function definition(): array
    {
        $brand = $this->faker->randomElement(['Tokyo Marui', 'CYMA', 'VFC', 'G&G', 'KWA', 'WE']);
        $model = $this->faker->randomElement(['M4', 'AK', 'G17', 'Hi-Capa', 'MP5', 'SCAR']) . ' ' . $this->faker->randomNumber(2);
        $seedBase = Str::slug($brand . '-' . $model . '-' . $this->faker->unique()->numberBetween(1000, 999999));

        return array_merge([
            'brand' => $brand,
            'model' => $model,
            'series' => $this->faker->optional()->word(),

            'platform' => $this->faker->optional()->randomElement(['pistol','rifle','smg','sniper','shotgun','lmg','other']),
            'power_source' => $this->faker->optional()->randomElement(['aeg','gbb','spring','hpa','co2']),
            'compatibility_platform' => $this->faker->optional()->randomElement(['M4', 'AK', 'Glock', '1911', 'Hi-Capa']),
            'gearbox_version' => $this->faker->optional()->randomElement(['V2', 'V3', 'N/A']),

            'fps' => $this->faker->optional()->numberBetween(200, 450),
            'joule' => $this->faker->optional()->randomFloat(2, 0.5, 2.5),

            'color' => $this->faker->optional()->randomElement(['black', 'tan', 'gray']),
            'body_material' => $this->faker->optional()->randomElement(['polymer', 'metal', 'mixed']),
            'metal_body' => $this->faker->boolean(35),
            'blowback' => $this->faker->boolean(25),

            'includes_magazines' => $this->faker->boolean(60),
            'magazine_count' => $this->faker->optional()->numberBetween(0, 8),

            'package_includes' => $this->faker->optional()->sentence(),
            'condition' => $this->faker->optional()->randomElement(['new','like_new','used','for_parts']),
            'notes' => $this->faker->optional()->paragraph(),
        ], $this->picsumImages($seedBase));
    }
}
