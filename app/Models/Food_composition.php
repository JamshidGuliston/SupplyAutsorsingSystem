<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food_composition extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'food_name_id',
    	'product_name_id',
        'gram',
        'weight_without_waste',
        'proteins',
        'fats',
        'carbohydrates',
        'kcal'
	];
    
}
