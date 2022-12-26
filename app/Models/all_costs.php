<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class all_costs extends Model
{
    use HasFactory;
    protected $fillable = [
        'cost_name_id',
        'allcost_name',
        'allcost_hide'
    ];
}
