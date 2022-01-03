<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temporary extends Model
{
    use HasFactory;
    protected $fillable = [
        	'kingar_name_id',
    		'age_id',
    		'age_number'
    ];

    
}
