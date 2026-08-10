<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use App\Models\CarModel;
use Illuminate\Database\Seeder;

class CarModelSeeder extends Seeder
{
    public function run(): void
    {
        $toyota = CarBrand::where('slug', 'toyota')->first();
        $honda = CarBrand::where('slug', 'honda')->first();
        $daihatsu = CarBrand::where('slug', 'daihatsu')->first();
        $suzuki = CarBrand::where('slug', 'suzuki')->first();
        $mitsubishi = CarBrand::where('slug', 'mitsubishi')->first();
        $hyundai = CarBrand::where('slug', 'hyundai')->first();

        $models = [
            ['car_brand_id' => $toyota->id, 'name' => 'Avanza', 'slug' => 'avanza'],
            ['car_brand_id' => $toyota->id, 'name' => 'Innova', 'slug' => 'innova'],
            ['car_brand_id' => $toyota->id, 'name' => 'Yaris', 'slug' => 'yaris'],
            ['car_brand_id' => $toyota->id, 'name' => 'Fortuner', 'slug' => 'fortuner'],
            ['car_brand_id' => $honda->id, 'name' => 'Brio', 'slug' => 'brio'],
            ['car_brand_id' => $honda->id, 'name' => 'Jazz', 'slug' => 'jazz'],
            ['car_brand_id' => $honda->id, 'name' => 'HR-V', 'slug' => 'hr-v'],
            ['car_brand_id' => $honda->id, 'name' => 'Civic', 'slug' => 'civic'],
            ['car_brand_id' => $daihatsu->id, 'name' => 'Xenia', 'slug' => 'xenia'],
            ['car_brand_id' => $daihatsu->id, 'name' => 'Terios', 'slug' => 'terios'],
            ['car_brand_id' => $daihatsu->id, 'name' => 'Ayla', 'slug' => 'ayla'],
            ['car_brand_id' => $suzuki->id, 'name' => 'Ertiga', 'slug' => 'ertiga'],
            ['car_brand_id' => $suzuki->id, 'name' => 'Jimny', 'slug' => 'jimny'],
            ['car_brand_id' => $mitsubishi->id, 'name' => 'Xpander', 'slug' => 'xpander'],
            ['car_brand_id' => $mitsubishi->id, 'name' => 'Pajero Sport', 'slug' => 'pajero-sport'],
            ['car_brand_id' => $hyundai->id, 'name' => 'Creta', 'slug' => 'creta'],
        ];

        foreach ($models as $model) {
            CarModel::create($model);
        }
    }
}
