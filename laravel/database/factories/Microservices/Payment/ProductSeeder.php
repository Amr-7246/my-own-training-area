<?php

namespace Database\Factories\Microservices\Payment;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'amount'      => $this->faker->numberBetween(1, 500),
            'price'       => $this->faker->randomFloat(2, 5, 999),
            'purchasable' => $this->faker->boolean(90),
        ];
    }
}
