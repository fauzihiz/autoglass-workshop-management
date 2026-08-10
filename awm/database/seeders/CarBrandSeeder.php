<?php

namespace Database\Seeders;

use App\Models\CarBrand;
use Illuminate\Database\Seeder;

class CarBrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            ['name' => 'Toyota', 'slug' => 'toyota'],
            ['name' => 'Honda', 'slug' => 'honda'],
            ['name' => 'Daihatsu', 'slug' => 'daihatsu'],
            ['name' => 'Suzuki', 'slug' => 'suzuki'],
            ['name' => 'Mitsubishi', 'slug' => 'mitsubishi'],
            ['name' => 'Hyundai', 'slug' => 'hyundai'],
            ['name' => 'Nissan', 'slug' => 'nissan'],
            ['name' => 'Kia', 'slug' => 'kia'],
        ];

        foreach ($brands as $brand) {
            CarBrand::create($brand);
        }
    }
}
