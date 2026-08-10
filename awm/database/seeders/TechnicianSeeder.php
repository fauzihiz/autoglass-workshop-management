<?php

namespace Database\Seeders;

use App\Models\Technician;
use Illuminate\Database\Seeder;

class TechnicianSeeder extends Seeder
{
    public function run(): void
    {
        $technicians = [
            ['name' => 'Pak Joko', 'phone' => '0813-1111-2222'],
            ['name' => 'Pak Rudi', 'phone' => '0852-3333-4444'],
            ['name' => 'Pak Agus', 'phone' => '0878-5555-6666'],
        ];

        foreach ($technicians as $technician) {
            Technician::create($technician);
        }
    }
}
