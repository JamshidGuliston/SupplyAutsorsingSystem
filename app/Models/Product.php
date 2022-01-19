<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'product_name',
        'size_name_id',
        'category_name_id',
        'product_image',
        'div'
    ];

    public function shop(){
        return $this->belongsToMany(Shop::class, 'shop_product');
    }
}
