<?php

use App\Enums\TransactionStatus;
use App\Models\GlassProduct;
use App\Models\StockAllocation;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Transaction;

it('restores stock when cancelling a confirmed transaction', function () {
    $data = createGlassProductWithStock();
    $tx = createPendingTransaction($data);

    // Confirm it first
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

    // Stock should be 8 after confirmation
    expect($data['product']->fresh()->total_stock)->toBe(8);

    // Now cancel
    DB::transaction(function () use ($tx) {
        $allocations = StockAllocation::whereHas('transactionItem', fn ($q) => $q->where('transaction_id', $tx->id))->get();
        foreach ($allocations as $allocation) {
            $balance = StockBalance::firstOrCreate(
                ['stock_lot_id' => $allocation->stock_lot_id, 'rack_id' => $allocation->rack_id],
                ['quantity' => 0]
            );
            $balance->increment('quantity', $allocation->quantity);

            StockMovement::create([
                'stock_lot_id' => $allocation->stock_lot_id, 'rack_id' => $allocation->rack_id,
                'type' => 'in', 'quantity' => $allocation->quantity,
                'reference_type' => Transaction::class, 'reference_id' => $tx->id,
                'notes' => 'Restored from cancelled ' . $tx->invoice_number,
            ]);
        }
        StockAllocation::whereHas('transactionItem', fn ($q) => $q->where('transaction_id', $tx->id))->delete();
        $tx->update(['status' => TransactionStatus::Cancelled->value]);
    });

    $tx->refresh();
    expect($tx->status)->toBe(TransactionStatus::Cancelled->value);
    expect($data['product']->fresh()->total_stock)->toBe(10);

    $allocations = StockAllocation::whereHas('transactionItem', fn ($q) => $q->where('transaction_id', $tx->id))->get();
    expect($allocations)->toHaveCount(0);
});
