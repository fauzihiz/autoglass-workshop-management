<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'default_price', 'is_active'];

    protected $casts = ['default_price' => 'decimal:2', 'is_active' => 'boolean'];
}
