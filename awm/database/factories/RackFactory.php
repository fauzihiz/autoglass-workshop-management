<?php

namespace Database\Factories;

use App\Models\Rack;
use Illuminate\Database\Eloquent\Factories\Factory;

class RackFactory extends Factory
{
    protected $model = Rack::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->bothify('Rack-##'),
            'location_description' => fake()->optional(0.3)->sentence(),
        ];
    }
}