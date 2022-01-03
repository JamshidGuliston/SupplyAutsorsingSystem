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
        'hide'
    ];

    public function product(){
        return $this->belongsToMany(Product::class);
    }

    public function kindgarden(){
        return $this->belongsToMany(Kindgarden::class);
    }
}
