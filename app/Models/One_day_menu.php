<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class One_day_menu extends Model
{
    use HasFactory;
    protected $fillable = [
        'one_day_menu_name',
        'menu_season_id'
    ];
}
