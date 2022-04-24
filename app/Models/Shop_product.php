<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'product_id'
    ];
}
