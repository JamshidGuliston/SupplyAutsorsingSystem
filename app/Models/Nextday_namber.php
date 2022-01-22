<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nextday_namber extends Model
{
    use HasFactory;
    protected $fillable = [
       'id',
       'kingar_name_id',
       'king_age_name_id',
       'kingar_children_number',
       'workers_count',
       'kingar_menu_id',
    ];
}
