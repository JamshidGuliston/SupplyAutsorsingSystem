<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Day;
use App\Models\Month;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Kindgarden;
use App\Models\Year;
use App\Models\Temporary;
use App\Models\Menu_composition;
use App\Models\Number_children;
use App\Models\One_day_menu;
use Dompdf\Dompdf;

class TechnologController extends Controller
{
    public function index(Request $request)
    {
        $month = Month::where('month_active', 1)->get();
        // dd($month[0]->id);
        // faqat aktiv oy sanalarini oladi
        $days = Day::where('month_id', $month[0]->id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->select('days.id', 'days.day_number', 'days.month_id', 'months.month_name', 'years.year_name')
            ->orderBy('days.id', 'DESC')->get();
        $kingar = Kindgarden::all();
        date_default_timezone_set('Asia/Tashkent');
        // date("h:i:sa:M-d-Y");
        $d = strtotime("+1 day");
        return view('technolog.home', ['date' => $days, 'tomm' => $d, 'kingardens' => $kingar]);
    }

    public function newday(Request $request)
    {
        $months = Month::all();
        $year = Year::orderBy('id', 'DESC')->first();
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("+1 day");
        foreach ($months as $month) {
            if ($month->month_en == date("F", $d)) {
                Month::where('month_en', $request->daymonth)
                    ->update(['month_active' => 1]);
                $activeID = $month;
            } else {
                Month::where('month_en', $month->month_en)
                    ->update(['month_active' => 0]);
            }
        }

        if (date("Y", $d) != $year->year_name) {
            Year::where('id', $year->id)
                ->update(['year_active' => 0]);
            $rr = Year::create([
                'year_name' => $request->dayyear,
                'year_active' => 1
            ]);
            $year = $rr;
        }

        $newday = Day::create([
            'day_number' => date("d", $d),
            'month_id' => $activeID->id,
            'year_id' => $year->id
        ]);

        return view('technolog.newday', ['newday' => $newday, 'month' => $activeID, 'year' => $year]);
    }


    public function sendmenu()
    {
        return view('technolog.newday');
    }
}
