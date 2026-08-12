<?php

namespace Database\Factories;

use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Customer;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'car_brand_id' => CarBrand::factory(),
            'car_model_id' => CarModel::factory(),
            'license_plate' => strtoupper(fake()->unique()->bothify('??-####')),
            'year' => fake()->year(),
            'color' => fake()->safeColorName(),
            'notes' => null,
        ];
    }
}