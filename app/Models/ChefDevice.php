<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChefDevice extends Model
{
    protected $fillable = [
        'user_id', 'platform', 'fcm_token',
        'device_model', 'app_version', 'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
