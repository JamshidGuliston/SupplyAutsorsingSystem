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
    ];
}
