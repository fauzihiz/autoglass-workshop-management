<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\StockMovementType;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\CarBrand;
use App\Models\CarModel;
use App\Models\Customer;
use App\Models\GlassProduct;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceAssignment;
use App\Models\StockAllocation;
use App\Models\StockBalance;
use App\Models\StockLot;
use App\Models\StockMovement;
use App\Models\Technician;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $customer = Customer::first();
        $technician = Technician::first();
        $service = Service::first();

        // Create a vehicle for the customer
        $toyotaBrand = CarBrand::where('slug', 'toyota')->first();
        $avanza = CarModel::where('slug', 'avanza')->first();
        $vehicle = Vehicle::create([
            'customer_id' => $customer->id,
            'car_brand_id' => $toyotaBrand->id,
            'car_model_id' => $avanza->id,
            'license_plate' => 'L 1234 AB',
            'year' => 2020,
            'color' => 'White',
        ]);

        // Transaction 1: Glass installation
        $tx1 = Transaction::create([
            'customer_id' => $customer->id,
            'vehicle_id' => $vehicle->id,
            'type' => TransactionType::GlassInstallation->value,
            'invoice_number' => 'INV-2026-0001',
            'status' => TransactionStatus::Confirmed->value,
            'notes' => 'Kaca depan retak, ganti baru',
        ]);

        // Add glass item
        $avanzaLfw = GlassProduct::where('sku', 'LFW-TOY-AVZ-01')->first();
        $glassItem = TransactionItem::create([
            'transaction_id' => $tx1->id,
            'itemable_type' => GlassProduct::class,
            'itemable_id' => $avanzaLfw->id,
            'quantity' => 1,
            'unit_price' => 450000,
            'total_price' => 450000,
        ]);

        // Add service item
        $svcItem = TransactionItem::create([
            'transaction_id' => $tx1->id,
            'itemable_type' => Service::class,
            'itemable_id' => $service->id,
            'quantity' => 1,
            'unit_price' => 150000,
            'total_price' => 150000,
        ]);

        ServiceAssignment::create([
            'transaction_item_id' => $svcItem->id,
            'technician_id' => $technician->id,
        ]);

        // Create stock allocation
        $lot = StockLot::where('glass_product_id', $avanzaLfw->id)->first();
        $balance = StockBalance::where('stock_lot_id', $lot->id)->first();
        $balance->decrement('quantity', 1);

        StockAllocation::create([
            'transaction_item_id' => $glassItem->id,
            'stock_lot_id' => $lot->id,
            'rack_id' => $balance->rack_id,
            'quantity' => 1,
        ]);

        StockMovement::create([
            'stock_lot_id' => $lot->id,
            'rack_id' => $balance->rack_id,
            'type' => StockMovementType::Out->value,
            'quantity' => 1,
            'reference_type' => Transaction::class,
            'reference_id' => $tx1->id,
            'notes' => 'Stock out for INV-2026-0001',
        ]);

        // Payment for tx1
        Payment::create([
            'transaction_id' => $tx1->id,
            'amount' => 600000,
            'method' => PaymentMethod::Cash->value,
            'paid_at' => $tx1->created_at,
        ]);

        // Transaction 2: Pending (no payment yet)
        $customer2 = Customer::where('name', 'Siti Rahayu')->first();
        $tx2 = Transaction::create([
            'customer_id' => $customer2->id,
            'vehicle_id' => $vehicle->id,
            'type' => TransactionType::ServiceOnly->value,
            'invoice_number' => 'INV-2026-0002',
            'status' => TransactionStatus::Pending->value,
            'notes' => 'Servis power window kiri',
        ]);

        TransactionItem::create([
            'transaction_id' => $tx2->id,
            'itemable_type' => Service::class,
            'itemable_id' => $service->id,
            'quantity' => 1,
            'unit_price' => 200000,
            'total_price' => 200000,
        ]);
    }
}
