<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    protected $fillable = ['transaction_id', 'itemable_type', 'itemable_id', 'quantity', 'unit_price', 'total_price', 'notes'];

    protected $casts = ['quantity' => 'integer', 'unit_price' => 'decimal:2', 'total_price' => 'decimal:2'];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function itemable()
    {
        return $this->morphTo();
    }

    public function allocations()
    {
        return $this->hasMany(StockAllocation::class);
    }

    public function serviceAssignment()
    {
        return $this->hasOne(ServiceAssignment::class);
    }
}
