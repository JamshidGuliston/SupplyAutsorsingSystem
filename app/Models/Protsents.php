<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protsents extends Model
{
    use HasFactory;
    protected $fillable = [
        'region_id',
        'month_id',
        'nds',
        'raise',
        'protsent'
    ];
}
