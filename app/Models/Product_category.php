<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_category extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'pro_cat_name',
        'pro_cat_image',
        'limit_quantity'
    ];
    
    public function products()
    {
        return $this->hasMany(Product::class, 'category_name_id');
    }
}
