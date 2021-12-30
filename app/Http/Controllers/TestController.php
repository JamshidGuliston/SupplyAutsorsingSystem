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
	public function __construct()
	{
		$this->middleware('auth');
	}
	function dash(Request $request)
	{
		return view('dash');
	}
	function index(Request $request)
	{
		$gr = Temporary::join('kindgardens', 'temporaries.kingar_name_id', '=', 'kindgardens.id')->orderBy('kingar_name_id')->get();
		$menu = One_day_menu::all();

		return view('adminhome', ['gardens' => $gr, 'menu' => $menu]);
	}

	public function tomany()
	{
		$kind = Kindgarden::find(2);
		$tags = [1, 3];
		$kind->age_range()->sync($tags);
		dd($kind->age_range);
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
		$d = strtotime("+1 day");
		// if (date("w", $d) != 6) {
		// dd(date("w", $d));
		DB::insert('insert into days (day_number, year_id, month_id) values (?, ?, ?)', [date('d', $d), $year[0]['id'], $month[0]['id']]);
		// } else {
		//     $startdate = strtotime("Monday");
		//     date("d", $startdate);
		// }
	}



	public function menustart(Request $request)
	{
		$days = Day::orderBy('id', 'DESC')->first();
		$chil_number = Temporary::all();

		foreach ($chil_number as $child) {
			Number_children::create([
				'kingar_name_id' => $child->kingar_name_id,
				'day_id' => (int)$days['day_number'],
				'king_age_name_id' => $child->age_id,
				'kingar_children_number' => $child->age_number,
				'kingar_menu_id' => $request->menus,
			]);
			$path = "https://api.telegram.org/bot";
			$token = "1843436308:AAE9-UuWjEeAuNkz_lwpuEEQSufTL_Yky9Y";
			$tday = $days['day_number'];
			$text = "Менюни юклаб олинг.";
			$buttons = '{"inline_keyboard":[[{"text":"1-Меню","url":"https://cj56359.tmweb.ru/showmenu/' . $child->kingar_name_id . '/' . $tday . '/1"}]]}';
			$user = Kindgarden::where('id', '=', $child->kingar_name_id)->get();
			$this->curl_get_contents($path . $token . '/sendmessage?chat_id=' . $user[0]['telegram_user_id'] . '&text=' . $text . '&reply_markup=' . $buttons);
		}

		$temp = Temporary::truncate();
		$gr = Kindgarden::all();

		return view('kingardens', ['gardens' => $gr, 'day' => $days]);
	}

	public function showmenu(Request $request, $kid, $did, $aid)
	{
		$menu = Number_children::where([
			['kingar_name_id', '=', $kid],
			['day_id', '=', $did],
			['king_age_name_id', '=', $aid]
		])->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')->get();
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
		$child = 1;
		$days = 4;
		$path = "https://api.telegram.org/bot";
		$token = "1843436308:AAE9-UuWjEeAuNkz_lwpuEEQSufTL_Yky9Y";
		$text = "Менюни юклаб олинг. 1-меню https://cj56359.tmweb.ru/showmenu/" . $child . "/" . (int)$days . "/1";
		$buttons = '{"inline_keyboard":[[{"text":"1-Меню","url":"https://cj56359.tmweb.ru/showmenu/' . $child . '/' . $days . '/1"}, {"text":"2-Меню","url":"https://cj56359.tmweb.ru/showmenu/2/4/2"}]]}';
		$user = Kindgarden::where('id', '=', 4)->get();
		$this->curl_get_contents($path . $token . '/sendmessage?chat_id=' . $user[0]['telegram_user_id'] . '&text=' . $text . '&reply_markup=' . $buttons);

		return view('alltable', ['menu' => $menu, 'menuitem' => $menuitem]);
	}

	public function downloadPDF(Request $request, $kid, $did, $aid)
	{
		$menu = Number_children::where([
			['day_id', '=', $did],
			['kingar_name_id', '=', $kid],
			['king_age_name_id', '=', $aid]
		])->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')->get();

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

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
	}

	public function start(Request $request)
	{
		$temp = Temporary::truncate();
		$users = Kindgarden::where('hide', 1)->get();
		$path = "https://api.telegram.org/bot";
		$token = "1843436308:AAE9-UuWjEeAuNkz_lwpuEEQSufTL_Yky9Y";
		$text = "Боғчангиз учун эртанги овқатлар менюсига болалар сонини критинг.| 3-4 yoshgacha = 0; | 4-7 yoshgacha = 0";
		$buttons = '{"inline_keyboard":[[{"text":"1","callback_data":"addnumber_1"}, {"text":"2","callback_data":"addnumber_2"}, {"text":"3","callback_data":"addnumber_3"}], [{"text":"4","callback_data":"addnumber_4"}, {"text":"5","callback_data":"addnumber_5"}, {"text":"6","callback_data":"addnumber_6"}], [{"text":"7","callback_data":"addnumber_7"}, {"text":"8","callback_data":"addnumber_8"}, {"text":"9","callback_data":"addnumber_9"}], [{"text":"4-7 yoshlilar","callback_data":"addnumber_@"}, {"text":"<","callback_data":"addnumber_<"}], [{"text":"Yuborish","callback_data":"addnumber_ok"}]]}';
		// dd($users);
		foreach ($users as $user) {
			Person::where('telegram_id', $user->telegram_user_id)->update(array('childs_count' => '0'));
			$this->curl_get_contents($path . $token . '/sendmessage?chat_id=' . $user->telegram_user_id . '&text=' . $text . '&reply_markup=' . $buttons);
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
