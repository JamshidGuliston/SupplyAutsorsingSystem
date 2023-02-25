<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Take_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'takegroup_id',
        'product_id',
        'weight',
        'cost',
    ];
}
