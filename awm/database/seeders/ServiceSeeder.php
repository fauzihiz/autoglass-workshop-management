<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['name' => 'Pemasangan Kaca', 'description' => 'Glass installation service', 'default_price' => 150000],
            ['name' => 'Lem Kaca', 'description' => 'Glass adhesive / sealing service', 'default_price' => 75000],
            ['name' => 'Potong Kaca', 'description' => 'Glass cutting / custom shaping', 'default_price' => 100000],
            ['name' => 'Pembersihan Kaca', 'description' => 'Glass cleaning and polish', 'default_price' => 50000],
            ['name' => 'Servis Power Window', 'description' => 'Power window mechanism repair', 'default_price' => 200000],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
