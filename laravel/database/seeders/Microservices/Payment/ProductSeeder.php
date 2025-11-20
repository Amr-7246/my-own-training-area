<?php

namespace Database\Seeders\Microservices\Payment;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Generate 20 synthetic product entries
        Product::factory()->count(20)->create();

        // Add a few curated “business-grade” items
        Product::create([
            'amount'      => 50,
            'price'       => 199.99,
            'purchasable' => true,
        ]);

        Product::create([
            'amount'      => 0,
            'price'       => 89.00,
            'purchasable' => false,
        ]);
    }
}
