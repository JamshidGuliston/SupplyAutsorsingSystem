<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Protsent extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'region_id',
        'age_range_id',
        'eater_cost',
        'start_date',
        'end_date',
        'nds',
        'raise',
        'protsent'
    ];

    /**
     * Get the age range that owns the protsent.
     */
    public function ageRange()
    {
        return $this->belongsTo(Age_range::class, 'age_range_id');
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'eater_cost' => 'double',
        'nds' => 'double',
        'raise' => 'double',
        'protsent' => 'double'
    ];

    /**
     * Scope to get records by date range
     */
    public function scopeByDateRange($query, $date)
    {
        return $query->where('start_date', '<=', $date)
                    ->where('end_date', '>=', $date);
    }

    /**
     * Scope to get active records for current date
     */
    public function scopeActive($query)
    {
        return $query->byDateRange(now()->toDateString());
    }

    /**
     * Check if the record is active for given date
     */
    public function isActiveFor($date)
    {
        return $this->start_date <= $date && $this->end_date >= $date;
    }

    /**
     * Check if the record is currently active
     */
    public function isActive()
    {
        return $this->isActiveFor(now()->toDateString());
    }
}
