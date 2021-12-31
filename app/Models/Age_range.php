<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Age_range extends Model
{

    use HasFactory;

    protected $fillable = [
        'age_name'
    ];

    // public function kindgarden(){
    //     return $this->belongsToMany(Kindgarden::class);
    // }
}
