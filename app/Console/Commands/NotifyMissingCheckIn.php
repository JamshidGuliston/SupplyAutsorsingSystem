<?php

namespace App\Console\Commands;

use App\Constants\Roles;
use App\Models\ChefAttendance;
use App\Models\User;
use App\Services\Push\PushService;
use Illuminate\Console\Command;

class NotifyMissingCheckIn extends Command
{
    protected $signature = 'chef:notify-missing-checkin';
    protected $description = 'Notify all Addelkadirs about chefs who have not checked in today (09:15 Asia/Tashkent)';

    public function handle(PushService $push): int
    {
        $today = now()->setTimezone('Asia/Tashkent')->toDateString();

        $checkedInUserIds = ChefAttendance::where('date', $today)
            ->whereNotNull('check_in_at')
            ->pluck('user_id')
            ->all();

        $missing = User::where('role_id', Roles::CHEF)
            ->whereNotIn('id', $checkedInUserIds)
            ->orderBy('name')
            ->get(['id', 'name']);

        if ($missing->isEmpty()) {
            $this->info('All chefs checked in today.');
            return self::SUCCESS;
        }

        $names = $missing->pluck('name')->take(5)->implode(', ');
        $extra = $missing->count() > 5 ? ' va yana ' . ($missing->count() - 5) . ' ta' : '';
        $body = $missing->count() . ' oshpaz hali kelmadi: ' . $names . $extra;

        $result = $push->sendToRole(
            Roles::ADDELKADIR,
            'Kelmagan oshpazlar',
            $body,
            ['kind' => 'missing_checkin', 'count' => (string) $missing->count()]
        );

        $this->info('Sent: ' . $result['success_count'] . ', missing chefs: ' . $missing->count());
        return self::SUCCESS;
    }
}
