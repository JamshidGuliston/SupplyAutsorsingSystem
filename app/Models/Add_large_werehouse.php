<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Add_large_werehouse extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'add_group_id',
        'shop_id',
        'product_id',
        'weight',
        'cost'
    ];
}
