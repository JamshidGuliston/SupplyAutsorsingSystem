<?php

namespace App\Http\Controllers;

use App\Models\ChefAttendance;
use App\Models\ChefLocationEvent;
use App\Models\Kindgarden;
use App\Models\User;
use App\Services\Attendance\LocationEventSummary;
use App\Constants\Roles;
use App\Services\Attendance\AttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AddelkadirController extends Controller
{
    public function home(): View
    {
        $today = now()->setTimezone('Asia/Tashkent')->toDateString();
        $totalChefs = User::where('role_id', Roles::CHEF)->count();
        $todayRows = ChefAttendance::where('date', $today)->with('user', 'kindgarden')->get();

        $cameCount = $todayRows->whereNotNull('check_in_at')->count();
        $lateCount = $todayRows->where('check_in_is_late', true)->count();
        $absentCount = max(0, $totalChefs - $cameCount);

        return view('addelkadir.home', [
            'totalChefs' => $totalChefs,
            'cameCount' => $cameCount,
            'lateCount' => $lateCount,
            'absentCount' => $absentCount,
            'todayRows' => $todayRows,
            'today' => $today,
        ]);
    }

    public function attendance(Request $request): View
    {
        $from = $request->input('from', now()->subDays(7)->toDateString());
        $to = $request->input('to', now()->toDateString());

        $rows = ChefAttendance::with('user', 'kindgarden')
            ->whereBetween('date', [$from, $to])
            ->orderByDesc('date')
            ->paginate(50);

        return view('addelkadir.attendance', compact('rows', 'from', 'to'));
    }

    public function selfie(Request $request, int $attendanceId, string $type)
    {
        abort_unless(in_array($type, ['check_in', 'check_out'], true), 404);
        $att = ChefAttendance::findOrFail($attendanceId);
        $path = $type === 'check_in' ? $att->check_in_selfie_path : $att->check_out_selfie_path;
        abort_if(!$path, 404);
        return response()->file(storage_path('app/' . $path));
    }

    public function kindgardens(): View
    {
        $items = Kindgarden::orderBy('id')->get();
        return view('addelkadir.kindgardens', compact('items'));
    }

    public function updateKindgardenCoords(Request $request, int $id)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'geofence_radius' => 'required|integer|min:50|max:1000',
        ]);
        Kindgarden::findOrFail($id)->update($data);
        return redirect()->route('addelkadir.kindgardens')->with('status', 'Saqlandi');
    }

    public function undoCheckOut(int $attendanceId, AttendanceService $svc): RedirectResponse
    {
        try {
            $svc->adminUndoCheckOut($attendanceId);
            return back()->with('status', 'Ketish bekor qilindi.');
        } catch (\App\Exceptions\Attendance\NotCheckedOutException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function chefs(): View
    {
        $chefs = User::where('role_id', Roles::CHEF)
            ->with('kindgarden')
            ->orderBy('name')
            ->get();

        $devices = \App\Models\ChefDevice::all()->keyBy('user_id');

        return view('addelkadir.chefs', compact('chefs', 'devices'));
    }

    public function locationEvents(Request $request, LocationEventSummary $summary): View
    {
        $date = $request->input('date', now()->setTimezone('Asia/Tashkent')->toDateString());
        $chefId = $request->input('chef_id');
        $eventType = $request->input('event_type');

        $query = ChefLocationEvent::with('user', 'kindgarden')
            ->whereDate('happened_at', $date)
            ->orderBy('happened_at');

        if ($chefId) {
            $query->where('user_id', $chefId);
        }
        if (in_array($eventType, ['exit', 'enter', 'beacon'], true)) {
            $query->where('event_type', $eventType);
        }

        $events = $query->paginate(100)->withQueryString();

        // Stats intentionally ignore $eventType so totals always reflect the full day.
        $allForStats = ChefLocationEvent::whereDate('happened_at', $date)
            ->when($chefId, fn ($q) => $q->where('user_id', $chefId))
            ->orderBy('happened_at')
            ->get(['event_type', 'happened_at']);

        $counts = $summary->countByType($allForStats);
        $minutesOutside = $summary->totalMinutesOutside($allForStats);

        $chefs = User::where('role_id', Roles::CHEF)->orderBy('name')->get(['id', 'name']);

        return view('addelkadir.location_events', [
            'events' => $events,
            'summary' => $summary,
            'counts' => $counts,
            'minutesOutside' => $minutesOutside,
            'chefs' => $chefs,
            'date' => $date,
            'chefId' => $chefId,
            'eventType' => $eventType,
        ]);
    }
}
