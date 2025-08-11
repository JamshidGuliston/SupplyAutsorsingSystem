<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Take_product extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'takegroup_id',
        'sale_id',
        'product_id',
        'weight',
        'cost',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
