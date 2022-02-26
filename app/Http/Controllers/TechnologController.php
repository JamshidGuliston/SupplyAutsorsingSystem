<?php

namespace App\Http\Controllers;

use App\Models\Active_menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\MakeComponents;
use App\Traits\RequestTrait;
use App\Models\Age_range;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use App\Models\Day;
use App\Models\Food;
use App\Models\Food_category;
use App\Models\Food_composition;
use App\Models\Month;
use Illuminate\Http\Request;
use App\Models\Person;
use App\Models\Kindgarden;
use App\Models\Year;
use App\Models\Temporary;
use App\Models\Menu_composition;
use App\Models\Number_children;
use App\Models\Titlemenu;
use App\Models\order_product;
use App\Models\history_process;
use App\Models\Meal_time;
use App\Models\minus_multi_storage;
use App\Models\Nextday_namber;
use App\Models\order_product_structure;
use App\Models\plus_multi_storage;
use App\Models\Product;
use App\Models\Product_category;
use App\Models\Season;
use App\Models\Shop;
use App\Models\Size;
use App\Models\titlemenu_food;
use Dompdf\Dompdf;
use TCG\Voyager\Models\Category;

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
        // dd($days);
        $kingar = Kindgarden::all();
        $nextdaymenu = Nextday_namber::all();
        $season = Season::where('hide', 1)->first();
        $menus = Titlemenu::where('menu_season_id', $season->id)->get();
        // dd($season);
        date_default_timezone_set('Asia/Tashkent');
        // date("h:i:sa:M-d-Y");
        $d = strtotime("-10 hours 30 minutes");
        // dd($days[0]->day_number);
        return view('technolog.home', ['date' => $days, 'tomm' => $d, 'kingardens' => $kingar, 'menus' => $menus, 'next' => $nextdaymenu]);
    }

    // yangi kun ishlari
    public function newday(Request $request)
    {
        Temporary::truncate();
        $months = Month::all();
        $year = Year::orderBy('id', 'DESC')->first();
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
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

        // vaqtinchalik keyingi kun menyusini bugungi kungi menyu sifatida ishlatishni boshlaydi
        sleep(4);
        $nextdays = Nextday_namber::orderBy('kingar_name_id', 'ASC')->get();
        $endday = Day::orderBy('id', 'DESC')->first();
        foreach($nextdays as $nextrow){
        	$king = Kindgarden::where('id', $nextrow->kingar_name_id)->where('hide', 1)->first();
        	if(isset($king->id)){
	        	Temporary::create([
	        		'kingar_name_id' => $nextrow->kingar_name_id,
		    		'age_id' => $nextrow->king_age_name_id,
		    		'age_number' => $nextrow->kingar_children_number
	        	]);
        	}
            Number_children::create([
                'kingar_name_id' => $nextrow->kingar_name_id,
                'day_id' => $endday->id,
                'king_age_name_id' => $nextrow->king_age_name_id,
                'kingar_children_number' => $nextrow->kingar_children_number,
                'workers_count' => $nextrow->workers_count,
                'kingar_menu_id' => $nextrow->kingar_menu_id,
            ]);
            $findmenu = Active_menu::where('day_id', $endday->id)->where('title_menu_id', $nextrow->kingar_menu_id)->get();
            if($findmenu->count() == 0){
                $menuitems = Menu_composition::where('title_menu_id', $nextrow->kingar_menu_id)
                        ->orderby('menu_meal_time_id', 'ASC')
                        ->orderby('id', 'ASC')
                        ->get();
                foreach($menuitems as $row){
                    Active_menu::create([
                        'day_id' => $endday->id,
                        'title_menu_id' => $row->title_menu_id,
                        'menu_meal_time_id' => $row->menu_meal_time_id,
                        'menu_food_id' => $row->menu_food_id,
                        'product_name_id' => $row->product_name_id,
                        'age_range_id' => $row->age_range_id,
                        'weight' => $row->weight
                    ]);
                }
            }
        }
        Nextday_namber::truncate();
        // bog'chalarga bugungi menyu faollashdi

        // $users = Kindgarden::where('hide', 1)->get();
        // $path = "https://api.telegram.org/bot";
        // $token = "5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k";
        // $text = "Боғчангиз учун эртанги овқатлар менюсига болалар сонини критинг. <b>3-4 ёшгача = ?</b>";
        // $buttons = '{"inline_keyboard":[[{"text":"1","callback_data":"addnumber_1"}, {"text":"2","callback_data":"addnumber_2"}, {"text":"3","callback_data":"addnumber_3"}], [{"text":"4","callback_data":"addnumber_4"}, {"text":"5","callback_data":"addnumber_5"}, {"text":"6","callback_data":"addnumber_6"}], [{"text":"7","callback_data":"addnumber_7"}, {"text":"8","callback_data":"addnumber_8"}, {"text":"9","callback_data":"addnumber_9"}], [{"text":"0","callback_data":"addnumber_0"}, {"text":"<","callback_data":"remove_<"}]]}';
        // dd($users);
        // foreach ($users as $user) {
        //     Person::where('telegram_id', $user->telegram_user_id)->update(array('childs_count' => '0'));
        //     $this->curl_get_contents($path . $token . '/sendmessage?chat_id=' . $user->telegram_user_id . '&text=' . $text . '&parse_mode=html&reply_markup=' . $buttons);
        // }    
        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }


    public function sendmenu($day)
    {
        // dd($day);
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        $ages = Age_range::all();
        // dd($ages);
        $sid = Season::where('hide', 1)->first();
        // dd($sid);
        $menus = Titlemenu::where('menu_season_id', $sid->id)->get();
        if ($day == date("d-F-Y", $d)) {
            $gr = Temporary::join('kindgardens', 'temporaries.kingar_name_id', '=', 'kindgardens.id')
                ->orderby('kindgardens.id', 'ASC')->get();

            $gar = Kindgarden::where('hide', 1)->with('age_range')->get();
            // unset($gar[0]);
            // dd($gar);
            $mass = array();
            $loo = 0;
            for ($i = 0; $i < count($gr); $i++) {
                $mass[$loo]['id'] = $gr[$i]->id;
                $mass[$loo]['name'] = $gr[$i]->kingar_name;
                $mass[$loo]['workers'] = $gr[$i]->worker_count;
                // for($l=0; $l<count($age); $l++){
                $kages = Kindgarden::find($gr[$i]->id);
                // dd($kages->age_range);
                foreach ($kages->age_range as $age) {
                    if ($age->id == $gr[$i]->age_id) {
                        $mass[$loo][$age->id] = $gr[$i]->age_number;
                    }
                    if (empty($mass[$loo][$age->id]) and $age->id > 0 and $age->id != $gr[$i]->age_id) {
                        $mass[$loo][$age->id] = "-";
                    }
                }
                for ($j = 0; $j < count($gar); $j++) {
                    if ($gar[$j]['id'] == $gr[$i]['id']) {
                        $gar[$j]['ok'] = 1;
                    }
                }
                if ($i + 1 < count($gr) and $gr[$i + 1]->id != $mass[$loo]['id']) {
                    $loo++;
                }
            }
            $activ = Kindgarden::where('hide', 1)->get();
            // dd($activ);
            $nextday = Nextday_namber::join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
                            ->leftjoin('temporaries', function($join){
                                $join->on('nextday_nambers.kingar_name_id', '=', 'temporaries.kingar_name_id');
                                $join->on('nextday_nambers.king_age_name_id', '=', 'temporaries.age_id');
                            })
                            ->orderby('nextday_nambers.kingar_name_id', 'ASC')
                            ->get([
                                'nextday_nambers.id',
                                'nextday_nambers.king_age_name_id', 
                                'nextday_nambers.kingar_children_number', 
                                'nextday_nambers.workers_count', 
                                'nextday_nambers.kingar_menu_id', 
                                'nextday_nambers.kingar_name_id', 
                                'nextday_nambers.id', 
                                'kindgardens.id as kingarid',
                                'kindgardens.kingar_name',
                                'temporaries.id as tempid',
                                'temporaries.age_number'
                            ]);
            // dd($nextday);
            $nextdayitem = array();
            $loo = 0;
            for($i = 0; $i < count($nextday); $i++){
                $nextdayitem[$loo]['id'] = $nextday[$i]->id;
                $nextdayitem[$loo]['kingar_name_id'] = $nextday[$i]->kingar_name_id;
                $nextdayitem[$loo]['kingar_name'] = $nextday[$i]->kingar_name;
                $nextdayitem[$loo][$nextday[$i]->king_age_name_id] = array($nextday[$i]->id, $nextday[$i]->kingar_children_number, $nextday[$i]->tempid, $nextday[$i]->age_number, $nextday[$i]->kingar_menu_id);
                $nextdayitem[$loo]['workers_count'] = $nextday[$i]->workers_count;
                if ($i + 1 < count($nextday) and $nextday[$i + 1]->kingar_name_id != $nextdayitem[$loo]['kingar_name_id']) {
                    $loo++;
                }
            }

            $shops = Shop::where('hide', 1)->with('kindgarden')->with('product')->get();

            // dd($nextdayitem);
            $endday = Day::orderBy('id', 'DESC')->first();
            $mf = titlemenu_food::orderBy('day_id', 'DESC')->first();
            $sendmenu = 0;
            // dd($mf);
            if(isset($mf->day_id) and $endday->id == $mf->day_id){
                $sendmenu = 1;
            }
            $nextday = 1;
            return view('technolog.newday', ['sendmenu' => $sendmenu, 'nextdayitem' => $nextdayitem, 'shops' => $shops, 'ages' => $ages, 'menus' => $menus, 'temps' => $mass, 'gardens' => $gar, 'activ'=>$activ]);
        } else {

            $nextday = Number_children::where('day_id', $day)->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')
                            ->orderby('number_childrens.kingar_name_id', 'ASC')
                            ->get();
            // dd($nextday);
            $nextdayitem = array();
            $loo = 0;
            for($i = 0; $i < count($nextday); $i++){
                $nextdayitem[$loo]['kingar_name_id'] = $nextday[$i]->kingar_name_id;
                $nextdayitem[$loo]['kingar_name'] = $nextday[$i]->kingar_name;
                $nextdayitem[$loo][$nextday[$i]->king_age_name_id] = array($nextday[$i]->id, $nextday[$i]->kingar_children_number, $nextday[$i]->tempid, $nextday[$i]->age_number, $nextday[$i]->kingar_menu_id);
                $nextdayitem[$loo]['workers_count'] = $nextday[$i]->workers_count;
                if ($i + 1 < count($nextday) and $nextday[$i + 1]->kingar_name_id != $nextdayitem[$loo]['kingar_name_id']) {
                    $loo++;
                }
            }

            // dd($nextdayitem);
            return view('technolog.showdate', ['day' => $day, 'ages' => $ages, 'nextdayitem' => $nextdayitem]);
        }
    }
    // yetkazib beruvchilar

    public function nextdelivershop(Request $request, $id){
        $shop = Shop::where('id', $id)->with('kindgarden')->with('product')->first();
        // dd($shop);

        $shopproducts = array();
        foreach($shop->kindgarden as $row){
            $shopproducts[$row->id]['name'] = $row->kingar_name;    
            $day = Day::orderBy('id', 'DESC')->first();
            foreach($shop->product as $prod){
            	// echo $prod->id;
            	$shopproducts[$row->id][$prod->id] = "";
                $allsum = 0;
                $onesum = 0;
                $workers = 0;
                $weight = 0;
                $itempr = "";
        		$nextday = Nextday_namber::orderBy('kingar_name_id', 'ASC')->orderBy('king_age_name_id', 'ASC')->get();
        		// dd($nextday);
                foreach($nextday as $next){
                    if($row->id == $next->kingar_name_id){
                        $prlar =  Menu_composition::where('title_menu_id', $next->kingar_menu_id)->where('age_range_id', $next->king_age_name_id)->where('product_name_id', $prod->id)->get();
                        foreach($prlar as $prw){
                        	$itempr = $itempr . "+".$prw->weight." * ". $next->kingar_children_number;
                        	$weight += $prw->weight * $next->kingar_children_number;
                        }
                        // $allsum += $weight * $next->kingar_children_number;
                        // $onesum += $weight; 
                        // $workers = $next->workers_count;
                    }
                }
                $workeat = titlemenu_food::where('day_id', $day->id)->get();

                foreach($workeat as $wo){
                        $woe = Menu_composition::where('title_menu_id', $wo->titlemenu_id)
                                ->where('menu_food_id', $wo->food_id)
                                ->where('age_range_id', $wo->worker_age_id)
                                ->where('product_name_id', $prod->id)
                                ->sum('weight');
                        // if($woe > 0){
                        // 	$itempr = $itempr . 
                        // }
                }



                $prdiv = Product::where('id', $prod->id)->first();
                // $itempr . "=" .
                $shopproducts[$row->id][$prod->id] = $weight / $prod->div; 
            }
        }

        // dd($shopproducts);
        return view('technolog.nextdelivershop', compact('shopproducts', 'shop'));
    }

    public function nextdayshoppdf(Request $request, $id){
        $shop = Shop::where('id', $id)->with('kindgarden')->with('product')->first();
        // dd($shop);
        $nextday = Nextday_namber::orderBy('kingar_name_id', 'ASC')->get();

        $shopproducts = array();
        foreach($shop->kindgarden as $row){
            $shopproducts[$row->id]['name'] = $row->kingar_name;    
            $day = Day::orderBy('id', 'DESC')->first();
            foreach($shop->product as $prod){
                $allsum = 0;
                $onesum = 0;
                $workers = 0;
                foreach($nextday as $next){
                    if($row->id == $next->kingar_name_id){
                        $weight =  Menu_composition::where('title_menu_id', $next->kingar_menu_id)->where('product_name_id', $prod->id)->sum('weight');
                        $allsum += $weight * $next->kingar_children_number;
                        $onesum += $weight; 
                        $workers = $next->workers_count;
                    }
                }
                $workeat = titlemenu_food::where('day_id', $day->id)->get();

                foreach($workeat as $wo){
                        $woe = Menu_composition::where('title_menu_id', $wo->titlemenu_id)
                                ->where('menu_food_id', $wo->food_id)
                                ->where('age_range_id', $wo->worker_age_id)
                                ->where('product_name_id', $prod->id)
                                ->sum('weight');
                }

                $prdiv = Product::where('id', $prod->id)->first();
                
                $shopproducts[$row->id][$prod->id] = $allsum / $prdiv->div; 
            }
        }

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('technolog.nextdayshoppdf', compact('shopproducts', 'shop')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }

    // PDF next day //////////////////////////////////////////////////////////
    public function nextdaymenuPDF(Request $request, $gid, $ageid)
    {
        $menu = Nextday_namber::where([
			['kingar_name_id', '=', $gid],
			['king_age_name_id', '=', $ageid]
		])->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
        ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();
		// dd($menu);  
		$products = Product::where('hide', 1)
			->orderBy('sort', 'ASC')->get();
		
		$menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                        ->where('age_range_id', $ageid)
                        ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
                        ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
                        ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
                        ->orderBy('menu_meal_time_id')
                        ->get();

        // dd($menuitem);
        // xodimlar ovqati uchun
        $day = Day::join('months', 'months.id', '=', 'days.month_id')->orderBy('days.id', 'DESC')->first(['days.day_number','days.id as id', 'months.month_name']);
        // dd($day);
        $workerfood = titlemenu_food::where('day_id', $day->id)
                    ->where('worker_age_id', $ageid)
                    ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                    ->get();
        // dd($workerfood);
        $nextdaymenuitem = [];
        $workerproducts = [];
        // kamchilik bor boshlangich qiymat berishda
        $productallcount = array_fill(1, 500, 0);
        foreach($menuitem as $item){
            $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name; 
            $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
            $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name; 
            $productallcount[$item->product_name_id] += $item->weight;
            for($i = 0; $i<count($products); $i++){
                if(empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id){
                    $products[$i]['yes'] = 1;
                    // array_push($yesproduct, $products[$i]);
                }
            }
        }
        // dd($productallcount);
        // kamchilik bor boshlangich qiymat berishda
        $workerproducts = array_fill(1, 500, 0);
        foreach($workerfood as $tr){
            foreach($nextdaymenuitem[3][$tr->food_id] as $key => $value){
                if($key != 'foodname'){
                    $workerproducts[$key] += $value; 
                }
                // array_push($workerproducts, $nextdaymenuitem[3][$tr->food_id]);
            }
        }
        // dd($workerproducts);    
        
        // dd($workerfood);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('alltable', ['day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
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

    public function ageranges(Request $request, $id)
    {
        $results = Kindgarden::where('id', $id)->with('age_range')->get();
        // dd($results[0]->age_range);
        $html = [];
        foreach ($results[0]->age_range as $rows) {
            // $html = $html + "<input type='text' value='salom'>";
            array_push($html, "<div class='input-group mb-3 mt-3'>
            <span class='input-group-text' id='inputGroup-sizing-default'>" . $rows['age_name'] . "</span>
            <input type='number' name='ages[]' data-id=" . $rows['id'] . "  class='ageranges form-control' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-default'>
            </div>");
        }
        return $html;
    }


    public function addage(Request $request, $bogid, $ageid, $qiymati)
    {
        // $find = Temporary::where('kingar_name_id', $bogid)->first();
        // if ($find->age_id == $ageid) {
        //     $find->delete();
        //     return 0;
        // }

        Temporary::create([
            'kingar_name_id' => $bogid,
            'age_id' => $ageid,
            'age_number' => $qiymati
        ]);
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
                'worker_age_id' => $request->worker_age_id,
                'hide' => $request->hide,
            ]);
        return redirect()->route('technolog.home');
    }

    public function getage(Request $request, $bogid)
    {
        $results = Kindgarden::where('id', $bogid)->with('age_range')->get();
        // dd($results[0]->age_range);
        $htmls = [];
        array_push($htmls, "<h3>" . $results[0]['kingar_name'] . "</h3> <input type='hidden' name='kingarediteid' value=" . $results[0]['id'] . " >");
        foreach ($results[0]->age_range as $rows) {
            $edite =  Temporary::where('kingar_name_id', $bogid)->where('age_id', $rows['id'])->first();
            if (empty($edite['age_number'])) {

                $edite['age_number'] = 0;
            }
            // $html = $html + "<input type='text' value='salom'>";
            array_push($htmls, "  <div class='input-group mb-3 mt-3'>
            <span class='input-group-text' id='inputGroup-sizing-default'>" . $rows['age_name'] . "</span>
            <input type='number' required name='ages[]' value=" . $edite['age_number'] . " data-id=" . $rows['id'] . "  class='age_ranges form-control' aria-label='Sizing example input' aria-describedby='inputGroup-sizing-default'>
            <input type='hidden' required name='agesid[]' value=" . $rows['id'] . ">
            </div>");
        }
        return $htmls;
    }

    public function editage(Request $request)
    {
        // dd($request->all());
        $ages = $request->ages;
        $agesid = $request->agesid;
        for($i=0; $i < count($ages); $i++){
            $find = Temporary::where('kingar_name_id', $request->kingarediteid)->where('age_id', $agesid[$i])->get();

            if (empty($find[0])) {
                Temporary::create([
                    'kingar_name_id' => $request->kingarediteid,
                    'age_id' => $agesid[$i],
                    'age_number' => $ages[$i]
                ]);
            } else {
                Temporary::where('kingar_name_id', $request->kingarediteid)->where('age_id', $agesid[$i])->update([
                    'age_id' => $agesid[$i],
                    'age_number' => $ages[$i]
                ]);
            }
        }

        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");

        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }


    // mayda skladlarga product buyurtma berish

    public function addproduct()
    {
        $months = Month::all();
        $days = Day::orderby('id', 'DESC')->get();
        $orederproduct = order_product::join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('days', 'days.id', '=', 'order_products.day_id')
            // ->where('day_id', $days[1]->id)
            ->select('order_products.id', 'days.day_number', 'order_products.order_title', 'order_products.document_processes_id', 'kindgardens.kingar_name') 
            ->orderby('order_products.id', 'DESC')
            ->get();
        $orederitems = order_product_structure::join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->get();
        // dd($orederproduct);
        $kingar = Kindgarden::all();
        
        foreach($orederproduct as $item){
            $t = 0;
            foreach($kingar as $ki){
                if($item->kingar_name == $ki->kingar_name)
                {
                    // $kingar[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        return view('technolog.addproduct', ['gardens' => $kingar, 'orders' => $orederproduct, 'products'=>$orederitems, 'months'=>$months]);
    }

    public function ordername(Request $request)
    {
        $days = Day::orderby('id', 'DESC')->get();
        $orderproduct = order_product::create([
            'kingar_name_id' => $request->mtmname,
            'day_id' => $days[1]->id,
            'order_title' => $request->title,
            'document_processes_id' => 1,
        ]);

        history_process::create([
            'order_product_id' => $orderproduct->id,
            'user_name_id' => Auth::user()->id,
            'order_title' => $request->title,
            'document_process_id' => 1,
            'action' => 1
        ]);

        return redirect()->route('technolog.addproduct');
    }

    public function orderitem(Request $request, $id)
    {
        $orederproduct = order_product::where('order_products.id', $id)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('days', 'days.id', '=', 'order_products.day_id')
            ->first();
        $days = Day::orderby('id', 'DESC')->get();
        // agar yangi kun ochilsa hujjat oxiriga yetmagan hisoblanadi
        // if (empty($orederproduct->day_number) or $days[1]->day_number != $orederproduct->day_number or $days[1]->month_id != $orederproduct->month_id) {
        //     return redirect()->route('technolog.addproduct');
        // }
        // shu joyida hide ishlatishimiz kerak majbur
        $newsproduct = Product::all();
        $items = order_product_structure::where('order_product_name_id', $id)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->select('order_product_structures.id', 'order_product_structures.product_weight', 'products.product_name')
            ->get();
        foreach($items as $item){
            $t = 0;
            foreach($newsproduct as $pro){
                if($item->product_name == $pro->product_name){
                    $newsproduct[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        // dd($items);
        return view('technolog.orderitem', ['orderid' => $id, 'productall' => $newsproduct, 'items' => $items, 'ordername' => $orederproduct]);
    }
    public function plusproduct(Request $request)
    {
        // dd($request->all());
        order_product_structure::create([
            'order_product_name_id' => $request->titleid,
            'product_name_id' => $request->productsid,
            'product_weight' => $request->sizeproduct,
        ]);
        return redirect()->route('technolog.orderitem', $request->titleid);
    }
    // parolni tasdiqlash
    public function controlpassword(Request $request)
    {
        $password = Auth::user()->password;
        if (Hash::check($request->password, $password)) {
            $result = 1;
            order_product::where('id', $request->orderid)->update([
                'document_processes_id' => 2
            ]);
        } else {
            $result = 0;
        }
        return $result;
    }
    // botga start bosganlarni tashkilotiga bog'lash
    public function getbotusers(Request $request){
        $users = Person::with('shop')->with('garden')->orderby('id', 'DESC')->get();
        $gardens = Kindgarden::all();
        $shops = Shop::all();
        // dd($users);
        return view('technolog.botusers', compact('users', 'gardens', 'shops'));
    }
    // orderproduct malulotlarini olish 
    public function getproduct(Request $request)
    {

        $number = order_product_structure::where('order_product_structures.id', $request->id)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->select('order_product_structures.id', 'order_product_structures.product_weight', 'products.product_name')
            ->first();

        $htmlproduct = "<div class='input-group mb-3'>
            <span class='input-group-text' id='basic-addon2'>" . $number['product_name'] . " </span>
            <input  type='text' data-producy=" . $number['id'] . " value=" . $number['product_weight'] . " required class='form-control  product_order'  placeholder='raqam kiriting'></div>";

        return $htmlproduct;
    }

    // orderproduct malumotlarni tahrirlash

    public function editproduct(Request $request)
    {
        order_product_structure::where('id', $request->producid)->update(
            ['product_weight' => $request->orderinpval]
        );
    }

    //  orderproduct malumotlarini o'chirish 

    public function deleteid(Request $request)
    {
        order_product_structure::where('id', $request->id)->delete();
    }

    public function bindgarden(Request $request){
        $per = Person::where('id', $request['personid'])->first();
        $rr = Kindgarden::where('id', $request['mname'])
            ->update([
                'telegram_user_id' => $per->telegram_id
            ]);
        Person::where('id', $request['personid'])
            ->update([
                'kingar_id' => $request['mname'],
                'shop_id' => -1
            ]);
        return redirect()->route('technolog.getbotusers');
    }

    public function bindshop(Request $request){
        $per = Person::where('id', $request['personid'])->first();
        Shop::where('id', $request['shname'])
            ->update([
                'telegram_id' => $per->telegram_id
            ]);
        Person::where('id', $request['personid'])
            ->update([
                'kingar_id' => 0,
                'shop_id' => $request['shname']
            ]);
        return redirect()->route('technolog.getbotusers');
    }

    // Menu saqlash
	
	public function todaynextdaymenu(Request $request)
	{
        // dd($request->all());
        $mid = $request->mid;
        $dmf = $request->dmf;
        $menuages = [];
        foreach($mid as $mi){
            $param = explode("_", $mi);
            $menuages[$param[0]] = $param[1]; 
        }
        // dd($menuages);
		$days = Day::orderBy('id', 'DESC')->first();
		$chil_number = Temporary::all();
        // dd($chil_number);
		foreach ($chil_number as $child) {
			$workers = Kindgarden::where('id', $child->kingar_name_id)->first();
			// dd($workers['worker_count']);
			$menusi = $request['manuone'];
			if($child->age_id == 3){
				$menusi = $request['two'];
			}
            Nextday_namber::create([
                'kingar_name_id' => $child->kingar_name_id,
                'king_age_name_id' => $child->age_id,
                'kingar_children_number' => $child->age_number,
                'workers_count' => $workers['worker_count'],
                'kingar_menu_id' => $menuages[$child->age_id],
            ]);

		}

        foreach($dmf as $dm){
            $param = explode("_", $dm);
            titlemenu_food::create([
                'day_id' => $days->id,
                'worker_age_id' => $param[0],
                'titlemenu_id' => $param[1],
                'food_id' => $param[2]
            ]);
        }

		$temp = Temporary::truncate();
		$gr = Kindgarden::all();

		return redirect()->route('technolog.home');
	}

    public function allproducts(Request $request)
    {
        $products = Product::with('shop')->get();
        // dd($products);
        return view('technolog.allproducts', compact('products'));
    }

    public function settingsproduct(Request $request, $id)
    {
        $product = Product::where('id', $id)->first();
        $categories = Product_category::all();
        $sizes = Size::all(); 
        // dd($product);
        return view('technolog.settingsproduct', compact('product', 'categories', 'sizes'));
    }

    public function updateproduct(Request $request)
    {
        // dd($request->all());
        Product::where('id', $request['productid'])
            ->update([
                'size_name_id' => $request['sizeid'],
                'category_name_id' => $request['catid'],
                'div' => $request['div'],
                'sort' => $request['sort'],
                'hide' => $request['hide']
            ]);
        return redirect()->route('technolog.allproducts');
    }

    public function shops(Request $request)
    {
        $shops = Shop::all();
        // dd($shops);
        return view('technolog.shops', compact('shops'));
    }

    public function shopsettings(Request $request, $id)
    {
        $shop = Shop::where('id', $id)->with('product')->with('kindgarden')->first();
        $products = Product::all();
        $gardens = Kindgarden::all();
        return view('technolog.shopsettings', compact('shop', 'products', 'gardens'));
    }

    public function updateshop(Request $request)
    {
        $shop = Shop::find($request->shopid);
        $prd = $request->products;
        $shop->product()->sync($prd);
        $grd = $request->gardens;
        $shop->kindgarden()->sync($grd);
        $shop->update([
                'shop_name' => $request->shopname,
                'hide' => $request->hide
            ]);
        return redirect()->route('technolog.shops');
    }

    public function addshop()
    {
        $products = Product::all();
        $gardens = Kindgarden::all();

        return view('technolog.addshop', compact('products', 'gardens'));
    }

    public function createshop(Request $request)
    {
        $shop = Shop::create([
            'shop_name' => $request->name,
            'telegram_id' => 0,
            'hide' => 1
        ]);
        $prd = $request->products;
        $shop->product()->sync($prd);
        $grd = $request->gardens;
        $shop->kindgarden()->sync($grd);

        return redirect()->route('technolog.shops');
    }

    public function food(Request $request)
    {
        $food = Food::all();
        return view('technolog.food', compact('food'));
    }

    public function foodsettings(Request $request, $id)
    {
        $food = Food::where('id', $id)->first();
        $categories = Food_category::all();
        $times = Meal_time::all();
        return view('technolog.foodsettings', compact('food', 'categories', 'times'));
    }

    public function updatefood(Request $request)
    {
        Food::where('id', $request->foodid)
            ->update([
                'food_cat_id' => $request->catid,
    	        'meal_time_id' => $request->timeid
            ]);
        
        return redirect()->route('food');
    }

    public function fooditem(Request $request, $id)
    {
        $productall = Product::all();
        $food = Food_composition::where('food_name_id', $id)->join('food', 'food.id', '=', 'food_compositions.food_name_id')
                        ->join('products', 'products.id', '=', 'food_compositions.product_name_id')
                        ->get(['food_compositions.id', 'food.food_name','products.product_name']);
        // dd($food);
        foreach($food as $item){
            $t = 0;
            foreach($productall as $pro){
                if($item->product_name == $pro->product_name){
                    $productall[$t]['ok'] = 1;
                }
                $t++;
            }
        }
        return view('technolog.fooditem', compact('food', 'productall', 'id'));
    }

    public function addproductfood(Request $request)
    {
        Food_composition::create([
            'food_name_id' => $request->titleid,
    	    'product_name_id' => $request->productid
        ]);
        return redirect()->route('fooditem', $request->titleid);
    }

    public function deleteproductfood(Request $request)
    {
        Food_composition::where('id', $request->id)->delete();
    }

    public function editproductfood(Request $request)
    {
        Food_composition::where('id', $request->id)
            ->update([
    	        'product_name_id' => $request->productid
            ]);
        
        return redirect()->route('fooditem', $request->titleid);
    }

    public function addfood(Request $request)
    {
        $categories = Food_category::all();
        $times = Meal_time::all();
        return view('technolog.addfood', compact('categories', 'times'));
    }

    public function createfood(Request $request)
    {
        Food::create([
            'food_name' => $request->name,
            'food_cat_id' => $request->catid,
            'meal_time_id' => $request->timeid,
            'food_prepar_tech' => '...',
            'food_image' => 'png.png'
        ]);

        return redirect()->route('food');
    }

    public function seasons(Request $request)
    {
        $seasons = Season::all();
        return view('technolog.seasons', compact('seasons'));
    }

    public function menus(Request $request, $id)
    {
        $menus = Titlemenu::where('menu_season_id', $id)->get();
        $works = Nextday_namber::all();
        for($i = 0; $i < count($menus); $i++){
            $menus[$i]['us'] = 0;
            foreach($works as $row){
                if($row->kingar_menu_id == $menus[$i]['id']){
                    $menus[$i]['us'] = 1;
                }
            }
        }
        return view('technolog.menus', compact('menus', 'id', 'works'));
    }

    public function addtitlemenu(Request $request, $id)
    {
        $ages = Age_range::all();
        return view('technolog.addtitlemenu', compact('id', 'ages'));
    }

    public function createmenu(Request $request)
    {
        // dd($request->all());
        $menu = Titlemenu::create([
            'menu_name' => $request->name,
            'menu_season_id' => $request->seasonid
        ]);

        $age = $request->yongchek;
        $menu->age_range()->sync($age);

        return redirect()->route('technolog.menus', $request->seasonid);
    }

    public function menuitem(Request $request, $id)
    {
        $times = Meal_time::all();
        $titlemenu = Titlemenu::where('id', $id)->with('age_range')->first();
        $menuitem = Menu_composition::where('title_menu_id', $id)
                ->join('titlemenus', 'titlemenus.id', '=', 'menu_compositions.title_menu_id')
                ->join('meal_times', 'meal_times.id', '=', 'menu_compositions.menu_meal_time_id')
                ->join('food', 'food.id', '=', 'menu_compositions.menu_food_id')
                ->join('products', 'products.id', '=', 'menu_compositions.product_name_id')
                ->join('age_ranges', 'age_ranges.id', '=', 'menu_compositions.age_range_id')
                ->orderby('menu_compositions.menu_meal_time_id', 'ASC')
                ->orderby('menu_compositions.id', 'ASC')
                ->get([
                    'titlemenus.menu_name', 
                    'titlemenus.menu_season_id', 
                    'titlemenus.id as menuid', 
                    'meal_times.meal_time_name', 
                    'meal_times.id as meal_timeid', 
                    'food.food_name', 
                    'food.id as foodid', 
                    'products.product_name', 
                    'products.id as productid', 
                    'age_ranges.id as ageid', 
                    'menu_compositions.weight',
                    'menu_compositions.id'
                ]); 
        // dd($menuitem);
        return view('technolog.menuitem', compact('id', 'times', 'titlemenu', 'menuitem'));
    }
    //  copy 
    public function copymenuitem(Request $request){
        $titlemenu = Titlemenu::where('id', $request->menuid)->with('age_range')->first();
        $ages = array();
        $loop = 0;
        foreach($titlemenu->age_range as $age){
            $ages[$loop++] = $age->id;
        }

        $newtitlemenu = Titlemenu::create([
            'menu_name' => $request->newmenuname,
            'menu_season_id' => $request->seasonid
        ]);

        $newtitlemenu->age_range()->sync($ages);

        $menu = Menu_composition::where('title_menu_id', $request->menuid)->get();

        foreach($menu as $row){
            Menu_composition::create([
                "title_menu_id" => $newtitlemenu->id,
                "menu_meal_time_id" => $row->menu_meal_time_id,
                "menu_food_id" => $row->menu_food_id,
                "product_name_id" => $row->product_name_id,
                "age_range_id" => $row->age_range_id,
                "weight" => $row->weight
            ]);
        }

        return redirect()->route('technolog.menuitem', $newtitlemenu->id);
    }
    

    public function getfood(Request $request)
    {
        $food = Food::where('meal_time_id', $request->id)
                ->orwhere('meal_time_id', 0)
                ->get();

        $html = "<select id='foodid' name='foodid' onchange='change()' class='form-select' required aria-label='Default select example'>
                        <option value=''>--Taomni tanlang--</option>";
        foreach($food as $row){
            $html = $html."<option value=".$row->id.">".$row->food_name."</option>";
        }
        $html = $html."</select>";
        return $html;
    }

    public function getfoodcomposition(Request $request)
    {
        $menu = Titlemenu::where('id', $request->menuid)->with('age_range')->first();
        $foodcom = Food_composition::where('food_name_id', $request->id)
                ->join('products', 'products.id', '=', 'food_compositions.product_name_id')->get();
        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>...</th>
                        <th scope='col'>Maxsulot</th>";
        foreach($menu->age_range as $row){
            $html = $html."<th scope='col'>".$row['age_name']."</th>";
        }
        $html = $html."</tr>
                </thead>
                <tbody>";
        foreach($foodcom as $product){
            $html = $html."<tr>
                <td><input type='hidden' name='products[]' value='".$product->id."'></td>
                <td>".$product->product_name."</td>";
                foreach($menu->age_range as $row){
                    $html = $html."<td><input type='text' name='ages".$product->id."[]' required style='width: 100%;'></td>";
                }
                
                $html = $html."</tr>";
        }
        $html = $html."</tbody>
            </table>";
        
        return $html;
    }

    public function createmenucomposition(Request $request)
    {
        // dd($request->all());
        $menu = Titlemenu::where('id', $request->titleid)->with('age_range')->first();
        foreach($request->products as $product)
        {
            $ages = "ages".$product;
            $t = 0;
            foreach($menu->age_range as $age)
            {
                // echo "menu: ".$request->titleid." mealtime: ".$request->timeid." food: ".$request->foodid." product: ".$product." age: ".$age->id." weight: ".$request[$ages][$t++]." <br/>";
                Menu_composition::create([
                    'title_menu_id' => $request->titleid,
                    'menu_meal_time_id' => $request->timeid,
                    'menu_food_id' => $request->foodid,
                    'product_name_id' => $product,
                    'age_range_id' => $age->id,
                    'weight' => $request[$ages][$t++]
                ]);
            }

        }
        
        return redirect()->route('technolog.menuitem', $request->titleid);
    }

    public function getmenuproduct(Request $request)
    {
        $menu = Titlemenu::where('id', $request->menuid)->with('age_range')->first();
        $foodcom = Menu_composition::where('title_menu_id', $request->menuid)
                ->where('menu_meal_time_id', $request->timeid)
                ->where('menu_food_id', $request->foodid)
                ->where('product_name_id', $request->prodid)
                ->join('products', 'products.id', '=', 'menu_compositions.product_name_id')
                ->get(['menu_compositions.id', 'products.product_name', 'age_range_id', 'weight']);
        // dd($foodcom);
        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>...</th>
                        <th scope='col'>Maxsulot</th>";
        foreach($menu->age_range as $row){
            $html = $html."<th scope='col'>".$row['age_name']."</th>";
        }
        $html = $html."</tr>
                </thead>
                <tbody>";
        for($it = 0; $it < count($foodcom); $it++){
            $html = $html."<tr>
                <td></td>
                <td>".$foodcom[$it]['product_name']."</td>";
                foreach($menu->age_range as $row){
                    $html = $html."<td><input type='text' name='ages[]' value='".$foodcom[$it]['weight']."' required style='width: 100%;'></td>";
                    $html = $html."<input type='hidden' name='rows[]' value='".$foodcom[$it]['id']."'>";
                    $it++;
                }
                
                $html = $html."</tr>";
        }
        $html = $html."</tbody>
            </table>";
        
        return $html;

    }

    public function editemenuproduct(Request $request)
    {
        // dd($request->all());
        $it = 0;
        foreach($request->rows as $row){
            Menu_composition::where('id', $row)
                    ->update([
                        'weight' => $request->ages[$it]
                    ]);
            $it++;
        }
        return redirect()->route('technolog.menuitem', $request->menuid);
    }

    public function deletemenufood(Request $request)
    {
        Menu_composition::where('title_menu_id', $request->menuid)
                ->where('menu_meal_time_id', $request->timeid)
                ->where('menu_food_id', $request->foodid)
                ->delete();
        return redirect()->route('technolog.menuitem', $request->menuid);
    }

    public function getfoodnametoday(Request $request){
        $food = Menu_composition::where('title_menu_id', $request->menuid)
            ->where('menu_meal_time_id', 3)
            ->join('food', 'food.id', '=', 'menu_compositions.menu_food_id')
            ->get(['food.food_name', 'food.id as foodid']);

        $html = "<p>Xodimlar ovqatini tanlang.</p>";
        $bool = [];
        foreach($food as $row){
            if(empty($bool[$row->foodid])){
                $bool[$row->foodid] = 1;
                $html = $html."<input type='checkbox' class='checkfood' value=".$row->foodid."> <span id=".'worfood'.$row->foodid.">".$row->food_name."</span> <br>";
            }
        }
        $html = $html."</select>";
        echo $html;
    }

    public function sendtoallgarden(Request $request){
        dd('OK');
    }

    public function editnextworkers(Request $request){
        // soat
        Nextday_namber::where('kingar_name_id', $request->kingid)
                    ->update(['workers_count' => $request->workers]);
        
    }

    public function editnextcheldren(Request $request){
        // soat
        date_default_timezone_set('Asia/Tashkent');
        $d = strtotime("-10 hours 30 minutes");
        Nextday_namber::where('id', $request->nextrow)
                    ->update(['kingar_children_number' => $request->agecount]);
        Temporary::where('id', $request->temprow)->delete();
        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }
    public function fornextmenuselect(Request $request){
        $s = Season::where('hide', 1)->first();
        $titles = Titlemenu::where('menu_season_id', $s->id)->get();
        $html = "<select name='menuid' class='form-select' required aria-label='Default select example'>";
        foreach($titles as $row){
            if($row->id == $request->menuid)
                $html = $html."<option value=".$row->id." selected>".$row->menu_name."</option>";
            else
                $html = $html."<option value=".$row->id.">".$row->menu_name."</option>";
        }

        $html = $html."</select>";
        
        return $html;
    }

    public function editnextmenu(Request $request){
        date_default_timezone_set('Asia/Tashkent');
    	$d = strtotime("-10 hours 30 minutes");
        Nextday_namber::where('id', $request->nextrow)->update(['kingar_menu_id' => $request->menuid]);
        return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
    }

    // sklad
    public function addshopproduct(Request $request, $dayid=0){
        $months = Month::all();
        $activid = 0;
        foreach($months as $row){
            if($row->month_active == 1){
                $activid = $row->id;
            }
        }
        $days = Day::where('month_id', $activid)->get();
        for($i = 0; $i < count($days); $i++){
            if($dayid == 0){
                $days[count($days)-1]['active'] = 1;
                // $dayid = $days[$i]['id'];
            }
            if($days[$i]['id'] == $dayid){
                $days[$i]['active'] = 1;
                $dayid = $days[$i]['id'];
            }
        }
        // dd($days);
        $shops = Shop::where('hide', 1)->get();
        $products = Product::where('hide', 1)->get();
        $orederproduct = plus_multi_storage::where('day_id', $dayid)
            ->join('kindgardens', 'kindgardens.id', '=', 'plus_multi_storages.kingarden_name_d')
            ->join('shops', 'shops.id', '=', 'plus_multi_storages.shop_id')
            ->join('products', 'products.id', '=', 'plus_multi_storages.product_name_id')
            // ->where('day_id', $days[1]->id)
            // ->select('order_products.id', 'days.day_number', 'order_products.order_title', 'order_products.document_processes_id', 'kindgardens.kingar_name') 
            ->orderby('plus_multi_storages.id', 'DESC')
            ->get();
        
        // dd($orederproduct);
        $kingar = Kindgarden::where('hide', 1)->get();
        
        // foreach($orederproduct as $item){
            //     $t = 0;
            //     foreach($kingar as $ki){
                //         if($item->kingar_name == $ki->kingar_name)
                //         {
                    //             $kingar[$t]['ok'] = 1;
                    //         }
                    //         $t++;
                    //     }
                    // }
        // dd($products);
        return view('technolog.addshopproduct', ['allproducts' => $products, 'shops' => $shops, 'days' => $days, 'gardens' => $kingar, 'orders' => $orederproduct, 'months'=>$months]);   
    }

    public function productshoptogarden(Request $request){
        // dd($request->all());
        plus_multi_storage::create([
            'day_id' => $request->dayid,
            'shop_id' => $request->shopname,
            'kingarden_name_d' => $request->mtmname,
            'order_product_id' => 0,
            'product_name_id' => $request->productid,
            'product_weight' => $request->weight
        ]);

        return redirect()->route('technolog.addshopproduct', $request->dayid);
    }
    // kichkina skladlar /////////////////////////////////////////
    public function minusmultistorage(Request $request, $kid){
        $king = Kindgarden::where('id', $kid)->first();
        $month = Month::where('month_active', 1)->first();
        $days = Day::where('month_id', $month->id)->get();
        $minusproducts = [];
        foreach($days as $day){
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                    'minus_multi_storages.id',
                    'minus_multi_storages.product_name_id',
                    'minus_multi_storages.day_id',
                    'minus_multi_storages.kingarden_name_id',
                    'minus_multi_storages.product_weight',
                    'products.product_name',
                    'products.size_name_id',
                    'products.div',
                    'products.sort'
                ]);
            foreach($minus as $row){
                $minusproducts[$row->product_name_id][$day->id] = round($row->product_weight / $row->div, 2);
                $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        // dd($minusproducts);
        return view('technolog.minusmultistorage', ['minusproducts' => $minusproducts, 'kingar' => $king, 'days' => $days]);   
    }

    public function plusmultistorage(Request $request, $kid){
        $king = Kindgarden::where('id', $kid)->first();
        $month = Month::where('month_active', 1)->first();
        $days = Day::where('month_id', $month->id)->get();
        $minusproducts = [];
        foreach($days as $day){
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $kid)
                ->join('products', 'minus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                    'minus_multi_storages.id',
                    'minus_multi_storages.product_name_id',
                    'minus_multi_storages.day_id',
                    'minus_multi_storages.kingarden_name_id',
                    'minus_multi_storages.product_weight',
                    'products.product_name',
                    'products.size_name_id',
                    'products.div',
                    'products.sort'
                ]);
            foreach($minus as $row){
                if(!isset($minusproducts[$row->product_name_id])){
                    $minusproducts[$row->product_name_id] = 0;
                }
                $minusproducts[$row->product_name_id] += round($row->product_weight / $row->div, 2);
                // $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        // dd($minusproducts);
        $plusproducts = [];
        foreach($days as $day){
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $kid)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                    'plus_multi_storages.id',
                    'plus_multi_storages.product_name_id',
                    'plus_multi_storages.day_id',
                    'plus_multi_storages.kingarden_name_d',
                    'plus_multi_storages.product_weight',
                    'products.product_name',
                    'products.size_name_id',
                    'products.div',
                    'products.sort'
                ]);
            foreach($plus as $row){
                $plusproducts[$row->product_name_id][$row->day_id] = $row->product_weight;
                $plusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        // dd($minusproducts);
        return view('technolog.plusmultistorage', ['plusproducts' => $plusproducts, 'minusproducts' => $minusproducts, 'kingar' => $king, 'days' => $days]); 
    }
    //  /////////////////////////////////////////

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
