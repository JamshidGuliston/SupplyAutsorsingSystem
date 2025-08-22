<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Kindgarden extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'region_id',
        'kingar_name',
        'short_name',
        'kingar_password',
        'telegram_user_id',
        'worker_count',
        'worker_age_id',
        'number_of_org',
        'hide'
    ];

    // Validation rules
    public static function rules($id = null)
    {
        return [
            'kingar_name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50|unique:kindgardens,short_name,' . $id,
            'number_of_org' => 'required|string|max:50',
            'region_id' => 'required|exists:regions,id',
            'worker_count' => 'nullable|integer|min:0',
            'kingar_password' => 'nullable|string|min:6',
        ];
    }

    public function age_range(){
        return $this->belongsToMany(Age_range::class);
    }

    public function user(){
        return $this->belongsToMany(User::class, 'user_kindgardens');
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }
}
