<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildrenCountHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'kingar_name_id',
        'king_age_name_id',
        'old_children_count',
        'new_children_count',
        'changed_by',
        'changed_at',
        'change_reason'
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Bog'cha bilan bog'lanish
     */
    public function kindgarden(): BelongsTo
    {
        return $this->belongsTo(Kindgarden::class, 'kingar_name_id');
    }

    /**
     * Yosh guruhi bilan bog'lanish
     */
    public function ageRange(): BelongsTo
    {
        return $this->belongsTo(Age_range::class, 'king_age_name_id');
    }

    /**
     * O'zgartirgan foydalanuvchi bilan bog'lanish
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Bog'cha va yosh guruhi bo'yicha tarixni olish
     */
    public static function getHistoryForKindgarden($kindgardenId, $ageId = null)
    {
        $query = self::with(['kindgarden', 'ageRange', 'changedBy'])
            ->where('kingar_name_id', $kindgardenId)
            ->orderBy('changed_at', 'desc');

        if ($ageId) {
            $query->where('king_age_name_id', $ageId);
        }

        return $query->get();
    }

    /**
     * Oxirgi o'zgartirishni olish
     */
    public static function getLastChange($kindgardenId, $ageId)
    {
        return self::where('kingar_name_id', $kindgardenId)
            ->where('king_age_name_id', $ageId)
            ->orderBy('changed_at', 'desc')
            ->first();
    }
}

