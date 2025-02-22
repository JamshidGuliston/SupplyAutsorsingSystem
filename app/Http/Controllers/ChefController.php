<?php

namespace App\Http\Controllers;

use App\Models\Active_menu;
use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Menu_composition;
use App\Models\minus_multi_storage;
use App\Models\Nextday_namber;
use App\Models\Number_children;
use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\plus_multi_storage;
use App\Models\Product;
use App\Models\Temporary;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChefController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Tashkent');
        $user = User::where('id', auth()->user()->id)->with('kindgarden')->first();
        $kindgarden = Kindgarden::where('id', $user->kindgarden[0]['id'])->with('age_range')->first();
        $sendchildcount = Temporary::where('kingar_name_id', $user->kindgarden[0]['id'])->get();
        $productall = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        $day = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('id', 'DESC')->first(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        // dd($day);
        $bool = minus_multi_storage::where('day_id', $day->id + 1)->where('kingarden_name_id', $kindgarden->id)->get();
        $ages = Age_range::all();
		foreach($ages as $age){
            $menu = Nextday_namber::where([
                ['kingar_name_id', '=', $kindgarden->id],
                ['king_age_name_id', '=', $age->id]
            ])->get();	
            if(count($menu) == 0){
                continue;
            }
            for($i = 0; $i<count($productall); $i++){
                $menuitem = Menu_composition::where('title_menu_id', $menu[0]['kingar_menu_id'])
                    ->where('age_range_id', $age->id)
                    ->where('product_name_id', $productall[$i]['id'])
                    ->get();
                // echo $menuitem->count().' | ';
                if($menuitem->count() > 0){
                    $productall[$i]['yes'] = 1;
                }
            }
        }

        $oder = order_product::where('kingar_name_id', $kindgarden->id)
                    ->where('document_processes_id', 4)
                    ->orderBy('id', 'DESC')
                    ->first();
        $inproducts = [];
        if(isset($oder->day_id) > 0 and $day->id-$oder->day_id <= 3){
            $inproducts = order_product_structure::where('order_product_name_id', $oder->id)
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        }
        // dd($productall);
        return view('chef.home', compact('productall', 'kindgarden', 'sendchildcount', 'day', 'bool', 'inproducts'));
    }
    public function minusproducts(Request $request){
        $bool = minus_multi_storage::where('day_id', $request->dayid + 1)->where('kingarden_name_id', $request->kindgarid)->get();
        if($bool->count() == 0){
            foreach($request->orders as $key => $value){
                $val = "";
                $bool = 1;
                for($i = 0; $i < strlen($value); $i++){
                    if (($value[$i] == ',' or $value[$i] == '.') and $bool){
                        $val = $val . '.';
                        $bool = 0;
                    }
                    elseif(is_numeric($value[$i])){
                        $val = $val . $value[$i];
                    }
                }
                if($val == ""){
                    $val = 0;
                }
                minus_multi_storage::create([
                    'day_id' => $request->dayid + 1,
                    'kingarden_name_id' => $request->kindgarid,
                    'kingar_menu_id' => 0,
                    'product_name_id' => $key,
                    'product_weight' => $val,
                ]);
            }
        }

        return redirect()->route('chef.home');
    }
    public function sendnumbers(Request $request)
    {
        // dd($request->all());
        $row = Temporary::where('kingar_name_id', $request->kingar_id)->get();
        if($row->count() == 0){
            foreach($request->agecount as  $key => $value){
                Temporary::create([
                    'kingar_name_id' => $request->kingar_id,
                    'age_id' => $key,
                    'age_number' => $value
                ]);
            }
        }
        return redirect()->route('chef.home');
    }

    public function right(Request $request)
    {
        $day = Day::orderby('id', 'DESC')->first();
        order_product::where('id', $request->orderid)->update([
            'document_processes_id' => 5
        ]);
        DB::transaction(function () use ($request, $day) {
            $order = order_product::where('id', $request->orderid)->first();
            $product = order_product_structure::where('order_product_name_id', $request->orderid)->get();
            foreach ($product as $row) {
                $exists = plus_multi_storage::where('order_product_id', $order['id'])
                                    ->where('kingarden_name_d', $order['kingar_name_id'])
                                    ->where('product_name_id', $row['product_name_id'])
                                    ->exists();
                if(!$exists){
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
        });

        return redirect()->route('home');
    }
}
