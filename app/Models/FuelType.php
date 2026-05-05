<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    protected $table = 'fuel_types';

    protected $fillable = [
        'name',
        'emission_factor',
    ];

    protected $casts = [
        'emission_factor' => 'float',
    ];

    public function consumptionEntries()
    {
        return $this->hasMany(ConsumptionEntry::class, 'fuel_type_id');
    }
}
