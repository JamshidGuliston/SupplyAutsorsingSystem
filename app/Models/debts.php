<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class debts extends Model
{
    use HasFactory;
    protected $fillable = [
    	'shop_id',
	    'pay',
        'loan',
        'hisloan',
        'row_id'
	];
}
