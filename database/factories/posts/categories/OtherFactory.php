<?php

namespace Database\Factories\posts\categories;

use App\Models\posts\categories\Other;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtherFactory extends Factory
{
    protected $model = Other::class;

    public function definition(): array
    {
        return [
            'weapon_type' => $this->faker->optional()->randomElement([
                'knife','sword','machete','axe','tomahawk','baton','stick','tonfa','spear','other'
            ]),
            'subcategory' => $this->faker->optional()->word(),
            'intended_use' => $this->faker->optional()->randomElement(['utility','training','display','collection','outdoors']),

            'brand' => $this->faker->optional()->randomElement(['Cold Steel', 'Gerber', 'Kershaw', 'Ontario', 'CRKT', 'Generic']),
            'model' => $this->faker->optional()->bothify('Model-###??'),
            'variant' => $this->faker->optional()->randomElement(['Mk I', 'Mk II', 'Pro', 'Lite']),
            'country_of_origin' => $this->faker->optional()->country(),

            'blade_type' => $this->faker->optional()->randomElement(['fixed', 'folding', 'serrated', 'tanto', 'drop point']),
            'edge_type' => $this->faker->optional()->randomElement(['plain', 'serrated', 'combo']),
            'steel_type' => $this->faker->optional()->randomElement(['D2', '440C', 'VG-10', 'carbon steel', 'stainless steel']),
            'finish' => $this->faker->optional()->randomElement(['stonewash', 'satin', 'coated']),
            'full_tang' => $this->faker->optional()->boolean(),

            'overall_length' => $this->faker->optional()->randomFloat(2, 10, 120),
            'blade_length' => $this->faker->optional()->randomFloat(2, 5, 80),
            'head_length' => $this->faker->optional()->randomFloat(2, 5, 30),
            'handle_length' => $this->faker->optional()->randomFloat(2, 5, 50),
            'length_unit' => $this->faker->randomElement(['cm', 'in']),

            'weight' => $this->faker->optional()->randomFloat(3, 0.2, 5.0),
            'weight_unit' => $this->faker->randomElement(['kg', 'lb']),

            'handle_material' => $this->faker->optional()->randomElement(['G10', 'micarta', 'wood', 'rubber', 'polymer']),
            'handle_color' => $this->faker->optional()->safeColorName(),
            'grip_texture' => $this->faker->optional()->randomElement(['smooth', 'checkered', 'wrapped']),

            'is_folding' => $this->faker->boolean(25),
            'opening_mechanism' => $this->faker->optional()->randomElement(['manual', 'assisted']),
            'lock_type' => $this->faker->optional()->randomElement(['liner lock', 'frame lock', 'back lock']),

            'includes_sheath' => $this->faker->boolean(40),
            'sheath_type' => $this->faker->optional()->randomElement(['kydex', 'leather', 'nylon', 'scabbard']),
            'carry_type' => $this->faker->optional()->randomElement(['belt', 'molle', 'pocket clip']),

            'condition' => $this->faker->optional()->randomElement(['new', 'like_new', 'used', 'refurbished', 'for_parts']),
            'has_box' => $this->faker->boolean(25),
            'has_receipt' => $this->faker->boolean(15),

            'package_includes' => $this->faker->optional()->sentence(),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
