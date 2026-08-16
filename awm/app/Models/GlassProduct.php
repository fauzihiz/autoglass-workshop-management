<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $total_stock
 */
class GlassProduct extends Model
{
    use SoftDeletes;

    protected $fillable = ['glass_position_id', 'name', 'sku', 'description', 'minimum_stock', 'is_active'];

    protected $casts = ['minimum_stock' => 'integer', 'is_active' => 'boolean'];

    public function position()
    {
        return $this->belongsTo(GlassPosition::class, 'glass_position_id');
    }

    public function compatibilities()
    {
        return $this->hasMany(ProductCompatibility::class);
    }

    public function accessories()
    {
        return $this->belongsToMany(Accessory::class, 'product_accessories');
    }

    public function stockLots()
    {
        return $this->hasMany(StockLot::class);
    }

    /**
     * Total stock quantity across all lots and racks.
     */
    public function getTotalStockAttribute(): int
    {
        return StockBalance::whereIn(
            'stock_lot_id',
            $this->stockLots()->pluck('id')
        )->sum('quantity');
    }
}
