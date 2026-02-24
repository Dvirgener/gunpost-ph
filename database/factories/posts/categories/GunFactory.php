<?php

namespace Database\Factories\posts\categories;

use App\Models\posts\categories\Gun;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GunFactory extends Factory
{
    protected $model = Gun::class;

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
        $brand = $this->faker->randomElement(['Glock', 'Colt', 'SIG Sauer', 'Smith & Wesson', 'Beretta', 'Ruger']);
        $model = $this->faker->randomElement(['G19', 'G17', 'P320', 'M&P9', '92FS', 'SR9']) . ' ' . $this->faker->randomNumber(2);
        $seedBase = Str::slug($brand . '-' . $model . '-' . $this->faker->unique()->numberBetween(1000, 999999));

        return array_merge([
            // post_id will be injected by PostFactory afterCreating
            'manufacturer' => $brand,
            'model' => $model,
            'variant' => $this->faker->optional()->randomElement(['Gen 3', 'Gen 4', 'Gen 5']),
            'series' => $this->faker->optional()->word(),
            'country_of_origin' => $this->faker->optional()->country(),

            'platform' => $this->faker->optional()->randomElement(['handgun','rifle','shotgun','pcc','smg','sniper','other']),
            'type' => $this->faker->optional()->randomElement(['1911', 'AR-15', 'AK-pattern', 'Revolver']),
            'action' => $this->faker->optional()->randomElement(['semi-auto', 'bolt', 'pump', 'lever', 'revolver']),

            'caliber' => $this->faker->optional()->randomElement(['9mm', '.45 ACP', '5.56 NATO', '7.62x39', '12 GA']),
            'capacity' => $this->faker->optional()->numberBetween(5, 30),

            'barrel_length' => $this->faker->optional()->randomFloat(2, 2.5, 20.0),
            'overall_length' => $this->faker->optional()->randomFloat(2, 5.0, 50.0),
            'height' => $this->faker->optional()->randomFloat(2, 8.0, 30.0),
            'width' => $this->faker->optional()->randomFloat(2, 2.0, 10.0),
            'weight' => $this->faker->optional()->randomFloat(3, 0.5, 6.0),
            'weight_unit' => $this->faker->randomElement(['kg', 'lb']),

            'finish' => $this->faker->optional()->randomElement(['matte', 'satin', 'blued', 'cerakote']),
            'color' => $this->faker->optional()->randomElement(['black', 'tan', 'gray']),
            'optic_ready' => $this->faker->boolean(30),

            'threaded_barrel' => $this->faker->boolean(20),
            'muzzle_device_included' => $this->faker->boolean(15),

            'condition' => $this->faker->optional()->randomElement(['new','like_new','used','refurbished','for_parts']),
            'notes' => $this->faker->optional()->paragraph(),
        ], $this->picsumImages($seedBase));
    }
}
