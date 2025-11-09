<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Age_range extends Model
{

    use HasFactory;

    protected $fillable = [
        'age_name',
        'description',
        'parent_id'
    ];

    public function parent(){
        return $this->belongsTo(Age_range::class, 'parent_id');
    }

    public function children(){
        return $this->hasMany(Age_range::class, 'parent_id');
    }
}
