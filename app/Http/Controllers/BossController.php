<?php

namespace App\Http\Controllers;

use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\cashes;
use App\Models\costs;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Number_children;
use App\Models\Region;
use App\Models\Year;
use Illuminate\Http\Request;

class BossController extends Controller
{
    public function days(){
        $days = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderby('days.id', 'DESC')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }
    public function months_of_year($yearid){
        $months = Month::where('yearid', $yearid)->get();
        return $months;
    }

    public function days_of_month($month_id){
        $month = Month::where('id', $month_id)->first();
        $days = Day::where('month_id', $month->id)->where('year_id', $month->yearid)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $cashes = cashes::join('all_costs', 'all_costs.id', '=', 'cashes.allcost_id')
            ->select('cashes.id as cashid', 'cashes.description', 'cashes.summ', 'months.month_name', 'years.year_name', 'all_costs.allcost_name', 'days.day_number', 'cashes.status')
            ->join('days', 'days.id', '=', 'cashes.day_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->join('months', 'months.id', '=', 'days.month_id')
            ->orderby('cashes.id', 'DESC')
            ->paginate(50);
        
        return view('boss.home', compact('cashes'));
    }

    
    public function report(Request $request){
        $days = $this->days();
        $costs = costs::where('cost_hide', 1)->get();
        
        return view('boss.report', compact('days', 'costs'));
    }

    public function incomereport(){
        $yid = Year::where('year_active', 1)->first()->id;
        $months = $this->months_of_year($yid);
        $kinds = Kindgarden::all();
        
        return view('boss.incomereport', compact('months', 'kinds'));
    }

    public function showincome(Request $request){
        $daysofmonth = $this->days_of_month($request->monthid);
        $regions = Region::all();
        if($request->kindid == 0)
            $kindgardens = Kindgarden::all();
        else
            $kindgardens = Kindgarden::where('id', $request->kindid)->get();
        $inregions = [];
        $allproducts = [];
        foreach($kindgardens as $kindgar){
            foreach($daysofmonth as $day){
                foreach(Age_range::all() as $age){
                    $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('number_childrens.kingar_name_id', $kindgar->id)
                        ->where('number_childrens.king_age_name_id', $age->id)
                        ->join('kindgardens', 'kindgardens.id', '=', 'number_childrens.kingar_name_id')
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.day_id', '=', 'active_menus.day_id');
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                            $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                        })
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
                    array_push($allproducts, $join);
                }
            }
        }

        $report = [];
        foreach($allproducts as $day){
            foreach($day as $product){
                if(!isset($report[$product->product_name_id]["kg"])){
                    $report[$product->product_name_id]["kg"] = 0;
                }
                $report[$product->product_name_id]["kg"] += $product->weight/$product->div * $product->kingar_children_number;
            }
        }

        $cost = bycosts::where('day_id', bycosts::where('day_id', '<', $daysofmonth->last()->id)->where('region_name_id', 1)->orderBy('day_id', 'DESC')->first()->day_id)
            ->where('region_name_id', 1)
            ->where('praduct_name_id', $product->product_name_id)
            ->first()->price_cost;
        // $mods = $this->multimods();
        // dd($mods);
        // usort($incomes, function ($a, $b){
        //     if(isset($a["p_sort"]) and isset($b["p_sort"])){
        //         return $a["p_sort"] > $b["p_sort"];
        //     }
        // });

        return $report;


        
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function accepted(Request $request)
    {
        cashes::where('id', $request->id)->update(['status' => 2]);
        return redirect()->route('boss.home')->with('status', "Qabul qilindi"); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
