<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsumptionEntry extends Model
{
    protected $table = 'consumption_entries';

    protected $fillable = [
        'user_id',
        'factor_items_id',
        'entry_date',
        'emissions',
        'image',
        'metadata',
        'quantity',
    ];

    protected $casts = [
        'entry_date' => 'datetime',
        'emissions' => 'float',
        'quantity' => 'float',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function factorItem()
    {
        return $this->belongsTo(EmissionFactorItem::class, 'factor_items_id');
    }

    public function getImageAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}
