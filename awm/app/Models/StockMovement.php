<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['stock_lot_id', 'rack_id', 'type', 'quantity', 'reference_type', 'reference_id', 'notes'];

    protected $casts = ['quantity' => 'integer'];

    public function lot()
    {
        return $this->belongsTo(StockLot::class, 'stock_lot_id');
    }

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
