<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\user\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'package' => $this->faker->randomElement(['Basic', 'Premium', 'VIP', 'Enterprise']),
            'quantity' => $this->faker->numberBetween(1, 10),
            'credits' => $this->faker->numberBetween(100, 10000),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'ticket_id' => $this->faker->optional()->uuid(),
            'payment_method' => $this->faker->randomElement(['credit_card', 'paypal', 'bank_transfer', 'crypto']),
            'user_id' => User::factory(),
            'amount' => $this->faker->numberBetween(50, 5000),
            'confirmed_by_id' => $this->faker->optional()->randomElement([User::factory(), null]),
            'confirmed_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'image_path' => $this->faker->optional()->imageUrl(),
        ];
    }
}
