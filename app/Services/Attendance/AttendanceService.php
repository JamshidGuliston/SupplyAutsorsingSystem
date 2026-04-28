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
}
