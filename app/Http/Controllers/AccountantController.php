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
        $year = Year::orderBy('id', 'DESC')->first();
        $startday = Day::where('year_id', $year->id)->first();
        $costs = bycosts::where('day_id', '>', $startday->id)->where('region_name_id', $id)->get();
        $productall = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        return view('accountant.bycosts', compact('costs', 'productall'));
    }
}
