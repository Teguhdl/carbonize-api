<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumptionEntry extends Model
{
    use HasFactory;
    protected $table = 'consumption_entries';

    protected $fillable = [
        'user_id',
        'entry_type',
        'entry_date',
        'quantity',
        'emissions',
        'image',
        'metadata',
        // Food
        'food_item_id',
        // Private vehicle
        'vehicle_type_id',
        'fuel_type_id',
        'custom_efficiency',
        // Public transit
        'transit_vehicle_id',
    ];

    protected $casts = [
        'entry_date'        => 'date',
        'quantity'          => 'float',
        'emissions'         => 'float',
        'custom_efficiency' => 'float',
        'metadata'          => 'array',
        'created_at'        => 'datetime',
    ];

    /* --------------------------------------------------------------------------
     | Relations
     -------------------------------------------------------------------------- */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Food & Packaging */
    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }

    /** Private Vehicle — kendaraan */
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    /** Private Vehicle — bahan bakar */
    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    /** Public Transit */
    public function transitVehicle()
    {
        return $this->belongsTo(TransitVehicle::class, 'transit_vehicle_id');
    }

    /* --------------------------------------------------------------------------
     | Accessors
     -------------------------------------------------------------------------- */

    public function getImageAttribute($value): ?string
    {
        return $value ? url('storage/' . $value) : null;
    }
}
