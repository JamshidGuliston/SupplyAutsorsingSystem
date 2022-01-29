<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class titlemenu_food extends Model
{
    use HasFactory;
    protected $fillable = [
        'day_id',
        'worker_age_id',
        'titlemenu_id',
        'food_id'
    ];
}
