<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCompatibility extends Model
{
    protected $fillable = ['glass_product_id', 'car_model_id', 'year_from', 'year_to'];

    public function glassProduct()
    {
        return $this->belongsTo(GlassProduct::class);
    }

    public function carModel()
    {
        return $this->belongsTo(CarModel::class);
    }
}
