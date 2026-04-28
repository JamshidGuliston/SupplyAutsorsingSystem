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
}
