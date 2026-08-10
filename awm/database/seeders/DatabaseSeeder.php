<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@workshop.local',
        ]);

        $this->call([
            CarBrandSeeder::class,
            CarModelSeeder::class,
            GlassPositionSeeder::class,
            SupplierSeeder::class,
            AccessorySeeder::class,
            ServiceSeeder::class,
            TechnicianSeeder::class,
            CustomerSeeder::class,
            RackSeeder::class,
            GlassProductSeeder::class,
            InventorySeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
