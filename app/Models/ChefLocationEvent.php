<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChefLocationEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id', 'kindgarden_id', 'event_type',
        'happened_at', 'lat', 'lng', 'distance_m', 'is_mock',
    ];

    protected $casts = [
        'happened_at' => 'datetime',
        'lat' => 'float',
        'lng' => 'float',
        'is_mock' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kindgarden()
    {
        return $this->belongsTo(Kindgarden::class);
    }
}
