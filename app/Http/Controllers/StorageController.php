<?php

namespace App\Http\Controllers;

use App\Models\Add_group;
use App\Models\Add_large_werehouse;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\plus_multi_storage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $group = Add_group::create([
            'day_id' => 19,
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

        return redirect()->route('storage.addproductform');
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
	                    'day_id' => $day[0]->id,
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
}
