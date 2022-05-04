<?php

namespace App\Http\Controllers;

use App\Models\bycosts;
use App\Models\Day;
use App\Models\Product;
use App\Models\Region;
use App\Models\Year;
use Illuminate\Http\Request;

class AccountantController extends Controller
{
    public function index(Request $request)
    {
        return view('accountant.home');
    }

    public function costs(Request $request){
        $regions = Region::all();
        // dd($regions);
        return view('accountant.bycostregions', compact('regions'));
    }

    public function bycosts(Request $request, $id){
        $region = Region::where('id', $id)->first();
        $year = Year::orderBy('id', 'DESC')->first();
        // $days = Day::where('year_id', $year->id)->get();
        $days = Day::where('year_id', $year->id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        $costs = bycosts::where('day_id', '>', $days[0]['id'])
                ->where('region_name_id', $id)
                ->join('products', 'bycosts.praduct_name_id', '=', 'products.id')
                ->get(['bycosts.id', 'bycosts.praduct_name_id', 'bycosts.day_id', 'bycosts.price_cost', 'products.product_name']);
        
        $minusproducts = [];
        foreach($costs as $row){
            $days->where('id', $row->day_id)->first()->yes = "yes";
            $minusproducts[$row->praduct_name_id][$row->day_id] = $row->price_cost;
            $minusproducts[$row->praduct_name_id]['productname'] = $row->product_name;
            // $minusproducts[$row->praduct_name_id]['rowid'] = $row->id;
        }
        // dd($minusproducts);
        $productall = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        
        return view('accountant.bycosts', compact('region', 'minusproducts', 'costs', 'productall', 'id', 'days'));
    }

    public function pluscosts(Request $request){
        // dd($request->all());
        $bool = bycosts::where('day_id', $request->dayid)->where('region_name_id', $request->regionid)->get();
        if($bool->count() == 0){
            foreach($request->orders as $key => $value){
                if($value == null){
                    $value = 0;
                }
                bycosts::create([
                    'day_id' => $request->dayid,
                    'region_name_id' => $request->regionid,
                    'praduct_name_id' => $key,
                    'price_cost' => $value,
                    'tax_product' => 0,
                    'waste_number' => 0 
                ]);
            }
        }

        return redirect()->route('accountant.bycosts', $request->regionid);
    }

    public function editcost(Request $request){
        // dd($request->all());
        bycosts::where('day_id', $request->dayid)
                ->where('region_name_id', $request->regid)
                ->where('praduct_name_id', $request->prodid)
                ->update(['price_cost' => $request->kg]);
        return redirect()->route('accountant.bycosts', $request->regid);
    }
}
