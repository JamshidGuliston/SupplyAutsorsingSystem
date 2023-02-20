<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cashes extends Model
{
    use HasFactory;
    protected $fillable = [
        'allcost_id',
        'day_id',
        'summ',
        'description',
        'vid',
        'status'
    ];
}
