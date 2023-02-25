<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Take_group extends Model
{
    use HasFactory;
    protected $fillable = [
        'contur_id',
        'day_id',
        'taker_id',
        'outside_id',
        'title',
        'description',
    ];
}
