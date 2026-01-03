<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Titlemenu extends Model
{
    use HasFactory;
    protected $fillable = [
        'menu_name',
        'menu_season_id',
        'parent_id',
        'order_number',
        'short_name'
    ];

    public function age_range(){
        return $this->belongsToMany(Age_range::class, 'titlemenu_age_range');
    }

    // Parent menyu
    public function parent(){
        return $this->belongsTo(Titlemenu::class, 'parent_id');
    }

    // Child menyular
    public function children(){
        return $this->hasMany(Titlemenu::class, 'parent_id')->orderBy('order_number', 'ASC');
    }
}
