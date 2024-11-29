<?php

namespace App\Http\Controllers;

use App\Models\Add_group;
use App\Models\Add_large_werehouse;
use App\Models\Day;
use App\Models\debts;
use App\Models\Kindgarden;
use App\Models\Menu_composition;
use App\Models\minus_multi_storage;
use App\Models\Month;
use App\Models\Nextday_namber;
use App\Models\Number_children;
use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\Outside_product;
use App\Models\plus_multi_storage;
use App\Models\Product;
use App\Models\Season;
use App\Models\Shop;
use App\Models\Shop_product;
use App\Models\Take_group;
use App\Models\Take_product;
use App\Models\Take_small_base;
use App\Models\Titlemenu;
use App\Models\Groupweight;
use App\Models\Weightproduct;
use App\Models\User;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Dompdf\Dompdf;
use TCG\Voyager\Models\MenuItem;
use DB;
class StorageController extends Controller
{
    public function days(){
        $days = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderby('days.id', 'DESC')
                ->get(['days.id', 'days.day_number', 'months.id as month_id', 'months.month_name', 'years.year_name']);
        return $days;
    }
    public function activmonth($month_id){
        $month = Month::where('id', $month_id)->first();
        $days = Day::where('month_id', $month->id)->where('year_id', $month->yearid)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function activyear($menuid){
        $days = Day::where('month_id', $menuid)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderby('days.id', 'DESC')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function index(Request $request, $yearid=0, $id = 0)
    {
        if($yearid == 0){
            $yearid = Year::where('year_active', 1)->first()->id;
        }
        $year = Year::where('id', $yearid)->first();
        $months = Month::where('yearid', $yearid)->get();
        
        $il = $id;
        if($id == 0){
            $il = Month::where('month_active', 1)->where('yearid', $yearid)->first()->id;
            if($il == null){
                $il = Month::where('yearid', $yearid)->first()->id;
            }
        }
        $dayes = Day::orderby('id', 'DESC')->get();
        $month_days = $this->activmonth($il);
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
                    ->where('add_groups.day_id', '<=', $month_days->last()->id)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        
        $alladd = [];
        $t = 0;
        foreach($addlarch as $row){
            if(!isset($alladd[$row->product_id])){
                // $alladd[$t++.'id'] = $row->product_id;
                $alladd[$row->product_id]['weight'] = 0;
                $alladd[$row->product_id]['minusweight'] = 0;
                $alladd[$row->product_id]['p_name'] = $row->product_name;
                $alladd[$row->product_id]['size_name'] = $row->size_name;
                $alladd[$row->product_id]['p_sort'] = $row->sort;
            }
            $alladd[$row->product_id]['weight'] += $row->weight; 
        }


        $minuslarch = order_product_structure::where('order_products.day_id', '>=', $month_days->first()->id)
                    ->where('order_products.day_id', '<=', $month_days->last()->id)
                    ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();

        foreach($minuslarch as $row){
            if(!isset($alladd[$row->product_name_id])){
                $alladd[$row->product_name_id]['weight'] = 0;
                $alladd[$row->product_name_id]['minusweight'] = 0;
                $alladd[$row->product_name_id]['p_name'] = $row->product_name;
                $alladd[$row->product_name_id]['size_name'] = $row->size_name;
                $alladd[$row->product_name_id]['p_sort'] = $row->sort;
            }
            $alladd[$row->product_name_id]['minusweight'] += $row->product_weight;
        }

        usort($alladd, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });
        
        return view('storage.home', ['year' => $year, 'months' => $months, 'products' => $alladd, 'id' => $il]);
    }

    public function addproductform(Request $request){
        $products = Product::where('hide', 1)->get();
        return view('storage.addproductform', ['products' => $products]);
    }

    public function addproducts(Request $request){
        $id = $request->month_id;
        $products = $request->productsid;
        $weights = $request->weights;
        $costs = $request->costs;
        $shops = $request->shops;
        $pays = $request->pays;
        if($products != null){
            $group = Add_group::create([
                'day_id' => $request->date_id,
                'group_name' => $request->title,
                'residual' => 0,
            ]);
        }
        $real = [];
        $ids = array();
        for($i = 0; $i < count($products);  $i++){
            $tid = Add_large_werehouse::create([
                'add_group_id' => $group->id,
                'shop_id' => $shops[$i],
                'product_id' => $products[$i],
                'weight' => $weights[$i],
                'cost' => $costs[$i]
            ])->id;
            array_push($ids, $tid);
        }
        $ww = [];
        $total = [];
        for($i = 0; $i < count($shops); $i++){
            if(empty($total[$i])){
                $total[$i] = 0;
                $real[$i] = 0;
            }
            $ww[$i] = $shops[$i];
            $total[$i] += $pays[$i];
            $real[$i] += $costs[$i] * $weights[$i];
        }

        for($i = 0; $i < count($shops); $i++){
            debts::create([
                'shop_id' => $shops[$i],
                'day_id' => $request->date_id,
                'pay' => $total[$i],
                'loan' => $real[$i],
                'hisloan' => 0,
                'row_id' => $ids[$i]
            ]);
        }

        return redirect()->route('storage.addedproducts', [ 0,  $id]);
    }

    public function addr_products(Request $request){
        // dd($request->all());
        $id = $request->month_id;
        $products = $request->productsid;
        $weights = $request->weights;
        $costs = $request->costs;
        // if(Add_group::where('day_id')->get()->count() == 0){
        $group = Add_group::create([
            'day_id' => $request->date_id,
            'group_name' => $request->title,
            'residual' => 1,
        ]);

        for($i = 0; $i < count($products);  $i++){
            Add_large_werehouse::create([
                'add_group_id' => $group->id,
                'shop_id' => 0,
                'product_id' => $products[$i],
                'weight' => $weights[$i],
                'cost' => $costs[$i]
            ]);
        }
    
        return redirect()->route('storage.addedproducts', $id);
    }

    public function addmultisklad(Request $request){
        $season = Season::where('hide', 1)->first();
        $menus = Titlemenu::where('menu_season_id', $season->id)->get();
        $gardens = Kindgarden::where('hide', 1)->get();
        $orders = order_product::orderby('id', 'DESC')->get();
        $days = $this->days();
        // dd($menus);
        return view('storage.addmultisklad', compact('orders','gardens', 'menus', 'days'));
    }

    public function report(Request $request){
        dd($request->all());

        $days = Day::where('days.id', '>=', $request->start)->where('days.id', '<=', $request->end)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
                
        $nakproducts = [];
        $kindgardens = [];
        foreach($request->kindgardens as $row_id){
            array_push($kindgardens, Kindgarden::where('id', $row_id)->first());
            foreach($days as $day){
                $ages = Age_range::all();
                foreach($ages as $age){
                    $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('kingar_name_id', $row_id)
                        ->where('king_age_name_id', $age->id)
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                            $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                        })
                        ->where('active_menus.day_id', $day->id)
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
                    $productscount = array();
                    foreach($join as $row){
                        if(!isset($productscount[$row->product_name_id][$row->age_range_id])){
                            $productscount[$row->product_name_id][$row->age_range_id] = 0;
                        }
                        $productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
                        $productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
                        $productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
                        $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                        $productscount[$row->product_name_id][$row->age_range_id.'sort'] = $row->sort;
                        $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                    }
                    
                    foreach($productscount as $key => $row){
                        if(!isset($nakproducts[$key][$row_id])){
                            $nakproducts[$key][$row_id] = 0;
                        }
                        $nakproducts[$key][$row_id] += ($row[$age->id]*$row[$age->id.'-children']) / $row[$age->id.'div'];
                        $nakproducts[$key]['product_name'] = $row['product_name'];
                        $nakproducts[$key]['sort'] = $row[$age->id.'sort'];
                        $nakproducts[$key]['size_name'] = $row['size_name'];
                    }
    
                }
                 
            }
        }
    }

    public function onedaymulti(Request $request, $dayid){
        $orederproduct = order_product::where('day_id', $dayid)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->select('order_products.id', 'order_products.order_title', 'order_products.document_processes_id', 'kindgardens.kingar_name') 
            ->orderby('order_products.id', 'DESC')
            ->get();
        // $orederitems = order_product_structure::join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            // ->get();
        $orederitems = [];
        $kingar = Kindgarden::all();

        return view('storage.onedaymulti', ['gardens' => $kingar, 'orders' => $orederproduct, 'products'=>$orederitems, 'dayid' => $dayid]);
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
        $newsproduct = Product::orderby('sort', 'ASC')->get();
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
        return view('storage.orderitem', ['orderid' => $id, 'productall' => $newsproduct, 'items' => $items, 'ordername' => $orederproduct]);
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

    public function editproduct(Request $request)
    {
        order_product_structure::where('id', $request->producid)->update(
            ['product_weight' => $request->orderinpval]
        );
    }

    public function deleteid(Request $request)
    {
        order_product_structure::where('id', $request->id)->delete();
    }

    public function productsmod($kid){
        $king = Kindgarden::where('id', $kid)->first();
        $month = Month::where('month_active', 1)->first();
        $products = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        $days = $this->activmonth($month->id);
        $minusproducts = [];
        $plusproducts = [];
        $takedproducts = [];
        $actualweights = [];
        $addeds = [];
        $prevmods = [];
        $plus = plus_multi_storage::where('day_id', '>=', $days->first()->id)->where('day_id', '<=', $days->last()->id)
				->where('kingarden_name_d', $kid)
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
		$minus = minus_multi_storage::where('day_id', '>=', $days->first()->id)->where('day_id', '<=', $days->last()->id)
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
		$trashes = Take_small_base::where('take_small_bases.kindgarden_id', $kid)
				->where('take_groups.day_id', '>=', $days->first()->id)->where('take_groups.day_id', '<=', $days->last()->id)
				->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
				->get([
					'take_small_bases.id',
					'take_small_bases.product_id',
					'take_groups.day_id',
					'take_small_bases.kindgarden_id',
					'take_small_bases.weight',
				]);
        foreach($days as $day){
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
                $minusproducts[$row->product_name_id] += $row->weight;
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
            $groups = Groupweight::where('kindergarden_id', $kid)
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
        
        $mods = [];
        foreach($products as $product){
            if(isset($minusproducts[$product->id]) or isset($plusproducts[$product->id])){
                if(isset($plusproducts[$product->id])){ 
                    $countin = $plusproducts[$product->id];
                }
                else
                    $countin = 0;
                
                if(isset($minusproducts[$product->id])){ 
                    $countout = $minusproducts[$product->id];
                }
                else
                    $countout = 0;

                $mods[$product->id] = $countin - $countout;
            }
        }

        return $mods;
    }

    public function menuproduct($stop, $menuid, $ageid, $child_count, $kindproducts){
        $menuitem = Menu_composition::where('title_menu_id', $menuid)->where('age_range_id', $ageid)->get();
        foreach($menuitem as $row){
            if(!isset($kindproducts[$row['product_name_id']])){
                $kindproducts[$row['product_name_id']] = 0;
            }
            $product = Product::where('id', $row['product_name_id'])->first();
            if($product->category_name_id == 0 and $stop == 1){
                // dd($product, $stop, $child_count);
                continue;
            }
            $kindproducts[$row['product_name_id']] += $row['weight'] * $child_count;
        }
        // dd($kindproducts);
        return $kindproducts;
    }

    public function workermenuproduct($stop, $menuid, $foodid, $worker_count, $kindproducts){
        $menuitem = Menu_composition::where('title_menu_id', $menuid)->where('menu_meal_time_id', 3)->where('menu_food_id', $foodid)->where('age_range_id', 4)->get();
        foreach($menuitem as $row){
            if(!isset($kindproducts[$row['product_name_id']])){
                $kindproducts[$row['product_name_id']] = 0;
            }
            $product = Product::where('id', $row['product_name_id'])->first();
            if($product->category_name_id == 0 and $stop == 1){
                continue;
            }
            $kindproducts[$row['product_name_id']] += $row['weight'] * $worker_count;
        }
        
        return $kindproducts;
    }

    public function getworkerfoods(Request $request){
        $foods = Menu_composition::where('title_menu_id', $request->menuid)->where('menu_meal_time_id', 3)
                ->join('food', 'food.id', '=', 'menu_compositions.menu_food_id')->get();
        $html = "<br><div class='col-md-5'>
                    <div class='product-select'>
                    <p>Xodimlar uchun:</p>";
        foreach($foods as $row){
            if(empty($bool[$row->menu_food_id])){
                $bool[$row->menu_food_id] = "OK";
                $html .= "<input type='checkbox' id='vehicle' name='".$request->menuid."' value='".$row->menu_food_id."' >
                <label for='vehicle'>".$row->food_name."</label><br>";
            }
        }            
        $html .= "</div>
        </div>";
        
        return $html;
    }

    public function newordersklad(Request $request){
        $today = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('id', 'DESC')->first(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        $kindproducts = [];
        $kindworkerproducts = [];
        foreach($request->gardens as $garden){
            $kindproducts[$garden]['k'] = '*';
            $kindworkerproducts[$garden]['k'] = '*';
            $kind = Kindgarden::where('id', $garden)->with('age_range')->first();
            $stop = 0;
            foreach($request->onemenu as $tr => $day){
                if($tr > $request->maxday){
                    $stop = 1;
                }
                foreach($kind->age_range as $age){
                    // $ch = Number_children::where('kingar_name_id', $garden)->where('king_age_name_id', $age->id)->orderby('day_id', 'DESC')->first();
                    // if(empty($ch)){
                    $ch = Nextday_namber::where('kingar_name_id', $garden)->where('king_age_name_id', $age->id)->first();
                    // }
                    $kindproducts[$garden] = $this->menuproduct($stop, $day[$ch['king_age_name_id']], $ch['king_age_name_id'], $ch['kingar_children_number'], $kindproducts[$garden]);
                }
                foreach($request->workerfoods[$tr] as $key => $val){
                    $kindworkerproducts[$garden] = $this->workermenuproduct($stop, $val, $key, $kind->worker_count, $kindworkerproducts[$garden]);
                }
            }
            // dd($kindworkerproducts[$garden]);
            $mods = $this->productsmod($garden);
            date_default_timezone_set('Asia/Tashkent');
            $order = order_product::create([
                'kingar_name_id' => $garden,
                'day_id' => $today->id,
                'order_title' => date("d-m-Y"),
                'document_processes_id' => 3,
            ]);
            
            foreach($kindproducts[$garden] as $key => $val){
                if($key == 'k') continue;
                $prod = Product::where('id', $key)->with('shop')->first();
                // if($prod->shop->count() == 0){
                if(!isset($mods[$key]) or $mods[$key] <= 0){
                    $mods[$key] = 0;
                }
                if(isset($kindworkerproducts[$garden][$key])){
                    $val = $val + $kindworkerproducts[$garden][$key];
                }
                if(($val / $prod->div) - $mods[$key] > 0){
                    order_product_structure::create([
                        'order_product_name_id' => $order->id,
                        'product_name_id' => $key,
                        'product_weight' => ($val / $prod->div) - $mods[$key],
                    ]);
                }
                // }
            }

        }

        return redirect()->route('storage.addmultisklad');
    }

    public function orders()
    {
        $days = Day::orderby('id', 'DESC')->get();
        $orederproduct = order_product::join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('days', 'days.id', '=', 'order_products.day_id')
            // ->where('day_id', $days[1]->id)
            ->select('order_products.id', 'days.day_number', 'order_products.order_title', 'order_products.document_processes_id', 'kindgardens.kingar_name')
            ->orderby('order_products.id', 'DESC')
            ->where('document_processes_id', '>', 1)
            ->get();
        $orederitems = order_product_structure::join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->get();
            
        return view('storage.orders', ['orders' => $orederproduct, 'products' => $orederitems]);
    }

    // hujjatni qabul qilish
    // hujjat
    public function getdoc(Request $request)
    {
        order_product::where('id', $request->getid)->update([
            'document_processes_id' => 3
        ]);
    }

    public function addedproducts(Request $request, $yearid = 0, $id){
        if($yearid == 0){
            $yearid = Year::where('year_active', 1)->first()->id;
        }
        $year = Year::where('id', $yearid)->first();
        $months = Month::where('yearid', $yearid)->get();
        $il = $id;
        if($id == 0){
            $il = Month::where('month_active', 1)->where('yearid', $yearid)->first()->id;
            if($il == null){
                $il = Month::where('yearid', $yearid)->first()->id;
            }
        }
        $start = $this->activmonth($il);
        $days = $this->days();
        $group = Add_group::where('day_id', '>=', $start->first()->id)->where('day_id', '<=', $start->last()->id)
                ->join('days', 'days.id', '=', 'add_groups.day_id')
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderby('add_groups.id', 'DESC')
                ->get(['add_groups.id', 'add_groups.group_name', 'days.id as dayid', 'days.day_number', 'months.month_name', 'years.year_name']);
        
        $products = Product::all();
        $shops = Shop::where('hide', 1)->get();
        $id = $il;
        return view('storage.addedproducts', compact('shops', 'group', 'months', 'id', 'days', 'products', 'year', 'start'));
    }
    
    public function editegroup(Request $request){
    	Add_group::where('id', $request->group_id)->update(['day_id' => $request->editedayid , 'group_name' => $request->nametitle]);
        return redirect()->route('storage.addedproducts', ['year' => $request->year_id, 'id' => $request->month_id]);
    }
    
    public function document(Request $request){
        $items = "";
        $products = Add_large_werehouse::where('add_group_id', $request->id)
                ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')->get();
        $document = []; 
        foreach($products as $row){
            $document[$row->product_id]['add_group_id'] = $row->add_group_id;
            $document[$row->product_id]['product_name'] = $row->product_name;
            $document[$row->product_id]['size_name'] = $row->size_name;
            $document[$row->product_id]['sort'] = $row->sort;
            $document[$row->product_id]['weight'] = $row->weight;
            $document[$row->product_id]['cost'] = $row->cost;
        }
        usort($document, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });
        // dd($document);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.storage.orderskladpdf', compact('items', 'document')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4',  'landscape');
		$dompdf->render();
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }
    // svod sklad
    public function ordersvodpdf(Request $request, $id){
        $document = order_product::where('order_products.day_id', $id)->get();
        $items = [];
        foreach($document as $row){
            $item = order_product_structure::where('order_product_name_id', $row->id)
                ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get();
            foreach($item as $in){
                if(!isset($items[$in->product_name_id])){
                    $items[$in->product_name_id]['product_weight'] = 0;
                    $items[$in->product_name_id]['product_name'] = $in->product_name;
                    $items[$in->product_name_id]['size_name'] = $in->size_name;
                    $items[$in->product_name_id]['p_sort'] = $in->sort;
                }
                $items[$in->product_name_id]['product_weight'] += $in->product_weight;
            }  
        }

        usort($items, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.storage.ordersvodpdf', compact('items', 'document')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4');
		$dompdf->render();
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }

    public function ingroup(Request $request, $id){
        $products = Product::all();
        $productall = Add_large_werehouse::where('add_group_id', $id)
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->get(['add_large_werehouses.id', 'products.product_name', 'sizes.size_name', 'add_large_werehouses.weight','add_large_werehouses.cost', 'add_groups.created_at']);
        foreach($productall as $item){
            $t = 0;
            foreach($products as $pro){
                if($item->product_name == $pro->product_name){
                    $products[$t]['ok'] = 1;
                }
                $t++;
            }
        }

        $group = Add_group::where('add_groups.id', $id)
                ->join('days', 'days.id', '=', 'add_groups.day_id')
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->first(['add_groups.id', 'months.id as month_id', 'add_groups.group_name', 'days.day_number', 'months.month_name', 'years.year_name']);
        // dd($group);
        return view('storage.ingroup', compact('products', 'productall', 'group', 'id'));
    }

    public function addproduct(Request $request){
        // dd($request->all());
        Add_large_werehouse::create([
            'add_group_id' => $request->titleid,
            'shop_id' => 0,
            'product_id' => $request->productid,
            'weight' => $request->weight,
            'cost' => $request->cost
        ]);

        return redirect()->route('storage.ingroup', $request->titleid);
    }

    public function deleteproduct(Request $request){
        Add_large_werehouse::where('id', $request->id)->delete(); 
    }
    // Parolni tekshirib mayda skladlarga yuborish
    public function controlpassword(Request $request)
    {   
        $day = Day::where('year_id', Year::where('year_active', 1)->first()->id)->where('month_id', Month::where('month_active', 1)->first()->id)->orderby('id', 'DESC')->first();
    
        $password = Auth::user()->password;
        if (Hash::check($request->password, $password)) {
            $result = 1;
            order_product::where('id', $request->orderid)->update([
                'document_processes_id' => 4
            ]);

            $order = order_product::where('id', $request->orderid)->first();
            $product = order_product_structure::where('order_product_name_id', $request->orderid)->get();
            foreach ($product as $row) {
            	$find = plus_multi_storage::where('kingarden_name_d', $order['kingar_name_id'])
            						->where('order_product_id', $order['id'])
            						->where('product_name_id', $row['product_name_id'])
            						->where('product_weight', $row['product_weight'])
            						->get();
            	if($find->count() == 0){
	                plus_multi_storage::create([
	                    'day_id' => $day->id,
	                    'shop_id' => 0,
	                    'kingarden_name_d' => $order['kingar_name_id'],
	                    'order_product_id' => $order['id'],
                        'residual' => 0,
	                    'product_name_id' => $row['product_name_id'],
	                    'product_weight' => $row['product_weight'],
	                ]);
            	}
            }
        } else {
            $result = 0;
        }
        return $result;
    }
    
    public function dostcontrolpassword(Request $request)
    {
        $password = Auth::user()->password;
        if (Hash::check($request->password, $password)) {
            $result = 1;
            order_product::where('id', $request->orderid)->update([
                'document_processes_id' => 4
            ]);
        } else {
            $result = 0;
        }
        return $result;
    }

    public function backcontrolpassword(Request $request){
        $password = Auth::user()->password;
        if (Hash::check($request->password, $password)) {
            $result = 1;
            order_product::where('id', $request->orderid)->update([
                'document_processes_id' => 3
            ]);
        } else {
            $result = 0;
        }
        return $result;
    }

    public function takecategories(Request $request){
        $categories = Outside_product::where('hide', 1)->get();
        return view('storage.takecategories', compact('categories'));
    }
    public function add_takecategory(Request $request){
        Outside_product::create([
            'outside_name' => $request->title,
            'hide' => 1
        ]);
        return redirect()->route('storage.takecategories');
    }
    public function update_takecategory(Request $request){
        Outside_product::where('id', $request->nameid)->update([
            'outside_name' => $request->title
        ]);
        return redirect()->route('storage.takecategories');
    }
    public function delete_takecategory(Request $request){
        Outside_product::where('id', $request->nameid)->update([
            'hide' => 0
        ]);
        return redirect()->route('storage.takecategories');
    }

    public function deleteorder(Request $request){
        order_product::where('id', $request->orderid)->delete();
        order_product_structure::where('order_product_name_id', $request->orderid)->delete();
        return redirect()->route('storage.onedaymulti', $request->dayid)->with('status', "Maxsulotlar o\'chirildi!");
    }

    public function debts(Request $request){
        $shops = Shop::where('type_id', 2)->get();
        $products = Product::all();
        $days = $this->days();
        
        $debts = debts::select(['debts.id as debtid', 'products.id as productid', 'debts.day_id', 'debts.shop_id', 'shops.shop_name', 'add_large_werehouses.cost', 'add_large_werehouses.weight', 'add_large_werehouses.id as lid', 'sizes.size_name', 'products.product_name', 'debts.pay', 'debts.loan', 'debts.hisloan', 'debts.row_id', 'debts.created_at as date'])
            ->leftjoin('shops', 'debts.shop_id', '=', 'shops.id')
            ->leftjoin('add_large_werehouses', 'debts.row_id', '=', 'add_large_werehouses.id')
            ->leftjoin('products', 'add_large_werehouses.product_id', '=', 'products.id')
            ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->orderby('debts.id', 'DESC')
            ->paginate(50);

        $pay = debts::sum('pay');
        $loan = debts::sum('loan');
        
        return view('storage.debts', compact('debts', 'shops', 'products', 'days', 'pay', 'loan'));
    }

    public function editedebts(Request $request){
        // dd($request->all());
        if($request->larid != null)
            Add_large_werehouse::where('id', $request->larid)->update(['shop_id' => $request->editeshop_id, 'product_id' => $request->productid, 'weight' => $request->weight, 'cost' => $request->cost]);
        debts::where('id', $request->debt_id)->update(['shop_id' => $request->editeshop_id, 'day_id' => $request->editedayid, 'pay' => $request->pay_value, 'loan' => $request->weight * $request->cost]);

        return redirect()->route('storage.debts');
    }

    public function deletedebt(Request $request){
        if($request->dlarid != null){
        	// dd($request->dlarid);
            Add_large_werehouse::where('id', $request->dlarid)->delete();
        }
        // dd(0);
        debts::where('id', $request->ddebt_id)->delete();

        return redirect()->route('storage.debts');
    }

    public function shopdebts(Request $request){
        $days = $this->days();
        $debts = debts::select(['debts.id as debtid', 'debts.day_id', 'debts.shop_id', 'shops.shop_name', 'add_large_werehouses.cost', 'add_large_werehouses.weight', 'products.product_name', 'debts.pay', 'debts.loan', 'debts.hisloan', 'debts.row_id', 'debts.created_at as date'])
                ->where('debts.shop_id', $request->ShopId)
                ->leftjoin('shops', 'debts.shop_id', '=', 'shops.id')
                ->leftjoin('add_large_werehouses', 'debts.row_id', '=', 'add_large_werehouses.id')
                ->leftjoin('products', 'add_large_werehouses.product_id', '=', 'products.id')
                ->orderby('debts.id', 'DESC')
                ->paginate(50);
        return view('storage.shopdebts', compact('debts', 'days'));
    }

    public function payreport(){
        $shops = Shop::all();
        $days = $this->days();

        return view('storage.payreport', compact('shops', 'days'));
    }
    
    public function createpay(Request $request){
        
        debts::create([
            'shop_id' => $request->catid,
            'day_id' => $request->dayid,
            'pay' => $request->value,
            'loan' => 0,
            'hisloan' => 0,
            'row_id' => 0
        ]);

        return redirect()->route('storage.debts');
    }

    public function selectreport($id, $b, $e){
        if($b == 0){
            $b = Day::first()->id;
        }
        if($e == 0){
            $e = Day::orderby('id', 'DESC')->first()->id;
        }
        
        if($id == 0){
            $shops = Shop::where('type_id', 2)->get();
        }
        else{
            $shops = Shop::where('id', $id)->get();
        }
        
        $report = [];
        $days = $this->days();

        foreach($shops as $row){
            $name = $shops->find($row->id)->shop_name;
            $oldpay = debts::where('shop_id', $row->id)->where('day_id', '<', $b)->sum('pay');
            $oldloan = debts::where('shop_id', $row->id)->where('day_id', '<', $b)->sum('loan');
            
            $deb = debts::where('shop_id', $row->id)
                ->where('day_id', '>=', $b)
                ->where('day_id', '<=', $e)
                ->get()->toarray();
            
            $deb["shop"] = array("name" => $name, "oldpay" => $oldpay, "oldloan" => $oldloan, "debt" => $oldpay-$oldloan);
            
            array_push($report, $deb);
        }

        $html = "<table class='table table-light py-4 px-4'>
                    <thead>
                        <tr>
                            <th scope='col'>Tashkilot</th>
                            <th scope='col'>To'langan</th>
                            <th scope='col'>Haqiqiy miqdor</th>
                            <th scope='col'>Farqi</th>
                            <th scope='col'>Qarzdorlik</th>
                            <th scope='col'>Sana</th>
                        </tr>
                    </thead>
                    <tbody>";
                    $total = 0;
                    foreach($report as $shop){
                        $total1 = 0;
                        $total2 = 0;
                        $html = $html."<tr>
                                    <td><b>".$shop['shop']['name']."</b></td>
                                    <td>".$shop['shop']['oldpay']."</td>
                                    <td>".$shop['shop']['oldloan']."</td>
                                    <td>".$shop['shop']['debt']."</td>
                                    <td></td>
                                    <td>Hisobot davriga qadar</td>
                                </tr>";
                        foreach($shop as $key => $row){
                            if($key != 'shop'){
                                $total1 = $total1 + $row['pay'];
                                $total2 = $total2 + $row['loan'];
                                $html = $html."<tr>
                                        <td></td>
                                        <td>".$row['pay']."</td>
                                        <td>".$row['loan']."</td>
                                        <td></td>
                                        <td></td>
                                        <td>".$row['day_id']."</td>
                                    </tr>";
                            }
                        }

                        $html = $html."<tr>
                                    <td><b>Yakunda Jami:</b></td>
                                    <td><b>".$total1."</b></td>
                                    <td><b>".$total2."</b></td>
                                    <td><b>".$total1 - $total2."</b></td>
                                    <td><b>".$shop['shop']['debt'] + $total1-$total2."</b></td>
                                    <td></td>
                                </tr>";
                        $total = $total + $shop['shop']['debt'] + $total1-$total2;
                    }

        $html = $html."<tr><td><b>Jami:</b></td><td colspan='3'></td><td><b>".$total."</b></td><td></td></tr></tbody>
                </table>";

        return $html;

    }

    public function takinglargebase(){
        $res = Take_group::select(
                        'take_groups.id as gid',
                        'take_groups.title',
                        'take_groups.day_id',
                        'take_groups.taker_id',
                        'outside_products.outside_name',
                        'users.name',
                        'take_groups.day_id',
                    )
                    ->where('users.role_id', '!=', 6)
                    ->orderby('take_groups.id', 'DESC')
                    ->join('users', 'users.id', '=', 'take_groups.taker_id')
                    ->join('outside_products', 'outside_products.id', '=', 'take_groups.outside_id')
                    ->get();
        $days = $this->days();
        $outtypes = Outside_product::all();
        $users = User::where('users.role_id', '!=', 6)->get();

        return view('storage.takinglargebase', compact('res', 'days', 'users', 'outtypes'));
    }

    public function addtakinglargebase(Request $request){
        // dd($request->all());
        Take_group::create([
            'contur_id' => 1,
            'day_id' => $request->day_id,
            'taker_id' => $request->user_id,
            'outside_id' => $request->outid,
            'title' => $request->title,
            'description' => "",
        ]);

        return redirect()->route('storage.takinglargebase');
    }
    public function deletetakinglargebase(Request $request){
        Take_group::where('id', $request->gid);
        return redirect()->route('storage.takinglargebase');
    }

    public function intakinglargebase(Request $request, $id){
        $res = Take_product::select(
                        'take_products.id as tid',
                        'take_products.product_id',
                        'products.product_name',
                        'sizes.size_name',
                        'take_products.weight',
                        'take_products.cost',
                    )
                    ->where('take_products.takegroup_id', $id)
                    ->join('take_groups', 'take_groups.id', '=', 'take_products.takegroup_id')
                    ->join('products', 'products.id', '=', 'take_products.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->orderby('take_products.id', 'DESC')
                    ->get();
        // dd($res);
        $products = Product::all();
        
        return view('storage.intakinglargebase', compact('res', 'products', 'id'));
    }

    public function addintakinglargebase(Request $request){

        Take_product::create([
            'takegroup_id' => $request->groid,
            'product_id' => $request->productid,
            'weight' => $request->weight,
            'cost' => $request->cost,
        ]);

        return redirect()->route('storage.intakinglargebase', ['id' => $request->groid]);
    }
    public function deleteintakinglargebase(Request $request){
        Take_product::where('id', $request->rowid)->delete();

        return redirect()->route('storage.intakinglargebase', ['id' => $request->grodid]);
    }
    public function takingsmallbase(){
        $res = Take_group::select(
                        'take_groups.id as gid',
                        'take_groups.title',
                        'take_groups.day_id',
                        'take_groups.taker_id',
                        'outside_products.outside_name',
                        'users.name',
                        'users.id as uid',
                        'take_groups.day_id',
                    )
                    ->where('users.role_id', '=', 6)
                    ->orderby('take_groups.id', 'DESC')
                    ->join('users', 'users.id', '=', 'take_groups.taker_id')
                    ->join('outside_products', 'outside_products.id', '=', 'take_groups.outside_id')
                    ->get();
       
        $days = $this->days();
        $outtypes = Outside_product::all();
        $users = User::where('users.role_id', '=', 6)->with('kindgarden')->get();
       
        return view('storage.takingsmallbase', compact('res', 'days', 'users', 'outtypes'));
    }

    public function addtakingsmallbase(Request $request){
        
        Take_group::create([
            'contur_id' => 1,
            'day_id' => $request->day_id,
            'taker_id' => $request->user_id,
            'outside_id' => $request->outid,
            'title' => $request->title,
            'description' => "",
        ]);

        return redirect()->route('storage.takingsmallbase');
    }

    public function intakingsmallbase(Request $request, $id, $kid, $day){
        $res = Take_small_base::select(
                'take_small_bases.id as tid',
                'take_groups.id as groupid',
                'take_small_bases.product_id',
                'products.product_name',
                'sizes.size_name',
                'take_small_bases.weight',
                'take_small_bases.cost',
            )
            ->where('take_small_bases.kindgarden_id', $kid)
            ->where('take_groups.day_id', $day)
            ->leftjoin('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
            ->leftjoin('products', 'products.id', '=', 'take_small_bases.product_id')
            ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->orderby('take_small_bases.id', 'DESC')
            ->get();
		// dd($res);
        $products = Product::all();

        $kind = Kindgarden::where('id', $kid)->first();

        return view('storage.intakingsmallbase', compact('res', 'products', 'id', 'kind', 'day'));    
    }
    
    public function intakingsmallbasepdf(Request $request, $day, $kid){
        $res = Take_small_base::select(
                'take_small_bases.id as tid',
                'take_groups.id as groupid',
                'take_small_bases.product_id',
                'products.product_name',
                'sizes.size_name',
                'take_small_bases.weight',
                'take_small_bases.cost',
            )
            ->where('take_small_bases.kindgarden_id', $kid)
            ->where('take_groups.day_id', $day)
            ->leftjoin('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
            ->leftjoin('products', 'products.id', '=', 'take_small_bases.product_id')
            ->leftjoin('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->orderby('take_small_bases.id', 'DESC')
            ->get();
            
        $products = Product::all();

        $kind = Kindgarden::where('id', $kid)->first();   
            
        // usort($res, function ($a, $b){
        //     if(isset($a["sort"]) and isset($b["sort"])){
        //         return $a["sort"] > $b["sort"];
        //     }
        // });
        $days = $this->days();
        $users = User::where('users.role_id', '=', 6)->with('kindgarden')->get();
        
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.storage.intakingsmallbasepdf', compact('kind', 'days', 'day', 'products', 'res', 'users')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4',  'landscape');
		$dompdf->render();
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }

    public function increasedreport(Request $request)
    {
        $king = Kindgarden::where('id', $request->gardenID)->with('user')->first();	
        $days = Day::where('id', '>=', $request->start)->where('id', '<=', $request->end)->get();
        $products = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        $prevmods = [];
        $minusproducts = [];
        $plusproducts = [];
        $takedproducts = [];
        $actualweights = [];
        $addeds = [];
        $isThisMeasureDay = [];

        foreach($days as $day){
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $king->id)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
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
            $minus = minus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_id', $king->id)
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
            $trashes = Take_small_base::where('take_small_bases.kindgarden_id', $king->id)
                ->where('take_groups.day_id', $day->id)
                ->join('take_groups', 'take_groups.id', '=', 'take_small_bases.takegroup_id')
                ->get([
                    'take_small_bases.id',
                    'take_small_bases.product_id',
                    'take_groups.day_id',
                    'take_small_bases.kindgarden_id',
                    'take_small_bases.weight',
                ]);
                
            foreach($minus as $row){
                if(!isset($minusproducts[$row->product_name_id])){
                    $minusproducts[$row->product_name_id] = 0;
                }
                $minusproducts[$row->product_name_id] += $row->product_weight;
            }
            foreach($plus as $row){
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
                }
            }
            foreach($trashes as $row){
                if(!isset($takedproducts[$row->product_id])){
                    $takedproducts[$row->product_id] = 0;
                }
                if(!isset($minusproducts[$row->product_name_id])){
                    $minusproducts[$row->product_name_id] = 0;
                }
                $takedproducts[$row->product_id] += $row->weight;
            }
        
            $groups = Groupweight::where('kindergarden_id', $king->id)
                ->where('day_id', $day->id)
                ->first();
            if(isset($groups)){
                $actuals = Weightproduct::where('groupweight_id', $groups->id)->get();
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
                    if($weight -(($prevmods[$row->id] + $plusproducts[$row->id]) - ($minusproducts[$row->id] + $takedproducts[$row->id])) < 0){
                        $lost[$row->id] += (($prevmods[$row->id] + $plusproducts[$row->id]) - ($minusproducts[$row->id] + $takedproducts[$row->id])) - $weight;
                    }
                    else{
                        $added[$row->id] += $weight - (($prevmods[$row->id] + $plusproducts[$row->id]) - ($minusproducts[$row->id] + $takedproducts[$row->id]));
                        $plusproducts[$row->id] += $weight - ($plusproducts[$row->id] - $minusproducts[$row->id]);
                    }   
                }
            }
        }


        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.storage.increasedskladpdf', ['document' => $products, 'added' => $added]), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'landscape');
		$dompdf->render();
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);

    }

    public function allreport(Request $request){
        // dd($request->all());
        $kindergardens = [];
        foreach($request->gardens as $row){
            $kindergardens[] = order_product::where('order_products.day_id', '>=', $request->start)
                        ->where('order_products.day_id', '<=', $request->end)
                        ->where('order_products.kingar_name_id', '=', $row)
                        ->where('order_products.document_processes_id', 5)
                        ->get();
        }
        $items = [];
        foreach($kindergardens as $kindergarden){
            foreach($kindergarden as $row){
                $item = order_product_structure::where('order_product_name_id', $row->id)
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
                foreach($item as $in){
                    if(!isset($items[$in->product_name_id])){
                        $items[$in->product_name_id]['product_weight'] = 0;
                        $items[$in->product_name_id]['product_name'] = $in->product_name;
                        $items[$in->product_name_id]['size_name'] = $in->size_name;
                        $items[$in->product_name_id]['p_sort'] = $in->sort;
                    }
                    $items[$in->product_name_id]['product_weight'] += $in->product_weight;
                }  
            }
        }

        usort($items, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.storage.allreportpdf', compact('items')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4');
		$dompdf->render();
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);

    }

    public function addintakingsmallbase(Request $request){
        Take_small_base::create([
            'kindgarden_id' => $request->kid,
            'takegroup_id' => $request->groid,
            'product_id' => $request->productid,
            'weight' => $request->weight,
            'cost' => $request->cost,
        ]);

        return redirect()->route('storage.intakingsmallbase', ['id' => $request->groid, 'kid' => $request->kid, 'day' => $request->day]);
    }

    public function deletetakingsmallbase(Request $request){
        Take_small_base::where('id', $request->rowid)->delete();

        return redirect()->route('storage.intakingsmallbase', ['id' => $request->grodid, 'kid' => $request->kind_id, 'day' => $request->day]);
    }

    public function changesome(){
        while(0){
            $pp = plus_multi_storage::where('order_product_id', 0)->get();
            foreach($pp as $row){
                if($row->kingarden_name_d != 1 and $row->kingarden_name_d != 24){
                    $row->update(['day_id' => 296]);
                }
            }
            break;
        }
        dd("OK");
    }
    
}
