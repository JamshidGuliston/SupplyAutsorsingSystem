<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Add_group extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'day_id',
        'group_name'
    ];
}
