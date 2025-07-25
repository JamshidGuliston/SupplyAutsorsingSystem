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
        'norm_cat_id',
        'div',
        'sort',
        'hide',
        'proteins',
        'fats',
        'carbohydrates',
        'kcal'
    ];

    public function shop(){
        return $this->belongsToMany(Shop::class, 'shop_product');
    }
}
