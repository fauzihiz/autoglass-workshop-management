<?php

use App\Models\StockBalance;
use App\Models\StockMovement;

it('increments stock balance on stock-in', function () {
    $data = createGlassProductWithStock(['stock_quantity' => 5]);

    StockBalance::where('stock_lot_id', $data['lot']->id)
        ->where('rack_id', $data['rack']->id)
        ->increment('quantity', 3);

    StockMovement::create([
        'stock_lot_id' => $data['lot']->id,
        'rack_id' => $data['rack']->id,
        'type' => 'in',
        'quantity' => 3,
        'notes' => 'Test stock in',
    ]);

    $balance = StockBalance::where('stock_lot_id', $data['lot']->id)
        ->where('rack_id', $data['rack']->id)
        ->first();

    expect($balance->quantity)->toBe(8);
    expect($data['product']->fresh()->total_stock)->toBe(8);
});

it('creates a new balance if lot-rack combo does not exist', function () {
    $data = createGlassProductWithStock(['stock_quantity' => 0]);
    $rack2 = \App\Models\Rack::create(['name' => 'Rack B']);

    $balance = StockBalance::firstOrCreate(
        ['stock_lot_id' => $data['lot']->id, 'rack_id' => $rack2->id],
        ['quantity' => 0]
    );
    $balance->increment('quantity', 5);

    expect($balance->fresh()->quantity)->toBe(5);
});
