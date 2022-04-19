<?php

namespace App\Http\Controllers;

use App\Models\Active_menu;
use App\Models\Age_range;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\minus_multi_storage;
use App\Models\Number_children;
use App\Models\Product;
use App\Models\Temporary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $bool = minus_multi_storage::where('day_id', $day->id)->where('kingarden_name_id', $kindgarden->id)->get();
        $ages = Age_range::all();
		foreach($ages as $age){
            $menu = Number_children::where([
                ['kingar_name_id', '=', $kindgarden->id],
                ['day_id', '=', $day->id],
                ['king_age_name_id', '=', $age->id]
            ])->get();	
            if(count($menu) == 0){
                continue;
            }
            for($i = 0; $i<count($productall); $i++){
                $menuitem = Active_menu::where('day_id', $day->id)
                    ->where('title_menu_id', $menu[0]['kingar_menu_id'])
                    ->where('age_range_id', $age->id)
                    ->where('product_name_id', $productall[$i]['id'])
                    ->get();
                // echo $menuitem->count().' | ';
                if($menuitem->count() > 0){
                    $productall[$i]['yes'] = 1;
                }
            }
        }
            
        // dd($productall);
        return view('chef.home', compact('productall', 'kindgarden', 'sendchildcount', 'day', 'bool'));
    }
    public function minusproducts(Request $request){
        // dd($request->all());
        $bool = minus_multi_storage::where('day_id', $request->dayid)->where('kingarden_name_id', $request->kindgarid)->get();
        if($bool->count() == 0){
            foreach($request->orders as $key => $value){
                minus_multi_storage::create([
                    'day_id' => $request->dayid,
                    'kingarden_name_id' => $request->kindgarid,
                    'kingar_menu_id' => 0,
                    'product_name_id' => $key,
                    'product_weight' => $value,
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
}
