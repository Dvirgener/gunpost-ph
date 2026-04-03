<?php

namespace Database\Factories\posts\categories;

use App\Models\posts\categories\Gun;
use App\Models\posts\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class GunFactory extends Factory
{
    protected $model = Gun::class;

    public function definition(): array
    {
        return [
            // Identification / naming
            'post_id' => Post::factory(),
            'manufacturer' => $this->faker->randomElement(['Glock', 'Colt', 'SIG Sauer', 'Smith & Wesson', 'Beretta', 'Ruger']),
            'model' => $this->faker->randomElement(['G19', 'G17', 'P320', 'M&P9', '92FS', 'SR9']).' '.$this->faker->randomNumber(2),
            'variant' => $this->faker->optional()->randomElement(['Gen 3', 'Gen 4', 'Gen 5']),
            'series' => $this->faker->optional()->word(),
            'country_of_origin' => $this->faker->optional()->country(),

            // Classification
            'platform' => $this->faker->optional()->randomElement(['handgun', 'rifle', 'shotgun', 'pcc', 'smg', 'sniper', 'other']),
            'type' => $this->faker->optional()->word(),
            'action' => $this->faker->optional()->randomElement(['semi-auto', 'bolt', 'pump', 'lever', 'revolver']),

            // Core specs
            'caliber' => $this->faker->optional()->randomElement(['9mm', '.45 ACP', '5.56 NATO', '7.62x39', '12 GA']),
            'capacity' => $this->faker->optional()->numberBetween(1, 100),
            'barrel_length' => $this->faker->optional()->randomFloat(2, 2.0, 30.0),
            'overall_length' => $this->faker->optional()->randomFloat(2, 5.0, 60.0),
            'height' => $this->faker->optional()->randomFloat(2, 5.0, 40.0),
            'width' => $this->faker->optional()->randomFloat(2, 1.0, 15.0),
            'weight' => $this->faker->optional()->randomFloat(3, 0.3, 10.0),
            'weight_unit' => $this->faker->randomElement(['kg', 'lb']),

            // Materials / finish / ergonomics
            'frame_material' => $this->faker->optional()->word(),
            'slide_material' => $this->faker->optional()->word(),
            'barrel_material' => $this->faker->optional()->word(),
            'finish' => $this->faker->optional()->randomElement(['matte', 'satin', 'blued', 'cerakote']),
            'color' => $this->faker->optional()->randomElement(['black', 'tan', 'gray']),
            'grip_type' => $this->faker->optional()->word(),
            'stock_type' => $this->faker->optional()->word(),
            'handguard_type' => $this->faker->optional()->word(),
            'rail_type' => $this->faker->optional()->word(),

            // Sights / optics
            'sight_type' => $this->faker->optional()->word(),
            'optic_ready' => $this->faker->boolean(30),
            'optic_mount_pattern' => $this->faker->optional()->word(),

            // Barrel / muzzle
            'threaded_barrel' => $this->faker->boolean(20),
            'thread_pitch' => $this->faker->optional()->randomFloat(2, 0.1, 2.0).' mm',
            'muzzle_device_included' => $this->faker->boolean(15),
            'muzzle_device_type' => $this->faker->optional()->word(),

            // Safety / trigger
            'trigger_type' => $this->faker->optional()->word(),
            'trigger_pull' => $this->faker->optional()->randomFloat(2, 1, 10),
            'trigger_pull_unit' => $this->faker->randomElement(['lb', 'kg']),
            'has_manual_safety' => $this->faker->boolean(50),
            'has_firing_pin_safety' => $this->faker->boolean(50),

            // Manufacturer identifiers
            'sku' => $this->faker->optional()->bothify('??-#####'),
            'upc' => $this->faker->optional()->ean13(),

            // Ownership / condition metadata
            'condition' => $this->faker->optional()->randomElement(['new', 'like_new', 'used', 'refurbished', 'for_parts']),
            'round_count_estimate' => $this->faker->optional()->numberBetween(0, 5000),
            'has_box' => $this->faker->boolean(30),
            'has_receipt' => $this->faker->boolean(30),
            'has_documents' => $this->faker->boolean(20),
            'document_notes' => $this->faker->optional()->sentence(),

            // Included items
            'included_magazines' => $this->faker->optional()->numberBetween(0, 10),
            'included_accessories' => $this->faker->optional()->sentence(),

            // Extra notes
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
