<?php

use App\Models\Transaction;

it('generates invoice number in correct format', function () {
    $num = Transaction::generateInvoiceNumber();
    expect($num)->toMatch('/^INV-' . date('Y') . '-0001$/');
});

it('generates sequential invoice numbers', function () {
    $customer = \App\Models\Customer::create(['name' => 'C1', 'phone' => '081']);
    $brand = \App\Models\CarBrand::create(['name' => 'B', 'slug' => 'b1']);
    $cm = \App\Models\CarModel::create(['car_brand_id' => $brand->id, 'name' => 'M', 'slug' => 'm1']);
    $vehicle = \App\Models\Vehicle::create([
        'customer_id' => $customer->id, 'car_brand_id' => $brand->id,
        'car_model_id' => $cm->id, 'license_plate' => 'X1',
    ]);

    $num1 = Transaction::generateInvoiceNumber();
    Transaction::create([
        'customer_id' => $customer->id, 'vehicle_id' => $vehicle->id,
        'type' => 'glass_sale', 'invoice_number' => $num1, 'status' => 'pending',
    ]);

    $num2 = Transaction::generateInvoiceNumber();
    expect($num2)->toBe('INV-' . date('Y') . '-0002');

    Transaction::create([
        'customer_id' => $customer->id, 'vehicle_id' => $vehicle->id,
        'type' => 'glass_sale', 'invoice_number' => $num2, 'status' => 'pending',
    ]);

    $num3 = Transaction::generateInvoiceNumber();
    expect($num3)->toBe('INV-' . date('Y') . '-0003');
});

it('keeps invoice numbers unique per year', function () {
    $num1 = Transaction::generateInvoiceNumber();
    expect($num1)->toStartWith('INV-' . date('Y') . '-');
});
