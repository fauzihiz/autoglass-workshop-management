<?php

namespace Database\Seeders;

use App\Models\GlassPosition;
use Illuminate\Database\Seeder;

class GlassPositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['name' => 'Left Front Windshield', 'code' => 'LFW', 'description' => 'Front windshield, left side'],
            ['name' => 'Front Door Right', 'code' => 'FDR', 'description' => 'Front door glass, right side'],
            ['name' => 'Front Door Left', 'code' => 'FDL', 'description' => 'Front door glass, left side'],
            ['name' => 'Rear Door Right', 'code' => 'RDR', 'description' => 'Rear door glass, right side'],
            ['name' => 'Rear Door Left', 'code' => 'RDL', 'description' => 'Rear door glass, left side'],
            ['name' => 'Rear Windshield', 'code' => 'RW', 'description' => 'Rear windshield glass'],
            ['name' => 'Quarter Glass', 'code' => 'QTG', 'description' => 'Quarter panel glass'],
            ['name' => 'Other', 'code' => 'OTHER', 'description' => 'Other glass positions'],
        ];

        foreach ($positions as $position) {
            GlassPosition::create($position);
        }
    }
}
