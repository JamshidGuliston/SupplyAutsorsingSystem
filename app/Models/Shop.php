<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'shop_name',
        'telegram_id',
        'hide'
    ];

    public function product(){
        return $this->belongsToMany(Product::class, 'shop_product');
    }

    public function kindgarden(){
        return $this->belongsToMany(Kindgarden::class, 'shop_kindgarden');
    }
}
