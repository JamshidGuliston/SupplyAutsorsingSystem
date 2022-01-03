<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Day;
use App\Models\Month;
use App\Models\Person;
use App\Models\Kindgarden;
use App\Models\Year;
use App\Models\Temporary;
use App\Models\Menu_composition;
use App\Models\Number_children;
use App\Models\One_day_menu;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

class TestController extends Controller
{
	// public function __construct()
 //   {
 //       $this->middleware('auth');
 //   }

    function index(Request $request)
    {
        $gr = Temporary::join('kindgardens', 'temporaries.kingar_name_id', '=', 'kindgardens.id')->orderBy('kingar_name_id')->get();
        $menu = One_day_menu::all();
        $days = Day::join('years', 'days.year_id', '=', 'years.id')
        			->join('months', 'days.month_id', '=', 'months.id')
        			 ->select(
			                  'days.id',
			                  'days.day_number',
			                  'years.year_name',
			                  'months.month_name'
			        )
        			->orderBy('days.id', 'DESC')->get();
        // dd($days);
        return view('adminhome', ['gardens' => $gr, 'menu'=> $menu, 'days'=>$days]);
    }

    public function tomorrowdate(Request $request)
    {
        $days = Day::orderBy('id', 'DESC')->first();
        $year = Year::where('year_active', 1)->get();
        $month = Month::where('month_active', 1)->get();

        if (empty($days["day_number"])) {
            $days["day_number"] = 0;
        }
        
        date_default_timezone_set('Asia/Tashkent');
		// date("h:i:sa:M-d-Y");
        // $d = strtotime("+1 day");
        // if (date("w", $d) != 6) {
        	// dd(date("w", $d));
        DB::insert('insert into days (day_number, day_name, year_id, month_id) values (?, ?, ?, ?)', [$days["day_number"]+1, 'Dushanba', $year[0]['id'], $month[0]['id']]);
        // } else {
        //     $startdate = strtotime("Monday");
        //     date("d", $startdate);
        // }
    }
    
    
    
    public function menustart(Request $request){
		$days = Day::orderBy('id', 'DESC')->first();
    	$chil_number = Temporary::join('age_ranges', 'temporaries.age_id', '=', 'age_ranges.id')->get();
    	
    	
    	foreach($chil_number as $child){
    		Number_children::create([
	    		'kingar_name_id' => $child->kingar_name_id,
	    		'day_id' => (int)$days['id'],
	    		'king_age_name_id' => $child->age_id,
	    		'kingar_children_number'=> $child->age_number,
	    		'kingar_menu_id'=> $request->menus,
			]);
			$path = "https://api.telegram.org/bot";
	    	$token = "5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k";
	    	$tday = $days['id'];
	    	$text = $child->age_name. "ли болалар учун менюни юклаб олинг.";
	    	$user = Kindgarden::where('id', '=', $child->kingar_name_id)->get();
	    	$this->curl_get_contents($path.$token.'/sendmessage?chat_id='.$user[0]['telegram_user_id']."&text=<a href='https://cj56359.tmweb.ru/downloadPDF/".$child->kingar_name_id."/".$tday."/".$child->age_id."'>".$text."</a>&parse_mode=HTML");
    	}
    	
    	$temp = Temporary::truncate();
    	$gr = Kindgarden::all();
    	
    	return view('kingardens', ['gardens' => $gr, 'day' => $days]);
    }
    
    public function showmenu(Request $request, $kid, $did, $aid){
    	$menu = Number_children::where([
    				['kingar_name_id', '=', $kid],
    				['day_id', '=', $did],
    				['king_age_name_id', '=', $aid]
    			])->get();
    			
    			// dd($menu [0]['kingar_menu_id']);
    	$menuitem = DB::table('menu_compositions')
    				->where('menu_compositions.one_day_menu_id', '=', $menu[0]['kingar_menu_id'])
    				->join('food_compositions', 'menu_compositions.menu_food_id', '=', 'food_compositions.food_name_id')
    				->where('food_compositions.age_name_id', '=', $menu[0]['king_age_name_id'])
    				->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
    				->join('food', 'food_compositions.food_name_id', '=', 'food.id')
    				->join('products', 'food_compositions.product_name_id', '=', 'products.id')
    				->orderBy('menu_meal_time_id')
    				->get();
    
    	return view('alltable', ['menu' => $menu, 'menuitem' => $menuitem]);
    }
    
	public function downloadPDF(Request $request, $kid, $did, $aid){
		$menu = Number_children::where([
			['kingar_name_id', '=', $kid],
			['day_id', '=', $did],
			['king_age_name_id', '=', $aid]
		])->get();
		
		// dd($menu [0]['kingar_menu_id']);
		$menuitem = DB::table('menu_compositions')
					->where('menu_compositions.one_day_menu_id', '=', $menu[0]['kingar_menu_id'])
					->join('food_compositions', 'menu_compositions.menu_food_id', '=', 'food_compositions.food_name_id')
					->where('food_compositions.age_name_id', '=', $menu[0]['king_age_name_id'])
					->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
					->join('food', 'food_compositions.food_name_id', '=', 'food.id')
					->join('products', 'food_compositions.product_name_id', '=', 'products.id')
					->orderBy('menu_meal_time_id')
					->get();
		$dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('alltable', ['menu' => $menu, 'menuitem' => $menuitem]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		
		$dompdf->setPaper('A4', 'landscape');
		
		$dompdf->render();
	
		$dompdf->stream('demo.pdf', ['Attachment'=>0]);
	}

    public function start(Request $request)
    {
    	$temp = Temporary::truncate();
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
    }
    
    public function addchilds(Request $request)
    {
    	Temporary::create([
    		'kingar_name_id' => $_GET['bogcha'],
    		'age_id' => $_GET['yoshi'],
    		'age_number' => $_GET['soni']
		]);
    }
    
    
    public function getstart(Request $request)
    {
    	
    	
    }
    
    public function buildInlineKeyBoard(array $options)
    {
        // собираем кнопки
        $replyMarkup = [
            'inline_keyboard' => $options,
        ];
        // преобразуем в JSON объект
        $encodedMarkup = json_encode($replyMarkup, true);
        // возвращаем клавиатуру
        return $encodedMarkup;
    }
    
    function curl_get_contents($url) {
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_HEADER, 0);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_URL, $url);
	    $data = curl_exec($ch);
	    curl_close($ch);
	    return $data;
	}
}
