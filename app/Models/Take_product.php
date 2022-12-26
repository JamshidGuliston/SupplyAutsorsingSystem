<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Take_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'outside_id',
        'takegroup_id',
        'product_id',
        'weight',
    ];
}
