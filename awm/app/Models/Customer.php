<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'email', 'address', 'notes'];

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
