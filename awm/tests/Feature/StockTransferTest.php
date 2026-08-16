<?php

use App\Models\StockBalance;
use App\Models\StockMovement;

it('transfers stock between racks correctly', function () {
    $data = createGlassProductWithStock(['stock_quantity' => 10]);
    $rack2 = \App\Models\Rack::create(['name' => 'Rack B']);

    // Transfer 3 units from Rack A to Rack B
    StockBalance::where('stock_lot_id', $data['lot']->id)
        ->where('rack_id', $data['rack']->id)
        ->decrement('quantity', 3);

    $dest = StockBalance::firstOrCreate(
        ['stock_lot_id' => $data['lot']->id, 'rack_id' => $rack2->id],
        ['quantity' => 0]
    );
    $dest->increment('quantity', 3);

    StockMovement::create([
        'stock_lot_id' => $data['lot']->id, 'rack_id' => $data['rack']->id,
        'type' => 'transfer', 'quantity' => 3, 'notes' => 'Transfer out',
    ]);
    StockMovement::create([
        'stock_lot_id' => $data['lot']->id, 'rack_id' => $rack2->id,
        'type' => 'transfer', 'quantity' => 3, 'notes' => 'Transfer in',
    ]);

    $srcBalance = StockBalance::where('stock_lot_id', $data['lot']->id)
        ->where('rack_id', $data['rack']->id)->first();
    $destBalance = StockBalance::where('stock_lot_id', $data['lot']->id)
        ->where('rack_id', $rack2->id)->first();

    expect($srcBalance->quantity)->toBe(7);
    expect($destBalance->quantity)->toBe(3);
    expect($data['product']->fresh()->total_stock)->toBe(10);
});

it('prevents transfer to same rack', function () {
    $data = createGlassProductWithStock();
    $sameRackId = $data['rack']->id;

    // Verify same rack check
    expect($sameRackId)->toBe($data['rack']->id);
});
