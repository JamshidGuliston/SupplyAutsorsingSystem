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
use PhpParser\Node\Stmt\Foreach_;
use TCG\Voyager\Models\Category;

class TestController extends Controller
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
                }
            }
        }
        $workerproducts = array_fill(1, 500, 0);
        foreach($workerfood as $tr){
            foreach($nextdaymenuitem[3][$tr->food_id] as $key => $value){
                if($key != 'foodname'){
                    $workerproducts[$key] += $value; 
                }
            }
        }
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.alltable', ['day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'landscape');
		$name = $day['id'].'-'.$gid.'-'.$ageid."nextmenu.pdf";
		// Render the HTML as PDF
		$dompdf->render();
		
		// Output the generated PDF to Browser
		$dompdf->stream($name, ['Attachment' => 0]);
	}

	public function activmenuPDF(Request $request, $today, $gid, $ageid)
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
        $day = Day::where('days.id', $today)->join('months', 'months.id', '=', 'days.month_id')->orderBy('days.id', 'DESC')->first(['days.day_number','days.id as id', 'months.month_name']);
        // dd($day);
        $workerfood = titlemenu_food::where('day_id', ($today-1))
                    ->where('worker_age_id', $ageid)
                    ->where('titlemenu_id', $menu[0]['kingar_menu_id'])
                    ->get();
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
            foreach($nextdaymenuitem[3][$tr->food_id] as $key => $value){
                if($key != 'foodname' and $key != 'foodweight'){
                    $workerproducts[$key] += $value; 
                }
            }
        }
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.activmenu', ['day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
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
					
					// minus_multi_storage::create([
					// 	'day_id' => $today,
					// 	'kingarden_name_id' => $gid,
					// 	'kingar_menu_id' => 0,
					// 	'product_name_id' => $key,
					// 	'product_weight' => $summ,
					// ]);
					
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
        // dd($menuitem);
        
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.technolog.activmenu', ['day' => $day,'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menu, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
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
                    	$workeat = titlemenu_food::where('day_id', $day->id)->get();
                        $prlar =  Menu_composition::where('title_menu_id', $next->kingar_menu_id)->where('age_range_id', $next->king_age_name_id)->where('product_name_id', $prod->id)->get();
                        foreach($prlar as $prw){
                        	$itempr = $itempr . "+".$prw->weight." * ". $next->kingar_children_number;
                        	$weight += $prw->weight * $next->kingar_children_number;
                        	if($next->king_age_name_id == 1){
                        		$workeat = titlemenu_food::where('day_id', $day->id)->where('food_id', $prw->menu_food_id)->get();
                        		if($workeat->count() > 0){
                        			$weight += $prw->weight * $next->workers_count;
                        		}
                        		
                        	}
                        }
                        // $allsum += $weight * $next->kingar_children_number;
                        // $onesum += $weight; 
                        // $workers = $next->workers_count;
                    }
                }
                // foreach($workeat as $wo){
                //         $woe = Menu_composition::where('title_menu_id', $wo->titlemenu_id)
                //                 ->where('menu_food_id', $wo->food_id)
                //                 ->where('age_range_id', $wo->worker_age_id)
                //                 ->where('product_name_id', $prod->id)
                //                 ->sum('weight');
                //         dd($woe);
                        // if($woe > 0){
                        // 	$itempr = $itempr . 
                        // }
                // }



                $prdiv = Product::where('id', $prod->id)->first();
                // $itempr . "=" .
                $shopproducts[$row->id][$prod->id] = $weight / $prod->div; 
            }
        }
        
        
        $day = Day::join('months', 'days.month_id', '=', 'months.id')->orderBy('days.id', 'DESC')->first();
        
        // dd($day);

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('technolog.nextdayshoppdf', compact('shopproducts', 'shop', 'day')), 'HTML-ENTITIES', 'UTF-8');
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
		$products = Product::orderBy('sort', 'ASC')->get();
		$nextdaymenuitem = [];
		$workerproducts = [];
		// kamchilik bor boshlangich qiymat berishda
		$workerproducts = array_fill(1, 500, 0);
		$productallcount = array_fill(1, 500, 0);
		$menuage = [];
		$ages = Age_range::all();
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

			$costs = bycosts::where('day_id', bycosts::where('day_id', '<=', $today)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->first()->day_id)->where('region_name_id', Kindgarden::where('id', $gid)->first()->region_id)->orderBy('day_id', 'DESC')->get();
			$narx = [];
			foreach($costs as $row){
				if(!isset($narx[$row->praduct_name_id])){
					$narx[$row->praduct_name_id] = $row->price_cost;
				}
			}
			// xodimlar ovqati uchun
			$day = Day::where('days.id', $today)->join('months', 'months.id', '=', 'days.month_id')->orderBy('days.id', 'DESC')->first(['days.day_number','days.id as id', 'months.month_name']);
			// dd($day);
			$workerfood = titlemenu_food::where('day_id', ($today-1))
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
		$html = mb_convert_encoding(view('pdffile.technolog.activsecondmenu', ['narx' => $narx,'day' => $day, 'productallcount' => $productallcount, 'workerproducts' => $workerproducts,'menu' => $menuage, 'menuitem' => $nextdaymenuitem, 'products' => $products, 'workerfood' => $workerfood]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $day['id']."activemenu.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser ['Attachment' => 0]
		if (auth()->user()->id < 6){
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
		$minusproduct = [];
		$modproduct = [];
		foreach($prevmonth as $day){
			// bog'chalar o'tgan oyda ishlatgan maxsulotlar
			$minus = minus_multi_storage::where('day_id', $day->id)->get();
			foreach($minus as $row){
				if(!isset($minusproduct[$row->kingarden_name_id][$row->product_name_id])){
					$minusproduct[$row->kingarden_name_id][$row->product_name_id] = 0;
				}
				$minusproduct[$row->kingarden_name_id][$row->product_name_id] += $row->product_weight;
			}
		}

		foreach($prevmonth as $day){
			// bog'chalarga o'tgan oyda yuborilganlar maxsulotlarning qoldiqlarini xisoblash
			$plus = plus_multi_storage::where('day_id', $day->id)->get();
			foreach($plus as $row){
				if(!isset($minusproduct[$row->kingarden_name_d][$row->product_name_id])){
					$minusproduct[$row->kingarden_name_d][$row->product_name_id] = 0;
				}
				if(!isset($modproduct[$row->kingarden_name_d][$row->product_name_id])){
					$modproduct[$row->kingarden_name_d][$row->product_name_id] = -$minusproduct[$row->kingarden_name_d][$row->product_name_id];
				}
				$modproduct[$row->kingarden_name_d][$row->product_name_id] += $row->product_weight;
			}
		}

		$firstday = Day::where('month_id', $thismonth->id)->first();

		foreach($modproduct as $kid => $row){
			foreach($row as $pid => $value){
				$mod = plus_multi_storage::where('day_id', $firstday->id)
					->where('kingarden_name_d', $kid)
					->where('order_product_id', -1)
					->where('product_name_id', $pid)
					->get();
				if($mod->count() == 0 and $value >= 0){
					plus_multi_storage::create([
						'day_id' => $firstday->id,
						'shop_id' => 0,
						'kingarden_name_d' => $kid,
						'order_product_id' => -1,
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
				plus_multi_storage::where('id', $row->id)->delete();
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
