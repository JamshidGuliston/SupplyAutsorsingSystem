<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class plus_multi_storage extends Model
{
    use HasFactory;
    protected $fillable = [
        'day_id',
        'shop_id',
        'kingarden_name_d',
        'order_product_id',
        'product_name_id',
        'product_weight',
    ];
}
