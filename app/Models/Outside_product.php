<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outside_product extends Model
{
    use HasFactory;
    protected $fillable = [
        'outside_name',
        'hide'
    ];
}
