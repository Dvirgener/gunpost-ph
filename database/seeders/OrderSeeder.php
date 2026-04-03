<?php

namespace Database\Seeders;

use App\Models\Order;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    private const ORDERS_COUNT = 50; // fixed number of orders to seed

    public function run(): void
    {
        // Create orders using the factory
        Order::factory()
            ->count(self::ORDERS_COUNT)
            ->create();
    }
}
