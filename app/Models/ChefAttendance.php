<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChefAttendance extends Model
{
    protected $fillable = [
        'user_id', 'kindgarden_id', 'date',
        'check_in_at', 'check_in_lat', 'check_in_lng', 'check_in_distance_m',
        'check_in_selfie_path', 'check_in_is_late', 'check_in_replaced_count',
        'check_out_at', 'check_out_lat', 'check_out_lng', 'check_out_distance_m',
        'check_out_selfie_path', 'check_out_replaced_count',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
        'check_in_is_late' => 'boolean',
        'check_in_lat' => 'float',
        'check_in_lng' => 'float',
        'check_out_lat' => 'float',
        'check_out_lng' => 'float',
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
