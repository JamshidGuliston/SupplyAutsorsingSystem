<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_product_structure extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'order_product_name_id',
        'product_name_id',
        'product_weight',
        'actual_weight',
    ];
    
    public function orderProduct()
    {
        return $this->belongsTo(order_product::class, 'order_product_name_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_name_id');
    }
}
