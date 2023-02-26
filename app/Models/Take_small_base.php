<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Take_small_base extends Model
{
    use HasFactory;
    protected $fillable = [
        'kindgarden_id',
        'takegroup_id',
        'product_id',
        'weight',
        'cost',
    ];
}
