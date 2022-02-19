<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class minus_multi_storage extends Model
{
    use HasFactory;
    protected $fillable = [
    	'day_id',
	    'kingarden_name_id',
	    'kingar_menu_id',
	    'product_name_id',
	    'product_weight',
	];
}
