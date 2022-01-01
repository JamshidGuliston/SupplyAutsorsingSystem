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
use App\Models\Season;
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
        Temporary::truncate();
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
        $newday = Day::where('year_id', $year->id)
            ->where('month_id', $activeID->id)
            ->where('day_number', date("d", $d))->first();
       
        if (empty($newday->day_number)) {
            $newday = Day::create([
                'day_number' => date("d", $d),
                'month_id' => $activeID->id,
                'year_id' => $year->id
            ]);
        }

        $users = Kindgarden::where('hide', 1)->get();
    	$path = "https://api.telegram.org/bot";
    	$token = "5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k";
    	$text = "Боғчангиз учун эртанги овқатлар менюсига болалар сонини критинг. <b>3-4 ёшгача = ?</b>";
        $buttons = '{"inline_keyboard":[[{"text":"1","callback_data":"addnumber_1"}, {"text":"2","callback_data":"addnumber_2"}, {"text":"3","callback_data":"addnumber_3"}], [{"text":"4","callback_data":"addnumber_4"}, {"text":"5","callback_data":"addnumber_5"}, {"text":"6","callback_data":"addnumber_6"}], [{"text":"7","callback_data":"addnumber_7"}, {"text":"8","callback_data":"addnumber_8"}, {"text":"9","callback_data":"addnumber_9"}], [{"text":"0","callback_data":"addnumber_0"}, {"text":"<","callback_data":"remove_<"}]]}';
    	// dd($users);
    	foreach($users as $user){
    		Person::where('telegram_id', $user->telegram_user_id)->update(array('childs_count' => '0'));
    		$this->curl_get_contents($path.$token.'/sendmessage?chat_id='.$user->telegram_user_id.'&text='.$text.'&parse_mode=html&reply_markup='.$buttons);
    	}

        return redirect()->route('technolog.sendmenu', ['day'=> date("d-F-Y", $d)]);
    }


    public function sendmenu($day)
    {
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("+1 day");
        if($day == date("d-F-Y", $d)){
            $ages = Age_range::all();
            $sid = Season::where('hide', 1)->first();
            $menus = One_day_menu::where('menu_season_id', $sid->id)->get();
            $gr = Temporary::join('kindgardens', 'temporaries.kingar_name_id', '=', 'kindgardens.id')
                ->orderby('kindgardens.id', 'ASC')->get();
            $gar = Kindgarden::with('age_range')->get();
            $mass = array();
            $loo = 0;
            for($i=0; $i<count($gr); $i++){
                $mass[$loo]['id'] = $gr[$i]->id;
                $mass[$loo]['name'] = $gr[$i]->kingar_name;
                $mass[$loo]['workers'] = $gr[$i]->worker_count;
                // for($l=0; $l<count($age); $l++){
                $kages = Kindgarden::find($gr[$i]->id);
                foreach($kages->age_range as $age){
                    if($age->id == $gr[$i]->age_id){
                        $mass[$loo][$age->id] = $gr[$i]->age_number;
                    }
                    if(empty($mass[$loo][$age->id]) and $age->id>0 and $age->id != $gr[$i]->age_id){
                        $mass[$loo][$age->id] = "-";
                    }
                }
                // }
                if($i+1<count($gr) and $gr[$i+1]->id != $mass[$loo]['id']){
                    $loo++;
                }
            }
            // dd($gar[0]->age_range[1]->id);
            return view('technolog.newday', ['ages'=>$ages, 'menus'=>$menus, 'temps'=> $mass]);
        }
        else{
            return view('technolog.showdate');
        }
    }

    // bog'chalar sozlanmalari

    public function settings(Request $request, $id)
    {
        $kgarden = Kindgarden::find($id);
        $age = Age_range::all();
        $region = Region::all();
        // dd($kgarden->age_range);
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

    function curl_get_contents($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
}
