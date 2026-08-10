<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    protected $fillable = ['name', 'slug'];

    public function models()
    {
        return $this->hasMany(CarModel::class);
    }
}
