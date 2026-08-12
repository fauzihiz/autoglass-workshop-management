<?php

namespace Database\Factories;

use App\Models\CarModel;
use App\Models\GlassProduct;
use App\Models\ProductCompatibility;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCompatibilityFactory extends Factory
{
    protected $model = ProductCompatibility::class;

    public function definition(): array
    {
        return [
            'glass_product_id' => GlassProduct::factory(),
            'car_model_id' => CarModel::factory(),
            'year_from' => fake()->year(),
            'year_to' => fake()->year(),
        ];
    }
}