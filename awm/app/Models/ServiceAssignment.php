<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAssignment extends Model
{
    protected $fillable = ['transaction_item_id', 'technician_id'];

    public function transactionItem()
    {
        return $this->belongsTo(TransactionItem::class);
    }

    public function technician()
    {
        return $this->belongsTo(Technician::class);
    }
}
