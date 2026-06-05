<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Constants\Roles;
use App\Http\Controllers\Controller;
use App\Services\Attendance\AttendanceService;
use App\Services\Push\PushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LocationEventController extends Controller
{
    public function __construct(
        private AttendanceService $svc,
        private PushService $push,
    ) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'events' => 'present|array|max:100',
            'events.*.event_type' => 'required|in:exit,enter,beacon',
            'events.*.lat' => 'required|numeric|between:-90,90',
            'events.*.lng' => 'required|numeric|between:-180,180',
            'events.*.happened_at' => 'required|date',
            'events.*.is_mock' => 'required|boolean',
        ]);

        $count = $this->svc->recordLocationEvents($request->user(), $data['events']);
        $this->maybeNotifyAddelkadirsOfExit($request->user(), $data['events']);
        return response()->json(['inserted' => $count]);
    }

    private function maybeNotifyAddelkadirsOfExit(\App\Models\User $user, array $events): void
    {
        $tashkent = now()->setTimezone('Asia/Tashkent');
        $hour = (int) $tashkent->format('H');
        if ($hour < 8 || $hour >= 19) {
            return;
        }
        $hasExit = false;
        foreach ($events as $e) {
            if (($e['event_type'] ?? null) === 'exit') {
                $hasExit = true;
                break;
            }
        }
        if (!$hasExit) {
            return;
        }
        $cacheKey = 'exit-push:user:' . $user->id;
        if (Cache::has($cacheKey)) {
            return;
        }
        Cache::put($cacheKey, true, now()->addMinutes(5));
        try {
            $this->push->sendToRole(
                Roles::ADDELKADIR,
                'Oshpaz bog\'chadan chiqdi',
                ($user->name ?? 'Oshpaz') . ' bog\'cha hududidan chiqib ketdi',
                ['kind' => 'exit', 'user_id' => (string) $user->id]
            );
        } catch (\Throwable $e) {
            \Log::error('Exit push failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
        }
    }
}
