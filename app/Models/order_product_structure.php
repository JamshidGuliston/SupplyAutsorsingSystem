<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_product_structure extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_product_name_id',
        'product_name_id',
        'product_weight',
    ];
}
