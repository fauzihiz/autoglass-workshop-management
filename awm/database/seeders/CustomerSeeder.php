<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Budi Hartono', 'phone' => '0812-1111-2222', 'email' => 'budi@gmail.com', 'address' => 'Jl. Kenjeran No. 10, Surabaya'],
            ['name' => 'Siti Rahayu', 'phone' => '0856-3333-4444', 'email' => null, 'address' => 'Jl. Dharmawangsa No. 55, Surabaya'],
            ['name' => 'Agus Prasetyo', 'phone' => '0878-5555-6666', 'email' => 'agus@yahoo.com', 'address' => 'Jl. Dharmo No. 88, Surabaya'],
            ['name' => 'Dewi Lestari', 'phone' => '0813-7777-8888', 'email' => null, 'address' => 'Jl. Pemuda No. 123, Surabaya'],
            ['name' => 'Rudi Kurniawan', 'phone' => '0852-9999-0000', 'email' => 'rudi@outlook.com', 'address' => 'Jl. Rungkut No. 200, Surabaya'],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
