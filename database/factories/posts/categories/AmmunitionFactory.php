<?php

namespace Database\Factories\posts\categories;

use App\Models\posts\categories\Ammunition;
use App\Models\posts\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class AmmunitionFactory extends Factory
{
    protected $model = Ammunition::class;

    public function definition(): array
    {
        $boxes = $this->faker->optional()->numberBetween(1, 20);
        $rpb = $boxes ? $this->faker->randomElement([20, 25, 50, 100]) : null;

        return [
            'post_id' => Post::factory(),
            'brand' => $this->faker->randomElement(['Federal', 'Winchester', 'Hornady', 'Remington', 'PMC', 'Magtech']),
            'product_line' => $this->faker->optional()->word(),
            'caliber' => $this->faker->randomElement(['9mm', '.45 ACP', '5.56 NATO', '7.62x39', '.22 LR', '12 GA']),
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

            'condition' => $this->faker->optional()->randomElement(['factory_new', 'sealed', 'opened', 'mixed', 'other']),
            'reloads' => $this->faker->boolean(8),
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
