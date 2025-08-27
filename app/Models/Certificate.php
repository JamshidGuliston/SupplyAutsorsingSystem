<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'certificate_number',
        'name',
        'description',
        'start_date',
        'end_date',
        'pdf_file',
        'image_file',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    // Maxsulotlar bilan one-to-many munosabat
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Faol sertifikatlarni olish
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Muddati o'tmagan sertifikatlarni olish
    public function scopeValid($query)
    {
        return $query->where('end_date', '>=', now());
    }

    // Muddati yaqinlashgan sertifikatlarni olish (30 kun qolgan)
    public function scopeExpiringSoon($query, $days = 30)
    {
        $futureDate = now()->addDays($days);
        return $query->where('end_date', '<=', $futureDate)
                    ->where('end_date', '>=', now())
                    ->where('is_active', true);
    }

    // Sertifikat muddati tugashiga qolgan kunlar
    public function getDaysLeftAttribute()
    {
        return now()->diffInDays($this->end_date, false);
    }

    // Sertifikat holatini tekshirish
    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return ['class' => 'status-inactive', 'text' => 'Nofaol'];
        }
        
        $daysLeft = $this->days_left;
        
        if ($daysLeft < 0) {
            return ['class' => 'status-expired', 'text' => 'Muddati tugagan'];
        }
        
        if ($daysLeft <= 30) {
            return ['class' => 'status-warning', 'text' => $daysLeft . ' kun qoldi'];
        }
        
        return ['class' => 'status-active', 'text' => 'Faol'];
    }
} 