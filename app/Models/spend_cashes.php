<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class spend_cashes extends Model
{
    use HasFactory;
    protected $fillable = [
        'allcost_id',
        'day_id',
        'summ',
        'description',
        'status',
        'summ_hide'
    ];
}
