<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    protected $fillable = ['car_brand_id', 'name', 'slug'];

    public function brand()
    {
        return $this->belongsTo(CarBrand::class);
    }

    public function compatibilities()
    {
        return $this->hasMany(ProductCompatibility::class);
    }

    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }
}
