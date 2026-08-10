<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Sinar Mulia Glass',
                'contact_person' => 'Budi Santoso',
                'phone' => '0812-3456-7890',
                'email' => 'budi@sinarulia.co.id',
                'address' => 'Jl. Pahlawan No. 45, Surabaya',
            ],
            [
                'name' => 'Mitra Kaca Indonesia',
                'contact_person' => 'Andi Wijaya',
                'phone' => '0856-9012-3456',
                'email' => 'andi@mitrakaca.co.id',
                'address' => 'Jl. Raya Kertajaya No. 123, Surabaya',
            ],
            [
                'name' => 'Pacific Glass Supply',
                'contact_person' => 'Hendra Lim',
                'phone' => '0878-5678-1234',
                'email' => 'hendra@pacificglass.co.id',
                'address' => 'Jl. Ketintang No. 67, Surabaya',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
