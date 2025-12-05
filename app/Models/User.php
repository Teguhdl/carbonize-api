<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profileImage',
        'dailyCarbonLimit',
        'dateOfBirth',
        'lastLogin',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'dailyCarbonLimit' => 'integer',
        'dateOfBirth' => 'date',
        'lastLogin' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relations
    public function consumptionEntries()
    {
        return $this->hasMany(ConsumptionEntry::class, 'user_id');
    }
}
