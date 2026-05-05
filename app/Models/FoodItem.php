<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    protected $table = 'food_items';

    protected $fillable = [
        'name',
        'emission_factor',
        'climatiq_id',
        'calculation_method',
    ];

    protected $casts = [
        'emission_factor' => 'float',
    ];

    public function consumptionEntries()
    {
        return $this->hasMany(ConsumptionEntry::class, 'food_item_id');
    }
}
