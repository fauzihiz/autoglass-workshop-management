<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createGlassProductWithStock(array $overrides = []): array
{
    $position = \App\Models\GlassPosition::create(['name' => 'Front', 'code' => 'F', 'description' => 'Front windshield']);
    $supplier = \App\Models\Supplier::create(['name' => 'Test Supplier', 'is_active' => true]);
    $product = \App\Models\GlassProduct::create(array_merge([
        'glass_position_id' => $position->id,
        'name' => 'Test Glass Product',
        'sku' => 'TGP-001',
        'minimum_stock' => 5,
        'is_active' => true,
    ], $overrides));

    $rack = \App\Models\Rack::create(['name' => 'Rack A']);
    $lot = \App\Models\StockLot::create([
        'glass_product_id' => $product->id,
        'supplier_id' => $supplier->id,
        'lot_number' => 'LOT-001',
        'purchase_cost' => 100000,
        'purchase_date' => now()->subDays(10),
    ]);

    \App\Models\StockBalance::create([
        'stock_lot_id' => $lot->id,
        'rack_id' => $rack->id,
        'quantity' => $overrides['stock_quantity'] ?? 10,
    ]);

    $customer = \App\Models\Customer::create(['name' => 'Test Customer', 'phone' => '08123456789']);
    $brand = \App\Models\CarBrand::create(['name' => 'Toyota', 'slug' => 'toyota']);
    $model = \App\Models\CarModel::create(['car_brand_id' => $brand->id, 'name' => 'Avanza', 'slug' => 'avanza']);
    $vehicle = \App\Models\Vehicle::create([
        'customer_id' => $customer->id,
        'car_brand_id' => $brand->id,
        'car_model_id' => $model->id,
        'license_plate' => 'B 1234 TEST',
    ]);

    return compact('position', 'supplier', 'product', 'rack', 'lot', 'customer', 'brand', 'model', 'vehicle');
}

function createPendingTransaction(array $data): \App\Models\Transaction
{
    $transaction = \App\Models\Transaction::create([
        'customer_id' => $data['customer']->id,
        'vehicle_id' => $data['vehicle']->id,
        'type' => \App\Enums\TransactionType::GlassSale->value,
        'invoice_number' => \App\Models\Transaction::generateInvoiceNumber(),
        'status' => \App\Enums\TransactionStatus::Pending->value,
    ]);

    \App\Models\TransactionItem::create([
        'transaction_id' => $transaction->id,
        'itemable_type' => \App\Models\GlassProduct::class,
        'itemable_id' => $data['product']->id,
        'quantity' => $data['quantity'] ?? 2,
        'unit_price' => $data['unit_price'] ?? 500000,
        'total_price' => ($data['quantity'] ?? 2) * ($data['unit_price'] ?? 500000),
    ]);

    return $transaction;
}
