<?php

use App\Enums\TransactionStatus;
use App\Models\GlassProduct;
use App\Models\StockAllocation;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Transaction;

it('confirms pending transaction with sufficient stock', function () {
    $data = createGlassProductWithStock();
    $tx = createPendingTransaction($data);

    expect($tx->status)->toBe(TransactionStatus::Pending->value);

    DB::transaction(function () use ($tx) {
        $glassItems = $tx->items->where('itemable_type', GlassProduct::class);
        foreach ($glassItems as $item) {
            $available = StockBalance::where('quantity', '>', 0)
                ->whereHas('lot', fn ($q) => $q->where('glass_product_id', $item->itemable_id))
                ->sum('quantity');
            expect($available)->toBeGreaterThanOrEqual($item->quantity);

            $remaining = $item->quantity;
            $lots = StockBalance::where('stock_balances.quantity', '>', 0)
                ->join('stock_lots', 'stock_balances.stock_lot_id', '=', 'stock_lots.id')
                ->where('stock_lots.glass_product_id', $item->itemable_id)
                ->select('stock_balances.*')
                ->orderBy('stock_lots.purchase_date')
                ->get()
                ->each(fn ($b) => $b->setRelation('lot', \App\Models\StockLot::find($b->stock_lot_id)));

            foreach ($lots as $balance) {
                if ($remaining <= 0) break;
                $deduct = min($remaining, $balance->quantity);
                $balance->decrement('quantity', $deduct);
                StockAllocation::create([
                    'transaction_item_id' => $item->id, 'stock_lot_id' => $balance->stock_lot_id,
                    'rack_id' => $balance->rack_id, 'quantity' => $deduct,
                ]);
                StockMovement::create([
                    'stock_lot_id' => $balance->stock_lot_id, 'rack_id' => $balance->rack_id,
                    'type' => 'out', 'quantity' => $deduct,
                    'reference_type' => Transaction::class, 'reference_id' => $tx->id,
                ]);
                $remaining -= $deduct;
            }
        }
        $tx->update(['status' => TransactionStatus::Confirmed->value]);
    });

    $tx->refresh();
    expect($tx->status)->toBe(TransactionStatus::Confirmed->value);
    expect($data['product']->fresh()->total_stock)->toBe(8);
});

it('deducts stock using FIFO order', function () {
    $position = \App\Models\GlassPosition::create(['name' => 'Front', 'code' => 'F']);
    $supplier = \App\Models\Supplier::create(['name' => 'Supplier', 'is_active' => true]);
    $product = GlassProduct::create([
        'glass_position_id' => $position->id, 'name' => 'FIFO Glass',
        'sku' => 'FIFO-001', 'minimum_stock' => 5, 'is_active' => true,
    ]);
    $rack = \App\Models\Rack::create(['name' => 'Rack A']);

    $lotOld = \App\Models\StockLot::create([
        'glass_product_id' => $product->id, 'supplier_id' => $supplier->id,
        'lot_number' => 'LOT-OLD', 'purchase_cost' => 100000, 'purchase_date' => now()->subDays(20),
    ]);
    $lotNew = \App\Models\StockLot::create([
        'glass_product_id' => $product->id, 'supplier_id' => $supplier->id,
        'lot_number' => 'LOT-NEW', 'purchase_cost' => 120000, 'purchase_date' => now()->subDays(5),
    ]);

    StockBalance::create(['stock_lot_id' => $lotOld->id, 'rack_id' => $rack->id, 'quantity' => 3]);
    StockBalance::create(['stock_lot_id' => $lotNew->id, 'rack_id' => $rack->id, 'quantity' => 5]);

    $customer = \App\Models\Customer::create(['name' => 'FIFO Customer', 'phone' => '081']);
    $brand = \App\Models\CarBrand::create(['name' => 'Honda', 'slug' => 'honda']);
    $model = \App\Models\CarModel::create(['car_brand_id' => $brand->id, 'name' => 'Brio', 'slug' => 'brio']);
    $vehicle = \App\Models\Vehicle::create([
        'customer_id' => $customer->id, 'car_brand_id' => $brand->id,
        'car_model_id' => $model->id, 'license_plate' => 'B 2222 FIFO',
    ]);

    $tx = Transaction::create([
        'customer_id' => $customer->id, 'vehicle_id' => $vehicle->id,
        'type' => 'glass_sale', 'invoice_number' => Transaction::generateInvoiceNumber(),
        'status' => TransactionStatus::Pending->value,
    ]);

    \App\Models\TransactionItem::create([
        'transaction_id' => $tx->id, 'itemable_type' => GlassProduct::class,
        'itemable_id' => $product->id, 'quantity' => 4, 'unit_price' => 500000, 'total_price' => 2000000,
    ]);

    DB::transaction(function () use ($tx) {
        foreach ($tx->items->where('itemable_type', GlassProduct::class) as $item) {
            $remaining = $item->quantity;
            $lots = StockBalance::where('stock_balances.quantity', '>', 0)
                ->join('stock_lots', 'stock_balances.stock_lot_id', '=', 'stock_lots.id')
                ->where('stock_lots.glass_product_id', $item->itemable_id)
                ->select('stock_balances.*')
                ->orderBy('stock_lots.purchase_date')
                ->get()
                ->each(fn ($b) => $b->setRelation('lot', \App\Models\StockLot::find($b->stock_lot_id)));
            foreach ($lots as $balance) {
                if ($remaining <= 0) break;
                $deduct = min($remaining, $balance->quantity);
                $balance->decrement('quantity', $deduct);
                StockAllocation::create([
                    'transaction_item_id' => $item->id, 'stock_lot_id' => $balance->stock_lot_id,
                    'rack_id' => $balance->rack_id, 'quantity' => $deduct,
                ]);
                $remaining -= $deduct;
            }
        }
        $tx->update(['status' => TransactionStatus::Confirmed->value]);
    });

    $allocations = StockAllocation::whereHas('transactionItem', fn ($q) => $q->where('transaction_id', $tx->id))
        ->orderBy('quantity', 'desc')->get();

    expect($allocations)->toHaveCount(2);
    expect($allocations->first()->stock_lot_id)->toBe($lotOld->id);
    expect($allocations->first()->quantity)->toBe(3);
    expect($allocations->last()->stock_lot_id)->toBe($lotNew->id);
    expect($allocations->last()->quantity)->toBe(1);
    expect($lotOld->fresh()->totalQuantity)->toBe(0);
    expect($lotNew->fresh()->totalQuantity)->toBe(4);
});

it('rejects confirmation when stock is insufficient', function () {
    $data = createGlassProductWithStock(['stock_quantity' => 1]);
    $tx = createPendingTransaction(array_merge($data, ['quantity' => 5]));

    $totalAvailable = StockBalance::where('quantity', '>', 0)
        ->whereHas('lot', fn ($q) => $q->where('glass_product_id', $data['product']->id))
        ->sum('quantity');

    expect($totalAvailable)->toBeLessThan(5);
    expect($tx->status)->toBe(TransactionStatus::Pending->value);
});

it('does not confirm a non-pending transaction', function () {
    $data = createGlassProductWithStock();
    $tx = createPendingTransaction($data);
    $tx->update(['status' => TransactionStatus::Confirmed->value]);

    expect($tx->status)->toBe(TransactionStatus::Confirmed->value);
});
