<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kindgarden extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'region_id',
        'kingar_name',
        'kingar_password',
        'telegram_user_id',
        'worker_count',
        'hide'
    ];
    public function age_range(){
        return $this->belongsToMany(Age_range::class);
    }
}
