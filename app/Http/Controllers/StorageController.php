<?php

namespace App\Http\Controllers;

use App\Models\Add_large_werehouse;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\order_product;
use App\Models\order_product_structure;
use App\Models\plus_multi_storage;
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
        return view('storage.home', ['count' => count($count), 'praducts' => $addlarch]);
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
            // order_product::where('id', $request->orderid)->update([
            //     'document_processes_id' => 4
            // ]);

            $order = order_product::where('id', $request->orderid)->first();
            $product = order_product_structure::where('order_product_name_id', $request->orderid)->get();
            foreach ($product as $row) {
                plus_multi_storage::create([
                    'day_id' => 20,
                    'shop_id' => 0,
                    'kingarden_name_d' => $order['kingar_name_id'],
                    'order_product_id' => $order['id'],
                    'product_name_id' => $row['product_name_id'],
                    'product_weight' => $row['product_weight'],
                ]);
            }
        } else {
            $result = 0;
        }
        return $result;
    }
}
