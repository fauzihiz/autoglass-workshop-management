<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->optional(0.5)->sentence(),
            'default_price' => fake()->randomFloat(2, 50000, 500000),
            'is_active' => true,
        ];
    }
}