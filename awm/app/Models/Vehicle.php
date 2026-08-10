<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;

    protected $fillable = ['customer_id', 'car_brand_id', 'car_model_id', 'license_plate', 'year', 'color', 'notes'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function brand()
    {
        return $this->belongsTo(CarBrand::class, 'car_brand_id');
    }

    public function model()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
