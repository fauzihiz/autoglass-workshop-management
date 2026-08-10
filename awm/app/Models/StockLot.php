<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLot extends Model
{
    protected $fillable = ['glass_product_id', 'supplier_id', 'lot_number', 'purchase_cost', 'purchase_date', 'notes'];

    protected $casts = ['purchase_cost' => 'decimal:2', 'purchase_date' => 'date'];

    public function product()
    {
        return $this->belongsTo(GlassProduct::class, 'glass_product_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function balances()
    {
        return $this->hasMany(StockBalance::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
