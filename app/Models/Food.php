<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'food_name',
    	'food_cat_id',
	    'meal_time_id',
        'food_prepar_tech',
        'food_image'
	];
}
