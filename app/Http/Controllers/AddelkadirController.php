<?php

namespace App\Http\Controllers;

use App\Models\ChefAttendance;
use App\Models\Kindgarden;
use App\Models\User;
use App\Constants\Roles;
use Illuminate\Http\Request;
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
}
