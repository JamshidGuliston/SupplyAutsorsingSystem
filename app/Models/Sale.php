<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'buyer_shop_id',
        'user_id',
        'day_id',
        'invoice_number',
        'total_amount',
        'paid_amount',
        'image',
        'status',
        'notes'
    ];

    public function buyerShop()
    {
        return $this->belongsTo(Shop::class, 'buyer_shop_id');
    }

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }
} 