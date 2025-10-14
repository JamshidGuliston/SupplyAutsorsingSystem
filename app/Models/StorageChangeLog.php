<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StorageChangeLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'kingarden_id',
        'product_id',
        'day_id',
        'type',
        'old_value',
        'new_value',
        'difference',
        'user_id',
        'user_name',
    ];
    
    public function kindgarden()
    {
        return $this->belongsTo(Kindgarden::class, 'kingarden_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    
    public function day()
    {
        return $this->belongsTo(Day::class, 'day_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

