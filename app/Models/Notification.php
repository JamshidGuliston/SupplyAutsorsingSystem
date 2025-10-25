<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Notifiable model bilan bog'lanish
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Notification yaratish
     */
    public static function createNotification($type, $notifiable, $data)
    {
        return self::create([
            'type' => $type,
            'notifiable_type' => get_class($notifiable),
            'notifiable_id' => $notifiable->id,
            'data' => $data
        ]);
    }

    /**
     * Notification ni o'qilgan deb belgilash
     */
    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * O'qilmagan notificationlarni olish
     */
    public static function getUnreadForUser($userId)
    {
        return self::where('notifiable_type', User::class)
            ->where('notifiable_id', $userId)
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Bolalar soni o'zgartirish notification yaratish
     */
    public static function createChildrenCountChangeNotification($gardenId, $ageId, $oldCount, $newCount, $changedBy)
    {

        $garden = Kindgarden::find($gardenId);
        $age = Age_range::find($ageId);
        $user = User::find($changedBy);
        
        if (!$garden || !$age || !$user) {

            return null;
        }

        // User role() method orqali Texnolog rolega ega foydalanuvchilarni olish
        $technologs = User::whereHas('role', function($query) {
            $query->where('name', 'Texnolog');
        })->get();

        $notifications = [];
        foreach ($technologs as $technolog) {
            $data = [
                'garden_name' => $garden->kingar_name,
                'age_name' => $age->age_name,
                'old_count' => $oldCount,
                'new_count' => $newCount,
                'changed_by' => $user->name,
                'changed_at' => now()->setTimezone('Asia/Tashkent')->format('d.m.Y H:i'),
                'message' => "{$garden->kingar_name} bog'chasida {$age->age_name} yosh guruhi uchun bolalar soni {$oldCount} dan {$newCount} ga o'zgartirildi. O'zgartirgan: {$user->name}"
            ];

            try {
                $notification = self::createNotification(
                    'children_count_changed',
                    $technolog,
                    $data
                );
                $notifications[] = $notification;
            } catch (\Exception $e) {

            }
        }

        return $notifications;
    }
}
