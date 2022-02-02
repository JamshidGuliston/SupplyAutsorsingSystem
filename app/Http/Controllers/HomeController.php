<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


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
use App\Models\Nextday_namber;
use App\Models\order_product_structure;
use App\Models\Product;
use App\Models\Product_category;
use App\Models\Season;
use App\Models\Shop;
use App\Models\Size;
use App\Models\titlemenu_food;
use Dompdf\Dompdf;
use TCG\Voyager\Models\Category;


class HomeController extends Controller
{
	
	public function nextdaymenuPDF(Request $request, $gid, $ageid)
    {
        $menu = Nextday_namber::where([
			['kingar_name_id', '=', $gid],
			['king_age_name_id', '=', $ageid]
		])->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
        ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();
		// dd($menu);
		$menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                        ->where('age_range_id', $ageid)
                        ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
                        ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
                        ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
                        ->orderBy('menu_meal_time_id')
                        ->get();
        // dd($menuitem);
        // xodimlar ovqati uchun
        $day = Day::orderBy('id', 'DESC')->first();
        $workerfood = titlemenu_food::where('day_id', $day->id)
                    ->where('worker_age_id', $ageid)
                    ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                    ->get();
        // dd($workerfood);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('alltable', ['menu' => $menu, 'menuitem' => $menuitem, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
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
    
	
	
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
<<<<<<< HEAD
	public function nextdayshoppdf(Request $request, $id){
        $shop = Shop::where('id', $id)->with('kindgarden')->with('product')->first();
        // dd($shop);
        $nextday = Nextday_namber::all();

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
                
                $shopproducts[$row->id][$prod->id] = ($allsum + $woe * $workers) / $prdiv->div; 
            }
        }

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('technolog.nextdayshoppdf', compact('shopproducts', 'shop')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
=======
>>>>>>> 45e1bd914ad05289d8a0da2c018f246a98f3abf0

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }
    
    public function send(){
    	
    	$send = "<a href='http://cj56359.tmweb.ru/technolog/nextdaymenuPDF/2/3'>ddd</a>";
    	
    	file_get_contents('https://api.telegram.org/bot5064211282:AAH8CZUdU5i2Vl-4WB3PF4Kll6KoCzgHk8k/sendMessage?chat_id=640892021$text='.$send.'&parse_mode=html');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
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
