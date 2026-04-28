<?php

namespace App\Services\Attendance;

use App\Exceptions\Attendance\AlreadyCheckedInException;
use App\Exceptions\Attendance\AlreadyCheckedOutException;
use App\Exceptions\Attendance\KindgardenCoordsNotSetException;
use App\Exceptions\Attendance\MockGpsDetectedException;
use App\Exceptions\Attendance\OutsideGeofenceException;
use App\Exceptions\Attendance\StaleCaptureException;
use App\Models\ChefAttendance;
use App\Models\ChefLocationEvent;
use App\Models\Kindgarden;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public const MAX_CAPTURE_SKEW_SECONDS = 300;

    public function __construct(
        private DistanceCalculator $distance,
        private SelfieStorage $storage,
    ) {}

    public function checkIn(User $user, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, bool $isMock): ChefAttendance
    {
        $kg = $this->resolveKindgarden($user);
        $this->guardCapture($capturedAt, $isMock);
        $distanceM = $this->guardGeofence($kg, $lat, $lng);

        $today = $capturedAt->copy()->setTimezone('Asia/Tashkent')->toDateString();

        return DB::transaction(function () use ($user, $kg, $photo, $lat, $lng, $capturedAt, $distanceM, $today) {
            $existing = ChefAttendance::where('user_id', $user->id)->where('date', $today)->first();
            if ($existing && $existing->check_in_at) {
                throw new AlreadyCheckedInException();
            }

            $path = $this->storage->store($photo, $user->id, 'check_in', $today);

            $row = $existing ?? new ChefAttendance([
                'user_id' => $user->id,
                'kindgarden_id' => $kg->id,
                'date' => $today,
            ]);
            $row->fill([
                'check_in_at' => $capturedAt,
                'check_in_lat' => $lat,
                'check_in_lng' => $lng,
                'check_in_distance_m' => $distanceM,
                'check_in_selfie_path' => $path,
                'check_in_is_late' => false,
            ]);
            $row->save();
            return $row->fresh();
        });
    }

    private function resolveKindgarden(User $user): Kindgarden
    {
        $kg = $user->kindgarden()->first();
        if (!$kg) {
            throw new KindgardenCoordsNotSetException();
        }
        if ($kg->lat === null || $kg->lng === null) {
            throw new KindgardenCoordsNotSetException();
        }
        return $kg;
    }

    private function guardCapture(Carbon $capturedAt, bool $isMock): void
    {
        if ($isMock) {
            throw new MockGpsDetectedException();
        }
        if (abs(now()->diffInSeconds($capturedAt, false)) > self::MAX_CAPTURE_SKEW_SECONDS) {
            throw new StaleCaptureException();
        }
    }

    private function guardGeofence(Kindgarden $kg, float $lat, float $lng): int
    {
        $distance = $this->distance->meters((float) $kg->lat, (float) $kg->lng, $lat, $lng);
        $maxRadius = (int) ($kg->geofence_radius ?: 200);
        if ($distance > $maxRadius) {
            throw new OutsideGeofenceException($distance, $maxRadius);
        }
        return $distance;
    }

    public function checkOut(User $user, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, bool $isMock): ChefAttendance
    {
        $kg = $this->resolveKindgarden($user);
        $this->guardCapture($capturedAt, $isMock);
        $distanceM = $this->guardGeofence($kg, $lat, $lng);

        $today = $capturedAt->copy()->setTimezone('Asia/Tashkent')->toDateString();

        return DB::transaction(function () use ($user, $photo, $lat, $lng, $capturedAt, $distanceM, $today) {
            $row = ChefAttendance::where('user_id', $user->id)->where('date', $today)->first();
            if (!$row || !$row->check_in_at) {
                throw new \App\Exceptions\Attendance\NotCheckedInException();
            }
            if ($row->check_out_at) {
                throw new AlreadyCheckedOutException();
            }

            $path = $this->storage->store($photo, $user->id, 'check_out', $today);
            $row->fill([
                'check_out_at' => $capturedAt,
                'check_out_lat' => $lat,
                'check_out_lng' => $lng,
                'check_out_distance_m' => $distanceM,
                'check_out_selfie_path' => $path,
            ])->save();

            return $row->fresh();
        });
    }

    public function replace(User $user, string $type, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, bool $isMock): ChefAttendance
    {
        if (!in_array($type, ['check_in', 'check_out'], true)) {
            throw new \InvalidArgumentException("Unsupported replace type: {$type}");
        }

        $kg = $this->resolveKindgarden($user);
        $this->guardCapture($capturedAt, $isMock);
        $distanceM = $this->guardGeofence($kg, $lat, $lng);

        $today = $capturedAt->copy()->setTimezone('Asia/Tashkent')->toDateString();

        return DB::transaction(function () use ($user, $kg, $type, $photo, $lat, $lng, $capturedAt, $distanceM, $today) {
            $row = ChefAttendance::where('user_id', $user->id)->where('date', $today)->first();

            if ($type === 'check_in') {
                return $this->applyCheckInReplace($row, $user, $kg, $photo, $lat, $lng, $capturedAt, $distanceM, $today);
            }
            return $this->applyCheckOutReplace($row, $user, $photo, $lat, $lng, $capturedAt, $distanceM, $today);
        });
    }

    private function applyCheckInReplace(?ChefAttendance $row, User $user, Kindgarden $kg, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, int $distanceM, string $today): ChefAttendance
    {
        $isLateEntry = !$row || !$row->check_in_at;
        $oldPath = $row?->check_in_selfie_path;
        $newPath = $this->storage->store($photo, $user->id, 'check_in', $today);

        $row = $row ?? new ChefAttendance([
            'user_id' => $user->id,
            'kindgarden_id' => $kg->id,
            'date' => $today,
        ]);
        $row->fill([
            'check_in_at' => $capturedAt,
            'check_in_lat' => $lat,
            'check_in_lng' => $lng,
            'check_in_distance_m' => $distanceM,
            'check_in_selfie_path' => $newPath,
            'check_in_is_late' => $isLateEntry,
            'check_in_replaced_count' => $isLateEntry ? 0 : ($row->check_in_replaced_count + 1),
        ]);
        $row->save();
        if (!$isLateEntry && $oldPath) {
            $this->storage->delete($oldPath);
        }
        return $row->fresh();
    }

    private function applyCheckOutReplace(?ChefAttendance $row, User $user, UploadedFile $photo, float $lat, float $lng, Carbon $capturedAt, int $distanceM, string $today): ChefAttendance
    {
        if (!$row || !$row->check_in_at) {
            throw new \App\Exceptions\Attendance\NotCheckedInException();
        }
        $oldPath = $row->check_out_selfie_path;
        $newPath = $this->storage->store($photo, $user->id, 'check_out', $today);

        $isFirstCheckOut = $row->check_out_at === null;
        $row->fill([
            'check_out_at' => $capturedAt,
            'check_out_lat' => $lat,
            'check_out_lng' => $lng,
            'check_out_distance_m' => $distanceM,
            'check_out_selfie_path' => $newPath,
            'check_out_replaced_count' => $isFirstCheckOut ? 0 : ($row->check_out_replaced_count + 1),
        ])->save();

        if (!$isFirstCheckOut && $oldPath) {
            $this->storage->delete($oldPath);
        }
        return $row->fresh();
    }

    public function recordLocationEvents(User $user, array $events): int
    {
        $kg = $user->kindgarden()->first();
        if (!$kg || $kg->lat === null || $kg->lng === null) {
            return 0;
        }
        $allowed = ['exit', 'enter', 'beacon'];

        $rows = [];
        foreach ($events as $e) {
            if (!in_array($e['event_type'] ?? null, $allowed, true)) {
                throw new \InvalidArgumentException('Invalid event_type: ' . ($e['event_type'] ?? 'null'));
            }
            $distance = $this->distance->meters((float) $kg->lat, (float) $kg->lng, (float) $e['lat'], (float) $e['lng']);
            $rows[] = [
                'user_id' => $user->id,
                'kindgarden_id' => $kg->id,
                'event_type' => $e['event_type'],
                'happened_at' => Carbon::parse($e['happened_at']),
                'lat' => $e['lat'],
                'lng' => $e['lng'],
                'distance_m' => $distance,
                'is_mock' => (bool) ($e['is_mock'] ?? false),
                'created_at' => now(),
            ];
        }
        if ($rows) {
            ChefLocationEvent::insert($rows);
        }
        return count($rows);
    }
}
