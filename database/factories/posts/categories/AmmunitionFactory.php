<?php

namespace Database\Factories\posts\categories;

use App\Models\posts\categories\Ammunition;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AmmunitionFactory extends Factory
{
    protected $model = Ammunition::class;

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
        $brand = $this->faker->randomElement(['Federal', 'Winchester', 'Hornady', 'Remington', 'PMC', 'Magtech']);
        $caliber = $this->faker->randomElement(['9mm', '.45 ACP', '5.56 NATO', '7.62x39', '.22 LR', '12 GA']);
        $seedBase = Str::slug($brand . '-' . $caliber . '-' . $this->faker->unique()->numberBetween(1000, 999999));

        $boxes = $this->faker->optional()->numberBetween(1, 20);
        $rpb = $boxes ? $this->faker->randomElement([20, 25, 50, 100]) : null;

        return array_merge([
            'brand' => $brand,
            'product_line' => $this->faker->optional()->word(),
            'caliber' => $caliber,
            'bullet_type' => $this->faker->optional()->randomElement(['FMJ', 'JHP', 'SP', 'OTM']),
            'grain' => $this->faker->optional()->randomElement(['55gr', '62gr', '115gr', '124gr', '147gr', '230gr']),
            'case_material' => $this->faker->optional()->randomElement(['brass', 'steel', 'aluminum']),
            'primer_type' => $this->faker->optional()->randomElement(['boxer', 'berdan']),
            'corrosive' => $this->faker->boolean(5),

            'boxes' => $boxes,
            'rounds_per_box' => $rpb,
            'total_rounds' => ($boxes && $rpb) ? $boxes * $rpb : $this->faker->optional()->numberBetween(20, 2000),

            'lot_number' => $this->faker->optional()->bothify('LOT-####??'),
            'sku' => $this->faker->optional()->bothify('SKU-####-??'),
            'upc' => $this->faker->optional()->ean13(),

            'condition' => $this->faker->optional()->randomElement(['factory_new','sealed','opened','mixed','other']),
            'reloads' => $this->faker->boolean(8),
            'notes' => $this->faker->optional()->sentence(),
        ], $this->picsumImages($seedBase));
    }
}
