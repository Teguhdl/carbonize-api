<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmissionFactorItem extends Model
{
    protected $table = 'emission_factor_items';

    protected $fillable = [
        'factor_category_id',
        'name',
        'value',
    ];

    public $timestamps = false;

    protected $casts = [
        'value' => 'string',
    ];

    // Relations
    public function category()
    {
        return $this->belongsTo(EmissionFactorCategory::class, 'factor_category_id');
    }

    public function consumptionEntries()
    {
        return $this->hasMany(ConsumptionEntry::class, 'factor_items_id');
    }
}
