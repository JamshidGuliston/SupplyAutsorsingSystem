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
        $day = Day::join('months', 'months.id', '=', 'days.month_id')
				->join('years', 'years.id', '=', 'days.year_id')
				->orderBy('days.id', 'DESC')->first(['days.day_number','days.id as id', 'months.month_name', 'years.year_name']);
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
                }
            }
        }

        // Xodimlar uchun ovqat gramajlarini hisoblash
        $workerproducts = array_fill(1, 500, 0);
        foreach($workerfood as $tr){
            // Tushlikdagi birinchi ovqat va nondan yeyishadi
            if(isset($nextdaymenuitem[3][$tr->food_id])){
                foreach($nextdaymenuitem[3][$tr->food_id] as $key => $value){
                    if($key != 'foodname' and $key != 'foodweight'){
                        $workerproducts[$key] += $value; 
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        $productallcount[$key] += $value;
                    }
                }
            }
        }
        
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.alltable', ['day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
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
	public function nextdayshoppdf(Request $request, $id){
        $shop = Shop::where('id', $id)->with('kindgarden.region')->with('product')->first();
        // dd($shop);
        $nextday = Nextday_namber::all();

        $shopproducts = array();
        foreach($shop->kindgarden as $row){
            $shopproducts[$row->id]['name'] = $row->kingar_name;
            $shopproducts[$row->id]['region_name'] = $row->region ? $row->region->region_name : '';
            $shopproducts[$row->id]['region_id'] = $row->region_id;
            $day = Day::orderBy('id', 'DESC')->first();
            foreach($shop->product as $prod){
                $allsum = 0;
                $onesum = 0;
                $workers = 0;
                $weight = 0;
                foreach($nextday as $next){
                    if($row->id == $next->kingar_name_id){
                        $prlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)->where('age_range_id', $next->king_age_name_id)->where('product_name_id', $prod->id)->get();
                        foreach($prlar as $prw){
                            $weight += $prw->weight * $next->kingar_children_number;
                        }
                        
                        // Xodimlar uchun ovqat gramajlarini hisoblash
                        $workerfood = titlemenu_food::where('day_id', $day->id)
                                    ->where('worker_age_id', $next->king_age_name_id)
                                    ->where('titlemenu_id', $next->kingar_menu_id)
                                    ->get();
                        
                        foreach($workerfood as $tr){
                            // Tushlikdagi birinchi ovqat va nondan yeyishadi
                            $workerprlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                                            ->where('age_range_id', $next->king_age_name_id)
                                            ->where('menu_food_id', $tr->food_id)
                                            ->where('product_name_id', $prod->id)
                                            ->get();
                            
                            foreach($workerprlar as $wpr){
                                $weight += $wpr->weight * $next->workers_count;
                            }
                        }
                    }
                }

                $prdiv = Product::where('id', $prod->id)->first();
                
                $shopproducts[$row->id][$prod->id] = $weight / $prdiv->div; 
            }
        }
        
        // Muassasa nomlarini region nomi va raqamiga qarab saralash
        uasort($shopproducts, function($a, $b) {
            // Avval region nomiga qarab saralash
            if ($a['region_name'] !== $b['region_name']) {
                return strcmp($a['region_name'], $b['region_name']);
            }
            
            // Region nomi bir xil bo'lsa, muassasa nomidagi raqamga qarab saralash
            $a_number = preg_replace('/[^0-9]/', '', $a['name']);
            $b_number = preg_replace('/[^0-9]/', '', $b['name']);
            
            if ($a_number && $b_number) {
                return intval($a_number) - intval($b_number);
            }
            
            // Raqam topilmasa, to'liq nomga qarab saralash
            return strcmp($a['name'], $b['name']);
        });

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
