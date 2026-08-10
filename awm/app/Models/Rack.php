<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rack extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'location_description'];

    public function balances()
    {
        return $this->hasMany(StockBalance::class);
    }
}
