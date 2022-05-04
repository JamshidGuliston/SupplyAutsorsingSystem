<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bycosts extends Model
{
    use HasFactory;
    protected $fillable = [
        'day_id',
        'region_name_id',
        'praduct_name_id',
        'price_cost',
        'tax_product',
        'waste_number', 
    ];
}
