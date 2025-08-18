<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class order_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'kingar_name_id',
        'day_id',
        'order_title',
        'document_processes_id',
        'data_of_weight',
        'to_menus', 
        'shop_id',      
    ];
    
    public function orderProductStructures()
    {
        return $this->hasMany(order_product_structure::class, 'order_product_name_id');
    }
    
    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
    
    public function day()
    {
        return $this->belongsTo(Day::class, 'day_id');
    }
}
