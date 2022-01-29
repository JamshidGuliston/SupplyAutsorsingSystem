<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Active_menu extends Model
{
    use HasFactory;
    protected $fillable = [
    	'day_id',
	    'title_menu_id',
        'menu_meal_time_id',
        'menu_food_id',
        'product_name_id',
        'age_range_id',
        'weight'
	];
}
