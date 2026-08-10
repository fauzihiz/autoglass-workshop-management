<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlassPosition extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function products()
    {
        return $this->hasMany(GlassProduct::class);
    }
}
