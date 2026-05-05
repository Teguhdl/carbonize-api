<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransitVehicle extends Model
{
    protected $table = 'transit_vehicles';

    protected $fillable = [
        'name',
        'emission_factor',
        'avg_passengers',
    ];

    protected $casts = [
        'emission_factor'  => 'float',
        'avg_passengers'   => 'float',
    ];

    public function consumptionEntries()
    {
        return $this->hasMany(ConsumptionEntry::class, 'transit_vehicle_id');
    }
}
