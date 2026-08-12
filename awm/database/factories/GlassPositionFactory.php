<?php

namespace Database\Factories;

use App\Models\GlassPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

class GlassPositionFactory extends Factory
{
    protected $model = GlassPosition::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'code' => strtoupper(fake()->unique()->bothify('??')),
            'description' => fake()->sentence(),
        ];
    }
}