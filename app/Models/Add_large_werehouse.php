<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Add_large_werehouse extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'id',
        'add_group_id',
        'shop_id',
        'product_id',
        'weight',
        'cost'
    ];
}
