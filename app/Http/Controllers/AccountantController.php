<?php

namespace App\Http\Controllers;

use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Number_children;
use App\Models\Product;
use App\Models\Region;
use App\Models\titlemenu_food;
use App\Models\Year;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Foreach_;

class AccountantController extends Controller
{
    public function activmonth(){
        $year = Year::orderBy('id', 'DESC')->first();
        $month = Month::where('month_active', 1)->first();
        $days = Day::where('month_id', $month->id)->where('year_id', $year->id)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }
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
        $thismonth = 
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

    public function reports(Request $request){
        $kinds = Kindgarden::all();

        return view('accountant.reports', compact('kinds'));
    }

    public function kindreport(Request $request, $id){
        $days = $this->activmonth();
        $nakproducts = [];
        $first = 0;
        foreach($days as $day){
            $first = $day->id;
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->leftjoin('active_menus', function($join){
                        // $join->on('day_id', '=', $today);
                        $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                    })
                    ->where('active_menus.day_id', $day->id)
                    ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                    ->get();
            // dd($join);	
            $ages = Age_range::all();
            $agerange = array();
            foreach($ages as $row){
                $agerange[$row->id] = 0;
            }
            $productscount = array_fill(1, 500, $agerange);
            $workproduct = array_fill(1, 500, 0);
            $workerfood = titlemenu_food::where('titlemenu_foods.day_id', ($day->id-1))->get();
            // dd($workerfood);
            foreach($join as $row){
                if($row->age_range_id == 1 and $row->menu_meal_time_id = 3){
                    foreach($workerfood as $ww){
                        if($row->menu_food_id == $ww->food_id){
                            $workproduct[$row->product_name_id] += $row->weight;
                            $workproduct[$row->product_name_id.'div'] = $row->div;
                            $workproduct[$row->product_name_id.'wcount'] = $row->workers_count;
                        }
                    }
                }
                $productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
                $productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->kingar_children_number;
                $productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
                $productscount[$row->product_name_id]['product_name'] = $row->product_name;
            }
            
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    $summ = 0;
                    foreach($ages as $age){
                        if(isset($row[$age['id'].'-children'])){
                            $summ += ($row[$age['id']]*$row[$age['id'].'-children']) / $row[$age['id'].'div'];
                        }
                    }
                    if(isset($workproduct[$key.'wcount'])){
                        // $summ += ($workproduct[$key]*$workproduct[$key.'wcount']) / $workproduct[$key.'div'];
                    }
                    $childs = Number_children::where('day_id', $day->id)
                                    ->where('kingar_name_id', $id)
                                    ->sum('kingar_children_number');    
                    $nakproducts[0][$day->id] = $childs;
                    $nakproducts[0]['product_name'] = "Болалар сони";
                    $nakproducts[$key][$day->id] = $summ;
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                }
            }

            $costs = bycosts::where('day_id', '<=', $first)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->orderBy('day_id', 'DESC')->limit(Product::all()->count())->get();
            
            foreach($costs as $cost){
                $nakproducts[0][0] = 0;
                if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                    $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
                }
            }

            // dd($nakproducts);
            

        }
        // dd($days);
        return view('accountant.kindreport', compact('days', 'nakproducts'));
    }
}
