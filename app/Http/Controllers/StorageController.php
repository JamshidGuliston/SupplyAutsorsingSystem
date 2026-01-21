<?php

namespace App\Http\Controllers;

use App\Models\Add_group;
use App\Models\Add_large_werehouse;
use App\Models\Day;
use App\Models\debts;
use App\Models\Kindgarden;
use App\Models\Menu_composition;
use App\Models\minus_multi_storage;
use App\Models\Region;
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
use App\Models\Product_category;
use App\Models\Take_product;
use App\Models\Take_small_base;
use App\Models\Titlemenu;
use App\Models\Groupweight;
use App\Models\Weightproduct;
use App\Models\User;
use App\Models\Age_range;
use App\Models\Year;
use App\Models\Payment;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use TCG\Voyager\Models\MenuItem;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderSvodExport;
use App\Exports\OrderAllRegionsExport;
use App\Exports\OrderTitleExport;
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

    public function rangeOfDays($start, $end){
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
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

    public function daysthisyear($id){
        $days = Day::where('years.id', $id)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('days.id', 'DESC')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function index(Request $request, $yearid=0, $id = 0)
    {
        // Log boshlanishi
        Log::info('Storage index funksiyasi ishga tushdi', [
            'user_id' => Auth::id(),
            'yearid' => $yearid,
            'id' => $id,
            'request_data' => $request->all()
        ]);

        if($yearid == 0){
            $yearid = Year::where('year_active', 1)->first()->id;
            Log::info('Faol yil topildi', ['yearid' => $yearid]);
        }
        $year = Year::where('id', $yearid)->first();
        $months = Month::where('yearid', $yearid)->get();
        
        $il = $id;
        if($id == 0){
            $il = Month::where('month_active', 1)->where('yearid', $yearid)->first()->id;
            if($il == null){
                $il = Month::where('yearid', $yearid)->first()->id;
            }
            Log::info('Faol oy topildi', ['month_id' => $il]);
        }
        $dayes = Day::orderby('id', 'DESC')->get();
        $month_days = $this->activmonth($il);
        
        Log::info('Oy kunlari olingan', [
            'month_id' => $il,
            'days_count' => $month_days->count(),
            'first_day_id' => $month_days->first()->id ?? null,
            'last_day_id' => $month_days->last()->id ?? null
        ]);
        // kirim bo'lgan maxsulotlar
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
                    ->where('add_groups.day_id', '<=', $month_days->last()->id)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        
        Log::info('Kirim maxsulotlari olingan', [
            'kirim_count' => $addlarch->count(),
            'day_range' => $month_days->first()->id . ' - ' . $month_days->last()->id
        ]);
        
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
        
        Log::info('Kirim maxsulotlari hisoblandi', [
            'unique_products_count' => count($alladd)
        ]);

        // chiqim bo'lgan maxsulotlar
        $minuslarch = order_product_structure::where('order_products.day_id', '>=', $month_days->first()->id)
                    ->where('order_products.day_id', '<=', $month_days->last()->id)
                    ->where('order_products.document_processes_id', 4)
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
        
        Log::info('Chiqim maxsulotlari hisoblandi', [
            'total_products_count' => count($alladd)
        ]);

        // sotuv bo'lgan maxsulotlar
        // $sales = Sale::where('day_id', '>=', $month_days->first()->id)
        //             ->where('day_id', '<=', $month_days->last()->id)
        //             ->join('take_products', 'take_products.sale_id', '=', 'sales.id')
        //             ->join('products', 'products.id', '=', 'take_products.product_id')
        //             ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
        //             ->get();
        
        // Log::info('Sotuv maxsulotlari olingan', [
        //     'sales_count' => $sales->count()
        // ]);
        
        // foreach($sales as $row){
        //     if(!isset($alladd[$row->product_id])){
        //         $alladd[$row->product_id]['weight'] = 0;
        //         $alladd[$row->product_id]['minusweight'] = 0;
        //         $alladd[$row->product_id]['p_name'] = $row->product_name;
        //         $alladd[$row->product_id]['size_name'] = $row->size_name;
        //         $alladd[$row->product_id]['p_sort'] = $row->sort;
        //     }
        //     $alladd[$row->product_id]['minusweight'] += $row->weight;
        // }
        
        Log::info('Sotuv maxsulotlari hisoblandi', [
            'final_products_count' => count($alladd)
        ]);

        usort($alladd, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });
        
        Log::info('Storage index funksiyasi tugadi', [
            'sorted_products_count' => count($alladd),
            'year' => $year->year_name ?? 'N/A',
            'month_id' => $il
        ]);
        
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
        $yearid = $request->yearid;
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
    
        return redirect()->route('storage.addedproducts', ['year' => 0, 'id' => 0]);
    }

    public function addmultisklad(Request $request){
        // Barcha menyular
        $allMenus = Titlemenu::join('seasons', 'seasons.id', '=', 'titlemenus.menu_season_id')
            ->with('age_range')
            ->get(['titlemenus.id', 'titlemenus.menu_name', 'titlemenus.parent_id', 'titlemenus.menu_season_id', 'seasons.season_name']);

        // Barcha yosh guruhlarni olish
        $ageRanges = Age_range::orderBy('id')->get();

        // Barcha seasonlarni ierarxik tuzilmada olish (har bir age_range uchun)
        $seasonsByAgeRange = [];
        foreach ($ageRanges as $ageRange) {
            // Ushbu age_range uchun barcha seasonlarni olish
            $seasons = Season::with(['titlemenus' => function($query) use ($ageRange) {
                $query->whereNull('parent_id')
                      ->with(['children', 'age_range'])
                      ->whereHas('age_range', function($q) use ($ageRange) {
                          $q->where('age_ranges.id', $ageRange->id);
                      })
                      ->orderBy('menu_name', 'ASC');
            }])->orderBy('season_name', 'ASC')->get();

            $seasonsByAgeRange[$ageRange->id] = $seasons;
        }

        // Har bir yosh guruhiga tegishli menyularni ajratib olish (eski format uchun)
        $menusByAgeRange = [];
        foreach ($ageRanges as $ageRange) {
            $menusByAgeRange[$ageRange->id] = [
                'age_range' => $ageRange,
                'menus' => $allMenus->filter(function($menu) use ($ageRange) {
                    return $menu->age_range->contains('id', $ageRange->id);
                })->map(function($menu) {
                    return [
                        'id' => $menu->id,
                        'menu_name' => $menu->menu_name,
                        'season_name' => $menu->season_name
                    ];
                })->values()
            ];
        }

        // Eski format uchun barcha menyular (qolgan joylar uchun)
        $menus = $allMenus->map(function($menu) {
            return [
                'id' => $menu->id,
                'menu_name' => $menu->menu_name,
                'season_name' => $menu->season_name
            ];
        });

        $gardens = Kindgarden::where('hide', 1)->get();
        $product_categories = Product_category::with('products')->get();
        $products = Product::with('category')->get();

        $orders = order_product::where('shop_id', 0)->where('parent_id', null)->orderby('id', 'DESC')->get();
        $days = $this->days();

        return view('storage.addmultisklad', compact('orders','gardens', 'menus', 'ageRanges', 'menusByAgeRange', 'seasonsByAgeRange', 'days', 'product_categories', 'products'));
    }
    
    // order_title bo'yicha guruhlangan ma'lumotlarni olish
    public function getOrderTitleDetails($orderTitle){
        $orders = order_product::where('order_title', $orderTitle)
            ->with(['kinggarden.region', 'orderProductStructures.product.size'])
            ->get();
            
        if($orders->isEmpty()) {
            return response()->json(['error' => 'Ma\'lumot topilmadi'], 404);
        }
        
        // Barcha maxsulotlarni olish
        $allProducts = [];
        $regions = [];
        
        foreach($orders as $order) {
            $region = $order->kinggarden->region;
            $regions[$region->id] = $region->region_name;
            
            foreach($order->orderProductStructures as $structure) {
                $productId = $structure->product_name_id;
                $product = $structure->product;
                
                if(!isset($allProducts[$productId])) {
                    $allProducts[$productId] = [
                        'id' => $productId,
                        'name' => $product->product_name,
                        'unit' => $product->size->size_name,
                        'sort' => $product->sort ?? 0
                    ];
                }
            }
        }
        
        // Maxsulotlarni sort bo'yicha saralash
        usort($allProducts, function($a, $b) {
            return $a['sort'] - $b['sort'];
        });
        
        // Har bir maxsulot uchun har bir bog'cha bo'yicha miqdorni olish
        $productData = [];
        foreach($allProducts as $product) {
            $productData[$product['id']] = [
                'name' => $product['name'],
                'unit' => $product['unit'],
                'kindergartens' => [],
                'total' => 0
            ];
            
            foreach($orders as $order) {
                $structure = $order->orderProductStructures
                    ->where('product_name_id', $product['id'])
                    ->first();
                
                $weight = $structure ? $structure->product_weight : 0;
                $productData[$product['id']]['kindergartens'][$order->kingar_name_id] = $weight;
                $productData[$product['id']]['total'] += $weight;
            }
        }
        
        return response()->json([
            'order_title' => $orderTitle,
            'regions' => $regions,
            'orders' => $orders,
            'products' => $productData
        ]);
    }
    
    // Excel export - Order Title
    public function generateOrderTitleExcel($orderTitle){
        return Excel::download(new OrderTitleExport($orderTitle), 'buyurtma_' . $orderTitle . '.xlsx');
    }
    
    // PDF generatsiya qilish
    public function generateOrderTitlePDF($orderTitle){
        // OPTIMIZATSIYA: Barcha ma'lumotlarni bir vaqtda olish (Excel export kabi)
        $orders = order_product::where('order_title', $orderTitle)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('regions', 'regions.id', '=', 'kindgardens.region_id')
            ->orderBy('regions.id')
            ->orderBy('kindgardens.number_of_org')
            ->get(['order_products.id', 'regions.id as region_id', 'regions.region_name', 'regions.short_name', 'kindgardens.kingar_name', 'kindgardens.number_of_org', 'kindgardens.id as kingar_name_id']);
        
        if($orders->isEmpty()){
            abort(404, 'Ma\'lumot topilmadi');
        }
        
        // OPTIMIZATSIYA: Barcha order structure ma'lumotlarini bir vaqtda olish
        $orderIds = $orders->pluck('id')->toArray();
        $allProductStructures = order_product_structure::whereIn('order_product_name_id', $orderIds)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->get(['order_product_structures.id', 'order_product_structures.order_product_name_id', 'products.size_name_id', 'order_product_structures.product_name_id', 'order_product_structures.product_weight', 'products.product_name', 'sizes.size_name', 'products.div', 'order_product_structures.actual_weight', 'products.sort', 'products.package_size']);
        
        // OPTIMIZATSIYA: Ma'lumotlarni index qilish
        $structuresByOrderAndProduct = [];
        foreach($allProductStructures as $structure) {
            $structuresByOrderAndProduct[$structure->order_product_name_id][$structure->product_name_id] = $structure;
        }
        
        // Regions va kindergartens ma'lumotlarini tayyorlash
        $regions = [];
        $kindergartens = [];
        $allProducts = [];
        
        foreach($orders as $order) {
            $regions[$order->region_id] = $order->region_name;
            
            $kindergartens[$order->kingar_name_id] = [
                'id' => $order->kingar_name_id,
                'name' => $order->kingar_name,
                'number_of_org' => $order->number_of_org,
                'region_id' => $order->region_id
            ];
        }
        
        // OPTIMIZATSIYA: Barcha maxsulotlarni bir marta olish
        foreach($allProductStructures as $structure) {
            $productId = $structure->product_name_id;
            
            if(!isset($allProducts[$productId])) {
                $allProducts[$productId] = [
                    'id' => $productId,
                    'name' => $structure->product_name,
                    'unit' => $structure->size_name,
                    'unit_id' => $structure->size_name_id,
                    'sort' => $structure->sort ?? 0,
                    'package_size' => $structure->package_size ?? 0
                ];
            }
        }
        
        // Maxsulotlarni sort bo'yicha saralash
        usort($allProducts, function($a, $b) {
            return $a['sort'] - $b['sort'];
        });
        
        // Bog'chalarni avval region, keyin raqam bo'yicha saralash
        usort($kindergartens, function($a, $b) {
            if($a['region_id'] != $b['region_id']) {
                return $a['region_id'] - $b['region_id'];
            }
            return $a['number_of_org'] - $b['number_of_org'];
        });
        
        // OPTIMIZATSIYA: Product data ni tezroq yaratish
        $productData = [];
        $ordersByKindergarten = [];
        foreach($orders as $order) {
            $ordersByKindergarten[$order->kingar_name_id] = $order;
        }
        
        foreach($allProducts as $product) {
            $productData[$product['id']] = [
                'name' => $product['name'],
                'package_size' => $product['package_size'],
                'unit' => $product['unit'],
                'unit_id' => $product['unit_id'],
                'kindergartens' => [],
                'total' => 0
            ];
            
            foreach($kindergartens as $kindergarten) {
                $weight = 0;
                
                if(isset($ordersByKindergarten[$kindergarten['id']])) {
                    $orderId = $ordersByKindergarten[$kindergarten['id']]->id;
                    
                    if(isset($structuresByOrderAndProduct[$orderId][$product['id']])) {
                        $weight = $structuresByOrderAndProduct[$orderId][$product['id']]->product_weight;
                    }
                }
                
                $productData[$product['id']]['kindergartens'][$kindergarten['id']] = $weight;
                $productData[$product['id']]['total'] += $weight;
            }
        }
        
        // PDF yaratish - OPTIMIZATSIYA
        $dompdf = new Dompdf([
            'enable_remote' => true,
            'enable_javascript' => false,
            'enable_html5_parser' => true,
            'default_font' => 'DejaVu Sans'
        ]);
        
        $html = view('pdffile.storage.orderTitlePdf', compact('orderTitle', 'regions', 'kindergartens', 'productData'))->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        
        // OPTIMIZATSIYA: Render jarayonini tezlashtirish
        $dompdf->getOptions()->set([
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => false,
            'isRemoteEnabled' => true,
            'defaultFont' => 'DejaVu Sans'
        ]);
        
        $dompdf->render();
        $dompdf->stream('buyurtma_' . $orderTitle . '.pdf', ['Attachment' => 0]);
    }
    
    public function getCategoryProducts(Request $request){
        $categoryId = $request->category_id;
        $products = Product::where('category_name_id', $categoryId)->get(['id', 'product_name']);
        
        return response()->json([
            'products' => $products
        ]);
    }

    public function report(Request $request){
        // dd($request->all());

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

    public function onedaymulti(Request $request, $dayid, $haschild = null){
        if($haschild){
            $orederproduct = order_product::where('parent_id', $dayid)
                ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
                ->select('order_products.id', 'order_products.order_title', 'order_products.data_of_weight', 'order_products.document_processes_id', 'kindgardens.kingar_name', 'order_products.parent_id') 
                ->orderby('order_products.id', 'DESC')
                ->get();
        }else{  
            $orederproduct = order_product::where('order_title', $dayid)
                ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
                ->select('order_products.id', 'order_products.order_title', 'order_products.data_of_weight', 'order_products.document_processes_id', 'kindgardens.kingar_name', 'order_products.parent_id') 
                ->orderby('order_products.id', 'DESC')
                ->get();
        }
        // $orederitems = order_product_structure::join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            // ->get();
        $orederitems = [];
        $kingar = Kindgarden::all();

        return view('storage.onedaymulti', ['gardens' => $kingar, 'orders' => $orederproduct, 'products'=>$orederitems, 'dayid' => $dayid, 'haschild' => $haschild]);
    }

    public function right(Request $request)
    {
        $day = Day::orderby('id', 'DESC')->first();
        order_product::where('id', $request->orderid)->update([
            'document_processes_id' => 5
        ]);
        DB::transaction(function () use ($request, $day) {
            $order = order_product::where('id', $request->orderid)->first();
            // dd($order);
            $product = order_product_structure::where('order_product_name_id', $request->orderid)->get();
            foreach ($product as $row) {
                $exists = plus_multi_storage::where('order_product_id', $order['id'])
                                    ->where('kingarden_name_d', $order['kingar_name_id'])
                                    ->where('product_name_id', $row['product_name_id'])
                                    ->exists();
                // dd($exists);
                if(!$exists){
                    plus_multi_storage::create([
                        'day_id' => $day->id,
                        'shop_id' => $order['shop_id'] ?? 0,
                        'kingarden_name_d' => $order['kingar_name_id'],
                        'order_product_id' => $order['id'],
                        'residual' => 0,
                        'product_name_id' => $row['product_name_id'],
                        'product_weight' => $row['product_weight'],
                    ]);
                }
            }
        });

        return redirect()->route('storage.addmultisklad');
    }

    public function orderitem(Request $request, $id)
    {

        $child = order_product::where('parent_id', $id)->first();
        if($child){
            // dd($child);
            return redirect()->route('storage.onedaymulti', ['id' => $id, 'haschild' => $child->id]);
        }

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
        $newsproduct = Product::orderby('sort', 'ASC')
            ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->select('products.id', 'products.product_name', 'sizes.size_name', 'products.div')
            ->get();
        $items = order_product_structure::where('order_product_name_id', $id)
            ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
            ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
            ->select('order_product_structures.id', 'order_product_structures.product_weight', 'products.product_name', 'sizes.size_name', 'products.div', 'order_product_structures.actual_weight')
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

    // data_of_weight ma'lumotlarini olish
    public function getDataOfWeight(Request $request)
    {
        // order_products jadvalidan to'g'ridan-to'g'ri ma'lumotlarni olish
        $orderProduct = order_product::where('id', $request->id)->first();
        
        if (!$orderProduct || !$orderProduct->data_of_weight) {
            return response()->json(['error' => 'Ma\'lumot topilmadi'], 404);
        }
        
        $data = json_decode($orderProduct->data_of_weight, true);
        
        // JSON ma'lumotlarni chiroyli formatda ko'rsatish
        $html = '<div class="container-fluid">';
        $html .= '<h5 class="mb-3">Maxsulot ma\'lumotlari</h5>';
        
        // Asosiy ma'lumotlar
        if (isset($data['product_name']) || isset($data['total_weight'])) {
            $html .= '<div class="card mb-3">';
            $html .= '<div class="card-header bg-primary text-white" style="cursor: pointer;" onclick="toggleSection(\'basic-info\')">';
            $html .= '<i class="fas fa-info-circle me-2"></i>Asosiy ma\'lumotlar <i class="fas fa-chevron-down float-end" id="basic-info-icon"></i>';
            $html .= '</div>';
            $html .= '<div class="card-body" id="basic-info">';
            $html .= '<div class="row">';
            if (isset($data['product_name'])) {
                $productName = $data['product_name'];
                if (is_array($productName)) $productName = json_encode($productName);
                $html .= '<div class="col-md-6"><strong>Maxsulot:</strong> ' . htmlspecialchars($productName) . '</div>';
            }
            if (isset($data['total_weight'])) {
                $totalWeight = $data['total_weight'];
                if (is_array($totalWeight)) $totalWeight = json_encode($totalWeight);
                $html .= '<div class="col-md-6"><strong>Jami og\'irlik:</strong> ' . htmlspecialchars($totalWeight) . ' гр</div>';
            }
            $html .= '</div>';
            $html .= '</div></div>';
        }
        
        // Menyular ma'lumotlari
        if (isset($data['menus']) && is_array($data['menus'])) {
            $html .= '<div class="card mb-3">';
            $html .= '<div class="card-header bg-success text-white" style="cursor: pointer;" onclick="toggleSection(\'menus-info\')">';
            $html .= '<i class="fas fa-utensils me-2"></i>Menyular ma\'lumotlari <i class="fas fa-chevron-down float-end" id="menus-info-icon"></i>';
            $html .= '</div>';
            $html .= '<div class="card-body" id="menus-info" style="display: none;">';
            $html .= '<div class="table-responsive">';
            $html .= '<table class="table table-sm table-bordered">';
            $html .= '<thead><tr><th>Kun</th><th>Yosh guruhi</th><th>O\'quvchilar soni</th><th>Og\'irlik (гр)</th></tr></thead><tbody>';
            
            foreach ($data['menus'] as $menu) {
                if (isset($menu['children_menus']) && is_array($menu['children_menus'])) {
                    foreach ($menu['children_menus'] as $childMenu) {
                        $html .= '<tr>';
                        
                        // Barcha maydonlarni string ga o'tkazish
                        $dayNumber = $menu['day_number'] ?? '';
                        $ageGroupName = $childMenu['age_group_name'] ?? '';
                        $childrenCount = $childMenu['children_count'] ?? 0;
                        $weight = $childMenu['weight'] ?? 0;
                        
                        // Array bo'lsa string ga o'tkazish
                        if (is_array($dayNumber)) $dayNumber = json_encode($dayNumber);
                        if (is_array($ageGroupName)) $ageGroupName = json_encode($ageGroupName);
                        if (is_array($childrenCount)) $childrenCount = json_encode($childrenCount);
                        if (is_array($weight)) $weight = json_encode($weight);
                        
                        $html .= '<td>' . htmlspecialchars($dayNumber) . '</td>';
                        $html .= '<td>' . htmlspecialchars($ageGroupName) . '</td>';
                        $html .= '<td>' . htmlspecialchars($childrenCount) . '</td>';
                        $html .= '<td>' . htmlspecialchars($weight) . '</td>';
                        
                        $html .= '</tr>';
                    }
                }
            }
            $html .= '</tbody></table></div>';
            $html .= '</div></div>';
        }
        
        // Xodimlar ma'lumotlari
        if (isset($data['summary']['total_workers']) && $data['summary']['total_workers'] > 0) {
            $html .= '<div class="card mb-3">';
            $html .= '<div class="card-header bg-warning text-dark" style="cursor: pointer;" onclick="toggleSection(\'workers-info\')">';
            $html .= '<i class="fas fa-users me-2"></i>Xodimlar ma\'lumotlari <i class="fas fa-chevron-down float-end" id="workers-info-icon"></i>';
            $html .= '</div>';
            $html .= '<div class="card-body" id="workers-info" style="display: none;">';
            
            $totalWorkers = $data['summary']['total_workers'];
            $totalWeightByWorkers = $data['summary']['total_weight_by_workers'];
            
            if (is_array($totalWorkers)) $totalWorkers = json_encode($totalWorkers);
            if (is_array($totalWeightByWorkers)) $totalWeightByWorkers = json_encode($totalWeightByWorkers);
            
            $html .= '<p><strong>Xodimlar soni:</strong> ' . htmlspecialchars($totalWorkers) . '</p>';
            $html .= '<p><strong>Xodimlar uchun og\'irlik:</strong> ' . htmlspecialchars($totalWeightByWorkers) . ' гр</p>';
            $html .= '</div></div>';
        }
        
        // Umumiy ma'lumotlar
        if (isset($data['summary'])) {
            $html .= '<div class="card mb-3">';
            $html .= '<div class="card-header bg-info text-white" style="cursor: pointer;" onclick="toggleSection(\'summary-info\')">';
            $html .= '<i class="fas fa-chart-bar me-2"></i>Umumiy ma\'lumotlar <i class="fas fa-chevron-down float-end" id="summary-info-icon"></i>';
            $html .= '</div>';
            $html .= '<div class="card-body" id="summary-info" style="display: none;">';
            
            $totalChildren = $data['summary']['total_children'] ?? 0;
            $totalWeightByChildren = $data['summary']['total_weight_by_children'] ?? 0;
            
            if (is_array($totalChildren)) $totalChildren = json_encode($totalChildren);
            if (is_array($totalWeightByChildren)) $totalWeightByChildren = json_encode($totalWeightByChildren);
            
            $html .= '<p><strong>Jami o\'quvchilar:</strong> ' . htmlspecialchars($totalChildren) . '</p>';
            $html .= '<p><strong>O\'quvchilar uchun og\'irlik:</strong> ' . htmlspecialchars($totalWeightByChildren) . ' гр</p>';
            $html .= '</div></div>';
        }
        
        // To'liq JSON ma'lumotlari
        $html .= '<div class="card mb-3">';
        $html .= '<div class="card-header bg-secondary text-white" style="cursor: pointer;" onclick="toggleSection(\'json-info\')">';
        $html .= '<i class="fas fa-code me-2"></i>To\'liq JSON ma\'lumotlari <i class="fas fa-chevron-down float-end" id="json-info-icon"></i>';
        $html .= '</div>';
        $html .= '<div class="card-body" id="json-info" style="display: none;">';
        
        // JSON ma'lumotlarni alohida bo'limlarga ajratish
        if (isset($data['menus']) && is_array($data['menus'])) {
            foreach ($data['menus'] as $menuKey => $menu) {
                $html .= '<div class="card mb-2">';
                $html .= '<div class="card-header bg-light text-dark" style="cursor: pointer; padding: 8px 12px;" onclick="toggleSection(\'menu-' . $menuKey . '\')">';
                $html .= '<i class="fas fa-list me-2"></i>Menyu ' . $menuKey . ' <i class="fas fa-chevron-down float-end" id="menu-' . $menuKey . '-icon"></i>';
                $html .= '</div>';
                $html .= '<div class="card-body p-2" id="menu-' . $menuKey . '" style="display: none;">';
                
                // Menyu asosiy ma'lumotlari
                $menuData = array_diff_key($menu, ['children_menus' => '', 'remainder' => '']);
                if (!empty($menuData)) {
                    $html .= '<div class="card mb-2">';
                    $html .= '<div class="card-header bg-info text-white" style="cursor: pointer; padding: 6px 10px; font-size: 12px;" onclick="toggleSection(\'menu-' . $menuKey . '-main\')">';
                    $html .= '<i class="fas fa-info-circle me-2"></i>Asosiy ma\'lumotlar <i class="fas fa-chevron-down float-end" id="menu-' . $menuKey . '-main-icon"></i>';
                    $html .= '</div>';
                    $html .= '<div class="card-body p-2" id="menu-' . $menuKey . '-main" style="display: none;">';
                    $html .= '<div class="bg-light p-2 rounded">';
                    $html .= '<pre class="mb-0" style="white-space: pre-wrap; font-family: monospace; font-size: 10px;">';
                    $html .= htmlspecialchars(json_encode($menuData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $html .= '</pre>';
                    $html .= '</div>';
                    $html .= '</div></div>';
                }
                
                // Children menus ma'lumotlari
                if (isset($menu['children_menus']) && is_array($menu['children_menus'])) {
                    foreach ($menu['children_menus'] as $childKey => $childMenu) {
                        $html .= '<div class="card mb-2">';
                        $html .= '<div class="card-header bg-success text-white" style="cursor: pointer; padding: 6px 10px; font-size: 12px;" onclick="toggleSection(\'menu-' . $menuKey . '-child-' . $childKey . '\')">';
                        $html .= '<i class="fas fa-child me-2"></i>Yosh guruhi: ' . ($childMenu['age_group_name'] ?? 'Noma\'lum') . ' <i class="fas fa-chevron-down float-end" id="menu-' . $menuKey . '-child-' . $childKey . '-icon"></i>';
                        $html .= '</div>';
                        $html .= '<div class="card-body p-2" id="menu-' . $menuKey . '-child-' . $childKey . '" style="display: none;">';
                        
                        // Weight ma'lumotlari
                        if (isset($childMenu['weight']) && is_array($childMenu['weight'])) {
                            $html .= '<div class="card mb-2">';
                            $html .= '<div class="card-header bg-warning text-dark" style="cursor: pointer; padding: 4px 8px; font-size: 11px;" onclick="toggleSection(\'menu-' . $menuKey . '-child-' . $childKey . '-weight\')">';
                            $html .= '<i class="fas fa-weight-hanging me-2"></i>Maxsulot og\'irliklari <i class="fas fa-chevron-down float-end" id="menu-' . $menuKey . '-child-' . $childKey . '-weight-icon"></i>';
                            $html .= '</div>';
                            $html .= '<div class="card-body p-2" id="menu-' . $menuKey . '-child-' . $childKey . '-weight" style="display: none;">';
                            
                            // Maxsulot nomlari bilan ko'rsatish
                            $html .= '<div class="table-responsive">';
                            $html .= '<table class="table table-sm table-bordered">';
                            $html .= '<thead><tr><th>Maxsulot</th><th>Og\'irlik (гр)</th></tr></thead><tbody>';
                            
                            foreach ($childMenu['weight'] as $productId => $weight) {
                                if ($productId === 'k') {
                                    $html .= '<tr><td><strong>Xodimlar</strong></td><td>' . htmlspecialchars($weight) . '</td></tr>';
                                } else {
                                    // Maxsulot nomini olish
                                    $product = \App\Models\Product::find($productId);
                                    $productName = $product ? $product->product_name : 'Noma\'lum maxsulot';
                                    
                                    $html .= '<tr>';
                                    $html .= '<td>' . htmlspecialchars($productName) . '</td>';
                                    $html .= '<td>' . htmlspecialchars($weight) . '</td>';
                                    $html .= '</tr>';
                                }
                            }
                            
                            $html .= '</tbody></table></div>';
                            $html .= '</div></div>';
                        }
                        
                        // Remainder ma'lumotlari
                        if (isset($childMenu['remainder']) && !empty($childMenu['remainder'])) {
                            $html .= '<div class="card mb-2">';
                            $html .= '<div class="card-header bg-danger text-white" style="cursor: pointer; padding: 4px 8px; font-size: 11px;" onclick="toggleSection(\'menu-' . $menuKey . '-child-' . $childKey . '-remainder\')">';
                            $html .= '<i class="fas fa-boxes me-2"></i>Qoldiq maxsulotlar <i class="fas fa-chevron-down float-end" id="menu-' . $menuKey . '-child-' . $childKey . '-remainder-icon"></i>';
                            $html .= '</div>';
                            $html .= '<div class="card-body p-2" id="menu-' . $menuKey . '-child-' . $childKey . '-remainder" style="display: none;">';
                            $html .= '<div class="bg-light p-2 rounded">';
                            $html .= '<pre class="mb-0" style="white-space: pre-wrap; font-family: monospace; font-size: 10px;">';
                            $html .= htmlspecialchars(json_encode($childMenu['remainder'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                            $html .= '</pre>';
                            $html .= '</div>';
                            $html .= '</div></div>';
                        }
                        
                        $html .= '</div></div>';
                    }
                }
                
                $html .= '</div></div>';
            }
        }
        
        // Boshqa ma'lumotlarni ham alohida ko'rsatish
        $otherData = array_diff_key($data, ['menus' => '']);
        if (!empty($otherData)) {
            $html .= '<div class="card mb-2">';
            $html .= '<div class="card-header bg-light text-dark" style="cursor: pointer; padding: 8px 12px;" onclick="toggleSection(\'other-data\')">';
            $html .= '<i class="fas fa-database me-2"></i>Boshqa ma\'lumotlar <i class="fas fa-chevron-down float-end" id="other-data-icon"></i>';
            $html .= '</div>';
            $html .= '<div class="card-body p-2" id="other-data" style="display: none;">';
            $html .= '<div class="bg-light p-2 rounded">';
            $html .= '<pre class="mb-0" style="white-space: pre-wrap; font-family: monospace; font-size: 11px;">';
            $html .= htmlspecialchars(json_encode($otherData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $html .= '</pre>';
            $html .= '</div>';
            $html .= '</div></div>';
        }
        
        $html .= '</div></div>';
        
        $html .= '</div>';
        
        return response()->json(['html' => $html]);
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
                if(!isset($minusproducts[$row->product_id])){
                    $minusproducts[$row->product_id] = 0;
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

            foreach($products as $row){
                if(!isset($plusproducts[$row->id])){
                    $plusproducts[$row->id] = 0;
                }
                if(!isset($minusproducts[$row->id])){
                    $minusproducts[$row->id] = 0;
                }
                $minusproducts[$row->id] = ($plusproducts[$row->id] - $minusproducts[$row->id] < 0) ? ($plusproducts[$row->id] - $minusproducts[$row->id]) + $minusproducts[$row->id] : $minusproducts[$row->id];
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

                $mods[$product->id] = round($countin - $countout, 3);
            }
        }
        
        return $mods;
    }
    
    public function menuproduct($tr, $category_quantity, $menuid, $ageid, $child_count, $kindproducts){
        // O'quvchilar uchun joriy menyu va yosh toifalari bo'yicha maxsulotlar hisobini olish
        $menuitem = Menu_composition::where('title_menu_id', $menuid)->where('age_range_id', $ageid)->get();
        // Menyu Maxsulotlari bo'yicha sikl
        foreach($menuitem as $row){
            // dd($row);
            // joriy maxsulotni olish
            $product = Product::where('id', $row['product_name_id'])->first();
            // Bog'cha maxsulotlari massivida maxsulotlar hisobini boshlash
            if(!isset($kindproducts[$row['product_name_id']]) and isset($category_quantity[$product->category_name_id][$product->id])){
                $kindproducts[$row['product_name_id']] = 0;
            }
            
            // agar maxsulotning category_name_id 0 bo'lsa va kunlar soni o'tgan bo'lsa, maxsulotni hisobga olmaslik uchun
            if(isset($category_quantity[$product->category_name_id]['total']) and $category_quantity[$product->category_name_id]['total'] < $tr){
                // dd($product, $stop, $child_count);
                continue;
            }
            // joriy maxsulotning gramlarini qo'shib borish
            if(isset($category_quantity[$product->category_name_id][$product->id])){
                // dd($row['weight'], $child_count);
                $kindproducts[$row['product_name_id']] += $row['weight'] * $child_count;
            }
        }
        // dd($kindproducts);
        return $kindproducts;
    }

    public function workermenuproduct($tr, $category_quantity, $menuid, $foodid, $worker_count, $kindproducts){
        // Xodimlar uchun joriy menyu va yosh toifalari bo'yicha maxsulotlar hisobini olish
        $menuitem = Menu_composition::where('title_menu_id', $menuid)->where('menu_meal_time_id', 3)->where('menu_food_id', $foodid)->where('age_range_id', 4)->get();
        // Menyu Maxsulotlari bo'yicha sikl
        foreach($menuitem as $row){
            // joriy maxsulotni olish
            $product = Product::where('id', $row['product_name_id'])->first();
            
            // Bog'cha maxsulotlari massivida maxsulotlar hisobini boshlash
            if(!isset($kindproducts[$row['product_name_id']]) and isset($category_quantity[$product->category_name_id][$product->id])){
                $kindproducts[$row['product_name_id']] = 0;
            }
            
            // agar maxsulotning category_name_id 0 bo'lsa va kunlar soni o'tgan bo'lsa, maxsulotni hisobga olmaslik uchun
            if(isset($category_quantity[$product->category_name_id]['total']) and $category_quantity[$product->category_name_id]['total'] < $tr){
                continue;
            }
            // joriy maxsulotning gramlarini qo'shib borish
            if(isset($category_quantity[$product->category_name_id][$product->id])){
                $kindproducts[$row['product_name_id']] += $row['weight'] * $worker_count;
            }
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
        // dd($request->all());
        $kindproducts = [];
        $kindworkerproducts = [];
        // Hisobot qilinishi kerak bo'lgan bog'chalar sikli
        foreach($request->gardens as $garden){
            $check = Nextday_namber::where('kingar_name_id', $garden)->first();
            if(!$check){
                continue;
            }
            $dataOfWeight = [
                'garden_id' => $garden,
                'garden_name' => "",
                'total_weight' => 0,
                'menus' => [],
                'workers' => [],
                'summary' => [
                    'total_children' => 0,
                    'total_workers' => 0,
                    'total_weight_by_children' => 0,
                    'total_weight_by_workers' => 0
                ]
            ];
            // bog'chalar kesimida maxsulotlar hisobini boshlash. 
            $kindproducts[$garden]['k'] = '*';
            // xodimlar uchun maxsulotlar hisobini boshlash
            $kindworkerproducts[$garden]['k'] = '*';
            $kind = Kindgarden::where('id', $garden)->with('age_range')->first(); // joriy bog'cha yosh toifalari bilan
            $dataOfWeight['garden_name'] = $kind->kingar_name;
            // O'quvchilar uchun menyu sikli
            foreach($request->menus as $tr => $day){
                // Bog'cha yosh toifalari bo'yicha sikl
                foreach($kind->age_range as $age){
                    // $ch = Number_children::where('kingar_name_id', $garden)->where('king_age_name_id', $age->id)->orderby('day_id', 'DESC')->first();
                    // if(empty($ch)){
                    // Bog'cha yosh toifalari bo'yicha o'quvchilar sonini hisobga olish
                    $ch = Nextday_namber::where('kingar_name_id', $garden)->where('king_age_name_id', $age->id)->first();
                    // }
                    // O'quvchilar uchun joriy menyu va yosh toifalari bo'yicha maxsulotlar hisobini olish
                    $kindproducts[$garden] = $this->menuproduct($tr, $request->category_quantity, $day[$ch['king_age_name_id']], $ch['king_age_name_id'], $ch['kingar_children_number'], $kindproducts[$garden]);
                    
                    $dataOfWeight['menus'][$tr]['children_menus'][] = [
                        'age_group_id' => $age->id,
                        'age_group_name' => $age->age_name,
                        'children_count' => $ch['kingar_children_number'],
                        'weight' => $kindproducts[$garden],
                    ];
                }
                // Xodimlar uchun joriy menyu va yosh toifalari bo'yicha maxsulotlar hisobini olish
                // dd($request->workerfoods[$tr]);
                foreach($request->workerfoods[$tr] as $key => $val){
                    // Xodimlar uchun joriy menyu va yosh toifalari bo'yicha maxsulotlar hisobini olish
                    $kindworkerproducts[$garden] = $this->workermenuproduct($tr, $request->category_quantity, $val, $key, $kind->worker_count, $kindworkerproducts[$garden]);
                    $dataOfWeight['workers'][$tr]['worker_menus'][] = [
                        'worker_type_id' => $key,
                        'worker_count' => $kind->worker_count,
                        'weight' => $kindworkerproducts[$garden]
                    ];
                }
            }
            // dd($kindworkerproducts[$garden]);
            // Bog'chalar uchun maxsulotlar kesimida joriy qoldiq larni olish (remainder)
            $mods = $this->productsmod($garden);
            $dataOfWeight['summary']['remainder'] = $mods;

            date_default_timezone_set('Asia/Tashkent');
            $order = order_product::create([
                'kingar_name_id' => $garden,
                'day_id' => $request->day,
                'order_title' => date("d-m-Y H"),
                'note' => $request->note,
                'document_processes_id' => 3,
                'data_of_weight' => json_encode($dataOfWeight),
                'to_menus' => json_encode([]),
                'shop_id' => 0
            ]);
            // joriy bog'cha  maxsulotlari bo'yicha sikl
            foreach($kindproducts[$garden] as $key => $val){
                if($key == 'k') continue;
                // joriy maxsulotni olish
                $prod = Product::where('id', $key)->with('shop')->first();
                if($prod->shop->count() == 0){
                    // agar maxsulotning category_name_id 0 bo'lsa va kunlar soni o'tgan bo'lsa, maxsulotni hisobga olmaslik uchun
                    if(!isset($mods[$key]) or $mods[$key] <= 0 or !isset($request->category_quantity[$prod->category_name_id]['qoldiq'])){
                        $mods[$key] = 0;
                    }
                    // xodimlar uchun maxsulotlar gramlarini qo'shib borish
                    if(isset($kindworkerproducts[$garden][$key])){
                        $val = $val + $kindworkerproducts[$garden][$key];
                    }
                    // dd($mods[$key], $val, $prod, $request->category_quantity[$prod->category_name_id]['qoldiq']);
                    // $mods[$key] bog'chada mavjud maxsulotlar qoldig'i                                                                        
                    if(($val / $prod->div) - $mods[$key] > 0){  
                        $actual_weight = ($val / $prod->div) - $mods[$key];
                        $result = $actual_weight;
                        if($prod->size_name_id == 3 or $prod->size_name_id == 2){ 
                            $result = round($actual_weight);
                        }
                        else{
                            if($prod->package_size != null){
                                $result = ceil($actual_weight / $prod->package_size) * $prod->package_size;
                            }
                            else{
                                $result = (round($actual_weight, 1) == 0) ? round($actual_weight, 2) : round($actual_weight, 1);
                            }
                        }
                        order_product_structure::create([
                            'order_product_name_id' => $order->id,
                            'product_name_id' => $key,
                            'product_weight' => $result,
                            'actual_weight' => $actual_weight,
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
        
        // Maxsulotlar qoldiqlari hisobi
        $month_days = $start;
        
        // Kategoriyalarni olish
        $categories = Product_category::all();
        
        // Kirim bo'lgan maxsulotlar
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
                    ->where('add_groups.day_id', '<=', $month_days->last()->id)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(['add_large_werehouses.*', 'products.product_name', 'products.sort', 'products.category_name_id', 'sizes.size_name']);
        
        $productsData = [];
        foreach($addlarch as $row){
            if(!isset($productsData[$row->product_id])){
                $productsData[$row->product_id]['kirim'] = 0;
                $productsData[$row->product_id]['chiqim'] = 0;
                $productsData[$row->product_id]['p_name'] = $row->product_name;
                $productsData[$row->product_id]['size_name'] = $row->size_name;
                $productsData[$row->product_id]['p_sort'] = $row->sort;
                $productsData[$row->product_id]['category_id'] = $row->category_name_id;
            }
            $productsData[$row->product_id]['kirim'] += $row->weight; 
        }
        
        // Chiqim bo'lgan maxsulotlar (document_processes_id = 5 bo'lgan orderlar)
        $chiqimlar = order_product_structure::where('order_products.day_id', '>=', $month_days->first()->id)
                    ->where('order_products.day_id', '<=', $month_days->last()->id)
                    ->where('order_products.document_processes_id', 4)
                    ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(['order_product_structures.product_name_id', 'order_product_structures.product_weight', 'products.product_name', 'products.sort', 'products.category_name_id', 'sizes.size_name']);
        
        foreach($chiqimlar as $row){
            if(!isset($productsData[$row->product_name_id])){
                $productsData[$row->product_name_id]['kirim'] = 0;
                $productsData[$row->product_name_id]['chiqim'] = 0;
                $productsData[$row->product_name_id]['p_name'] = $row->product_name;
                $productsData[$row->product_name_id]['size_name'] = $row->size_name;
                $productsData[$row->product_name_id]['p_sort'] = $row->sort;
                $productsData[$row->product_name_id]['category_id'] = $row->category_name_id;
            }
            $productsData[$row->product_name_id]['chiqim'] += $row->product_weight;
        }
        
        // Qoldiqni hisoblash
        foreach($productsData as $key => $row){
            $productsData[$key]['qoldiq'] = $row['kirim'] - $row['chiqim'];
        }
        
        // Saralash
        usort($productsData, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });
        
        $products = Product::all();
        $shops = Shop::where('hide', 1)->get();
        $id = $il;
        return view('storage.addedproducts', compact('shops', 'group', 'months', 'id', 'days', 'products', 'year', 'start', 'productsData', 'categories'));
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
        foreach ($products as $row) {
            $document[] = [
                'add_group_id' => $row->add_group_id,
                'product_name' => $row->product_name,
                'size_name'    => $row->size_name,
                'sort'         => $row->sort,
                'weight'       => $row->weight,
                'cost'         => $row->cost,
                'created_at'   => $row->created_at,
            ];
        }
        
        usort($document, function ($a, $b) {
            return $a['sort'] <=> $b['sort'];
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
    // Excel export - Svod
    public function ordersvodexcel(Request $request, $id){
        return Excel::download(new OrderSvodExport($id), 'svod_' . $id . '.xlsx');
    }
    
    public function ordersvodpdf(Request $request, $id){
        $document = order_product::where('order_products.order_title', $id)->get();
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
        $month_days = $this->activmonth(Day::where('id', $document->first()->day_id)->first()->month_id);
        // Qoldiqlarni hisoblash
        $remainders = [];
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
                    ->where('add_groups.day_id', '<=', $month_days->last()->id)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        
        foreach($addlarch as $row){
            if(!isset($remainders[$row->product_id])){
                $remainders[$row->product_id]['kirim'] = 0;
                $remainders[$row->product_id]['chiqim'] = 0;
            }
            $remainders[$row->product_id]['kirim'] += $row->weight;
        }
        
        // Chiqimlarni olish (order_product_structures with document_processes_id = 4)
        $chiqimlar = order_product_structure::where('order_products.day_id', '>=', $month_days->first()->id)
                    ->where('order_products.day_id', '<=', $month_days->last()->id)
                    ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                    ->where('order_products.document_processes_id', 4)
                    ->select('order_product_structures.product_name_id', 'order_product_structures.product_weight')
                    ->get();
        
        foreach($chiqimlar as $row){
            if(!isset($remainders[$row->product_name_id])){
                $remainders[$row->product_name_id]['kirim'] = 0;
                $remainders[$row->product_name_id]['chiqim'] = 0;
            }
            $remainders[$row->product_name_id]['chiqim'] += $row->product_weight;
        }
        
        // Har bir mahsulot uchun qoldiqni hisoblash
        foreach($items as $product_id => &$item){
            $kirim = isset($remainders[$product_id]) ? $remainders[$product_id]['kirim'] : 0;
            $chiqim = isset($remainders[$product_id]) ? $remainders[$product_id]['chiqim'] : 0;
            $item['qoldiq'] = $kirim - $chiqim;
            $item['farq'] = $item['product_weight'] - $item['qoldiq'];
        }
        unset($item);

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

    // Excel export - Barcha hududlar
    public function ordersvodAllRegionsExcel(Request $request, $id){
        return Excel::download(new OrderAllRegionsExport($id), 'barcha_hududlar_' . $id . '.xlsx');
    }
    
    public function ordersvodAllRegions(Request $request, $id){
        $document = order_product::where('order_products.order_title', $id)
            ->join('kindgardens', 'kindgardens.id', '=', 'order_products.kingar_name_id')
            ->join('regions', 'regions.id', '=', 'kindgardens.region_id')
            ->get(['order_products.id', 'order_products.day_id', 'regions.id as region_id', 'regions.region_name', 'regions.short_name']);
        $regions = [];
        $items = [];
        foreach($document as $row){
            $item = order_product_structure::where('order_product_name_id', $row->id)
                ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->get();
            foreach($item as $in){
                if(!isset($items[$in->product_name_id][$row->region_id])){
                    $items[$in->product_name_id][$row->region_id]['product_weight'] = 0;
                    $items[$in->product_name_id]['product_name'] = $in->product_name;
                    $items[$in->product_name_id]['size_name'] = $in->size_name;
                    $items[$in->product_name_id]['p_sort'] = $in->sort;
                }
                $items[$in->product_name_id][$row->region_id]['product_weight'] += $in->product_weight;
            }  
            $regions[$row->region_id]['short_name'] = $row->short_name;   
        }
        
        // Qoldiqlarni hisoblash
        if($document->count() > 0){
            $month_days = $this->activmonth(Day::where('id', $document->first()->day_id)->first()->month_id);
            $remainders = [];
            $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
                        ->where('add_groups.day_id', '<=', $month_days->last()->id)
                        ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                        ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                        ->get();
            
            foreach($addlarch as $row){
                if(!isset($remainders[$row->product_id])){
                    $remainders[$row->product_id]['kirim'] = 0;
                    $remainders[$row->product_id]['chiqim'] = 0;
                }
                $remainders[$row->product_id]['kirim'] += $row->weight;
            }
            
            // Chiqimlarni olish
            $chiqimlar = order_product_structure::where('order_products.day_id', '>=', $month_days->first()->id)
                        ->where('order_products.day_id', '<=', $month_days->last()->id)
                        ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                        ->where('order_products.document_processes_id', 4)
                        ->select('order_product_structures.product_name_id', 'order_product_structures.product_weight')
                        ->get();
            
            foreach($chiqimlar as $row){
                if(!isset($remainders[$row->product_name_id])){
                    $remainders[$row->product_name_id]['kirim'] = 0;
                    $remainders[$row->product_name_id]['chiqim'] = 0;
                }
                $remainders[$row->product_name_id]['chiqim'] += $row->product_weight;
            }
            
            // Har bir mahsulot uchun qoldiq va farqni hisoblash
            foreach($items as $product_id => &$item){
                $kirim = isset($remainders[$product_id]) ? $remainders[$product_id]['kirim'] : 0;
                $chiqim = isset($remainders[$product_id]) ? $remainders[$product_id]['chiqim'] : 0;
                $item['qoldiq'] = $kirim - $chiqim;
                
                // Jami miqdorni hisoblash
                $total_weight = 0;
                foreach($regions as $region_id => $region){
                    if(isset($item[$region_id]['product_weight'])){
                        $total_weight += $item[$region_id]['product_weight'];
                    }
                }
                $item['total_weight'] = $total_weight;
                $item['farq'] = $total_weight - $item['qoldiq'];
            }
            unset($item);
        }

        // usort($items, function ($a, $b){
        //     if(isset($a["p_sort"]) and isset($b["p_sort"])){
        //         return $a["p_sort"] > $b["p_sort"];
        //     }
        // });

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.storage.ordersvodAllRegions', compact('items', 'document', 'regions')), 'HTML-ENTITIES', 'UTF-8');
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
                    ->get(['add_large_werehouses.id', 'add_large_werehouses.product_id', 'add_large_werehouses.shop_id', 'products.product_name', 'sizes.size_name', 'add_large_werehouses.weight','add_large_werehouses.cost', 'add_groups.created_at']);
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
                ->first(['add_groups.id', 'add_groups.day_id as group_day_id', 'months.id as month_id', 'add_groups.group_name', 'days.day_number', 'months.month_name', 'years.year_name']);
        $shops = Shop::where('hide', 1)->get();
        return view('storage.ingroup', compact('products', 'productall', 'group', 'id', 'shops'));
    }

    public function addIngroupProduct(Request $request){
        $request->validate([
            'titleid' => 'required|integer|exists:add_groups,id',
            'productid' => 'required|integer|exists:products,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'weight' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'pay' => 'required|numeric|min:0',
        ]);
        $alwId = Add_large_werehouse::create([
            'add_group_id' => $request->titleid,
            'shop_id' => $request->shop_id,
            'product_id' => $request->productid,
            'weight' => $request->weight,
            'cost' => $request->cost
        ])->id;
        $group = Add_group::find($request->titleid);
        debts::create([
            'shop_id' => $request->shop_id,
            'day_id' => $group->day_id,
            'pay' => $request->pay,
            'loan' => $request->weight * $request->cost,
            'hisloan' => 0,
            'row_id' => $alwId
        ]);
        return redirect()->route('storage.ingroup', $request->titleid)->with('status', "Qo'shildi");
    }

    public function editIngroupProduct(Request $request){
        $request->validate([
            'row_id' => 'required|integer|exists:add_large_werehouses,id',
            'group_id' => 'required|integer|exists:add_groups,id',
            'productid' => 'required|integer|exists:products,id',
            'shop_id' => 'required|integer|exists:shops,id',
            'weight' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'pay' => 'required|numeric|min:0',
        ]);
        Add_large_werehouse::where('id', $request->row_id)->update([
            'shop_id' => $request->shop_id,
            'product_id' => $request->productid,
            'weight' => $request->weight,
            'cost' => $request->cost,
        ]);
        $group = Add_group::find($request->group_id);
        debts::updateOrCreate(
            ['row_id' => $request->row_id],
            [
                'shop_id' => $request->shop_id,
                'day_id' => $group->day_id,
                'pay' => $request->pay,
                'loan' => $request->weight * $request->cost,
                'hisloan' => 0
            ]
        );
        return redirect()->route('storage.ingroup', $request->group_id)->with('status', "Tahrirlandi");
    }

    public function deleteIngroupProduct(Request $request){
        $request->validate([
            'row_id' => 'required|integer|exists:add_large_werehouses,id',
            'group_id' => 'required|integer|exists:add_groups,id',
        ]);
        debts::where('row_id', $request->row_id)->delete();
        Add_large_werehouse::where('id', $request->row_id)->delete();
        return redirect()->route('storage.ingroup', $request->group_id)->with('status', "O'chirildi");
    }

    public function bulkDeleteIngroupProducts(Request $request){
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:add_large_werehouses,id',
            'group_id' => 'required|integer|exists:add_groups,id',
        ]);

        // Tanlangan yozuvlarni o'chirish
        foreach($request->ids as $id){
            debts::where('row_id', $id)->delete();
            Add_large_werehouse::where('id', $id)->delete();
        }

        $count = count($request->ids);
        return redirect()->route('storage.ingroup', $request->group_id)
            ->with('status', "{$count} ta mahsulot o'chirildi");
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
        // validation
        $request->validate([
            'catid' => 'required|exists:shops,id',
            'dayid' => 'required|exists:days,id',
            'cash_amount' => 'required|numeric|min:0',
            'card_amount' => 'required|numeric|min:0',
            'transfer_amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create([
            'shop_id' => $request->catid,
            'day_id' => $request->dayid,
            'total_amount' => $request->cash_amount + $request->card_amount + $request->transfer_amount,
            'cash_amount' => $request->cash_amount,
            'card_amount' => $request->card_amount,
            'transfer_amount' => $request->transfer_amount,
            'paid_to_debts' => 0,
            'excess_amount' => 0,
            'payment_type' => 'storage', // Kirim to'lovi
            'image' => $request->image ?? null,
            'description' => $request->description ?? 'Kirim to\'lovi'
        ]);
        
        // Firma bo'yicha barcha mavjud qarzlarni topish (storage qarzlari)
        $existingDebts = debts::where('shop_id', $request->catid)
            ->where('loan', '>', 0)
            ->where('debt_type', 'storage')
            ->orderBy('day_id', 'asc')
            ->get();
        
        $remainingAmount = $request->cash_amount + $request->card_amount + $request->transfer_amount;
        $excessAmount = 0;
        $totalPaidToDebts = 0;
        
        // Mavjud qarzlarni to'lab chiqish
        foreach($existingDebts as $debt) {
            if($remainingAmount > 0) {
                $debtAmount = $debt->loan;
                
                if($remainingAmount >= $debtAmount) {
                    $debt->update([
                        'pay' => $debt->pay + $debtAmount,
                        'loan' => 0,
                        'payment_id' => $payment->id
                    ]);
                    $remainingAmount -= $debtAmount;
                    $totalPaidToDebts += $debtAmount;
                } else {
                    $debt->update([
                        'pay' => $debt->pay + $remainingAmount,
                        'loan' => $debtAmount - $remainingAmount,
                        'payment_id' => $payment->id
                    ]);
                    $totalPaidToDebts += $remainingAmount;
                    $remainingAmount = 0;
                }
            }
        }
        
        // Agar ortiqcha pul qolsa
        if($remainingAmount > 0) {
            $excessAmount = $remainingAmount;
        }

        $payment->update([
            'paid_to_debts' => $totalPaidToDebts,
            'excess_amount' => $excessAmount
        ]);
        
        // Ortiqcha pul uchun yangi qarz yozuvi
        if($excessAmount > 0) {
            debts::create([
                'shop_id' => $request->catid,
                'day_id' => $request->dayid,
                'pay' => $excessAmount,
                'loan' => 0,
                'hisloan' => $excessAmount,
                'debt_type' => 'storage',
                'row_id' => 0,
                'payment_id' => $payment->id
            ]);
        }

        return redirect()->route('storage.payment_history');
    }

    public function createSalePayment(Request $request){
        // validation
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'day_id' => 'required|exists:days,id',
            'cash_amount' => 'required|numeric|min:0',
            'card_amount' => 'required|numeric|min:0',
            'transfer_amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::create([
            'shop_id' => $request->shop_id,
            'day_id' => $request->day_id,
            'total_amount' => $request->cash_amount + $request->card_amount + $request->transfer_amount,
            'cash_amount' => $request->cash_amount,
            'card_amount' => $request->card_amount,
            'transfer_amount' => $request->transfer_amount,
            'paid_to_debts' => 0,
            'excess_amount' => 0,
            'payment_type' => 'sale', // Sotuv to'lovi
            'image' => $request->image ?? null,
            'description' => $request->description ?? 'Sotuv to\'lovi'
        ]);
        
        $remainingAmount = $request->cash_amount + $request->card_amount + $request->transfer_amount;
        $totalPaidToDebts = 0;
        
        // Agar aniq sotuv tanlangan bo'lsa
        if($request->sale_id) {
            $sale = Sale::find($request->sale_id);
            $saleDebt = debts::where('sale_id', $request->sale_id)
                ->where('debt_type', 'sale')
                ->first();
            
            if($sale && $saleDebt && $saleDebt->hisloan > 0) {
                $debtAmount = $saleDebt->hisloan;
                
                if($remainingAmount >= $debtAmount) {
                    // To'liq qarz to'lanadi
                    $sale->update([
                        'paid_amount' => $sale->paid_amount + $debtAmount,
                        'status' => 'paid'
                    ]);
                    
                    // debts jadvalidagi qarzni yangilash
                    $saleDebt->update([
                        'pay' => $debtAmount,
                        'hisloan' => 0,
                        'payment_id' => $payment->id
                    ]);
                    
                    $remainingAmount -= $debtAmount;
                    $totalPaidToDebts += $debtAmount;
                } else {
                    // Qisman qarz to'lanadi
                    $sale->update([
                        'paid_amount' => $sale->paid_amount + $remainingAmount,
                        'status' => 'partial'
                    ]);
                    
                    // debts jadvalidagi qarzni yangilash
                    $saleDebt->update([
                        'pay' => $remainingAmount,
                        'hisloan' => $debtAmount - $remainingAmount,
                        'payment_id' => $payment->id
                    ]);
                    
                    $totalPaidToDebts += $remainingAmount;
                    $remainingAmount = 0;
                }
            }
        } else {
            // Avtomatik taqsimlash - eskiroq qarzlardan boshlab
            $existingDebts = debts::where('shop_id', $request->shop_id)
                ->where('hisloan', '>', 0)
                ->where('debt_type', 'sale')
                ->orderBy('day_id', 'asc')
                ->get();
            
            foreach($existingDebts as $debt) {
                if($remainingAmount > 0) {
                    $debtAmount = $debt->hisloan;
                    
                    if($remainingAmount >= $debtAmount) {
                        // To'liq qarz to'lanadi
                        $debt->update([
                            'pay' => $debt->pay + $debtAmount,
                            'hisloan' => 0,
                            'payment_id' => $payment->id
                        ]);
                        
                        // Sale ni yangilash
                        $sale = Sale::find($debt->sale_id);
                        if($sale) {
                            $sale->update([
                                'paid_amount' => $sale->paid_amount + $debtAmount,
                                'status' => 'paid'
                            ]);
                        }
                        
                        $remainingAmount -= $debtAmount;
                        $totalPaidToDebts += $debtAmount;
                    } else {
                        // Qisman qarz to'lanadi
                        $debt->update([
                            'pay' => $debt->pay + $remainingAmount,
                            'hisloan' => $debtAmount - $remainingAmount,
                            'payment_id' => $payment->id
                        ]);
                        
                        // Sale ni yangilash
                        $sale = Sale::find($debt->sale_id);
                        if($sale) {
                            $sale->update([
                                'paid_amount' => $sale->paid_amount + $remainingAmount,
                                'status' => 'partial'
                            ]);
                        }
                        
                        $totalPaidToDebts += $remainingAmount;
                        $remainingAmount = 0;
                    }
                }
            }
        }
        
        // Ortiqcha pul uchun yangi qarz yozuvi
        if($remainingAmount > 0) {
            debts::create([
                'shop_id' => $request->shop_id,
                'day_id' => $request->day_id,
                'pay' => $remainingAmount,
                'loan' => 0,
                'hisloan' => 0,
                'debt_type' => 'sale',
                'row_id' => 0,
                'payment_id' => $payment->id
            ]);
        }

        $payment->update([
            'paid_to_debts' => $totalPaidToDebts,
            'excess_amount' => $remainingAmount
        ]);

        return redirect()->route('storage.payment_history');
    }

    public function getShopSales($shopId){
        $sales = Sale::join('debts', 'sales.id', '=', 'debts.sale_id')
            ->where('sales.buyer_shop_id', $shopId)
            ->where('debts.hisloan', '>', 0)
            ->where('debts.debt_type', 'sale')
            ->select('sales.id', 'sales.invoice_number', 'debts.hisloan as debt_amount', 'sales.total_amount', 'sales.paid_amount')
            ->orderBy('sales.created_at', 'asc')
            ->get();
        
        return response()->json(['sales' => $sales]);
    }

    public function selectreport($id, $b, $e){
        if($b == 0){
            $b = Day::first()->id;
        }
        if($e == 0){
            $e = Day::orderby('id', 'DESC')->first()->id;
        }
        
        if($id == 0){
            $shops = Shop::all();
        }
        else{
            $shops = Shop::where('id', $id)->get();
        }
        
        $report = [];
        $days = $this->days();

        foreach($shops as $row){
            $name = $shops->find($row->id)->shop_name;
            // $oldpay = debts::where('shop_id', $row->id)->where('day_id', '<', $b)->sum('pay');
            // $oldloan = debts::where('shop_id', $row->id)->where('day_id', '<', $b)->sum('loan');
            
            // Hisobot davriga qadar bo'lgan qarzdorliklar
            $oldStoragePay = debts::where('shop_id', $row->id)
                ->where('day_id', '<', $b)
                ->where('debt_type', 'storage')
                ->sum('pay');
            $oldStorageLoan = debts::where('shop_id', $row->id)
                ->where('day_id', '<', $b)
                ->where('debt_type', 'storage')
                ->sum('loan');
            
            $oldSalePay = debts::where('shop_id', $row->id)
                ->where('day_id', '<', $b)
                ->where('debt_type', 'sale')
                ->sum('pay');
            $oldSaleHisloan = debts::where('shop_id', $row->id)
                ->where('day_id', '<', $b)
                ->where('debt_type', 'sale')
                ->sum('hisloan');
            
            // Hisobot davridagi qarzdorliklar
            $storageDebts = debts::where('debts.shop_id', $row->id)
                ->where('debts.day_id', '>=', $b)
                ->where('debts.day_id', '<=', $e)
                ->where('debts.debt_type', 'storage')
                ->leftjoin('add_large_werehouses', 'debts.row_id', '=', 'add_large_werehouses.id')
                ->leftjoin('products', 'add_large_werehouses.product_id', '=', 'products.id')
                ->leftjoin('payments', 'debts.payment_id', '=', 'payments.id')
                ->select(
                    'debts.*', 
                    'add_large_werehouses.cost as cost', 
                    'products.product_name as productName',
                    'payments.total_amount as payment_amount',
                    'payments.payment_type')
                ->get();
            $saleDebts = debts::where('debts.shop_id', $row->id)
                ->where('debts.day_id', '>=', $b)
                ->where('debts.day_id', '<=', $e)
                ->where('debts.debt_type', 'sale')
                ->leftjoin('sales', 'debts.sale_id', '=', 'sales.id')
                ->leftjoin('payments', 'debts.payment_id', '=', 'payments.id')
                ->select('debts.*', 'sales.invoice_number', 'sales.total_amount as sale_amount', 'payments.total_amount as payment_amount', 'payments.payment_type')
                ->get();
            
            $deb = [
                "shop" => [
                    "name" => $name, 
                    "oldStoragePay" => $oldStoragePay, 
                    "oldStorageLoan" => $oldStorageLoan,
                    "oldSalePay" => $oldSalePay,
                    "oldSaleHisloan" => $oldSaleHisloan
                ],
                "storage_debts" => $storageDebts,
                "sale_debts" => $saleDebts
            ];
            array_push($report, $deb);
            // dd($report);    
        }

        $html = "<table class='table table-light py-4 px-4'>
                    <thead>
                        <tr>
                            <th scope='col'>Tashkilot</th>
                            <th scope='col'>To'lov turi</th>
                            <th scope='col'>To'langan</th>
                            <th scope='col'>Qarzdorlik</th>
                            <th scope='col'>Ma'lumot</th>
                            <th scope='col'>Sana</th>
                        </tr>
                    </thead>
                    <tbody>";
                    
        $totalStoragePay = 0;
        $totalStorageLoan = 0;
        $totalSalePay = 0;
        $totalSaleHisloan = 0;
        
        foreach($report as $shop){
            // Storage qarzdorliklari
            $html .= "<tr class='table-info'>
                        <td><b>".$shop['shop']['name']."</b></td>
                        <td><span class='badge bg-success'><i class='fa fa-arrow-up'></i> Kirim qarzi</span></td>
                        <td><b>".number_format($shop['shop']['oldStoragePay'], 0, ',', ' ')." so'm</b></td>
                        <td><b>".number_format($shop['shop']['oldStorageLoan'], 0, ',', ' ')." so'm</b></td>
                        <td><i>Hisobot davriga qadar</i></td>
                        <td>-</td>
                    </tr>";
            
            foreach($shop['storage_debts'] as $debt){
                $totalStoragePay += $debt->pay;
                $totalStorageLoan += $debt->loan;
                
                $info = "";
                if($debt->product_name) {
                    $info = "<i class='fa fa-box'></i> ".$debt->product_name;
                } elseif($debt->payment_amount) {
                    $info = "<i class='fa fa-credit-card'></i> To'lov: ".number_format($debt->payment_amount, 0, ',', ' ')." so'm";
                }
                
                $html .= "<tr>
                            <td></td>
                            <td><span class='badge bg-success'><i class='fa fa-arrow-up'></i> Kirim qarzi</span></td>
                            <td>".number_format($debt->pay, 0, ',', ' ')." so'm</td>
                            <td>".number_format($debt->loan, 0, ',', ' ')." so'm</td>
                            <td>".$info."</td>
                            <td>".$days->find($debt->day_id)->day_number.".".$days->find($debt->day_id)->month_name.".".$days->find($debt->day_id)->year_name."</td>
                        </tr>";
            }
            
            // Sale qarzdorliklari
            $html .= "<tr class='table-primary'>
                        <td><b>".$shop['shop']['name']."</b></td>
                        <td><span class='badge bg-primary'><i class='fa fa-arrow-down'></i> Sotuv qarzi</span></td>
                        <td><b>".number_format($shop['shop']['oldSalePay'], 0, ',', ' ')." so'm</b></td>
                        <td><b>".number_format($shop['shop']['oldSaleHisloan'], 0, ',', ' ')." so'm</b></td>
                        <td><i>Hisobot davriga qadar</i></td>
                        <td>-</td>
                    </tr>";
            
            foreach($shop['sale_debts'] as $debt){
                $totalSalePay += $debt->pay;
                $totalSaleHisloan += $debt->hisloan;
                
                $info = "";
                if($debt->invoice_number) {
                    $info = "<i class='fa fa-file-invoice'></i> Faktura #".$debt->invoice_number;
                } elseif($debt->payment_amount) {
                    $info = "<i class='fa fa-credit-card'></i> To'lov: ".number_format($debt->payment_amount, 0, ',', ' ')." so'm";
                }
                
                $html .= "<tr>
                            <td></td>
                            <td><span class='badge bg-primary'><i class='fa fa-arrow-down'></i> Sotuv qarzi</span></td>
                            <td>".number_format($debt->pay, 0, ',', ' ')." so'm</td>
                            <td>".number_format($debt->hisloan, 0, ',', ' ')." so'm</td>
                            <td>".$info."</td>
                            <td>".$days->find($debt->day_id)->day_number.".".$days->find($debt->day_id)->month_name.".".$days->find($debt->day_id)->year_name."</td>
                        </tr>";
            }
        }

        $html .= "<tr class='table-warning'>
                    <td><b>JAMI:</b></td>
                    <td><span class='badge bg-warning'><i class='fa fa-calculator'></i> Hisobot</span></td>
                    <td><b>".number_format($totalStoragePay + $totalSalePay, 0, ',', ' ')." so'm</b></td>
                    <td><b>".number_format($totalStorageLoan + $totalSaleHisloan, 0, ',', ' ')." so'm</b></td>
                    <td><b>Hisobot davri</b></td>
                    <td>-</td>
                </tr>";

        $html .= "</tbody></table>";

        return $html;
    }

    public function takinglargebase(){
        $sales = Sale::where('status', 1)->get();
        $days = $this->days();
        $outtypes = Outside_product::all();
        $users = User::where('users.role_id', '!=', 6)->get();
        $products = Product::all();
        $shops = Shop::where('type_id', 2)->get();
        
        // Sale ma'lumotlarini olish
        $res = Sale::select(
                        'sales.id as sale_id',
                        'sales.invoice_number',
                        'sales.total_amount',
                        'sales.paid_amount',
                        'debts.hisloan as debt_amount',
                        'sales.status',
                        'sales.created_at',
                        'shops.shop_name as buyer_shop_name',
                        'days.day_number',
                        'days.month_id',
                        'days.year_id',
                        'users.name as seller_name'
                    )
                    ->leftjoin('shops', 'shops.id', '=', 'sales.buyer_shop_id')
                    ->leftjoin('days', 'days.id', '=', 'sales.day_id')
                    ->leftjoin('users', 'users.id', '=', 'sales.user_id')
                    ->leftjoin('debts', function($join) {
                        $join->on('debts.sale_id', '=', 'sales.id')
                             ->where('debts.debt_type', '=', 'sale');
                    })
                    ->orderby('sales.id', 'DESC')
                    ->paginate(10);

        return view('storage.takinglargebase', compact('res', 'days', 'users', 'outtypes', 'sales', 'products', 'shops'));
    }

    public function addtakinglargebase(Request $request){
        // dd($request->all());
        Take_group::create([
            'contur_id' => 1,
            'day_id' => $request->day_id,
            'taker_id' => $request->user_id,
            'outside_id' => $request->outid,
            'title' => $request->title,
            'description' => ""
        ]);

        return redirect()->route('storage.takinglargebase');
    }
    public function deletetakinglargebase(Request $request){
        Take_group::where('id', $request->gid);
        return redirect()->route('storage.takinglargebase');
    }

    public function shopsHistory(Request $request, $day=0){
        if($day == 0){
            $day = Day::orderBy('id', 'DESC')->first();
        }else{
            $day = Day::where('id', $day)->first();
        }
        $shops = Shop::where('type_id', 1)->with('product')->get();
        $orders = [];
        $months = Month::where('yearid', $day->year->id)->get();
        $days = Day::where('month_id', $day->month_id)
            ->orderBy('day_number', 'ASC')
            ->get();

        // Har bir shop uchun buyurtmalarni bog'cha va maxsulotlar bilan olish
        foreach($shops as $shop){
            $orders[$shop->id] = order_product::where('shop_id', $shop->id)
                ->where('day_id', $day->id)
                ->with(['kinggarden', 'orderProductStructures.product.size'])
                ->get();
        }

        // Barcha maxsulotlar
        $products = Product::with('size')->orderBy('product_name')->get();
        // Barcha bog'chalar
        $kindergartens = Kindgarden::orderBy('kingar_name')->get();

        return view('storage.shopsHistory', compact('shops', 'orders', 'day', 'months', 'days', 'products', 'kindergartens'));
    }

    // Shop uchun maxsulot qo'shish (o'tgan sanalar uchun)
    public function storeShopOrder(Request $request)
    {
        try {
            $request->validate([
                'shop_id' => 'required|integer',
                'day_id' => 'required|integer',
                'kingar_name_id' => 'required|integer',
                'products' => 'required|array',
                'products.*.product_id' => 'required|integer',
                'products.*.weight' => 'required|numeric|min:0',
            ]);

            // Mavjud order_product ni tekshirish (shu shop, shu kun, shu bog'cha uchun)
            $orderProduct = order_product::where('shop_id', $request->shop_id)
                ->where('day_id', $request->day_id)
                ->where('kingar_name_id', $request->kingar_name_id)
                ->first();

            // Agar mavjud bo'lmasa, yangi yaratish
            if (!$orderProduct) {
                $orderProduct = order_product::create([
                    'shop_id' => $request->shop_id,
                    'day_id' => $request->day_id,
                    'kingar_name_id' => $request->kingar_name_id,
                    'order_title' => 'O\'tgan sana uchun qo\'shildi'.$request->shop_id,
                    'document_processes_id' => 1,
                    'note' => $request->note ?? '',
                ]);
            }

            $addedCount = 0;
            $skippedCount = 0;

            // Maxsulotlarni qo'shish
            foreach ($request->products as $product) {
                if ($product['weight'] > 0) {
                    // Maxsulot allaqachon mavjudligini tekshirish
                    $existingStructure = order_product_structure::where('order_product_name_id', $orderProduct->id)
                        ->where('product_name_id', $product['product_id'])
                        ->first();

                    if ($existingStructure) {
                        // Mavjud bo'lsa, o'tkazib yuborish
                        $skippedCount++;
                    } else {
                        // Mavjud bo'lmasa, yangi qo'shish
                        order_product_structure::create([
                            'order_product_name_id' => $orderProduct->id,
                            'product_name_id' => $product['product_id'],
                            'product_weight' => $product['weight'],
                            'actual_weight' => $product['weight'],
                        ]);
                        $addedCount++;
                    }
                }
            }

            $message = $addedCount . ' ta maxsulot qo\'shildi.';
            if ($skippedCount > 0) {
                $message .= ' ' . $skippedCount . ' ta maxsulot allaqachon mavjud edi.';
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // order_product_structure ni o'zgartirish
    public function updateOrderProductStructure(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
                'weight' => 'required|numeric|min:0',
            ]);

            $structure = order_product_structure::find($request->id);

            if (!$structure) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yozuv topilmadi!'
                ], 404);
            }

            $structure->product_weight = $request->weight;
            $structure->actual_weight = $request->weight;
            $structure->save();

            return response()->json([
                'success' => true,
                'message' => 'Muvaffaqiyatli o\'zgartirildi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // order_product_structure ni o'chirish
    public function deleteOrderProductStructure(Request $request)
    {
        try {
            $request->validate([
                'id' => 'required|integer',
            ]);

            $structure = order_product_structure::find($request->id);

            if (!$structure) {
                return response()->json([
                    'success' => false,
                    'message' => 'Yozuv topilmadi!'
                ], 404);
            }

            $structure->delete();

            return response()->json([
                'success' => true,
                'message' => 'Muvaffaqiyatli o\'chirildi!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Shop uchun bog'chalarga yetkazilgan maxsulotlarni olish
    public function getShopDeliveries(Request $request)
    {
        try {
            $shopId = $request->input('shop_id');
            $dayId = $request->input('day_id');

            $deliveries = order_product::where('shop_id', $shopId)
                ->where('day_id', $dayId)
                ->with(['kinggarden', 'orderProductStructures.product.size'])
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'kindergarten' => $order->kinggarden ? $order->kinggarden->kingar_name : 'Noma\'lum',
                        'kingar_name_id' => $order->kingar_name_id,
                        'products' => $order->orderProductStructures->map(function ($structure) {
                            return [
                                'id' => $structure->id,
                                'product_name' => $structure->product ? $structure->product->product_name : 'Noma\'lum',
                                'size' => $structure->product && $structure->product->size ? $structure->product->size->size_name : '',
                                'weight' => $structure->product_weight,
                                'actual_weight' => $structure->actual_weight,
                            ];
                        }),
                        'created_at' => $order->created_at ? $order->created_at->format('d.m.Y H:i') : ''
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $deliveries
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    // Shop uchun maxsulotlar va bog'chalarni olish
    public function getShopProductsAndKindergartens(Request $request)
    {
        try {
            $shopId = $request->input('shop_id');
            $shop = Shop::with(['product.size', 'kindgarden'])->find($shopId);

            if (!$shop) {
                return response()->json([
                    'success' => false,
                    'message' => 'Shop topilmadi!'
                ], 404);
            }

            $products = $shop->product->map(function ($product) {
                return [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'size_name' => $product->size ? $product->size->size_name : 'kg'
                ];
            });

            $kindergartens = $shop->kindgarden->map(function ($kg) {
                return [
                    'id' => $kg->id,
                    'kingar_name' => $kg->kingar_name
                ];
            });

            return response()->json([
                'success' => true,
                'products' => $products,
                'kindergartens' => $kindergartens
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function intakinglargebase(Request $request, $id){
        $res = Take_product::select(
                        'take_products.id as tid',
                        'take_products.product_id',
                        'take_products.sale_id',
                        'products.product_name',
                        'sizes.size_name',
                        'take_products.weight',
                        'take_products.cost',
                    )
                    ->where('take_products.sale_id', $id)
                    ->join('products', 'products.id', '=', 'take_products.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->orderby('take_products.id', 'DESC')
                    ->get();
        
        $products = Product::all();
        $shops = Shop::where('type_id', 2)->get();
        $days = $this->days();
        
        return view('storage.intakinglargebase', compact('res', 'products', 'id', 'shops', 'days'));
    }

    public function intakinglargebasepdf(Request $request, $id){
        $res = Take_product::select(
                        'take_products.id as tid',
                        'take_products.product_id',
                        'take_products.sale_id',
                        'products.product_name',
                        'sizes.size_name',
                        'take_products.weight',
                        'take_products.cost',
                    )
                    ->where('take_products.sale_id', $id)
                    ->join('products', 'products.id', '=', 'take_products.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->orderby('take_products.id', 'DESC')
                    ->get();
        
        $products = Product::all();
        $shops = Shop::where('type_id', 2)->get();
        $days = $this->days();
        
        $dompdf = new Dompdf('UTF-8');
        $options = new \Dompdf\Options();
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsRemoteEnabled(true);
        $options->setDefaultFont('DejaVu Sans');
        $dompdf->setOptions($options);
        
        $html = mb_convert_encoding(view('pdffile.storage.intakinglargebasepdf', compact('res', 'products', 'id', 'shops', 'days')), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();
        $dompdf->stream('sale_products.pdf', ['Attachment' => 0]);
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
                    ->groupBy('take_groups.day_id', 'take_groups.taker_id', 'take_groups.id', 'take_groups.title', 'outside_products.outside_name', 'users.name', 'users.id')
                    ->paginate(10);
       
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
        $document = [];
        $prevmods = [];
        $plus = plus_multi_storage::where('day_id', $request->start)
                ->where('kingarden_name_d', $request->garden)
                ->where('residual', 1)
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
        foreach($plus as $row){
            if(!isset($prevmods[$row->product_name_id])){
                $prevmods[$row->product_name_id] = 0;
            }
            $prevmods[$row->product_name_id] += $row->product_weight;
        }
        $kindergardens = [];
        $kindergardens[] = order_product::where('order_products.day_id', '>=', $request->start)
                    ->where('order_products.day_id', '<=', $request->end)
                    ->where('order_products.kingar_name_id', '=', $request->garden)
                    ->where('order_products.document_processes_id', 5)
                    ->get();
        $items = [];
        $productscount = [];
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
                        $items[$in->product_name_id]['id'] = $in->product_name_id;
                        $productscount[$in->product_name_id] = 0;
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

        $kindgar = Kindgarden::where('id', $request->garden)->first();
        $document['kindgarden'] = $kindgar->kingar_name;
        $nakproducts = [];
        $days = $this->rangeOfDays($request->start, $request->end);
        $document['date'] = $days->last()->day_number."-".$days->last()->month_name."-".$days->last()->year_name;
        foreach($days as $day){
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $kindgar->id)
                    ->leftjoin('active_menus', function($join){
                        $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                    })
                    ->where('active_menus.day_id', $day->id)
                    ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                    ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                    ->get();
            
            foreach($join as $row){
                if(!isset($productscount[$row->product_name_id])){
                    $productscount[$row->product_name_id] = 0;
                }
                $productscount[$row->product_name_id] += ($row->weight * $row->kingar_children_number)/$row->div;
            }
            
        }

        $dompdf = new Dompdf('UTF-8');
        // there is two button in the form. One of them is allreport and the other is nakladnoy
        if($request->has('report')){
            $html = mb_convert_encoding(view('pdffile.storage.allreportpdf', compact('items', 'productscount', 'prevmods', 'document')), 'HTML-ENTITIES', 'UTF-8');
        }
        else if($request->has('nakladnoy')){
            $html = mb_convert_encoding(view('pdffile.storage.nakladnoypdf', compact('items', 'productscount', 'prevmods', 'document')), 'HTML-ENTITIES', 'UTF-8');
        }
		$dompdf->loadHtml($html);
		$dompdf->setPaper('A4', 'landscape');
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
    
    public function addrasxodgroup(Request $request){
        $request->validate([
            'group_name' => 'required|string|max:255',
            'date_id' => 'required|integer|exists:days,id',
            'kingar_name' => 'required|array|min:1',
            'kingar_name.*' => 'integer|exists:kindgardens,id'
        ]);

        // Har bir tanlangan bog'cha uchun alohida order yaratamiz
        foreach($request->kingar_name as $garden_id) {
            order_product::create([
                'kingar_name_id' => $garden_id,
                'day_id' => $request->date_id,
                'order_title' => $request->group_name,
                'document_processes_id' => 3,
                'shop_id' => 0,
            ]);
        }

        $count = count($request->kingar_name);
        return redirect()->route('storage.addmultisklad')->with('status', "{$count}ta muassasa uchun buyurtma muvaffaqiyatli yaratildi!");
    }
    
    public function plusproduct(Request $request){
        $request->validate([
            'titleid' => 'required|integer|exists:order_products,id',
            'orders' => 'required|array'
        ]);

        foreach($request->orders as $product_id => $weight) {
            if(!empty($weight) && $weight > 0) {
                // Maxsulot va order ma'lumotlarini olish
                $product = Product::find($product_id);
                $order = order_product::find($request->titleid);
                
                // data_of_weight uchun asosiy ma'lumotlarni to'plash
                $dataOfWeight = [
                    'product_id' => $product_id,
                    'product_name' => $product ? $product->product_name : 'Noma\'lum maxsulot',
                    'total_weight' => $weight,
                    'order_id' => $request->titleid,
                    'order_title' => $order ? $order->order_title : '',
                    'added_manually' => true,
                    'added_at' => now()->toISOString(),
                    'summary' => [
                        'manual_addition' => true,
                        'weight' => $weight
                    ]
                ];
                
                // Avval bunday product mavjudligini tekshiramiz
                $existing = order_product_structure::where('order_product_name_id', $request->titleid)
                    ->where('product_name_id', $product_id)
                    ->first();

                if($existing) {
                    // Mavjud bo'lsa, og'irlikni yangilaymiz
                    $existing->update([
                        'product_weight' => $existing->product_weight + $weight
                    ]);
                } else {
                    // Mavjud bo'lmasa, yangi record yaratamiz
                    order_product_structure::create([
                        'order_product_name_id' => $request->titleid,
                        'product_name_id' => $product_id,
                        'product_weight' => $weight,
                        'data_of_weight' => json_encode($dataOfWeight, JSON_UNESCAPED_UNICODE)
                    ]);
                }
            }
        }

        return redirect()->route('storage.orderitem', $request->titleid)->with('status', 'Maxsulotlar muvaffaqiyatli qo\'shildi!');
    }

    public function paymentHistory(Request $request){
        $shops = Shop::where('type_id', 2)->get();
        $days = $this->days();
        
        $payments = Payment::select(['payments.*', 'shops.shop_name', 'days.day_number', 'days.month_id', 'days.year_id'])
            ->leftjoin('shops', 'payments.shop_id', '=', 'shops.id')
            ->leftjoin('days', 'payments.day_id', '=', 'days.id')
            ->orderBy('payments.created_at', 'DESC')
            ->paginate(50);
        
        return view('storage.payment_history', compact('payments', 'shops', 'days'));
    }

    public function deletePayment(Request $request){
        
        $payment = Payment::find($request->payment_id);
        // dd($payment);
        // Payment o'chirish (deleting event avtomatik ishlaydi)
        $payment->delete();
        
        return redirect()->route('storage.payment_history')->with('success', 'To\'lov muvaffaqiyatli o\'chirildi');
    }

    public function editPayment($id){
        $payment = Payment::findOrFail($id);
        $shops = Shop::where('id', $payment->shop_id)->get();
        $days = $this->days();
        
        return view('storage.edit_payment', compact('payment', 'shops', 'days'));
    }

    public function updatePayment(Request $request, $id){
        $request->validate([
            'shop_id' => 'required|exists:shops,id',
            'day_id' => 'required|exists:days,id',
            'cash_amount' => 'required|numeric|min:0',
            'card_amount' => 'required|numeric|min:0',
            'transfer_amount' => 'required|numeric|min:0',
        ]);

        $payment = Payment::findOrFail($id);
        $oldTotalAmount = $payment->total_amount;
        $newTotalAmount = $request->cash_amount + $request->card_amount + $request->transfer_amount;

        // Eski to'lovni orqaga qaytarish
        if ($payment->paid_to_debts > 0) {
            if ($payment->payment_type == 'storage') {
                // Storage to'lovi uchun - loan qarzlarni orqaga qaytarish
                $existingDebts = debts::where('shop_id', $payment->shop_id)
                    ->where('loan', '>=', 0)
                    ->where('debt_type', 'storage')
                    ->where('payment_id', $payment->id)
                    ->orderBy('day_id', 'desc')
                    ->get();
                
                $remainingAmount = $payment->paid_to_debts;
                
                foreach ($existingDebts as $debt) {
                    if ($remainingAmount > 0) {
                        $currentPay = $debt->pay;
                        
                        if ($currentPay > 0) {
                            if ($remainingAmount >= $currentPay) {
                                $debt->update([
                                    'pay' => 0,
                                    'loan' => $debt->loan + $currentPay,
                                    'payment_id' => null
                                ]);
                                $remainingAmount -= $currentPay;
                            } else {
                                $debt->update([
                                    'pay' => $currentPay - $remainingAmount,
                                    'loan' => $debt->loan + $remainingAmount
                                ]);
                                $remainingAmount = 0;
                            }
                        }
                    }
                }
            } elseif ($payment->payment_type == 'sale') {
                // Sale to'lovi uchun - hisloan qarzlarni orqaga qaytarish
                $existingDebts = debts::where('shop_id', $payment->shop_id)
                    ->where('hisloan', '>=', 0)
                    ->where('debt_type', 'sale')
                    ->where('payment_id', $payment->id)
                    ->orderBy('day_id', 'desc')
                    ->get();
                
                $remainingAmount = $payment->paid_to_debts;
                
                foreach ($existingDebts as $debt) {
                    if ($remainingAmount > 0) {
                        $currentPay = $debt->pay;
                        
                        if ($currentPay > 0) {
                            if ($remainingAmount >= $currentPay) {
                                $debt->update([
                                    'pay' => 0,
                                    'hisloan' => $debt->hisloan + $currentPay,
                                    'payment_id' => null
                                ]);
                                
                                // Sale ni ham yangilash
                                $sale = Sale::find($debt->sale_id);
                                if ($sale) {
                                    $sale->update([
                                        'paid_amount' => $sale->paid_amount - $currentPay,
                                        'status' => $sale->paid_amount - $currentPay > 0 ? 'partial' : 'pending'
                                    ]);
                                }
                                
                                $remainingAmount -= $currentPay;
                            } else {
                                $debt->update([
                                    'pay' => $currentPay - $remainingAmount,
                                    'hisloan' => $debt->hisloan + $remainingAmount
                                ]);
                                
                                // Sale ni ham yangilash
                                $sale = Sale::find($debt->sale_id);
                                if ($sale) {
                                    $sale->update([
                                        'paid_amount' => $sale->paid_amount - $remainingAmount,
                                        'status' => $sale->paid_amount - $remainingAmount > 0 ? 'partial' : 'pending'
                                    ]);
                                }
                                
                                $remainingAmount = 0;
                            }
                        }
                    }
                }
            }
        }
        
        // Ortiqcha pulni orqaga qaytarish
        if ($payment->excess_amount > 0) {
            debts::where('shop_id', $payment->shop_id)
                ->where('day_id', $payment->day_id)
                ->where('hisloan', $payment->excess_amount)
                ->where('loan', 0)
                ->where('pay', $payment->excess_amount)
                ->where('debt_type', $payment->payment_type)
                ->where('payment_id', $payment->id)
                ->delete();
        }

        // To'lovni yangilash
        $payment->update([
            'shop_id' => $request->shop_id,
            'day_id' => $request->day_id,
            'total_amount' => $newTotalAmount,
            'cash_amount' => $request->cash_amount,
            'card_amount' => $request->card_amount,
            'transfer_amount' => $request->transfer_amount,
            'paid_to_debts' => 0,
            'excess_amount' => 0,
            'image' => $request->image ?? $payment->image,
            'description' => $request->description ?? $payment->description
        ]);

        // Yangi to'lovni qarzlarga taqsimlash
        $remainingAmount = $newTotalAmount;
        $totalPaidToDebts = 0;
        $excessAmount = 0;

        if ($payment->payment_type == 'storage') {
            // Storage to'lovi uchun - loan qarzlarni to'lash
            $existingDebts = debts::where('shop_id', $request->shop_id)
                ->where('loan', '>', 0)
                ->where('debt_type', 'storage')
                ->orderBy('day_id', 'asc')
                ->get();
            
            foreach($existingDebts as $debt) {
                if($remainingAmount > 0) {
                    $debtAmount = $debt->loan;
                    
                    if($remainingAmount >= $debtAmount) {
                        $debt->update([
                            'pay' => $debt->pay + $debtAmount,
                            'loan' => 0,
                            'payment_id' => $payment->id
                        ]);
                        $remainingAmount -= $debtAmount;
                        $totalPaidToDebts += $debtAmount;
                    } else {
                        $debt->update([
                            'pay' => $debt->pay + $remainingAmount,
                            'loan' => $debtAmount - $remainingAmount,
                            'payment_id' => $payment->id
                        ]);
                        $totalPaidToDebts += $remainingAmount;
                        $remainingAmount = 0;
                    }
                }
            }
        } elseif ($payment->payment_type == 'sale') {
            // Sale to'lovi uchun - hisloan qarzlarni to'lash
            $existingDebts = debts::where('shop_id', $request->shop_id)
                ->where('hisloan', '>', 0)
                ->where('debt_type', 'sale')
                ->orderBy('day_id', 'asc')
                ->get();
            
            foreach($existingDebts as $debt) {
                if($remainingAmount > 0) {
                    $debtAmount = $debt->hisloan;
                    
                    if($remainingAmount >= $debtAmount) {
                        $debt->update([
                            'pay' => $debt->pay + $debtAmount,
                            'hisloan' => 0,
                            'payment_id' => $payment->id
                        ]);
                        
                        // Sale ni yangilash
                        $sale = Sale::find($debt->sale_id);
                        if($sale) {
                            $sale->update([
                                'paid_amount' => $sale->paid_amount + $debtAmount,
                                'status' => 'paid'
                            ]);
                        }
                        
                        $remainingAmount -= $debtAmount;
                        $totalPaidToDebts += $debtAmount;
                    } else {
                        $debt->update([
                            'pay' => $debt->pay + $remainingAmount,
                            'hisloan' => $debtAmount - $remainingAmount,
                            'payment_id' => $payment->id
                        ]);
                        
                        // Sale ni yangilash
                        $sale = Sale::find($debt->sale_id);
                        if($sale) {
                            $sale->update([
                                'paid_amount' => $sale->paid_amount + $remainingAmount,
                                'status' => 'partial'
                            ]);
                        }
                        
                        $totalPaidToDebts += $remainingAmount;
                        $remainingAmount = 0;
                    }
                }
            }
        }

        // Ortiqcha pul uchun yangi qarz yozuvi
        if($remainingAmount > 0) {
            $excessAmount = $remainingAmount;
            debts::create([
                'shop_id' => $request->shop_id,
                'day_id' => $request->day_id,
                'pay' => $excessAmount,
                'loan' => 0,
                'hisloan' => $payment->payment_type == 'storage' ? $excessAmount : 0,
                'debt_type' => $payment->payment_type,
                'row_id' => 0,
                'payment_id' => $payment->id
            ]);
        }

        $payment->update([
            'paid_to_debts' => $totalPaidToDebts,
            'excess_amount' => $excessAmount
        ]);

        return redirect()->route('storage.payment_history')->with('success', 'To\'lov muvaffaqiyatli yangilandi');
    }

    public function createSaleWithTakeGroup(Request $request){
        // return response()->json(['success' => false, 'data' => $request->all()]);
        $total_amount = 0;
        foreach($request->products as $product){
            $total_amount += $product['cost'] * $product['weight'];
        }
        if($total_amount != $request->total_amount){
            return response()->json(['success' => false, 'message' => 'Jami summa to\'g\'ri kiritilmagan']);
        }
        try {
            DB::beginTransaction();
            $sale = Sale::create([
                'buyer_shop_id' => $request->buyer_shop_id,
                'day_id' => $request->day_id,
                'total_amount' => $total_amount,
                'paid_amount' => $request->paid_amount,
                'notes' => $request->notes,
            ]);
            $takegroup = Take_group::create([
                'contur_id' => 1,
                'day_id' => $request->day_id,
                'taker_id' => $request->user_id ?? 0,
                'outside_id' => $request->outid,
                'title' => "Sotish".$request->buyer_shop_id."-".$request->day_id,
                'description' => $request->notes,
            ]);
            foreach($request->products as $product){
                Take_product::create([
                    'takegroup_id' => $takegroup->id,
                    'sale_id' => $sale->id,
                    'product_id' => $product['product_id'],
                    'weight' => $product['weight'],
                    'cost' => $product['cost'],
                ]);
            }
            // Qarz yaratish (agar to'liq to'lanmagan bo'lsa)
            if ($request->total_amount > $request->paid_amount) {
                $debtAmount = $request->total_amount - $request->paid_amount;
                
                debts::create([
                    'shop_id' => $request->buyer_shop_id,
                    'sale_id' => $sale->id,
                    'day_id' => $request->day_id,
                    'pay' => 0,
                    'loan' => 0, // bizning qarzimiz
                    'hisloan' => $debtAmount, // shop qarzi
                    'debt_type' => 'sale', // Bu sotuv qarzi
                    'row_id' => 0
                ]);
            }
            
            DB::commit();
            
            return response()->json(['success' => true, 'sale_id' => $sale->id, 'takegroup_id' => $takegroup->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    public function deleteOrderProduct($order_title){
        $order_product = order_product::where('order_title', $order_title)->get();
        foreach($order_product as $row){
            if($row->document_processes_id == 3){
                $row->delete();
            }
        }
        return redirect()->back()->with('success', $order_title.' buyurtmasi muvaffaqiyatli o\'chirildi');
    }

    public function deleteSale(Request $request){
        try {
            DB::beginTransaction();
            
            $sale = Sale::find($request->sale_id);
            
            if (!$sale) {
                return response()->json(['success' => false, 'message' => 'Sotuv topilmadi']);
            }
            
            // Faqat pending statusdagi sotuvlarni o'chirish mumkin
            if ($sale->status != 'pending') {
                return response()->json(['success' => false, 'message' => 'Faqat pending statusdagi sotuvlarni o\'chirish mumkin']);
            }
            
            // Sale bilan bog'liq debts yozuvlarini o'chirish
            debts::where('sale_id', $sale->id)->delete();
            
            // Sale bilan bog'liq take_products yozuvlarini o'chirish
            Take_product::where('sale_id', $sale->id)->delete();
            
            // Sale ni o'chirish
            $sale->delete();
            
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Sotuv muvaffaqiyatli o\'chirildi']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => 'Xatolik: ' . $e->getMessage()]);
        }
    }

    public function modsofproducts(Request $request){
        $yearid = Year::where('year_active', 1)->first()->id;
        $days = $this->daysthisyear($yearid);
        
        return view('storage.modsofproducts', ['days' => $days]);
    }

    public function getreportlargebase(Request $request){    
        $yearid = Year::where('year_active', 1)->first()->id;
        $start = $this->daysthisyear($yearid)->last()->id;
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $start)
                    ->where('add_groups.day_id', '<=', $request->lastid)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        
        $alladd = [];
        $t = 0;
        foreach($addlarch as $row){
            if(!isset($alladd[$row->product_id])){
                $alladd[$row->product_id]['middlecost'] = 0;
                $mc = Add_large_werehouse::where('add_large_werehouses.product_id', $row->product_id)
                        ->where('add_groups.day_id', '>=', $start)
                        ->where('add_groups.day_id', '<=', $request->lastid)
                        ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                        ->avg('cost');
                $alladd[$row->product_id]['weight'] = 0;
                $alladd[$row->product_id]['minusweight'] = 0;
                $alladd[$row->product_id]['p_name'] = $row->product_name;
                $alladd[$row->product_id]['size_name'] = $row->size_name;
                $alladd[$row->product_id]['p_sort'] = $row->sort;
                $alladd[$row->product_id]['middlecost'] = $mc;
            }
            $alladd[$row->product_id]['weight'] += $row->weight; 
        }
        $minuslarch = order_product_structure::where('order_products.day_id', '>=', $start)
                    ->where('order_products.day_id', '<=', $request->lastid)
                    ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(["order_product_structures.product_name_id", "order_product_structures.product_weight", "products.product_name", "products.sort", "sizes.size_name" ]);
        
        foreach($minuslarch as $row){
            if(empty($alladd[$row->product_name_id])){
                $alladd[$row->product_name_id]['middlecost'] = 0;
                $alladd[$row->product_name_id]['weight'] = 0;
                $alladd[$row->product_name_id]['minusweight'] = 0;
                $alladd[$row->product_name_id]['p_name'] = $row->product_name;
                $alladd[$row->product_name_id]['size_name'] = $row->size_name;
                $alladd[$row->product_name_id]['p_sort'] = $row->sort;
            }
            $alladd[$row->product_name_id]['minusweight'] += $row->product_weight;
        }
        
        // $nochs = Number_children::where('day_id', '>=', $start)
        //             ->join('kindgardens', 'kindgardens.id', '=', 'number_childrens.kingar_name_id')
        //             ->where('day_id', '<=', $request->lastid)
        //             ->get();
        // $bymenus = Active_menu::where('day_id', '>=', $start)
        //                     ->where('day_id', '<=', $request->lastid)->get();
                            
        // $ages = Age_range::all();
        // $products = Product::all();
        // $totalproducts = [];
        // foreach($ages as $age){
        //     $foundmenu = $bymenus->where('age_range_id', $age->id);
        //     foreach($products as $prd){
        //     	if(!isset($totalproducts[$prd->id])){
        //               $totalproducts[$prd->id] = 0;
        //         } 
        //     	$w = $foundmenu->where('product_name_id', $prd->id)->sum('weight');
        //     	$totalproducts[$prd->id] += ($w * $nochs->where('king_age_name_id', $age->id)->sum('kingar_children_number')) / $prd->div;
        //     }
            // foreach($foundmenu as $menu){
            //     if(!isset($totalproducts[$menu->product_name_id])){
            //         $totalproducts[$menu->product_name_id] = 0;
            //     }               
            //     $totalproducts[$menu->product_name_id] += ($menu->weight * $noch->kingar_children_number) / $products->find($menu->product_name_id)->div;
            // }
        // }
        // return json_encode($totalproducts);
        
                            
        usort($alladd, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });
        
        $html = "<table style='background-color: white' class='table'>
                <thead>
                    <tr>
                        <th rowspan='2'>Махсулот номи</th>
                        <th rowspan='2'>Ул.бир</th>
                        <th colspan='3'>Кирим</th>
                        <th colspan='3'>Чиқим</th>
                        <th colspan='3'>Қолдиқ</th>
                    </tr>
                    <tr>
                        <th>Микдори</th>
                        <th>Уртача нархи</th>
                        <th>Суммаси</th>
                        <th>Микдори</th>
                        <th>Нархи</th>
                        <th>Суммаси</th>
                        <th>Микдори</th>
                        <th>Нархи</th>
                        <th>Суммаси</th>
                    </tr>
                </thead>
                <tbody>";
        $taking = 0;
        $giving = 0;
        $mod = 0;
        foreach($alladd as $key => $row){
            $taking = $taking + $row["weight"] * $row["middlecost"];
            $giving = $giving + $row["minusweight"] * $row["middlecost"];
            $mod = $mod + ($row["weight"]-$row["minusweight"]) * $row["middlecost"];
            $html = $html."
                <tr>
                    <td>".$row["p_name"]."</td>
                    <td>".$row["size_name"]."</td>
                    <td>".sprintf('%0.2f', $row["weight"])."</td>
                    <td>".sprintf('%0.2f', $row["middlecost"])."</td>   
                    <td>".sprintf('%0.2f', $row["weight"] * $row["middlecost"])."</td>
                    <td>".sprintf('%0.2f', $row["minusweight"])."</td>
                    <td>".sprintf('%0.2f', $row["middlecost"])."</td>   
                    <td>".sprintf('%0.2f', $row["minusweight"] * $row["middlecost"])."</td>
                    <td>".sprintf('%0.2f', $row["weight"]-$row["minusweight"])."</td>
                    <td>".sprintf('%0.2f', $row["middlecost"])."</td>   
                    <td>".sprintf('%0.2f', ($row["weight"]-$row["minusweight"]) * $row["middlecost"])."</td>    
                </tr>
            ";  
        }


        $html = $html."
            <tr>
                <td><b>Jami:</b></td>
                <td></td>
                <td></td>
                <td></td>   
                <td><b>".sprintf('%0.2f', $taking)."</b></td>
                <td></td>
                <td></td>   
                <td><b>".sprintf('%0.2f', $giving)."</b></td>
                <td></td>
                <td></td>   
                <td><b>".sprintf('%0.2f',$mod)."</b></td>    
            </tr>
        ";
        $html = $html."</tbody>
                </table>
                ";
            
        return $html;
    }
    
    public function getreportlargebasePDF(Request $request){    
        $yearid = Year::where('year_active', 1)->first()->id;
        $start = $this->daysthisyear($yearid)->last()->id;
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $start)
                    ->where('add_groups.day_id', '<=', $request->lastid)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        
        $alladd = [];
        $t = 0;
        foreach($addlarch as $row){
            if(!isset($alladd[$row->product_id])){
                $alladd[$row->product_id]['middlecost'] = 0;
                $mc = Add_large_werehouse::where('add_large_werehouses.product_id', $row->product_id)
                        ->where('add_groups.day_id', '>=', $start)
                        ->where('add_groups.day_id', '<=', $request->lastid)
                        ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                        ->avg('cost');
                $alladd[$row->product_id]['weight'] = 0;
                $alladd[$row->product_id]['minusweight'] = 0;
                $alladd[$row->product_id]['p_name'] = $row->product_name;
                $alladd[$row->product_id]['size_name'] = $row->size_name;
                $alladd[$row->product_id]['p_sort'] = $row->sort;
                $alladd[$row->product_id]['middlecost'] = $mc;
            }
            $alladd[$row->product_id]['weight'] += $row->weight; 
        }
        $minuslarch = order_product_structure::where('order_products.day_id', '>=', $start)
                    ->where('order_products.day_id', '<=', $request->lastid)
                    ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(["order_product_structures.product_name_id", "order_product_structures.product_weight", "products.product_name", "products.sort", "sizes.size_name" ]);
        
        foreach($minuslarch as $row){
            if(empty($alladd[$row->product_name_id])){
                $alladd[$row->product_name_id]['middlecost'] = 0;
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
        
        // Sana ma'lumotlarini olish
        $days = $this->daysthisyear($yearid);
        $startDay = $days->last();
        $endDay = $days->where('id', $request->lastid)->first();
        
        $reportData = [
            'products' => $alladd,
            'start_date' => $startDay->day_number . '.' . $startDay->month_name . '.' . $startDay->year_name,
            'end_date' => $endDay->day_number . '.' . $endDay->month_name . '.' . $endDay->year_name,
            'total_taking' => 0,
            'total_giving' => 0,
            'total_mod' => 0
        ];
        
        // Jami summalarni hisoblash
        foreach($alladd as $row){
            $reportData['total_taking'] += $row["weight"] * $row["middlecost"];
            $reportData['total_giving'] += $row["minusweight"] * $row["middlecost"];
            $reportData['total_mod'] += ($row["weight"]-$row["minusweight"]) * $row["middlecost"];
        }
        
        $dompdf = new Dompdf('UTF-8');
        $html = mb_convert_encoding(view('pdffile.storage.modsofproductsPdf', compact('reportData')), 'HTML-ENTITIES', 'UTF-8');
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('maxsulotlar_hisoboti.pdf', ['Attachment' => 0]);
    }

    public function separateOrders(Request $request)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();
            
            // Asosiy buyurtmani olish
            $parentOrder = order_product::findOrFail($request->order_id);
            
            // Buyurtma maxsulotlarini olish
            $orderStructures = order_product_structure::where('order_product_name_id', $parentOrder->id)
                ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                ->join('product_categories', 'product_categories.id', '=', 'products.category_name_id')
                ->select('order_product_structures.*', 'products.category_name_id', 'product_categories.pro_cat_name')
                ->get();
                
            // Kategoriyalar bo'yicha guruhlash
            $groupedProducts = $orderStructures->groupBy('category_name_id');
            // dd($groupedProducts);
            foreach($groupedProducts as $categoryId => $products) {
                // Har bir kategoriya uchun yangi buyurtma yaratish
                // agar buyurtma mavjud bo'lmasa, uni yaratish
                // dd($products);
                $existingOrder = order_product::where('parent_id', $parentOrder->id)->get();

                if($existingOrder->count() != $groupedProducts->count()){
                    $newOrder = order_product::create([
                        'kingar_name_id' => $parentOrder->kingar_name_id,
                        'day_id' => $parentOrder->day_id,
                        'order_title' => $parentOrder->order_title . " (Kategoriya: " . $products[0]->pro_cat_name . ")",
                        'document_processes_id' => $parentOrder->document_processes_id,
                        'data_of_weight' => $parentOrder->data_of_weight,
                        'to_menus' => $parentOrder->to_menus,
                        'shop_id' => 0,
                        'parent_id' => $parentOrder->id
                    ]);
                }else{
                    $newOrder = $existingOrder;
                }
    
                // Kategoriyaga tegishli maxsulotlarni yangi buyurtmaga o'tkazish
                foreach($products as $product) {
                    order_product_structure::where('id', $product->id)
                        ->update(['order_product_name_id' => $newOrder->id]);
                }
            }
            
            DB::commit();
            return redirect()->back()->with('status', 'Buyurtma muvaffaqiyatli ajratildi!');
            dd($e);
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function shopsHistoryReportPdf(Request $request)
    {
        try {
            // Filterlarni olish
            $dateType = $request->input('date_type', 'daily'); // daily, monthly, range
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $regionId = $request->input('region_id');
            $shopId = $request->input('shop_id');

            // Date range aniqlash
            $dayQuery = Day::query()->with(['month', 'year']);

            if ($dateType === 'daily' && $startDate) {
                // Sanani parse qilib Day modelidan topish
                $dateObj = \Carbon\Carbon::parse($startDate);
                $day = Day::whereHas('month', function($q) use ($dateObj) {
                    $q->where('month_active', $dateObj->month);
                })
                ->whereHas('year', function($q) use ($dateObj) {
                    $q->where('year_name', $dateObj->year);
                })
                ->where('day_number', $dateObj->day)
                ->first();

                if ($day) {
                    $dayQuery->where('id', $day->id);
                } else {
                    // Agar sana topilmasa, oxirgi kunni olish
                    $dayQuery->orderBy('id', 'DESC')->limit(1);
                }
            } elseif ($dateType === 'monthly' && $startDate) {
                // Oylik hisobot uchun shu oydagi barcha kunlarni olish
                $dateObj = \Carbon\Carbon::parse($startDate);
                $dayQuery->whereHas('month', function($q) use ($dateObj) {
                    $q->where('month_active', $dateObj->month);
                })
                ->whereHas('year', function($q) use ($dateObj) {
                    $q->where('year_name', $dateObj->year);
                });
            } elseif ($dateType === 'range' && $startDate && $endDate) {
                // Sanalar oralig'i uchun
                $startDateObj = \Carbon\Carbon::parse($startDate);
                $endDateObj = \Carbon\Carbon::parse($endDate);

                // Boshlanish va tugash kunlarini topish
                $startDay = Day::whereHas('month', function($q) use ($startDateObj) {
                    $q->where('month_active', $startDateObj->month);
                })
                ->whereHas('year', function($q) use ($startDateObj) {
                    $q->where('year_name', $startDateObj->year);
                })
                ->where('day_number', $startDateObj->day)
                ->first();

                $endDay = Day::whereHas('month', function($q) use ($endDateObj) {
                    $q->where('month_active', $endDateObj->month);
                })
                ->whereHas('year', function($q) use ($endDateObj) {
                    $q->where('year_name', $endDateObj->year);
                })
                ->where('day_number', $endDateObj->day)
                ->first();

                if ($startDay && $endDay) {
                    $dayQuery->whereBetween('id', [$startDay->id, $endDay->id]);
                }
            } else {
                // Default: oxirgi kun
                $day = Day::orderBy('id', 'DESC')->first();
                $dayQuery->where('id', $day->id);
            }

            $days = $dayQuery->get();
            $dayIds = $days->pluck('id')->toArray();

            // order_product ma'lumotlarini olish (faqat type_id = 1 bo'lgan shoplar)
            $ordersQuery = order_product::with([
                'kinggarden.region',
                'orderProductStructures.product.size',
                'shop'
            ])
            ->whereIn('day_id', $dayIds)
            ->whereNotNull('shop_id')
            ->whereHas('shop', function($q) {
                $q->where('type_id', 1);
            });

            if ($shopId) {
                $ordersQuery->where('shop_id', $shopId);
            }

            $orders = $ordersQuery->get();

            // Ma'lumotlarni tuman bo'yicha guruplash
            $reportData = [];

            foreach ($orders as $order) {
                if (!$order->kinggarden || !$order->kinggarden->region) {
                    continue;
                }

                $regionName = $order->kinggarden->region->region_name;
                $kindgardenId = $order->kinggarden->id;
                $numberOrg = $order->kinggarden->number_of_org ?? 'N/A';

                // Region filterni tekshirish
                if ($regionId && $order->kinggarden->region_id != $regionId) {
                    continue;
                }

                if (!isset($reportData[$regionName])) {
                    $reportData[$regionName] = [
                        'kindgardens' => [],
                        'products' => []
                    ];
                }

                // Bog'chani qo'shish
                if (!isset($reportData[$regionName]['kindgardens'][$kindgardenId])) {
                    $reportData[$regionName]['kindgardens'][$kindgardenId] = [
                        'number_org' => $numberOrg,
                        'name' => $order->kinggarden->kingar_name
                    ];
                }

                // Maxsulotlarni qo'shish
                foreach ($order->orderProductStructures as $structure) {
                    $productId = $structure->product_name_id;
                    $productName = $structure->product->product_name ?? 'N/A';
                    $sizeName = $structure->product->size->size_name ?? 'N/A';
                    $weight = $structure->product_weight ?? 0;

                    if (!isset($reportData[$regionName]['products'][$productId])) {
                        $reportData[$regionName]['products'][$productId] = [
                            'name' => $productName,
                            'size' => $sizeName,
                            'kindgardens' => []
                        ];
                    }

                    if (!isset($reportData[$regionName]['products'][$productId]['kindgardens'][$kindgardenId])) {
                        $reportData[$regionName]['products'][$productId]['kindgardens'][$kindgardenId] = 0;
                    }

                    $reportData[$regionName]['products'][$productId]['kindgardens'][$kindgardenId] += $weight;
                }
            }

            // PDF generatsiya
            $dompdf = new \Dompdf\Dompdf();
            $options = new \Dompdf\Options();
            $options->setIsHtml5ParserEnabled(true);
            $options->setIsRemoteEnabled(true);
            $options->setDefaultFont('DejaVu Sans');
            $dompdf->setOptions($options);

            $html = mb_convert_encoding(
                view('pdffile.storage.shopsHistoryReport', compact('reportData', 'days', 'dateType')),
                'HTML-ENTITIES',
                'UTF-8'
            );

            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();

            // Fayl nomini yaratish
            $fileName = 'shops_report_' . date('Y-m-d_H-i-s') . '.pdf';
            $filePath = storage_path('shopsHistory/' . $fileName);

            // Papka mavjudligini tekshirish
            if (!file_exists(storage_path('shopsHistory'))) {
                mkdir(storage_path('shopsHistory'), 0777, true);
            }

            // Faylni saqlash
            file_put_contents($filePath, $dompdf->output());

            // Faylni yuklab olish
            return $dompdf->stream($fileName, ['Attachment' => 0]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function shopsHistoryReportExcel(Request $request)
    {
        try {
            // Filterlarni olish
            $dateType = $request->input('date_type', 'daily');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $regionId = $request->input('region_id');
            $shopId = $request->input('shop_id');

            // Date range aniqlash
            $dayQuery = Day::query()->with(['month', 'year']);

            if ($dateType === 'daily' && $startDate) {
                // Sanani parse qilib Day modelidan topish
                $dateObj = \Carbon\Carbon::parse($startDate);
                $day = Day::whereHas('month', function($q) use ($dateObj) {
                    $q->where('month_active', $dateObj->month);
                })
                ->whereHas('year', function($q) use ($dateObj) {
                    $q->where('year_name', $dateObj->year);
                })
                ->where('day_number', $dateObj->day)
                ->first();

                if ($day) {
                    $dayQuery->where('id', $day->id);
                } else {
                    // Agar sana topilmasa, oxirgi kunni olish
                    $dayQuery->orderBy('id', 'DESC')->limit(1);
                }
            } elseif ($dateType === 'monthly' && $startDate) {
                // Oylik hisobot uchun shu oydagi barcha kunlarni olish
                $dateObj = \Carbon\Carbon::parse($startDate);
                $dayQuery->whereHas('month', function($q) use ($dateObj) {
                    $q->where('month_active', $dateObj->month);
                })
                ->whereHas('year', function($q) use ($dateObj) {
                    $q->where('year_name', $dateObj->year);
                });
            } elseif ($dateType === 'range' && $startDate && $endDate) {
                // Sanalar oralig'i uchun
                $startDateObj = \Carbon\Carbon::parse($startDate);
                $endDateObj = \Carbon\Carbon::parse($endDate);

                // Boshlanish va tugash kunlarini topish
                $startDay = Day::whereHas('month', function($q) use ($startDateObj) {
                    $q->where('month_active', $startDateObj->month);
                })
                ->whereHas('year', function($q) use ($startDateObj) {
                    $q->where('year_name', $startDateObj->year);
                })
                ->where('day_number', $startDateObj->day)
                ->first();

                $endDay = Day::whereHas('month', function($q) use ($endDateObj) {
                    $q->where('month_active', $endDateObj->month);
                })
                ->whereHas('year', function($q) use ($endDateObj) {
                    $q->where('year_name', $endDateObj->year);
                })
                ->where('day_number', $endDateObj->day)
                ->first();

                if ($startDay && $endDay) {
                    $dayQuery->whereBetween('id', [$startDay->id, $endDay->id]);
                }
            } else {
                // Default: oxirgi kun
                $day = Day::orderBy('id', 'DESC')->first();
                $dayQuery->where('id', $day->id);
            }

            $days = $dayQuery->get();
            $dayIds = $days->pluck('id')->toArray();

            // order_product ma'lumotlarini olish (faqat type_id = 1 bo'lgan shoplar)
            $ordersQuery = order_product::with([
                'kinggarden.region',
                'orderProductStructures.product.size',
                'shop'
            ])
            ->whereIn('day_id', $dayIds)
            ->whereNotNull('shop_id')
            ->whereHas('shop', function($q) {
                $q->where('type_id', 1);
            });

            if ($shopId) {
                $ordersQuery->where('shop_id', $shopId);
            }

            $orders = $ordersQuery->get();

            // Ma'lumotlarni tuman bo'yicha guruplash
            $reportData = [];

            foreach ($orders as $order) {
                if (!$order->kinggarden || !$order->kinggarden->region) {
                    continue;
                }

                $regionName = $order->kinggarden->region->region_name;
                $kindgardenId = $order->kinggarden->id;
                $numberOrg = $order->kinggarden->number_of_org ?? 'N/A';

                // Region filterni tekshirish
                if ($regionId && $order->kinggarden->region_id != $regionId) {
                    continue;
                }

                if (!isset($reportData[$regionName])) {
                    $reportData[$regionName] = [
                        'kindgardens' => [],
                        'products' => []
                    ];
                }

                // Bog'chani qo'shish
                if (!isset($reportData[$regionName]['kindgardens'][$kindgardenId])) {
                    $reportData[$regionName]['kindgardens'][$kindgardenId] = [
                        'number_org' => $numberOrg,
                        'name' => $order->kinggarden->kingar_name
                    ];
                }

                // Maxsulotlarni qo'shish
                foreach ($order->orderProductStructures as $structure) {
                    $productId = $structure->product_name_id;
                    $productName = $structure->product->product_name ?? 'N/A';
                    $sizeName = $structure->product->size->size_name ?? 'N/A';
                    $weight = $structure->product_weight ?? 0;

                    if (!isset($reportData[$regionName]['products'][$productId])) {
                        $reportData[$regionName]['products'][$productId] = [
                            'name' => $productName,
                            'size' => $sizeName,
                            'kindgardens' => []
                        ];
                    }

                    if (!isset($reportData[$regionName]['products'][$productId]['kindgardens'][$kindgardenId])) {
                        $reportData[$regionName]['products'][$productId]['kindgardens'][$kindgardenId] = 0;
                    }

                    $reportData[$regionName]['products'][$productId]['kindgardens'][$kindgardenId] += $weight;
                }
            }

            // Fayl nomi
            $fileName = 'shops_report_' . date('Y-m-d_H-i-s') . '.xlsx';
            $filePath = storage_path('shopsHistory/' . $fileName);

            // Papka mavjudligini tekshirish
            if (!file_exists(storage_path('shopsHistory'))) {
                mkdir(storage_path('shopsHistory'), 0777, true);
            }

            // Excel export qilish
            return Excel::download(new \App\Exports\ShopsHistoryReportExport($reportData, $days, $dateType), $fileName);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }
}
