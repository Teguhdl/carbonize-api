<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;
    protected $table = 'vehicle_types';

    protected $fillable = [
        'name',
        'default_efficiency',
    ];

    protected $casts = [
        'default_efficiency' => 'float',
    ];

    public function consumptionEntries()
    {
        return $this->hasMany(ConsumptionEntry::class, 'vehicle_type_id');
    }
}
