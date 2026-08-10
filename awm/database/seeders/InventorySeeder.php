<?php

namespace Database\Seeders;

use App\Models\GlassProduct;
use App\Models\Rack;
use App\Models\StockBalance;
use App\Models\StockLot;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        $supplier1 = Supplier::where('name', 'Sinar Mulia Glass')->first();
        $supplier2 = Supplier::where('name', 'Mitra Kaca Indonesia')->first();
        $rack1 = Rack::where('name', 'Rack A1')->first();
        $rack2 = Rack::where('name', 'Rack A2')->first();

        // Avanza front windshield - 2 lots
        $avanzaLfw = GlassProduct::where('sku', 'LFW-TOY-AVZ-01')->first();
        $lot1 = StockLot::create([
            'glass_product_id' => $avanzaLfw->id,
            'supplier_id' => $supplier1->id,
            'lot_number' => 'LOT-2026-001',
            'purchase_cost' => 350000,
            'purchase_date' => Carbon::now()->subMonths(2),
            'notes' => 'Initial stock purchase',
        ]);
        StockBalance::create(['stock_lot_id' => $lot1->id, 'rack_id' => $rack1->id, 'quantity' => 5]);

        $lot2 = StockLot::create([
            'glass_product_id' => $avanzaLfw->id,
            'supplier_id' => $supplier2->id,
            'lot_number' => 'LOT-2026-002',
            'purchase_cost' => 370000,
            'purchase_date' => Carbon::now()->subMonth(),
        ]);
        StockBalance::create(['stock_lot_id' => $lot2->id, 'rack_id' => $rack2->id, 'quantity' => 3]);

        // Brio front windshield
        $brioLfw = GlassProduct::where('sku', 'LFW-HON-BRI-01')->first();
        $lot3 = StockLot::create([
            'glass_product_id' => $brioLfw->id,
            'supplier_id' => $supplier1->id,
            'lot_number' => 'LOT-2026-003',
            'purchase_cost' => 300000,
            'purchase_date' => Carbon::now()->subMonth(),
        ]);
        StockBalance::create(['stock_lot_id' => $lot3->id, 'rack_id' => $rack1->id, 'quantity' => 4]);

        // Xenia front windshield - low stock
        $xeniaLfw = GlassProduct::where('sku', 'LFW-DAI-XEN-01')->first();
        $lot4 = StockLot::create([
            'glass_product_id' => $xeniaLfw->id,
            'supplier_id' => $supplier2->id,
            'lot_number' => 'LOT-2026-004',
            'purchase_cost' => 320000,
            'purchase_date' => Carbon::now()->subMonths(3),
        ]);
        StockBalance::create(['stock_lot_id' => $lot4->id, 'rack_id' => $rack1->id, 'quantity' => 1]);
    }
}
