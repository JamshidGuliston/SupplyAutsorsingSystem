<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class costs extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'cost_name',
        'cost_img',
        'cost_hide',
    ];
}
