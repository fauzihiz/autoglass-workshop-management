<?php

namespace Database\Factories;

use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class CarModelFactory extends Factory
{
    protected $model = CarModel::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();
        return [
            'car_brand_id' => CarBrand::factory(),
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
        ];
    }
}