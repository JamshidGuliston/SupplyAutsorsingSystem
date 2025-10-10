<?php

namespace App\Http\Controllers;

use App\Models\Active_menu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Traits\MakeComponents;
use App\Traits\RequestTrait;
use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Region;
use Illuminate\Support\Facades\DB;
use App\Models\Day;
use App\Models\debts;
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
use App\Models\Take_small_base;
use App\Models\Product;
use App\Models\Product_category;
use App\Models\Protsent;
use App\Models\Groupweight;
use App\Models\Weightproduct;
use App\Models\Season;
use App\Models\Shop;
use App\Models\Shop_product;
use App\Models\Size;
use App\Models\titlemenu_food;
use PhpParser\Node\Stmt\Foreach_;
use TCG\Voyager\Models\Category;
use Dompdf\Dompdf; 

class TestController extends Controller
{

	public function nextdaymenuPDF(Request $request, $gid, $ageid)
	{
		$menu = Nextday_namber::where([
			['kingar_name_id', '=', $gid],
			['king_age_name_id', '=', $ageid]
		])
		->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
        ->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();
		$taomnoma = Titlemenu::where('id', $menu[0]['kingar_menu_id'])->first();
		
		$products = Product::where('hide', 1)
		    ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
			->orderBy('sort', 'ASC')->get(['products.*', 'sizes.size_name']);
		
		$menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                        ->where('age_range_id', $ageid)
                        ->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
                        ->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
                        ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
                        ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                        ->orderBy('menu_meal_time_id')
                        ->get();

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
		$costs = bycosts::where('day_id', bycosts::where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->first()->day_id)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->get();
		$narx = [];
		foreach($costs as $row){
			if(!isset($narx[$row->praduct_name_id])){
				$narx[$row->praduct_name_id] = $row->price_cost;
			}
		}
        $nextdaymenuitem = [];
        $workerproducts = [];
        // kamchilik bor boshlangich qiymat berishda
        $productallcount = array_fill(1, 500, 0);
		// dd($menuitem);
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
                        // $productallcount[$key] += $value;
                    }
                }
            }
        }

		// oy va yilni o'zgartirish
		$today = new \DateTime();
		$nextWorkDay = clone $today;
		$nextWorkDay->modify('+1 day');

		// dam olish kunlari: 6 = shanba, 7 = yakshanba
		while (in_array($nextWorkDay->format('N'), [6, 7])) {
			$nextWorkDay->modify('+1 day');
		}

		$day->day_number = $nextWorkDay->format('d');
		$day->month_name = $nextWorkDay->format('F');
		$day->year_name = $nextWorkDay->format('Y');

		// Snappy bilan PDF yaratish
		try {
			$pdf = \PDF::loadView('pdffile.technolog.alltable', [
				'narx' => $narx,
				'day' => $day,
				'productallcount' => $productallcount,
				'workerproducts' => $workerproducts,
				'menu' => $menu,
				'menuitem' => $nextdaymenuitem,
				'products' => $products,
				'workerfood' => $workerfood,
				'taomnoma' => $taomnoma
			]);

			$pdf->setPaper('A4', 'landscape')
				->setOptions([
					'encoding' => 'UTF-8',
					'enable-javascript' => true,
					'javascript-delay' => 1000,
					'enable-smart-shrinking' => true,
					'no-stop-slow-scripts' => true,
					'disable-smart-shrinking' => false,
					'print-media-type' => true,
					'dpi' => 300,
					'image-quality' => 100,
					'margin-top' => 10,
					'margin-right' => 10,
					'margin-bottom' => 10,
					'margin-left' => 10,
					'enable-local-file-access' => true,
					'load-error-handling' => 'ignore',
					'load-media-error-handling' => 'ignore',
				]);

			$name = $day['id'].'-'.$gid.'-'.$ageid."taxminiy.pdf";

			return $pdf->stream($name, ['Attachment' => 0]);
		} catch (\Exception $e) {
			// Snappy ishlamasa, DomPDF ishlatish
			$dompdf = new Dompdf('UTF-8');
			$html = mb_convert_encoding(view('pdffile.technolog.alltable', [
				'narx' => $narx,
				'day' => $day,
				'productallcount' => $productallcount,
				'workerproducts' => $workerproducts,
				'menu' => $menu,
				'menuitem' => $nextdaymenuitem,
				'products' => $products,
				'workerfood' => $workerfood,
				'taomnoma' => $taomnoma
			]), 'HTML-ENTITIES', 'UTF-8');
			
			$dompdf->loadHtml($html);
			$dompdf->setPaper('A4', 'landscape');
			$name = $day['id'].'-'.$gid.'-'.$ageid."taxminiy.pdf";
			$dompdf->render();
			
			return $dompdf->stream($name, ['Attachment' => 0]);
		}
	}

	public function activmenuPDF(Request $request, $today, $gid, $ageid)
	{
		$menu = Number_children::where([
			['kingar_name_id', '=', $gid],
			['day_id', '=', $today],
			['king_age_name_id', '=', $ageid]
		])->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')
		->join('titlemenus', 'number_childrens.kingar_menu_id', '=', 'titlemenus.id')
        ->join('age_ranges', 'number_childrens.king_age_name_id', '=', 'age_ranges.id')->get();
		// dd($menu);  
		$products = Product::where('hide', 1)
			->orderBy('sort', 'ASC')->get();
		
		$menuitem = Active_menu::where('day_id', $today)
						->where('title_menu_id', $menu[0]['kingar_menu_id'])
                        ->where('age_range_id', $ageid)
                        ->join('meal_times', 'active_menus.menu_meal_time_id', '=', 'meal_times.id')
                        ->join('food', 'active_menus.menu_food_id', '=', 'food.id')
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->orderBy('menu_meal_time_id')
						->orderBy('menu_food_id')
                        ->get();	
		
        $day = Day::where('days.id', $today)
			->join('months', 'months.id', '=', 'days.month_id')
			->join('years', 'years.id', '=', 'days.year_id')
			->orderBy('days.id', 'DESC')
			->first(['days.day_number','days.id as id', 'months.month_name', 'months.id as month_id', 'years.year_name']);
        // dd($day);
        $workerfood = titlemenu_food::where('day_id', ($today-1))
                    ->where('worker_age_id', $ageid)
                    ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                    ->get();


		if($day->month_id % 12 == 0){
			$month_id = 12;
		}else{
			$month_id = $day->month_id % 12;
		}
        $protsent = Protsent::where('region_id', Kindgarden::where('id', $gid)->first()->region_id)
		                    ->where('start_date', '<=', $day->year_name.'-'.$month_id.'-'.$day->day_number)
							->where('end_date', '>=', $day->year_name.'-'.$month_id.'-'.$day->day_number)
		                    ->where('age_range_id', $ageid)->first();
		if(!$protsent){
			$protsent = new Protsent();
			// age_range_id 3 va 4 uchun
			$protsent->where('age_range_id', 3)->eater_cost = 0;
			$protsent->where('age_range_id', 4)->eater_cost = 0;
		}
		
        $nextdaymenuitem = [];
        $workerproducts = [];
        $productallcount = array_fill(1, 500, 0);
        foreach($menuitem as $item){
            $nextdaymenuitem[$item->menu_meal_time_id][0]['mealtime'] = $item->meal_time_name; 
            $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
            $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name; 
            $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodweight'] = $item->food_weight; 
            $productallcount[$item->product_name_id] += $item->weight;
            for($i = 0; $i<count($products); $i++){
                if(empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id){
                    $products[$i]['yes'] = 1;
                }
            }
        }
        $workerproducts = array_fill(1, 500, 0);
        foreach($workerfood as $tr){
            // Tushlikdagi birinchi ovqat va nondan yeyishadi
            if(isset($nextdaymenuitem[3][$tr->food_id])){
                foreach($nextdaymenuitem[3][$tr->food_id] as $key => $value){
                    if($key != 'foodname' and $key != 'foodweight'){
                        $workerproducts[$key] += $value; 
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        // $productallcount[$key] += $value;
                    }
                }
            }
        }
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.activmenu', ['protsent' => $protsent,'day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'landscape');
		$name = $day['id'].$ageid."activemenu.pdf";
		$dompdf->render();
		$dompdf->stream($name, ['Attachment' => 0]);
	}
	
	public function nextnakladnoyPDF($kid){
		$king = Kindgarden::where('id', $kid)->first();
		$join = Nextday_namber::where('kingar_name_id', $kid)
				->leftjoin('menu_compositions', function($join){
                    $join->on('nextday_nambers.kingar_menu_id', '=', 'menu_compositions.title_menu_id');
                    $join->on('nextday_nambers.king_age_name_id', '=', 'menu_compositions.age_range_id');
                })
                ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
				->get();
		// dd($join);
		$ages = Age_range::all();
		$agerange = array();
		foreach($ages as $row){
			$agerange[$row->id] = 0;
		}
		$endday = Day::orderBy('id', 'DESC')->first();
		$productscount = array_fill(1, 500, $agerange);
		$workproduct = array_fill(1, 500, 0);
		$workerfood = titlemenu_food::where('titlemenu_foods.day_id', $endday->id)->get();
		// dd($productscount);
		foreach($join as $row){
			if($row->age_range_id == 1 and $row->menu_meal_time_id = 3){
				foreach($workerfood as $ww){
					if($row->menu_food_id == $ww->food_id){
						$workproduct[$row->product_name_id] += $row->weight;
						$workproduct[$row->product_name_id.'div'] = $row->div;
						$workproduct[$row->product_name_id.'wcount'] = $row->workers_count;
					}
				}
			}
			$productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
			$productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
			$productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
			$productscount[$row->product_name_id]['product_name'] = $row->product_name;
		}
		// dd($productscount);
		
		$dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('docnextday.nakladnoy', ['workproduct' => $workproduct, 'productscount' => $productscount, 'king' => $king, 'ages' => $ages]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $endday['id'].$kid."nextnaklad.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($name, ['Attachment' => 0]);
	}
	
	public function nextdaysomenakladnoyPDF($kid){
		$king = Kindgarden::where('id', $kid)->first();
		$join = Nextday_namber::where('kingar_name_id', $kid)
				->leftjoin('menu_compositions', function($join){
                    $join->on('nextday_nambers.kingar_menu_id', '=', 'menu_compositions.title_menu_id');
                    $join->on('nextday_nambers.king_age_name_id', '=', 'menu_compositions.age_range_id');
                })
                ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
				->get();
		// dd($join);
		$ages = Age_range::all();
		$agerange = array();
		foreach($ages as $row){
			$agerange[$row->id] = 0;
		}
		$endday = Day::orderBy('id', 'DESC')->join('months', 'months.id', '=', 'days.month_id')
        	->join('years', 'years.id', '=', 'days.year_id')
        	->first(['days.id', 'days.day_number', 'months.id as MID', 'months.month_name', 'years.year_name']);
		// $productscount = array_fill(1, 500, $agerange);
		// $workproduct = array_fill(1, 500, 0);
		$workerfood = titlemenu_food::where('titlemenu_foods.day_id', $endday->id)->get();
		// dd($productscount);
		$workproduct = [];
		foreach($join as $row){
			$h = Product::where('id', $row->product_name_id)->with('shop')->first();
			if(empty($productscount[$row->product_name_id])){
				$productscount[$row->product_name_id][0] = 0;
				$productscount[$row->product_name_id][1] = 0;
				$productscount[$row->product_name_id][2] = 0;
				$productscount[$row->product_name_id][3] = 0;
				$productscount[$row->product_name_id][4] = 0;
				$productscount[$row->product_name_id][5] = 0;
				$productscount[$row->product_name_id][6] = 0;
			}
			if($row->age_range_id == 1 and $row->menu_meal_time_id = 3){
				foreach($workerfood as $ww){
					if($row->menu_food_id == $ww->food_id){
						if(empty($workproduct[$row->product_name_id])){
							$workproduct[$row->product_name_id][1] = 0;
							$workproduct[$row->product_name_id][2] = 0;
							$workproduct[$row->product_name_id][3] = 0;
							$workproduct[$row->product_name_id][4] = 0;
							$workproduct[$row->product_name_id][5] = 0;
							$workproduct[$row->product_name_id][6] = 0;
						}
						if($h->shop->count()>=1){
							$workproduct[$row->product_name_id] += $row->weight;
							$workproduct[$row->product_name_id.'div'] = $row->div;
							$workproduct[$row->product_name_id.'wcount'] = $row->workers_count;
						}
					}
				}
			}
			if($h->shop->count()>=1){
				$productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
				$productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
				$productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
				$productscount[$row->product_name_id]['product_name'] = $row->product_name;
			}
		}
		// dd($workproduct);
		
		$dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('docnextday.somenakladnoy', ['workproduct' => $workproduct, 'productscount' => $productscount, 'king' => $king, 'ages' => $ages, 'day' => $endday]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $endday['id'].$kid."nextnaklad.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		if (auth()->user()->id < 6){
			$dompdf->stream($name, ['Attachment' => 0]);
		}
		else{
			$dompdf->stream($name);	
		}
	}

	public function activnakladPDF(Request $request, $today, $gid)
	{
		$king = Kindgarden::where('id', $gid)->first();
		$join = Number_children::where('number_childrens.day_id', $today)
				->where('kingar_name_id', $gid)
				->leftjoin('active_menus', function($join){
                    // $join->on('day_id', '=', $today);
                    $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                    $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                })
				->where('active_menus.day_id', $today)
                ->join('products', 'active_menus.product_name_id', '=', 'products.id')
				->get();
		// dd($join);	
		$ages = Age_range::all();
		$agerange = array();
		foreach($ages as $row){
			$agerange[$row->id] = 0;
		}
		$productscount = array_fill(1, 500, $agerange);
		$workproduct = array_fill(1, 500, 0);
		$workerfood = titlemenu_food::where('titlemenu_foods.day_id', ($today-1))->get();
		// dd($workerfood);
		foreach($join as $row){
			if($row->age_range_id == 4 and $row->menu_meal_time_id = 3){
				foreach($workerfood as $ww){
					if($row->menu_food_id == $ww->food_id){
						$workproduct[$row->product_name_id] += $row->weight;
						$workproduct[$row->product_name_id.'div'] = $row->div;
						$workproduct[$row->product_name_id.'wcount'] = $row->workers_count;
					}
				}
			}
			$productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
			$productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
			$productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
			$productscount[$row->product_name_id]['product_name'] = $row->product_name;
		}
		// dd($workproduct);
		$bool = minus_multi_storage::where('day_id', $today)->where('kingarden_name_id', $gid)->get();
		// dd($bool);
        if($bool->count() == 0){
			// dd(1);
			foreach($productscount as $key => $row){
				if(isset($row['product_name'])){
					$summ = 0;
					foreach($ages as $age){
						if(isset($row[$age['id'].'-children'])){
							$summ += ($row[$age['id']]*$row[$age['id'].'-children']) / $row[$age['id'].'div'];
						}
					}
					if(isset($workproduct[$key.'wcount'])){
						$summ += ($workproduct[$key]*$workproduct[$key.'wcount']) / $workproduct[$key.'div'];
					}
					// dd($key, $summ);
					minus_multi_storage::create([
						'day_id' => $today,
						'kingarden_name_id' => $gid,
						'kingar_menu_id' => 0,
						'product_name_id' => $key,
						'product_weight' => $summ,
					]);
				}
			}
		}
		$dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('docnextday.nakladnoy', ['workproduct' => $workproduct, 'productscount' => $productscount, 'king' => $king, 'ages' => $ages]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $today.$gid."activnaklad.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($name, ['Attachment' => 0]);
	}

	public function createnextdaypdf(Request $request)
	{
		date_default_timezone_set('Asia/Tashkent');
    	$d = strtotime("-10 hours");
		$nextday = Nextday_namber::all();
		// dd($nextday);
		foreach($nextday as $row){
			$this->nextnakladnoycreatePDF($row->kingar_name_id);
			$this->nextdaycreatemenuPDF($row->kingar_name_id, $row->king_age_name_id);
		}
		return redirect()->route('technolog.sendmenu', ['day' => date("d-F-Y", $d)]);
	}
	
	public function createnewdaypdf(Request $request, $dayid)
	{
		date_default_timezone_set('Asia/Tashkent');
    	$d = strtotime("-10 hours");
		$newday = Number_children::where('day_id', $dayid)->get();
		// dd($newday);
		foreach($newday as $row){
			$this->activnakladcreatePDF($dayid, $row->kingar_name_id);
			$this->activcreatemenuPDF($dayid, $row->kingar_name_id, $row->king_age_name_id);
		}
		return redirect()->route('technolog.sendmenu', ['day' => $dayid]);
	}
	
	public function nextdaycreatemenuPDF($gid, $ageid)
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
        // Xodimlar uchun ovqat gramajlarini hisoblash
        $workerproducts = array_fill(1, 500, 0);
        foreach($workerfood as $tr){
            // Tushlikdagi birinchi ovqat va nondan yeyishadi
            if(isset($nextdaymenuitem[3][$tr->food_id])){
                foreach($nextdaymenuitem[3][$tr->food_id] as $key => $value){
                    if($key != 'foodname'){
                        $workerproducts[$key] += $value; 
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        $productallcount[$key] += $value;
                    }
                }
            }
        }
        // dd($workerproducts);    
        
        // dd($workerfood);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.alltable', ['day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $day['id'].'-'.$gid.'-'.$ageid."-nextmenu.pdf";
		// Render the HTML as PDF
		$dompdf->render();
		
		file_put_contents("pdf/".$name, $dompdf->output());
	}

	public function activcreatemenuPDF($today, $gid, $ageid)
	{
		$menu = Number_children::where([
			['kingar_name_id', '=', $gid],
			['day_id', '=', $today],
			['king_age_name_id', '=', $ageid]
		])->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')
        ->join('age_ranges', 'number_childrens.king_age_name_id', '=', 'age_ranges.id')->get();
		// dd($menu);  
		$products = Product::where('hide', 1)
			->orderBy('sort', 'ASC')->get();
		
		$menuitem = Active_menu::where('day_id', $today)
						->where('title_menu_id', $menu[0]['kingar_menu_id'])
                        ->where('age_range_id', $ageid)
                        ->join('meal_times', 'active_menus.menu_meal_time_id', '=', 'meal_times.id')
                        ->join('food', 'active_menus.menu_food_id', '=', 'food.id')
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->orderBy('menu_meal_time_id')
						->orderBy('menu_food_id')
                        ->get();	

        // dd($menuitem);
        // xodimlar ovqati uchun
        $day = Day::where('days.id', $today)->join('months', 'months.id', '=', 'days.month_id')->orderBy('days.id', 'DESC')->first(['days.day_number','days.id as id', 'months.month_name']);
        // dd($day);
        $workerfood = titlemenu_food::where('day_id', ($today-1))
                    ->where('worker_age_id', $ageid)
                    ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                    ->get();
        // dd($workerfood);
        $nextdaymenuitem = [];
        $workerproducts = [];
        // kamchilik bor boshlangich qiymat berishda
        $productallcount = array_fill(1, 500, 0);

		$costs = bycosts::where('day_id', bycosts::where('day_id', '<=', $today)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->first()->day_id)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->get();
		$narx = [];
		foreach($costs as $row){
			if(!isset($narx[$row->praduct_name_id])){
				$narx[$row->praduct_name_id] = $row->price_cost;
			}
		}
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
        // Xodimlar uchun ovqat gramajlarini hisoblash
        $workerproducts = array_fill(1, 500, 0);
        foreach($workerfood as $tr){
            // Tushlikdagi birinchi ovqat va nondan yeyishadi
            if(isset($nextdaymenuitem[3][$tr->food_id])){
                foreach($nextdaymenuitem[3][$tr->food_id] as $key => $value){
                    if($key != 'foodname'){
                        $workerproducts[$key] += $value; 
                        // Xodimlar gramajini ham productallcount ga qo'shish
                        $productallcount[$key] += $value;
                    }
                }
            }
        }
        // dd($menuitem);
        
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.activmenu', ['narx' => $narx, 'day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $day['id'].'-'.$gid.'-'.$ageid."-activemenu.pdf";
		// Render the HTML as PDF
		$dompdf->render();
		
		file_put_contents("pdf/".$name, $dompdf->output());
	}
	
	public function activnakladcreatePDF($today, $gid)
	{
		$king = Kindgarden::where('id', $gid)->first();
		$join = Number_children::where('number_childrens.day_id', $today)
				->where('kingar_name_id', $gid)
				->leftjoin('active_menus', function($join){
                    // $join->on('day_id', '=', $today);
                    $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                    $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                })
				->where('active_menus.day_id', $today)
                ->join('products', 'active_menus.product_name_id', '=', 'products.id')
				->get();
		// dd($join);	
		$ages = Age_range::all();
		$agerange = array();
		foreach($ages as $row){
			$agerange[$row->id] = 0;
		}
		$productscount = array_fill(1, 500, $agerange);
		$workproduct = array_fill(1, 500, 0);
		$workerfood = titlemenu_food::where('titlemenu_foods.day_id', ($today-1))->get();
		// dd($workerfood);
		foreach($join as $row){
			if($row->age_range_id == 1 and $row->menu_meal_time_id = 3){
				foreach($workerfood as $ww){
					if($row->menu_food_id == $ww->food_id){
						$workproduct[$row->product_name_id] += $row->weight;
						$workproduct[$row->product_name_id.'div'] = $row->div;
						$workproduct[$row->product_name_id.'wcount'] = $row->workers_count;
					}
				}
			}
			$productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
			$productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
			$productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
			$productscount[$row->product_name_id]['product_name'] = $row->product_name;
		}
		// dd($workproduct);
		
		$dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('docnextday.nakladnoy', ['workproduct' => $workproduct, 'productscount' => $productscount, 'king' => $king, 'ages' => $ages]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $today.'-'.$gid."-activnaklad.pdf";
		// Render the HTML as PDF
		$dompdf->render();
		
		file_put_contents("pdf/".$name, $dompdf->output());

	}
	
	public function nextnakladnoycreatePDF($kid){
		$king = Kindgarden::where('id', $kid)->first();
		$join = Nextday_namber::where('kingar_name_id', $kid)
				->leftjoin('menu_compositions', function($join){
                    $join->on('nextday_nambers.kingar_menu_id', '=', 'menu_compositions.title_menu_id');
                    $join->on('nextday_nambers.king_age_name_id', '=', 'menu_compositions.age_range_id');
                })
                ->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
				->get();
		// dd($join);
		$ages = Age_range::all();
		$agerange = array();
		foreach($ages as $row){
			$agerange[$row->id] = 0;
		}
		$endday = Day::orderBy('id', 'DESC')->first();
		$productscount = array_fill(1, 500, $agerange);
		$workproduct = array_fill(1, 500, 0);
		$workerfood = titlemenu_food::where('titlemenu_foods.day_id', $endday->id)->get();
		// dd($productscount);
		foreach($join as $row){
			if($row->age_range_id == 1 and $row->menu_meal_time_id = 3){
				foreach($workerfood as $ww){
					if($row->menu_food_id == $ww->food_id){
						$workproduct[$row->product_name_id] += $row->weight;
						$workproduct[$row->product_name_id.'div'] = $row->div;
						$workproduct[$row->product_name_id.'wcount'] = $row->workers_count;
					}
				}
			}
			$productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
			$productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
			$productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
			$productscount[$row->product_name_id]['product_name'] = $row->product_name;
		}
		// dd($productscount);
		
		$dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('docnextday.nakladnoy', ['workproduct' => $workproduct, 'productscount' => $productscount, 'king' => $king, 'ages' => $ages]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $endday['id'].'-'.$kid."-nextnaklad.pdf";
		// Render the HTML as PDF
		$dompdf->render();
		
		file_put_contents("pdf/".$name, $dompdf->output());
	}
	// pdf shoplarga
	public function nextdayshoppdf(Request $request, $id){
        $shop = Shop::where('id', $id)->with('kindgarden.region')->with('product')->first();
        
        $shopproducts = array();
        $regions = []; // Regionlar ro'yxati
        
        foreach($shop->kindgarden as $row){
            $shopproducts[$row->id]['name'] = $row->kingar_name;
            $shopproducts[$row->id]['region_name'] = $row->region ? $row->region->region_name : '';
            $shopproducts[$row->id]['region_id'] = $row->region_id;
            
            // Regionni ro'yxatga qo'shish
            if (!in_array($row->region_id, $regions)) {
                $regions[] = $row->region_id;
            }
            
            $day = Day::orderBy('id', 'DESC')->first();
            foreach($shop->product as $prod){
                $shopproducts[$row->id][$prod->id] = "";
                $allsum = 0;
                $onesum = 0;
                $workers = 0;
                $weight = 0;
                $itempr = "";
                
                $nextday = Nextday_namber::orderBy('kingar_name_id', 'ASC')->orderBy('king_age_name_id', 'ASC')->get();
                
                foreach($nextday as $next){
                    if($row->id == $next->kingar_name_id){
                        $prlar = Menu_composition::where('title_menu_id', $next->kingar_menu_id)
                            ->where('age_range_id', $next->king_age_name_id)
                            ->where('product_name_id', $prod->id)->get();
                        
                        foreach($prlar as $prw){
                            $itempr = $itempr . "+".$prw->weight." * ". $next->kingar_children_number;
                            $weight += $prw->weight * $next->kingar_children_number;
                        }
                        
                        // Xodimlar uchun ovqat gramajlarini hisoblash
                        $workerfood = titlemenu_food::where('day_id', $day->id)
                                    ->where('worker_age_id', $next->king_age_name_id)
                                    ->where('titlemenu_id', $next->kingar_menu_id)
                                    ->get();
                        
                        foreach($workerfood as $tr){
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
                $shopproducts[$row->id][$prod->id] = $weight / $prod->div; 
            }
        }
        
        // Muassasa nomlarini region nomi va raqamiga qarab saralash
        uasort($shopproducts, function($a, $b) {
            if ($a['region_name'] !== $b['region_name']) {
                return strcmp($a['region_name'], $b['region_name']);
            }
            
            $a_number = preg_replace('/[^0-9]/', '', $a['name']);
            $b_number = preg_replace('/[^0-9]/', '', $b['name']);
            
            if ($a_number && $b_number) {
                return intval($a_number) - intval($b_number);
            }
            
            return strcmp($a['name'], $b['name']);
        });
        
        $day = Day::join('months', 'days.month_id', '=', 'months.id')->orderBy('days.id', 'DESC')->first();
        
        // Regionlar bo'yicha guruhlash
        $groupedByRegions = [];
        foreach($shopproducts as $kindergartenId => $kindergartenData) {
            $regionId = $kindergartenData['region_id'];
            if (!isset($groupedByRegions[$regionId])) {
                $groupedByRegions[$regionId] = [
                    'region_name' => $kindergartenData['region_name'],
                    'kindergartens' => []
                ];
            }
            $groupedByRegions[$regionId]['kindergartens'][$kindergartenId] = $kindergartenData;
        }

        $dompdf = new Dompdf('UTF-8');
        $html = mb_convert_encoding(view('technolog.nextdayshoppdf', compact('groupedByRegions', 'shop', 'day')), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4');
        
        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }
	// TAXMINIY ikkinchi menyu
	public function nextdaysecondmenuPDF(Request $request, $gid){
		$products = Product::where('hide', 1)
			->orderBy('sort', 'ASC')->get();
		$nextdaymenuitem = [];
		$workerproducts = [];
		// kamchilik bor boshlangich qiymat berishda
		$workerproducts = array_fill(1, 500, 0);
		$productallcount = array_fill(1, 500, 0);
		$menuage = [];
		$ages = Age_range::all();
		foreach($ages as $age){
			$menu = Nextday_namber::where([
				['kingar_name_id', '=', $gid],
				['king_age_name_id', '=', $age->id]
			])->join('kindgardens', 'nextday_nambers.kingar_name_id', '=', 'kindgardens.id')
			->join('age_ranges', 'nextday_nambers.king_age_name_id', '=', 'age_ranges.id')->get();

			if($menu->count()>0)
				array_push($menuage, $menu);

			if(count($menu) == 0){
				continue;
			}
			
			$menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
				->where('age_range_id', $age->id)
				->join('meal_times', 'menu_compositions.menu_meal_time_id', '=', 'meal_times.id')
				->join('food', 'menu_compositions.menu_food_id', '=', 'food.id')
				->join('products', 'menu_compositions.product_name_id', '=', 'products.id')
				->orderBy('menu_meal_time_id')
				->get();

			// dd($menuitem);
			// xodimlar ovqati uchun
			$day = Day::join('months', 'months.id', '=', 'days.month_id')->orderBy('days.id', 'DESC')->first(['days.day_number','days.id as id', 'months.month_name']);
			// dd($day);
			$workerfood = titlemenu_food::where('day_id', ($day->id))
						->where('worker_age_id', $age->id)
						->where('titlemenu_id', $menu[0]['kingar_menu_id'])
						->get();
			// dd($workerfood);
			
			foreach($menuitem as $item){
				if(empty($nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['product'][$item->product_name_id])){
					$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['product'][$item->product_name_id] = 0;
				}
				// $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$age->id][$item->product_name_id]['one'] = $item->weight;
				// $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$age->id][$item->product_name_id] = array('allcount' => $item->weight * $menu[0]['kingar_children_number']);
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$age->id]['age_name'] = $menu[0]['age_name'];
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name; 
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodweight'] = $item->food_weight; 
				$nextdaymenuitem[$item->menu_meal_time_id]['mealtime'] = $item->meal_time_name; 
				$productallcount[$item->product_name_id] += ($item->weight * $menu[0]['kingar_children_number']) / $item->div;

				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['product'][$item->product_name_id] += ($item->weight * $menu[0]['kingar_children_number']) / $item->div;
				
				for($i = 0; $i<count($products); $i++){
					if(empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id){
						$products[$i]['yes'] = 1;
					}
				}
			}
			// dd($nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]);
			if($age->id == 1){
				foreach($workerfood as $tr){
					foreach($nextdaymenuitem[3][$tr->food_id][1] as $key => $value){
						if($key != 'age_name'){
							$workerproducts[$key] += $value['one'];
						} 
						// array_push($workerproducts, $nextdaymenuitem[3][$tr->food_id]);
					}
				}
			}
		}

		foreach($nextdaymenuitem as $key => $item){
			$nextdaymenuitem[$key]['rows'] = count($item)-1;
			foreach($item as $rkey => $row){
				if($rkey == 'mealtime'){
					continue;
				}
				$nextdaymenuitem[$key]['rows'] += count($row)-3;
			}
		}
		// dd($nextdaymenuitem);
        
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.nextsecondmenu', ['day' => $day, 'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menuage, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $day['id']."taxminiymenu.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($name);
	}
	// temp
	
    public function tempclear(){
        Temporary::truncate();
    }
    // ikkinchi menyu 
	public function activsecondmenuPDF(Request $request, $today, $gid){ 
		//dd($today);
		$products = Product::orderBy('sort', 'ASC')->get();
		$nextdaymenuitem = [];
		$workerproducts = [];
		$region_id = Kindgarden::where('id', $gid)->first()->region_id;
		$ages = Kindgarden::where('id', $gid)->with('age_range')->first();
		// dd($ages);
		// kamchilik bor boshlangich qiymat berishda
		$foundday = bycosts::where('day_id', '<=', $today)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->first();
		// dd($foundday);
		$narx = array_fill(1, 500, 0);
		if(empty($foundday)){
			$costs = [];
		}else{
			$costs = bycosts::where('day_id', $foundday->day_id)->where('region_name_id', $region_id)->orderBy('day_id', 'DESC')->get();
		}
		foreach($costs as $row){
			$narx[$row->praduct_name_id] = $row->price_cost;
		}
		$workerproducts = array_fill(1, 500, 0);
		$productallcount = array_fill(1, 500, 0);
		$menuage = [];
		$ages = Age_range::all();
		foreach($ages as $age){
			$allproductagesumm[$age->id] = array_fill(1, 500, 0);
		}
		foreach($ages as $age){
			$menu = Number_children::where([
				['kingar_name_id', '=', $gid],
				['day_id', '=', $today],
				['king_age_name_id', '=', $age->id]
				])
				->join('kindgardens', 'number_childrens.kingar_name_id', '=', 'kindgardens.id')
				->join('age_ranges', 'number_childrens.king_age_name_id', '=', 'age_ranges.id')->get();
			
			if($menu->count()>0)
				array_push($menuage, $menu);

			if(count($menu) == 0){
				continue;
			}
			// echo $age->id;
			$menuitem = Active_menu::where('day_id', $today)
							->where('title_menu_id', $menu[0]['kingar_menu_id'])
							->where('age_range_id', $age->id)
							->join('meal_times', 'active_menus.menu_meal_time_id', '=', 'meal_times.id')
							->join('food', 'active_menus.menu_food_id', '=', 'food.id')
							->join('products', 'active_menus.product_name_id', '=', 'products.id')
							->orderBy('menu_meal_time_id')
							->orderBy('menu_food_id')
							->get();	

			
			// xodimlar ovqati uchun
			$day = Day::where('days.id', $today)
				->join('months', 'months.id', '=', 'days.month_id')
				->join('years', 'years.id', '=', 'days.year_id')
				->orderBy('days.id', 'DESC')
				->first(['days.day_number','days.id as id', 'months.month_name', 'months.id as month_id', 'years.year_name']);
			// dd($day);
			$workerfood = titlemenu_food::where('day_id', ($today-1))
						->where('worker_age_id', $age->id)
						->where('titlemenu_id', $menu[0]['kingar_menu_id'])
						->get();
			// dd($workerfood);
			
			foreach($menuitem as $item){
				if(empty($nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['product'][$item->product_name_id])){
					$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['product'][$item->product_name_id] = 0;
					// $allproductagesumm[$age->id][$item->product_name_id]['value'] = 0;
				}
				// $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$item->product_name_id] = $item->weight;
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$age->id][$item->product_name_id]['one'] = $item->weight;
				// $nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$age->id][$item->product_name_id] = array('allcount' => $item->weight * $menu[0]['kingar_children_number']);
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id][$age->id]['age_name'] = $menu[0]['age_name'];
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodname'] = $item->food_name; 
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['foodweight'] = $item->food_weight; 
				$nextdaymenuitem[$item->menu_meal_time_id]['mealtime'] = $item->meal_time_name; 
				$productallcount[$item->product_name_id] += ($item->weight * $menu[0]['kingar_children_number']) / $item->div;
				$allproductagesumm[$age->id][$item->product_name_id] += ($item->weight * $menu[0]['kingar_children_number']) / $item->div * $narx[$item->product_name_id];
				$nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]['product'][$item->product_name_id] += ($item->weight * $menu[0]['kingar_children_number']) / $item->div;
				
				for($i = 0; $i<count($products); $i++){
					if(empty($products[$i]['yes']) and $products[$i]['id'] == $item->product_name_id){
						$products[$i]['yes'] = 1;
					}
				}
			}
			// dd($nextdaymenuitem[$item->menu_meal_time_id][$item->menu_food_id]);
			if($age->id == 4 and $workerfood->count() > 0){
				foreach($workerfood as $tr){
					foreach($nextdaymenuitem[3][$tr->food_id][4] as $key => $value){
						if($key != 'age_name'){
							$workerproducts[$key] += $value['one'];
						} 
						array_push($workerproducts, $nextdaymenuitem[3][$tr->food_id]);
					}
				}
				// dd($workerproducts);	
			}
		}
		// dd($workerproducts);
		foreach($nextdaymenuitem as $key => $item){
			$nextdaymenuitem[$key]['rows'] = count($item)-1;
			foreach($item as $rkey => $row){
				if($rkey == 'mealtime'){
					continue;
				}
				$nextdaymenuitem[$key]['rows'] += count($row)-3;
			}
		}
		// % nds ustama
		$dateString = sprintf(
			'%04d-%02d-%02d',
			$day->year_name,
			($day->month_id % 12 == 0 ? 12 : $day->month_id % 12),
			$day->day_number
		);
		
		$protsent = Protsent::where('region_id', $region_id)->where('start_date', '<=', $dateString)->where('end_date', '>=', $dateString)->get();
		
		if(!$protsent){
			$protsent = new Protsent();
			// age_range_id 3 va 4 uchun
			$protsent->where('age_range_id', 3)->eater_cost = 0;
			$protsent->where('age_range_id', 4)->eater_cost = 0;
		}

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.activsecondmenu', ['narx' => $narx,'day' => $day, 'agesumm' => $allproductagesumm, 'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menuage, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood, 'protsent' => $protsent]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $day['id']."activemenu.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser ['Attachment' => 0]
		if (isset(auth()->user()->id) and auth()->user()->id < 6){
			$dompdf->stream($name, ['Attachment' => 0]);
		}
		else{
			$dompdf->stream($name);	
		}
	}
    
	// Hozirgi kungacha ishlatilgan maxsulotlarni minus_multi_storajega yozish /////////////////////////////////////////////////////////////////////////////////////////////////
	public function minusproduct(Request $request){
		$days = Day::all();
		foreach($days as $day){
			$minus = [];
			$minuworker = [];
			$join = Number_children::where('number_childrens.day_id', $day->id)
				->leftjoin('active_menus', function($join){
                    // $join->on('day_id', '=', $today);
                    $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                    $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                })
				->where('active_menus.day_id', $day->id)
                ->join('products', 'active_menus.product_name_id', '=', 'products.id')
				->get();
			if($join->count() == 0){
				echo $day->id."- kun ishlamagan".'\n';
			}else{
				foreach($join as $row){
					$workerfood = titlemenu_food::where('day_id', $day->id-1)
						->where('worker_age_id', $row->age_range_id)
						->where('titlemenu_id', $row->kingar_menu_id)
						->where('food_id', $row->menu_food_id)
						->get();
					if(!isset($minus[$row->kingar_name_id.'-'.$row->product_name_id])){
						$minus[$row->kingar_name_id.'-'.$row->product_name_id] = 0;
					}
					if($workerfood->count() > 0 and !isset($minuworker[$row->kingar_name_id.'u'.$row->product_name_id.'uw'])){
						// boolen
						$minuworker[$row->kingar_name_id.'u'.$row->product_name_id.'uw'] = ($row->workers_count*$row->weight)/$row->div;	
						// uchun
						$minus[$row->kingar_name_id.'-'.$row->product_name_id] += $row->workers_count*$row->weight;
					}
					$minus[$row->kingar_name_id.'-'.$row->product_name_id] += $row->kingar_children_number*$row->weight;
				}

				foreach($minus as $key => $value){
					$param = explode("-", $key);

					$minusbool = minus_multi_storage::where('day_id', $day->id)
						->where('kingarden_name_id', $param[0])
						->where('product_name_id', $param[1])
						->get();
					if($minusbool->count() == 0){
						minus_multi_storage::create([
							'day_id' => $day->id,
							'kingarden_name_id' => $param[0],
							'kingar_menu_id' => 0,
							'product_name_id' => $param[1],
							'product_weight' => $value,
						]);
					}
				}
			}
		}

		echo "Yakunlandi";
	}
	// qoldiqni oldingi oydan shu oyning birinchi kuniga olib o'tish ///////////////////////////
	public function modproducts(Request $request){
		$thismonth = Month::where('month_active', 1)->first();
		$prevmonth = Day::where('month_id', $thismonth->id-1)->get();
		$kinds = Kindgarden::all();
		$products = Product::all();
		$modproduct = [];
		
		$allminusproducts = [];
		$allplusproducts = [];
		foreach($kinds as $kid){
			$prevmods = [];
			$minusproducts = [];
			$plusproducts = [];
			$takedproducts = [];
			$actualweights = [];
			$addeds = [];
			$plus = plus_multi_storage::where('day_id', '>=', $prevmonth->first()->id)->where('day_id', '<=', $prevmonth->last()->id)
				->where('kingarden_name_d', $kid->id)
				->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
				->orderby('plus_multi_storages.day_id', 'DESC')
				->get([
					'plus_multi_storages.id',
					'plus_multi_storages.product_name_id',
					'plus_multi_storages.day_id',
					'plus_multi_storages.residual',
					'plus_multi_storages.kingarden_name_d',
					'plus_multi_storages.product_weight',
					'products.product_name',
					'products.size_name_id',
					'products.div',
					'products.sort'
				]);
			$minus = minus_multi_storage::where('day_id', '>=', $prevmonth->first()->id)->where('day_id', '<=', $prevmonth->last()->id)
				->where('kingarden_name_id', $kid->id)
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
			$trashes = Take_small_base::where('take_small_bases.kindgarden_id', $kid->id)
				->where('take_groups.day_id', '>=', $prevmonth->first()->id)->where('take_groups.day_id', '<=', $prevmonth->last()->id)
				->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
				->get([
					'take_small_bases.id',
					'take_small_bases.product_id',
					'take_groups.day_id',
					'take_small_bases.kindgarden_id',
					'take_small_bases.weight',
				]);
			foreach($prevmonth as $day){
				foreach($minus->where('day_id', $day->id) as $row){
					if(!isset($minusproducts[$row->product_name_id])){
						$minusproducts[$row->product_name_id] = 0;
					}
					$minusproducts[$row->product_name_id] += $row->product_weight;
				}
				foreach($trashes->where('day_id', $day->id) as $row){
					if(!isset($takedproducts[$row->product_id])){
						$takedproducts[$row->product_id] = 0;
					}
					if(!isset($minusproducts[$row->product_name_id])){
						$minusproducts[$row->product_name_id] = 0;
					}
					$takedproducts[$row->product_id] += $row->weight;
					$minusproducts[$row->product_id] += $row->weight;
				}
				foreach($plus->where('day_id', $day->id) as $row){
					if(!isset($prevmods[$row->product_name_id])){
						$prevmods[$row->product_name_id] = 0;
					}
					if(!isset($plusproducts[$row->product_name_id])){
						$plusproducts[$row->product_name_id] = 0;
						$addeds[$row->product_name_id] = 0;
					}
					if($row->residual == 0){
						$plusproducts[$row->product_name_id] += $row->product_weight;
						$takedproducts[$row->product_name_id] = 0;
					}else{
						$prevmods[$row->product_name_id] += $row->product_weight;
						$plusproducts[$row->product_name_id] += $row->product_weight;
					}
	
				}
				$groups = Groupweight::where('kindergarden_id', $kid->id)
					->where('day_id', $day->id)
					->get();
				foreach($groups as $group){
					$actuals = Weightproduct::where('groupweight_id', $group->id)->get();
					foreach($products as $row){
						if(!isset($prevmods[$row->id])){
							$prevmods[$row->id] = 0;
						}
						if(!isset($plusproducts[$row->id])){
							$plusproducts[$row->id] = 0;
						}
						if(!isset($added[$row->id])){
							$added[$row->id] = 0;
						}
						if(!isset($minusproducts[$row->id])){
							$minusproducts[$row->id] = 0;
						}
						if(!isset($takedproducts[$row->id])){
							$takedproducts[$row->id] = 0;
						}
						if(!isset($lost[$row->id])){
							$lost[$row->id] = 0;
						}
						if($actuals->where('product_id', $row->id)->count() > 0){
							$weight = $actuals->where('product_id', $row->id)->first()->weight;
						}
						else{
							$weight = 0;
						}
						if($weight - ($plusproducts[$row->id] - $minusproducts[$row->id]) < 0){
							$lost[$row->id] += $weight - ($plusproducts[$row->id] - $minusproducts[$row->id]);
						}
						else{
							$added[$row->id] += $weight - ($plusproducts[$row->id] - $minusproducts[$row->id]);
							$plusproducts[$row->id] += $weight - ($plusproducts[$row->id] - $minusproducts[$row->id]);
						}
					}
				}

			}
			
			foreach($products as $row){
				if(!isset($allminusproducts[$kid->id][$row->id])){
					$allminusproducts[$kid->id][$row->id] = 0;
				}
				if(!isset($plusproducts[$row->id])){
					$plusproducts[$row->id] = 0;
				}
				if(!isset($minusproducts[$row->id])){
					$minusproducts[$row->id] = 0;
				}
				if(!isset($allplusproducts[$kid->id][$row->id])){
					$allplusproducts[$kid->id][$row->id] = 0;
				}
				$allplusproducts[$kid->id][$row->id] += $plusproducts[$row->id];
				$allminusproducts[$kid->id][$row->id] += $minusproducts[$row->id];
			}
			// dd($allminusproducts, $allplusproducts, $plusproducts, $added);
		}

		foreach($kinds as $kid){
			foreach($products as $row){
				if(!isset($modproduct[$kid->id][$row->id])){
					$modproduct[$kid->id][$row->id] = 0;
				}
				$modproduct[$kid->id][$row->id] = $allplusproducts[$kid->id][$row->id] - $allminusproducts[$kid->id][$row->id];
			}
		}

		$firstday = Day::where('month_id', $thismonth->id)->first();

		foreach($modproduct as $kid => $row){
			foreach($row as $pid => $value){
				$mod = plus_multi_storage::where('day_id', $firstday->id)
					->where('kingarden_name_d', $kid)
					->where('residual', 1)
					->where('product_name_id', $pid)
					->get();

				if($mod->count() == 0 and $value >= 0){
					plus_multi_storage::create([
						'day_id' => $firstday->id,
						'shop_id' => -1,
						'kingarden_name_d' => $kid,
						'order_product_id' => 0,
						'residual' => 1,
						'product_name_id' => $pid,
						'product_weight' => $value,
					]);
				}
			}
		}
		dd("OK");
	}

	public function deletemod(){
		$stor = plus_multi_storage::all();
		foreach($stor as $row){
			if($row->day_id == 21 and $row->order_product_id == -1){
				// plus_multi_storage::where('id', $row->id)->delete();
			}
			else{
				$yes[$row->day_id][$row->kingarden_name_d][$row->product_name_id][$row->product_weight] = 1;
			}

		}

		dd("OK");
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
