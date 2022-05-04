<?php

namespace App\Http\Controllers;

use App\Models\Add_group;
use App\Models\Add_large_werehouse;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Menu_composition;
use App\Models\minus_multi_storage;
use App\Models\Month;
use App\Models\Nextday_namber;
use App\Models\Number_children;
use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\plus_multi_storage;
use App\Models\Product;
use App\Models\Season;
use App\Models\Shop_product;
use App\Models\Titlemenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Dompdf\Dompdf;
use TCG\Voyager\Models\MenuItem;

class StorageController extends Controller
{
    public function index(Request $request)
    {
        $dayes = Day::orderby('id', 'DESC')->get();
        $count = order_product::where('day_id', $dayes[1]->id)->where('document_processes_id', 2)->get();
        $addlarch = Add_large_werehouse::join('products', 'products.id', '=', 'add_large_werehouses.product_id')->get();
        $alladd = [];
        $t = 0;
        foreach($addlarch as $row){
            if(!isset($alladd[$row->product_id])){
                // $alladd[$t++.'id'] = $row->product_id;
                $alladd[$row->product_id]['weight'] = 0;
                $alladd[$row->product_id]['p_name'] = $row->product_name;
            }
            $alladd[$row->product_id]['weight'] += $row->weight; 
        }
        // dd($alladd);
        return view('storage.home', ['count' => count($count), 'products' => $alladd]);
    }

    public function addproductform(Request $request){
        $products = Product::where('hide', 1)->get();
        return view('storage.addproductform', ['products' => $products]);
    }

    public function addproducts(Request $request){

        $products = $request->productsid;
        $weights = $request->weights;
        $costs = $request->costs;
        // if(Add_group::where('day_id')->get()->count() == 0){
        $group = Add_group::create([
            'day_id' => 81,
            'group_name' => time()
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
        // }
        return redirect()->route('storage.addproductform');
    }

    public function addmultisklad(Request $request){
        $months = Month::all();
        $season = Season::where('hide', 1)->first();
        $menus = Titlemenu::where('menu_season_id', $season->id)->get();
        $gardens = Kindgarden::where('hide', 1)->get();
        $orders = order_product::orderby('id', 'DESC')->get();
        // dd($menus);
        return view('storage.addmultisklad', compact('orders','gardens', 'months', 'menus'));
    }

    public function onedaymulti(Request $request, $dayid){
        $months = Month::all();
        $orederproduct = order_product::where('day_id', $dayid)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->select('order_products.id', 'order_products.order_title', 'order_products.document_processes_id', 'kindgardens.kingar_name') 
            ->orderby('order_products.id', 'DESC')
            ->get();
        $orederitems = order_product_structure::join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->get();
        $kingar = Kindgarden::all();

        return view('storage.onedaymulti', ['gardens' => $kingar, 'orders' => $orederproduct, 'products'=>$orederitems, 'months'=>$months]);
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
        $days = Day::where('month_id', $month->id)->get();
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

    public function newordersklad(Request $request){
        // dd($request->all());
        
        $today = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('id', 'DESC')->first(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        $kindproducts = [];
        foreach($request->gardens as $garden){
            $kindproducts[$garden]['k'] = '*';
            $kind = Kindgarden::where('id', $garden)->with('age_range')->first();
            $stop = 0;
            foreach($request->onemenu as $tr => $day){
                if($tr > $request->maxday){
                    $stop = 1;
                }
                foreach($kind->age_range as $age){
                    $ch = Number_children::where('kingar_name_id', $garden)->where('king_age_name_id', $age->id)->orderby('day_id', 'DESC')->first();
                    $kindproducts[$garden] = $this->menuproduct($stop, $day[$ch['king_age_name_id']], $ch['king_age_name_id'], $ch['kingar_children_number'], $kindproducts[$garden]);
                }
            }
            // dd($kindproducts[$garden]);
            $mods = $this->productsmod($garden);
            
            $order = order_product::create([
                'kingar_name_id' => $garden,
                'day_id' => $today->id,
                'order_title' => $today->id.'.'.$today->month_name.'.'.$today->year_name,
                'document_processes_id' => 3,
            ]);
            
            foreach($kindproducts[$garden] as $key => $val){
                if($key == 'k') continue;
                $prod = Product::where('id', $key)->with('shop')->first();
                if($prod->shop->count() == 0){
                    if(!isset($mods[$key])){
                        $mods[$key] = 0;
                    }
                    if(($val / $prod->div) - $mods[$key] > 0){
                        order_product_structure::create([
                            'order_product_name_id' => $order->id,
                            'product_name_id' => $key,
                            'product_weight' => ($val / $prod->div) - $mods[$key],
                        ]);
                    }
                }
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
        // dd($orederproduct);

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

    public function addedproducts(Request $request){
        $months = Month::all();
        $days = Day::orderby('id', 'DESC')->get();
        $group = Add_group::join('days', 'days.id', '=', 'add_groups.day_id')
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['add_groups.id', 'add_groups.group_name', 'days.day_number', 'months.month_name', 'years.year_name']);
        // dd($group);
        return view('storage.addedproducts', compact('group', 'months'));
    }
    
    public function document(Request $request){
        $items = "";
        $document = Add_large_werehouse::where('add_group_id', $request->id)
        ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
        ->join('sizes', 'sizes.id', '=', 'products.size_name_id')->get();
        
        // dd($items);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.storage.orderskladpdf', compact('items', 'document')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4',  'landscape');
		$dompdf->render();
		$dompdf->stream('demo.pdf', ['Attachment' => 0]);
    }
    // Parolni tekshirib mayda skladlarga yuborish
    public function controlpassword(Request $request)
    {
        $day = Day::orderby('id', 'DESC')->get();
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
	                    'day_id' => 81,
	                    'shop_id' => 0,
	                    'kingarden_name_d' => $order['kingar_name_id'],
	                    'order_product_id' => $order['id'],
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
}
