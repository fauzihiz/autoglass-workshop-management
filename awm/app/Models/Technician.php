<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Technician extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'phone', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function assignments()
    {
        return $this->hasMany(ServiceAssignment::class);
    }
}
