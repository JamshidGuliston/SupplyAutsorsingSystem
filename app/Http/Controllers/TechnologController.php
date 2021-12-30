<?php

namespace App\Http\Controllers;

use App\Models\Age_range;
use App\Models\Region;
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
        // dd($days[0]->day_number);
        return view('technolog.home', ['date' => $days, 'tomm' => $d, 'kingardens' => $kingar]);
    }

    // yangi kun ishlari
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
        if (empty($year->year_name)) {
            $rr = Year::create([
                'year_name' => $request->dayyear,
                'year_active' => 1
            ]);
            $year = $rr;
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
        $bool = Day::where('year_id', $year->id)
            ->where('month_id', $activeID->id)
            ->where('day_number', date("d", $d))->get();

        if (empty($bool->day_number)) {
            $newday = Day::create([
                'day_number' => date("d", $d),
                'month_id' => $activeID->id,
                'year_id' => $year->id
            ]);
        }

        return view('technolog.newday', ['newday' => $newday, 'month' => $activeID, 'year' => $year]);
    }


    public function sendmenu()
    {
        return view('technolog.newday');
    }

    // bog'chalar sozlanmalari

    public function settings(Request $request, $id)
    {
        $kgarden = Kindgarden::where('id', $id)->with('age_range')->get();
        $age = Age_range::all();
        $region = Region::all();
        dd($kgarden);
        return view('technolog.settings', ['garden' => $kgarden, 'ages' => $age, 'regions' => $region]);
    }

    public function updategarden(Request $request)
    {
        $kind = Kindgarden::find($request->kinname_id);
        $tags = $request->yongchek;
        $kind->age_range()->sync($tags);
        // dd($request->all());
        Kindgarden::where('id', $request->kinname_id)
            ->update([
                'kingar_name' => $request->kinname,
                'region_id' => $request->region,
                'kingar_password' => $request->kinparol,
                'worker_count' => $request->worker,
                'hide' => $request->hide,
            ]);
        return redirect()->route('technolog.home');
    }
}
