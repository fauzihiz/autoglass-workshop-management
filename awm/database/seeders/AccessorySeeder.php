<?php

namespace Database\Seeders;

use App\Models\Accessory;
use Illuminate\Database\Seeder;

class AccessorySeeder extends Seeder
{
    public function run(): void
    {
        $accessories = [
            ['name' => 'Sensor Kaca Depan', 'description' => 'Rain/light sensor module mounted on windshield'],
            ['name' => 'Shadeband', 'description' => 'Tinted band at top of windshield for sun protection'],
            ['name' => 'Moulding', 'description' => 'Rubber or chrome trim around glass edge'],
            ['name' => 'Antenna', 'description' => 'Embedded antenna in rear windshield'],
            ['name' => 'Rear Defogger', 'description' => 'Heating element lines on rear windshield'],
        ];

        foreach ($accessories as $accessory) {
            Accessory::create($accessory);
        }
    }
}
