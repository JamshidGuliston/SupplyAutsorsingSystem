<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

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
        'worker_age_id',
        'hide'
    ];

    public function age_range(){
        return $this->belongsToMany(Age_range::class);
    }

    public function user(){
        return $this->belongsToMany(User::class, 'user_kindgardens');
    }
}
