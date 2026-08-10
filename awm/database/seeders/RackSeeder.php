<?php

namespace Database\Seeders;

use App\Models\Rack;
use Illuminate\Database\Seeder;

class RackSeeder extends Seeder
{
    public function run(): void
    {
        $racks = [
            ['name' => 'Rack A1', 'location_description' => 'Main storage area, left side'],
            ['name' => 'Rack A2', 'location_description' => 'Main storage area, right side'],
            ['name' => 'Rack B1', 'location_description' => 'Secondary storage, near workshop'],
            ['name' => 'Rack B2', 'location_description' => 'Secondary storage, near entrance'],
        ];

        foreach ($racks as $rack) {
            Rack::create($rack);
        }
    }
}
