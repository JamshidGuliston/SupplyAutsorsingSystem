<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class debts extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
    	'shop_id',
        'sale_id', // Yangi qo'shilgan
        'day_id',
	    'pay',
        'loan',
        'hisloan',
        'debt_type', // Yangi qo'shilgan
        'row_id',
        'payment_id'
	];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function day()
    {
        return $this->belongsTo(Day::class);
    }
    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
