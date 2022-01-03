<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class history_process extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_product_id',
        'user_name_id',
        'document_process_id',
        'action'
    ];
}
