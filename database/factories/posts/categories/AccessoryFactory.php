<?php

namespace Database\Factories\posts\categories;

use App\Models\posts\categories\Accessory;
use App\Models\posts\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccessoryFactory extends Factory
{
    protected $model = Accessory::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'category' => $this->faker->randomElement(['optic', 'holster', 'light', 'sling', 'bag', 'grip', 'magazine', 'case']),
            'brand' => $this->faker->randomElement(['Magpul', 'SureFire', 'Streamlight', 'Vortex', 'Holosun', '5.11', 'Generic']),
            'model' => $this->faker->optional()->bothify('Model-###??'),

            'compatible_with' => $this->faker->optional()->randomElement(['Picatinny', 'M-LOK', 'KeyMod', 'Glock 19', 'M4', 'Universal']),
            'mount_type' => $this->faker->optional()->randomElement(['picatinny', 'mlok', 'keymod', 'none']),
            'size' => $this->faker->optional()->randomElement(['S', 'M', 'L', 'XL', 'One Size']),
            'color' => $this->faker->optional()->randomElement(['black', 'tan', 'gray', 'OD green']),
            'material' => $this->faker->optional()->randomElement(['polymer', 'nylon', 'kydex', 'aluminum', 'steel', 'leather']),

            'sku' => $this->faker->optional()->bothify('SKU-####-??'),
            'upc' => $this->faker->optional()->ean13(),

            'package_includes' => $this->faker->optional()->sentence(),
            'condition' => $this->faker->optional()->randomElement(['new', 'like_new', 'used', 'for_parts']),
            'notes' => $this->faker->optional()->paragraph(),
        ];
    }
}
