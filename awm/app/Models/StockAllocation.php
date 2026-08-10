<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAllocation extends Model
{
    protected $fillable = ['transaction_item_id', 'stock_lot_id', 'rack_id', 'quantity'];

    protected $casts = ['quantity' => 'integer'];

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }

    public function lot()
    {
        return $this->belongsTo(StockLot::class, 'stock_lot_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }
}
