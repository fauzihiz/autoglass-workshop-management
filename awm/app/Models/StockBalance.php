<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    protected $fillable = ['stock_lot_id', 'rack_id', 'quantity'];

    protected $casts = ['quantity' => 'integer'];

    public function lot()
    {
        return $this->belongsTo(StockLot::class, 'stock_lot_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }
}
