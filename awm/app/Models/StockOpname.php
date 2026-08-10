<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = ['opname_date', 'notes', 'status'];

    protected $casts = ['opname_date' => 'date'];

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }
}
