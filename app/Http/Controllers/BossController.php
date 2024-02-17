<?php

namespace App\Http\Controllers;

use App\Models\Active_menu;
use App\Models\Add_large_werehouse;
use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\cashes;
use App\Models\costs;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Number_children;
use App\Models\Product;
use App\Models\Protsents;
use App\Models\Region;
use App\Models\Year;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Averages;

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
    public function index(Request $request)
    {
    	$yearid = $request->yearid;
        if($yearid == 0){
            $yearid = Year::where('year_active', 1)->first()->id;
        }
        $year = Year::where('id', $yearid)->first();
        $months = Month::where('yearid', $yearid)->get();
        
        $il = $request->monthid;
        if($request->monthid == 0){
            $il = Month::where('month_active', 1)->where('yearid', $yearid)->first()->id;
            if($il == null){
                $il = Month::where('yearid', $yearid)->first()->id;
            }
        }
        $id = $il;
        $days = $this->days_of_month($id);
        $prt = Protsents::where('month_id', $id)->get();
        $nochs = Number_children::where('day_id', '>=', $days->first()->id)
                    ->join('kindgardens', 'kindgardens.id', '=', 'number_childrens.kingar_name_id')
                    ->where('day_id', '<=', $days->last()->id)
                    ->get();
        $bymenus = Active_menu::where('day_id', '>=', $days->first()->id)
                            ->where('day_id', '<=', $days->last()->id)->get();
        $avgproducts = Add_large_werehouse::groupBy('product_id')
                        ->selectRaw('avg(cost) as avgcost, product_id')
                        ->where('add_groups.day_id', '>=', $days->first()->id)
                        ->where('add_groups.day_id', '<=', $days->last()->id)
                        ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                        ->get();
        $products = Product::all();
        // dd();
        $totalproducts = [];
        foreach($nochs as $noch){
            // dd($noch);
            $foundmenu = $bymenus->where('day_id', $noch->day_id)
                        ->where('title_menu_id', $noch->kingar_menu_id)
                        ->where('age_range_id', $noch->king_age_name_id);
            foreach($foundmenu as $menu){
                  if(!isset($totalproducts[$noch->kingar_name_id][$menu->product_name_id])){
                    $totalproducts[$noch->kingar_name_id][$menu->product_name_id]['weight'] = 0;
                  }               
                  $totalproducts[$noch->kingar_name_id][$menu->product_name_id]['weight'] += ($menu->weight * $noch->kingar_children_number) / $products->find($menu->product_name_id)->div;
            }

        }

        $sumbyregion = [];
        $inarray[1] = [];
        $inarray[2] = [];
        $inarray[3] = [];
        // dd($totalproducts);
        foreach($totalproducts as $key => $kind){
            $k = Kindgarden::where('id', $key)->first();
            // print_r($k);
            $costs = bycosts::where('day_id', '>=', $days->first()->id)
                ->where('day_id', '<=', $days->last()->id)
                ->where('region_name_id', $k->region_id)
                ->orderBy('day_id', 'DESC')->get();
            
            foreach($kind as $pkey => $row){
                if(empty($sumbyregion[$k->region_id]['summ_sale'])){
                    $sumbyregion[$k->region_id]['summ_sale'] = 0;
                    $sumbyregion[$k->region_id]['summ_by'] = 0;
                    $sumbyregion[$k->region_id]['summ_by'] = 0;
                }
                $mc = Add_large_werehouse::where('add_large_werehouses.product_id', $pkey)
                        ->where('add_groups.day_id', '>=', $days->first()->id)
                        ->where('add_groups.day_id', '<=', $days->last()->id)
                        ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                        ->avg('cost');
                if($mc == 0){
                	$mc = Add_large_werehouse::where('add_large_werehouses.product_id', $pkey)
                	->orderBy('id', 'DESC')
                	->first('cost');
                	$mc = $mc ? $mc->cost : 0;
                }
                $sumbyregion[$k->region_id]['summ_sale'] += $row['weight'] * $costs->where('praduct_name_id', $pkey)->first()->price_cost;
                $sumbyregion[$k->region_id]['summ_by'] += $row['weight'] * ($mc == null)? 0 : $mc;
                array_push($inarray[$k->region_id], array($pkey => $row['weight'], 'cost' => $row['weight'] * $mc));
            }
        }
        
        $regions = Region::all();
        return view('boss.home', compact('year', 'months', 'id', 'prt', 'sumbyregion', 'regions'));
    }

    public function cashe()
    {
        $cashes = cashes::join('all_costs', 'all_costs.id', '=', 'cashes.allcost_id')
            ->select('cashes.id as cashid', 'cashes.description', 'cashes.summ', 'months.month_name', 'years.year_name', 'all_costs.allcost_name', 'days.day_number', 'cashes.status')
            ->join('days', 'days.id', '=', 'cashes.day_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->join('months', 'months.id', '=', 'days.month_id')
            ->orderby('cashes.id', 'DESC')
            ->paginate(50);
        
        return view('boss.cashe', compact('cashes'));
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
        $kindgardens = $request->kindid;
        $inregions = [];
        $allproducts = [];
        foreach($kindgardens as $kindgar){
            foreach($daysofmonth as $day){
                foreach(Age_range::all() as $age){
                    $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('number_childrens.kingar_name_id', $kindgar)
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
                	$report[$product->product_name_id]["p_name"] = $product->product_name;
                	$report[$product->product_name_id]["size_name"] = $product->size_name;
                    $report[$product->product_name_id]["kg"] = 0;
                    $cost = bycosts::where('day_id', bycosts::where('day_id', '<', $daysofmonth->last()->id)->where('region_name_id', 1)->orderBy('day_id', 'DESC')->first()->day_id)
			            ->where('region_name_id', 1)
			            ->where('praduct_name_id', $product->product_name_id)
			            ->first()->price_cost;
			        $report[$product->product_name_id]["salecost"] = $cost;
			        $mc = Add_large_werehouse::where('add_large_werehouses.product_id', $product->product_name_id)
                        ->where('add_groups.day_id', '>=', $daysofmonth->first()->id)
                        ->where('add_groups.day_id', '<=', $daysofmonth->last()->id)
                        ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                        ->avg('cost');
                    if($mc == 0){
                    	$mc = Add_large_werehouse::where('add_large_werehouses.product_id', $product->product_name_id)
                    	->orderBy('id', 'DESC')
                    	->first('cost');
                    	$mc = $mc ? $mc->cost : 0;
                    }
                    $report[$product->product_name_id]["bycost"] = $mc;
                }
                $report[$product->product_name_id]["kg"] += $product->weight/$product->div * $product->kingar_children_number;
            }
        }
		
		$html = "<table style='background-color: white; width: 100%;'>
                <thead>
                <tr>
                        <th>Maxsulot</th>
                        <th>..</th>
                        <th>Bir oylik kg</th>
                        <th>Kirim o'r.narx</th>
                        <th>Kirim summa</th>
                        <th>Chiqim narx</th>
                        <th>Chiqim summa</th>
                        <th>Farqi</th>
                        <th>Marja %</th>
                    </tr>
                </thead>
                <tbody>";
                
		$taking = 0;
        $giving = 0;
        $mod = 0;
        foreach($report as $key => $row){
            $taking = $taking + $row["kg"] * $row["bycost"];
            $giving = $giving + $row["kg"] * $row["salecost"];
            $mod = $mod +  $row["kg"] * $row["salecost"] - $row["kg"] * $row["bycost"];
            $html = $html."
                <tr>
                    <td>".$row["p_name"]."</td>
                    <td>".$row["size_name"]."</td>
                    <td>".sprintf('%0.2f', $row["kg"])."</td>
                    <td>".sprintf('%0.0f', $row["bycost"])."</td>   
                    <td>".sprintf('%0.2f', $row["kg"] * $row["bycost"])."</td>
                    <td>".sprintf('%0.0f', $row["salecost"])."</td>
                    <td>".sprintf('%0.2f', $row["kg"] * $row["salecost"])."</td>   
                    <td>".sprintf('%0.2f', $row["kg"] * $row["salecost"] - $row["kg"] * $row["bycost"])."</td>
                    <td>".sprintf('%0.0f', ($row["kg"] * $row["salecost"] - $row["kg"] * $row["bycost"]) / ($row["kg"] * $row["salecost"]) * 100)."</td>   
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
                <td><b>".sprintf('%0.2f', $giving)."</b></td>
                <td><b>".sprintf('%0.2f',$mod)."</b></td>    
                <td><b>".sprintf('%0.0f',$mod / $giving * 100)."</b></td>   
            </tr>
        ";
        $html = $html."</tbody>
                </table>
                ";
            
        return $html;
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
