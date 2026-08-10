<?php

namespace Database\Seeders;

use App\Models\CarModel;
use App\Models\GlassPosition;
use App\Models\GlassProduct;
use Illuminate\Database\Seeder;

class GlassProductSeeder extends Seeder
{
    public function run(): void
    {
        $lfw = GlassPosition::where('code', 'LFW')->first();
        $fdr = GlassPosition::where('code', 'FDR')->first();
        $fdl = GlassPosition::where('code', 'FDL')->first();
        $rw = GlassPosition::where('code', 'RW')->first();

        $products = [
            ['glass_position_id' => $lfw->id, 'name' => 'Kaca Depan Toyota Avanza', 'sku' => 'LFW-TOY-AVZ-01', 'minimum_stock' => 3],
            ['glass_position_id' => $fdr->id, 'name' => 'Kaca Pintu Depan Kanan Toyota Avanza', 'sku' => 'FDR-TOY-AVZ-01', 'minimum_stock' => 4],
            ['glass_position_id' => $fdl->id, 'name' => 'Kaca Pintu Depan Kiri Toyota Avanza', 'sku' => 'FDL-TOY-AVZ-01', 'minimum_stock' => 4],
            ['glass_position_id' => $lfw->id, 'name' => 'Kaca Depan Honda Brio', 'sku' => 'LFW-HON-BRI-01', 'minimum_stock' => 3],
            ['glass_position_id' => $fdr->id, 'name' => 'Kaca Pintu Depan Kanan Honda Brio', 'sku' => 'FDR-HON-BRI-01', 'minimum_stock' => 4],
            ['glass_position_id' => $rw->id, 'name' => 'Kaca Belakang Honda Brio', 'sku' => 'RW-HON-BRI-01', 'minimum_stock' => 2],
            ['glass_position_id' => $lfw->id, 'name' => 'Kaca Depan Daihatsu Xenia', 'sku' => 'LFW-DAI-XEN-01', 'minimum_stock' => 3],
            ['glass_position_id' => $fdr->id, 'name' => 'Kaca Pintu Depan Kanan Daihatsu Xenia', 'sku' => 'FDR-DAI-XEN-01', 'minimum_stock' => 4],
        ];

        $avanza = CarModel::where('slug', 'avanza')->first();
        $brio = CarModel::where('slug', 'brio')->first();
        $xenia = CarModel::where('slug', 'xenia')->first();

        foreach ($products as $product) {
            GlassProduct::create($product);
        }

        // Link compatibilities
        $avanzaLfw = GlassProduct::where('sku', 'LFW-TOY-AVZ-01')->first();
        $brioLfw = GlassProduct::where('sku', 'LFW-HON-BRI-01')->first();
        $xeniaLfw = GlassProduct::where('sku', 'LFW-DAI-XEN-01')->first();

        $avanzaLfw->compatibilities()->create(['car_model_id' => $avanza->id]);
        $brioLfw->compatibilities()->create(['car_model_id' => $brio->id]);
        $xeniaLfw->compatibilities()->create(['car_model_id' => $xenia->id]);
    }
}
