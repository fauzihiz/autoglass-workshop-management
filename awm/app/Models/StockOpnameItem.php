<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    protected $fillable = ['stock_opname_id', 'stock_lot_id', 'rack_id', 'system_quantity', 'actual_quantity', 'difference', 'notes'];

    protected $casts = ['system_quantity' => 'integer', 'actual_quantity' => 'integer', 'difference' => 'integer'];

    public function opname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
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
