<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;
    protected $fillable = [
        'season_name',
        'season_image',
        'hide'
    ];

    // Season ga tegishli barcha titlemenular
    public function titlemenus(){
        return $this->hasMany(Titlemenu::class, 'menu_season_id');
    }
}
