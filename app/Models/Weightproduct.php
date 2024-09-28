<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weightproduct extends Model
{
    use HasFactory;

    protected $fillable = [
    	'groupweight_id',
	    'product_id',
	    'weight'
	];



}
