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
use App\Models\Titlemenu;
use App\Models\Year;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Dompdf\Dompdf;
use TCG\Voyager\Models\MenuItem;

class StorageController extends Controller
{
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
            $last = debts::where('shop_id', $shops[$i])->orderby('id', 'DESC')->first();
            $r = 0;
            $hr = 0;
            if($last == null){
                if($total[$i] < $real[$i]){
                    $r = abs($total[$i] - $real[$i]);
                    $hr = 0;
                }else{
                    $r = 0;
                    $hr = abs($total[$i] - $real[$i]);
                }
            }elseif($total[$i] + $last->hisloan < $last->loan + $real[$i]){
                $r = abs($total[$i] + $last->hisloan - ($last->loan + $real[$i]));
                $hr = 0;
            }else{
                $r = 0;
                $hr = abs($total[$i] + $last->hisloan - ($last->loan + $real[$i]));
            }
            debts::create([
                'shop_id' => $shops[$i],
                'pay' => $total[$i],
                'loan' => $r,
                'hisloan' => $hr,
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
        // dd($menus);
        return view('storage.addmultisklad', compact('orders','gardens', 'menus'));
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
        $days = $this->activmonth($month->id);
        // dd($days);
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
            // echo $minus->count()." ";
            foreach($minus as $row){
                if(!isset($minusproducts[$row->product_name_id])){
                    $minusproducts[$row->product_name_id] = 0;
                }
                $minusproducts[$row->product_name_id] += $row->product_weight;
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
                if(!isset($plusproducts[$row->product_name_id])){
                    $plusproducts[$row->product_name_id] = 0;
                }
                $plusproducts[$row->product_name_id] += $row->product_weight;
            }
        }

        $products = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        
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
        $menuitem = Menu_composition::where('title_menu_id', $menuid)->where('menu_meal_time_id', 3)->where('menu_food_id', $foodid)->where('age_range_id', 1)->get();
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
        // dd($kindproducts);
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
        // dd($request->all());
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
            // dd($kindproducts[$garden]);
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
        $days = $this->activyear($il);
        $group = Add_group::where('day_id', '>=', $start->first()->id)->where('day_id', '<=', $start->last()->id)
                ->join('days', 'days.id', '=', 'add_groups.day_id')
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderby('add_groups.id', 'DESC')
                ->get(['add_groups.id', 'add_groups.group_name', 'days.day_number', 'months.month_name', 'years.year_name']);
        
        $products = Product::all();
        $shops = Shop::where('hide', 1)->get();
        $id = $il;
        return view('storage.addedproducts', compact('shops', 'group', 'months', 'id', 'days', 'products', 'year'));
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
        // dd($document);
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
        $debts = debts::select(['debts.id as debtid', 'debts.shop_id', 'shops.shop_name', 'debts.pay', 'debts.loan', 'debts.hisloan', 'debts.row_id', 'debts.created_at as date'])
                ->join('shops', 'shops.id', '=', 'debts.shop_id')
                ->join('add_large_werehouses', 'add_large_werehouses.id', '=', 'debts.row_id')
                ->where('add_large_werehouses.shop_id', ">", 0)
                ->orderby('debts.id', 'DESC')
                ->paginate(30);

        // dd($debts);
        return view('storage.debts', compact('debts'));
    }

    public function shopdebts(Request $request){
        $debts = debts::select(['debts.id as debtid', 'debts.shop_id', 'shops.shop_name', 'debts.pay', 'debts.loan', 'debts.hisloan', 'debts.row_id', 'debts.created_at as date'])
                ->where('debts.shop_id', $request->ShopId)
                ->join('shops', 'shops.id', '=', 'debts.shop_id')
                ->join('add_large_werehouses', 'add_large_werehouses.id', '=', 'debts.row_id')
                ->where('add_large_werehouses.shop_id', ">", 0)
                ->orderby('debts.id', 'DESC')
                ->paginate(30);
        // dd($debts);
        return view('storage.shopdebts', compact('debts'));
    }
    
}
