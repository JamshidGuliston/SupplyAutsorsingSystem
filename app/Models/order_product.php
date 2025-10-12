<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class order_product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'kingar_name_id',
        'day_id',
        'order_title',
        'note',
        'document_processes_id',
        'data_of_weight',
        'to_menus', 
        'shop_id',
        'parent_id',      
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
    
    public function kinggarden()
    {
        return $this->belongsTo(Kindgarden::class, 'kingar_name_id');
    }

    public function childs()
    {
        return $this->hasMany(order_product::class, 'parent_id');
    }
}
