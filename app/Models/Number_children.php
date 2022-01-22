<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Number_children extends Model
{
    use HasFactory;
    protected $fillable = [
		'id',
		'kingar_name_id',
		'day_id',
		'king_age_name_id',
		'kingar_children_number',
		'workers_count',
		'kingar_menu_id',
	];
}
