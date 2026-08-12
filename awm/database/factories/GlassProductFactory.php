<?php

namespace Database\Factories;

use App\Models\GlassPosition;
use App\Models\GlassProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

class GlassProductFactory extends Factory
{
    protected $model = GlassProduct::class;

    public function definition(): array
    {
        return [
            'glass_position_id' => GlassPosition::factory(),
            'name' => fake()->unique()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('??-####')),
            'description' => fake()->sentence(),
            'minimum_stock' => fake()->numberBetween(0, 50),
            'is_active' => true,
        ];
    }
}