<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmissionFactorCategory extends Model
{
    protected $table = 'emission_factor_categories';

    protected $fillable = [
        'category_name',
    ];

    public $timestamps = false;

    // Relations
    public function items()
    {
        return $this->hasMany(EmissionFactorItem::class, 'factor_category_id');
    }
}
