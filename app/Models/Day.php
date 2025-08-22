<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;
    protected $fillable = [
    	'day_number',
	    'month_id',
        'year_id'
	];
    
    public function month()
    {
        return $this->belongsTo(Month::class);
    }
    
    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
