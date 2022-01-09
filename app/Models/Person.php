<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;
    
    protected $fillable = [
		'kingar_id',
		'shop_id',
		'telegram_id',
    	'telegram_name',
	    'telegram_password',
	    'childs_count'
	];
	
	public function shop() {
		return $this->belongsTo('App\Models\Shop', 'shop_id');
	}	
	public function garden() {
		return $this->belongsTo('App\Models\Kindgarden', 'kingar_id');
	}	
}
