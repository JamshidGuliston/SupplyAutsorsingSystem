<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationEventController extends Controller
{
    public function __construct(private AttendanceService $svc) {}

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
        return response()->json(['inserted' => $count]);
    }
}
