<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'contract_date',
        'start_date',
        'end_date',
        'region_id',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'start_date'    => 'date',
        'end_date'      => 'date',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function kindgardens()
    {
        return $this->belongsToMany(Kindgarden::class, 'contract_kindgarden');
    }

    /**
     * Returns all contracts applicable to a given kindgarden
     * (direct assignment OR region-wide assignment).
     */
    public static function getForKindgarden(int $kindgardenId, int $regionId)
    {
        return static::where(function ($q) use ($kindgardenId) {
            $q->whereHas('kindgardens', fn($q2) => $q2->where('kindgardens.id', $kindgardenId));
        })
        ->orWhere('region_id', $regionId)
        ->orderByDesc('start_date')
        ->get();
    }

    /**
     * Find the best matching contract for a kindgarden within a date range.
     */
    public static function findForKindgarden(int $kindgardenId, int $regionId, string $startDate, string $endDate): ?self
    {
        // Prefer direct assignment
        $contract = static::whereHas('kindgardens', fn($q) => $q->where('kindgardens.id', $kindgardenId))
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->orderByDesc('start_date')
            ->first();

        if ($contract) {
            return $contract;
        }

        // Fall back to region-wide
        return static::where('region_id', $regionId)
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->orderByDesc('start_date')
            ->first();
    }
}
