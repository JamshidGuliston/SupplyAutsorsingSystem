<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Take_small_base extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'kindgarden_id',
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

    public function kindgarden()
    {
        return $this->belongsTo(Kindgarden::class);
    }
}
