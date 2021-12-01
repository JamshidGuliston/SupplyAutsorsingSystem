<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Month;
use App\Models\Kindgarden;
use App\Models\Temporary;
use Illuminate\Http\Request;

class TestController extends Controller
{
    function index(Request $request)
    {
        $gr = Temporary::join('kindgardens', 'temporaries.kingar_name_id', '=', 'kindgardens.id')->orderBy('kingar_name_id')->get();



        return view('adminhome', ['gardens' => $gr]);
    }

    public function tomorrowdate(Request $request)
    {
        $days = Day::orderBy('id', 'DESC')->first();
        if (empty($days["day_number"])) {
            $days["day_number"] = 0;
        }
        $d = strtotime("tomorrow");
        if (date("w", $d) != 0 and date("w", $d) != 6 and $days["day_number"] != date("d", $d)) {
            echo date("d", $d);
        } else {
            $startdate = strtotime("Monday");
            date("d", $startdate);
        }
    }
}
