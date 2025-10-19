<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_name',
        'size_name_id',
        'category_name_id',
        'product_image',
        'norm_cat_id',
        'div',
        'package_size',
        'sort',
        'hide',
        'proteins',
        'fats',
        'carbohydrates',
        'kcal',
        'certificate_id'
    ];

    public function shop(){
        return $this->belongsToMany(Shop::class, 'shop_product');
    }
    
    public function category()
    {
        return $this->belongsTo(Product_category::class, 'category_name_id');
    }
    
    public function size()
    {
        return $this->belongsTo(\App\Models\Size::class, 'size_name_id');
    }

    // Sertifikat bilan bog'lanish
    public function certificate()
    {
        return $this->belongsTo(Certificate::class);
    }
}
