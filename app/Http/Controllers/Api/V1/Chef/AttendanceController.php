<?php

namespace App\Http\Controllers\Api\V1\Chef;

use App\Http\Controllers\Controller;
use App\Services\Attendance\AttendanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $svc) {}

    public function checkIn(Request $request): JsonResponse
    {
        $data = $this->validateAttendancePayload($request);
        $att = $this->svc->checkIn(
            $request->user(), $data['photo'], $data['lat'], $data['lng'],
            $data['captured_at'], $data['is_mock'],
        );
        return response()->json(['attendance' => $att]);
    }

    public function checkOut(Request $request): JsonResponse
    {
        $data = $this->validateAttendancePayload($request);
        $att = $this->svc->checkOut(
            $request->user(), $data['photo'], $data['lat'], $data['lng'],
            $data['captured_at'], $data['is_mock'],
        );
        return response()->json(['attendance' => $att]);
    }

    public function replace(Request $request): JsonResponse
    {
        $request->validate(['type' => 'required|in:check_in,check_out']);
        $data = $this->validateAttendancePayload($request);
        $att = $this->svc->replace(
            $request->user(), $request->input('type'),
            $data['photo'], $data['lat'], $data['lng'], $data['captured_at'], $data['is_mock'],
        );
        return response()->json(['attendance' => $att]);
    }

    public function today(Request $request): JsonResponse
    {
        $user = $request->user();
        $kg = $user->kindgarden()->first();
        $today = now()->setTimezone('Asia/Tashkent')->toDateString();

        $row = $kg
            ? \App\Models\ChefAttendance::where('user_id', $user->id)->where('date', $today)->first()
            : null;

        return response()->json([
            'attendance' => $row,
            'kindgarden' => $kg ? [
                'id' => $kg->id,
                'lat' => $kg->lat,
                'lng' => $kg->lng,
                'geofence_radius' => (int) ($kg->geofence_radius ?: 200),
            ] : null,
            'server_time' => now()->toIso8601String(),
        ]);
    }

    private function validateAttendancePayload(Request $request): array
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'captured_at' => 'required|date',
            'is_mock' => 'required|boolean',
            'photo' => 'required|image|max:5120',
        ]);
        return [
            'lat' => (float) $validated['lat'],
            'lng' => (float) $validated['lng'],
            'captured_at' => Carbon::parse($validated['captured_at']),
            'is_mock' => (bool) $validated['is_mock'],
            'photo' => $request->file('photo'),
        ];
    }
}
