<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupweight extends Model
{
    use HasFactory;
    protected $table = 'groupweights';

    protected $fillable = [
        'name',
        'kindergarden_id',
        'day_id'
    ];
}
