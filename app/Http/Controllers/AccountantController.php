<?php

namespace App\Http\Controllers;

use App\Exports\NakapitelExport;
use App\Exports\FakturaExport;
use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Number_children;
use App\Models\Product;
use App\Models\Region;
use App\Models\titlemenu_food;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Models\Add_large_werehouse;
use App\Models\minus_multi_storage;
use App\Models\order_product_structure;
use App\Models\plus_multi_storage;
use App\Models\Protsents;
use App\Models\Season;
use App\Models\Titlemenu;
use App\Models\Year;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Foreach_;

class AccountantController extends Controller
{
    public function activmonth(){
        $year = Year::where('year_active', 1)->first();
        $month = Month::where('month_active', 1)->first();
        $days = Day::where('month_id', $month->id)->where('year_id', $year->id)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function daysofmonth($id){
        $year = Year::where('year_active', 1)->first();
        $month = Month::where('id', $id)->first();
        $days = Day::where('month_id', $month->id)->where('year_id', $year->id)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function activyear(){
        $days = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderby('days.id', 'DESC')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function fullydate($id){
        $day = Day::where('id', $id)->join('months', 'months.id', '=', 'days.month_id')
        ->join('years', 'years.id', '=', 'days.year_id')
        ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $day;
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
        $year = Year::where('year_active', 1)->first();
        $days = Day::where('year_id', $year->id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        $costs = bycosts::where('day_id', '>=', $days[0]['id'])
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
        $mid = Day::where('id', $request->dayid)->first()->month_id;

        $bool = bycosts::where('day_id', $request->dayid)->where('region_name_id', $request->regionid)->get();
        if($bool->count() == 0){
            Protsents::create([
                'region_id' => $request->regionid,
                'month_id' => $mid,
                'nds' => $request->nds,
                'raise' => $request->raise,
                'protsent' => 0
            ]);
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
        $regions = Region::all();
        $days = $this->activyear();
        // dd($regions);
        return view('accountant.reports', compact('days', 'kinds', 'regions'));
    }

    public function reportsworker(Request $request){
        $kinds = Kindgarden::all();
        $regions = Region::all();
        $days = $this->activyear();
        return view('accountant.reportsworker', compact('days', 'kinds', 'regions'));
    }

    public function narxselect(Request $request, $region_id){

        $costsdays = bycosts::where('region_name_id', $region_id)
                        ->join('days', 'bycosts.day_id', '=', 'days.id')
                        ->join('years', 'days.year_id', '=', 'years.id')
                        ->orderBy('day_id', 'DESC')
                        ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
        $costs = [];
        $bool = [];
        foreach($costsdays as $row){
            if(!isset($bool[$row->day_id])){
                array_push($costs, $row);
                $bool[$row->day_id] = 1;
            }
        }

        $html = "<select class='form-select' name='cost_id' aria-label='Default select example' required>
                    <option>-Narx-</option>";
                foreach($costs as $row){
                    $id = $row['day_id'];
                    $day = $row['day_number'];
                    $month = $row['month_id'];
                    $year = $row['year_name'];
                    $html .=  "<option value=".$id.">".sprintf("%02d", $day).".".sprintf("%02d", $month).".".$year."</option>";
                }
        $html .= "</select>";

        return $html;
    }

    public function kindreport(Request $request, $id){
        $days = $this->activmonth();
        $yeardays = $this->activyear();
        $kindgar = Kindgarden::where('id', $id)->first();
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

            $costs = bycosts::where('day_id', bycosts::where('day_id', '<=', $first)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)->first()->day_id)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->orderBy('day_id', 'DESC')->get();
            
            foreach($costs as $cost){
                $nakproducts[0][0] = 0;
                if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                    $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
                }
            }

            $costsdays = bycosts::where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                        ->join('days', 'bycosts.day_id', '=', 'days.id')
                        ->join('years', 'days.year_id', '=', 'years.id')
                        ->orderBy('day_id', 'DESC')
                        ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
            $costs = [];
            $bool = [];
            foreach($costsdays as $row){
                if(!isset($bool[$row->day_id])){
                    array_push($costs, $row);
                    $bool[$row->day_id] = 1;
                }
            }
        }

        // dd($days);
        return view('accountant.kindreport', compact('days', 'nakproducts', 'yeardays', 'costsdays', 'costs', 'ages', 'kindgar'));
    }

    public function kindreportworker(Request $request, $id){
        $days = $this->activmonth();
        $yeardays = $this->activyear();
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $first = 0;
        foreach($days as $day){
            $first = $day->id;
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->leftjoin('active_menus', function($join){
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

            $costs = bycosts::where('day_id', bycosts::where('day_id', '<=', $first)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)->first()->day_id)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->orderBy('day_id', 'DESC')->get();
            
            foreach($costs as $cost){
                $nakproducts[0][0] = 0;
                if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                    $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
                }
            }

            $costsdays = bycosts::where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                        ->join('days', 'bycosts.day_id', '=', 'days.id')
                        ->join('years', 'days.year_id', '=', 'years.id')
                        ->orderBy('day_id', 'DESC')
                        ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
            $costs = [];
            $bool = [];
            foreach($costsdays as $row){
                if(!isset($bool[$row->day_id])){
                    array_push($costs, $row);
                    $bool[$row->day_id] = 1;
                }
            }
        }

        // dd($days);
        return view('accountant.kindreportworker', compact('days', 'nakproducts', 'yeardays', 'costsdays', 'costs', 'ages', 'kindgar'));
    }

    public function nakapit(Request $request, $id, $ageid, $start, $end, $costid, $nds, $ust){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $ageid)->first();
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
        $allproducts = [];

        foreach($days as $day){
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->where('king_age_name_id', $ageid)
                    ->leftjoin('active_menus', function($join){
                        $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                    })
                    ->where('active_menus.day_id', $day->id)
                    ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                    ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                    ->get();
            // dd($join);
            // $agerange = array();
            $productscount = [];
            // $productscount = array_fill(1, 500, $agerange);
            foreach($join as $row){
                if(!isset($productscount[$row->product_name_id][$ageid])){
                    $productscount[$row->product_name_id][$ageid] = 0;
                }
                $productscount[$row->product_name_id][$ageid] += $row->weight;
                $productscount[$row->product_name_id][$ageid.'-children'] = $row->kingar_children_number;
                $productscount[$row->product_name_id][$ageid.'div'] = $row->div;
                $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                $productscount[$row->product_name_id][$ageid.'sort'] = $row->sort;
                $productscount[$row->product_name_id]['size_name'] = $row->size_name;
            }
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    $childs = Number_children::where('day_id', $day->id)
                                    ->where('kingar_name_id', $id)
                                    ->where('king_age_name_id', $ageid)
                                    ->sum('kingar_children_number');    
                    $nakproducts[0][$day->id] = $childs;
                    $nakproducts[0]['product_name'] = "Болалар сони";
                    $nakproducts[0]['size_name'] = "";
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];;
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['sort'] = $row[$ageid.'sort'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                }
            }
            // dd($nakproducts);
            $costs = bycosts::where('day_id', $costid)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->orderBy('day_id', 'DESC')->get();
            
            foreach($costs as $cost){
                $nakproducts[0][0] = 0;
                if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                    $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
                }
            }

            $costsdays = bycosts::where('day_id', $costid)
                        ->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                        ->join('days', 'bycosts.day_id', '=', 'days.id')
                        ->join('years', 'days.year_id', '=', 'years.id')
                        ->orderBy('day_id', 'DESC')
                        ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
            $costs = [];
            $bool = [];
            foreach($costsdays as $row){
                if(!isset($bool[$row->day_id])){
                    array_push($costs, $row);
                    $bool[$row->day_id] = 1;
                }
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.nakapit', compact('age', 'days', 'nakproducts', 'costsdays', 'costs', 'kindgar', 'nds', 'ust')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
        
		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $start.$end.$id.$ageid."nakapit.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($name, ['Attachment' => 0]);
    }

    public function nakapitexcel(Request $request, $id, $ageid, $start, $end, $costid, $nds, $ust){
        // Excel::store(new NakapitelExport($request, $id, $ageid, $start, $end, $costid), "nakapitel.xlsx");
        return Excel::download(new NakapitelExport($request, $id, $ageid, $start, $end, $costid, $nds, $ust), 'excellist.xlsx');
        // return response(Storage::get('nakapitel.xlsx'))->header('Content-Type', Storage::mimeType('nakapitel.xlsx'));
    }

    public function nakapitworker(Request $request, $id, $ageid, $start, $end, $costid){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $ageid)->first();
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
        
        foreach($days as $day){
            $foods = titlemenu_food::where('day_id', $day->id-1)->get();
            $productscount = [];
            foreach($foods as $food){
                $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('number_childrens.kingar_name_id', $id)
                        ->where('number_childrens.king_age_name_id', $food->worker_age_id)
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        })
                        ->where('active_menus.day_id', $day->id)
                        ->where('active_menus.age_range_id', $food->worker_age_id)
                        ->where('active_menus.menu_food_id', $food->food_id)
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
        
                foreach($join as $row){
                    if(!isset($productscount[$row->product_name_id][$ageid])){
                        $productscount[$row->product_name_id][$ageid] = 0;
                        $productscount[$row->product_name_id][$ageid.'-children'] = $row->workers_count;
                        $productscount[$row->product_name_id][$ageid.'div'] = $row->div;
                        $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                        $productscount[$row->product_name_id][$ageid.'sort'] = $row->sort;
                        $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                    }
                    $productscount[$row->product_name_id][$ageid] += $row->weight;
                }
            }
            
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    $childs = Number_children::where('day_id', $day->id)
                                    ->where('kingar_name_id', $id)
                                    ->where('king_age_name_id', $ageid)
                                    ->sum('workers_count');    
                    $nakproducts[0][$day->id] = $childs;
                    $nakproducts[0]['product_name'] = "Ходимлар сони";
                    $nakproducts[0]['size_name'] = "";
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];;
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['sort'] = $row[$ageid.'sort'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                }
            }
        }

        $costs = bycosts::where('day_id', $costid)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                ->orderBy('day_id', 'DESC')->get();
        
        foreach($costs as $cost){
            $nakproducts[0][0] = 0;
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }

        $costsdays = bycosts::where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->join('days', 'bycosts.day_id', '=', 'days.id')
                    ->join('years', 'days.year_id', '=', 'years.id')
                    ->orderBy('day_id', 'DESC')
                    ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
        $costs = [];
        $bool = [];
        foreach($costsdays as $row){
            if(!isset($bool[$row->day_id])){
                array_push($costs, $row);
                $bool[$row->day_id] = 1;
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.nakapitworker', compact('days', 'nakproducts', 'costsdays', 'costs', 'kindgar')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);
        
		$dompdf->setPaper('A4', 'landscape');
		$name = $start.$end.$id.$ageid."nakapit.pdf";
		$dompdf->render();
		$dompdf->stream($name, ['Attachment' => 0]);
    }

    public function schotfaktur(Request $request, $id, $ageid, $start, $end, $costid, $nds, $ust){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $ageid)->first();
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
        foreach($days as $day){
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->where('king_age_name_id', $ageid)
                    ->leftjoin('active_menus', function($join){
                        $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                    })
                    ->where('active_menus.day_id', $day->id)
                    ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                    ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                    ->get();
            // dd($join);
            // $agerange = array();
            $productscount = [];
            // $productscount = array_fill(1, 500, $agerange);
            foreach($join as $row){
                if(!isset($productscount[$row->product_name_id][$ageid])){
                    $productscount[$row->product_name_id][$ageid] = 0;
                }
                $productscount[$row->product_name_id][$ageid] += $row->weight;
                $productscount[$row->product_name_id][$ageid.'-children'] = $row->kingar_children_number;
                $productscount[$row->product_name_id][$ageid.'div'] = $row->div;
                $productscount[$row->product_name_id][$ageid.'sort'] = $row->sort;
                $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                $productscount[$row->product_name_id]['size_name'] = $row->size_name;
            }
            
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];;
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                    $nakproducts[$key]['sort'] = $row[$ageid.'sort'];
                }
            }
            // dd($nakproducts);
            $costs = bycosts::where('day_id', $costid)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->orderBy('day_id', 'DESC')->get();
            
            foreach($costs as $cost){
                if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                    $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
                }
            }

            $costsdays = bycosts::where('day_id', $costid)
                        ->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                        ->join('days', 'bycosts.day_id', '=', 'days.id')
                        ->join('years', 'days.year_id', '=', 'years.id')
                        ->orderBy('day_id', 'DESC')
                        ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
            $costs = [];
            $bool = [];
            foreach($costsdays as $row){
                if(!isset($bool[$row->day_id])){
                    array_push($costs, $row);
                    $bool[$row->day_id] = 1;
                }
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.schotfaktur', compact('age', 'days', 'nakproducts', 'costs', 'kindgar', 'nds', 'ust')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $start.$end.$id.$ageid."schotfaktur.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($name, ['Attachment' => 0]);
    }

    public function schotfakturworker(Request $request, $id, $ageid, $start, $end, $costid){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
        foreach($days as $day){
            $foods = titlemenu_food::where('day_id', $day->id-1)->get();
            $productscount = [];
            foreach($foods as $food){
                $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('number_childrens.kingar_name_id', $id)
                        ->where('number_childrens.king_age_name_id', $food->worker_age_id)
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        })
                        ->where('active_menus.day_id', $day->id)
                        ->where('active_menus.age_range_id', $food->worker_age_id)
                        ->where('active_menus.menu_food_id', $food->food_id)
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
        
                foreach($join as $row){
                    if(!isset($productscount[$row->product_name_id][$ageid])){
                        $productscount[$row->product_name_id][$ageid] = 0;
                        $productscount[$row->product_name_id][$ageid.'-children'] = $row->workers_count;
                        $productscount[$row->product_name_id][$ageid.'div'] = $row->div;
                        $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                        $productscount[$row->product_name_id][$ageid.'sort'] = $row->sort;
                        $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                    }
                    $productscount[$row->product_name_id][$ageid] += $row->weight;
                }
            }
            
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];;
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['sort'] = $row[$ageid.'sort'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                }
            }
        }

        $costs = bycosts::where('day_id', $costid)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                ->orderBy('day_id', 'DESC')->get();
        
        foreach($costs as $cost){
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }
        
        $costsdays = bycosts::where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->join('days', 'bycosts.day_id', '=', 'days.id')
                    ->join('years', 'days.year_id', '=', 'years.id')
                    ->orderBy('day_id', 'DESC')
                    ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
        $costs = [];
        $bool = [];
        foreach($costsdays as $row){
            if(!isset($bool[$row->day_id])){
                array_push($costs, $row);
                $bool[$row->day_id] = 1;
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.schotfakturworker', compact('days', 'ageid', 'nakproducts', 'costsdays', 'costs', 'kindgar')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		$dompdf->setPaper('A4');
		$name = $start.$end.$id.$ageid."schotfakturworker.pdf";
		
		$dompdf->render();
		$dompdf->stream($name, ['Attachment' => 0]);
    }
  
    public function schotfakturexcel(Request $request, $id, $ageid, $start, $end, $costid, $nds, $ust){
        return Excel::download(new FakturaExport($request, $id, $ageid, $start, $end, $costid, $nds, $ust), 'Fakturaexcellist.xlsx');
    }

    public function allschotfaktur(Request $request, $id, $start, $end, $costid, $nds, $ust){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $ages = Age_range::all();
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
                // ->join('months', 'months.id', '=', 'days.month_id')
                // ->join('years', 'years.id', '=', 'days.year_id')
                // ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        // dd($days);
        foreach($ages as $age){     
            foreach($days as $day){
                $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('kingar_name_id', $id)
                        ->where('king_age_name_id', $age->id)
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                            $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                        })
                        ->where('active_menus.day_id', $day->id)
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
                $productscount = [];
                foreach($join as $row){
                    if(!isset($productscount[$row->product_name_id][$age->id])){
                        $productscount[$row->product_name_id][$age->id] = 0;
                    }
                    $productscount[$row->product_name_id][$age->id] += $row->weight;
                    $productscount[$row->product_name_id][$age->id.'-children'] = $row->kingar_children_number;
                    $productscount[$row->product_name_id][$age->id.'div'] = $row->div;
                    $productscount[$row->product_name_id][$age->id.'sort'] = $row->sort;
                    $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                    $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                }
                
                foreach($productscount as $key => $row){
                    if(isset($row['product_name'])){
                        if(!isset($nakproducts[$key][$day->id])){
                            $nakproducts[$key][$day->id] = 0;
                        }
                        $nakproducts[$key][$day->id] += ($row[$age->id]*$row[$age->id.'-children']) / $row[$age->id.'div'];
                        $nakproducts[$key]['product_name'] = $row['product_name'];
                        $nakproducts[$key]['size_name'] = $row['size_name'];
                        $nakproducts[$key]['sort'] = $row[$age->id.'sort'];
                    }
                }
            }
        }
        // dd($nakproducts);
        
        $costs = bycosts::where('day_id', $costid)->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                ->orderBy('day_id', 'DESC')->get();
        
        foreach($costs as $cost){
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }

        $costsdays = bycosts::where('day_id', $costid)
                    ->where('region_name_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->join('days', 'bycosts.day_id', '=', 'days.id')
                    ->join('years', 'days.year_id', '=', 'years.id')
                    ->orderBy('day_id', 'DESC')
                    ->get(['bycosts.day_id', 'days.day_number', 'days.month_id', 'years.year_name']);
        $costs = [];
        $bool = [];
        foreach($costsdays as $row){
            if(!isset($bool[$row->day_id])){
                array_push($costs, $row);
                $bool[$row->day_id] = 1;
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.allschotfaktur', compact('ages', 'days', 'nakproducts', 'costsdays', 'costs', 'kindgar', 'nds', 'ust')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $start.$end.$id."allschotfaktur.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($name, ['Attachment' => 0]);
    }

    public function allschotfakturexcel(Request $request, $id, $start, $end, $costid){
        return Excel::download(new FakturaExport($request, $id, $ageid, $start, $end, $costid), 'Fakturaexcellist.xlsx');
    }

    public function norm(Request $request, $id, $ageid, $start, $end, $costid){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $ageid)->first();
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
        $date = $this->fullydate($start);
        dd($date);
        foreach($days as $day){
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->where('king_age_name_id', $ageid)
                    ->leftjoin('active_menus', function($join){
                        $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        $join->on('number_childrens.king_age_name_id', '=', 'active_menus.age_range_id');
                    })
                    ->where('active_menus.day_id', $day->id)
                    ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                    ->join('norm_categories', 'products.norm_cat_id', '=', 'norm_categories.id')
                    ->join('norms', 'products.norm_cat_id', '=', 'norms.norm_cat_id')
                    ->where('norms.norm_age_id', $ageid)
                    ->where('norms.noyuk_id', 1)
                    ->get();
            // $agerange = array();
            $productscount = [];
            foreach($join as $row){
                if(!isset($productscount[$row->norm_cat_id][$ageid])){
                    $productscount[$row->norm_cat_id][$ageid] = 0;
                    // $productscount[$row->norm_cat_id][$ageid.'-children'] = 0;
                }
                $productscount[$row->norm_cat_id][$ageid] += $row->weight;
                $productscount[$row->norm_cat_id][$ageid.'-children'] = $row->kingar_children_number;
                $productscount[$row->norm_cat_id][$ageid.'div'] = $row->div;
                $productscount[$row->norm_cat_id]['product_name'] = $row->norm_name_short;
                $productscount[$row->norm_cat_id][$ageid.'sort'] = $row->sort;
                $productscount[$row->norm_cat_id]['norm_weight'] = $row->norm_weight;
            }
            
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    if(!isset($nakproducts[$key]['children'])){
                        $nakproducts[$key]['children'] = 0;
                    }
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];;
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['norm_weight'] = $row['norm_weight'];
                    $nakproducts[$key]['children'] += $row[$ageid.'-children'];
                    $nakproducts[$key]['sort'] = $row[$ageid.'sort'];
                    $nakproducts[$key]['div'] = $row[$ageid.'div'];
                }
            }
            
        }
        // dd($nakproducts);
        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.norm', compact('age', 'days', 'nakproducts', 'kindgar')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4');
		// $customPaper = array(0,0,360,360);
		// $dompdf->setPaper($customPaper);
		$name = $start.$end.$id.$ageid."schotfaktur.pdf";
		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($name, ['Attachment' => 0]);    
    }

    public function normexcel(Request $request, $id, $ageid, $start, $end, $costid){
        
    }

    public function svod(Request $request){
        // dd($request->all());
        $over = $request->over;
        $nds = $request->nds;
        $days = Day::where('id', '>=', $request->start)->where('id', '<=', $request->end)->get();
        $nakproducts = [];
        $first = $days[0]['id'];
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

        $costs = bycosts::where('day_id', $request->cost_id)
                ->where('region_name_id', $request->region_id)
                ->orderBy('day_id', 'DESC')->get();

        foreach($costs as $cost){
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.svod', compact('age', 'nakproducts', 'kindgardens', 'over', 'nds')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		$dompdf->setPaper('A3',  'landscape');
		$name = "svod.pdf";
		$dompdf->render();
		$dompdf->stream($name, ['Attachment' => 0]); 
    }

    public function svodworkers(Request $request){
        $over = $request->over;
        $nds = $request->nds;
        $days = Day::where('id', '>=', $request->start)->where('id', '<=', $request->end)->get();
        $nakproducts = [];
        $first = $days[0]['id'];
        $kindgardens = [];
        foreach($request->kindgardens as $row_id){
            array_push($kindgardens, Kindgarden::where('id', $row_id)->first());
            foreach($days as $day){
                $foods = titlemenu_food::where('day_id', $day->id-1)->get();
                foreach($foods as $food){
                    $join = Number_children::where('number_childrens.day_id', $day->id)
                        ->where('number_childrens.kingar_name_id', $row_id)
                        ->where('number_childrens.king_age_name_id', $food->worker_age_id)
                        ->leftjoin('active_menus', function($join){
                            $join->on('number_childrens.kingar_menu_id', '=', 'active_menus.title_menu_id');
                        })
                        ->where('active_menus.day_id', $day->id)
                        ->where('active_menus.age_range_id', $food->worker_age_id)
                        ->where('active_menus.menu_food_id', $food->food_id)
                        ->join('products', 'active_menus.product_name_id', '=', 'products.id')
                        ->join('sizes', 'products.size_name_id', '=', 'sizes.id')
                        ->get();
                    $productscount = array();
                    foreach($join as $row){
                        if(!isset($productscount[$row->product_name_id][$row->age_range_id])){
                            $productscount[$row->product_name_id][$row->age_range_id] = 0;
                        }
                        $productscount[$row->product_name_id][$row->age_range_id] += $row->weight;
                        $productscount[$row->product_name_id][$row->age_range_id.'-children'] = $row->workers_count;
                        $productscount[$row->product_name_id][$row->age_range_id.'div'] = $row->div;
                        $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                        $productscount[$row->product_name_id][$row->age_range_id.'sort'] = $row->sort;
                        $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                    }
                    
                    foreach($productscount as $key => $row){
                        if(!isset($nakproducts[$key][$row_id])){
                            $nakproducts[$key][$row_id] = 0;
                        }
                        $nakproducts[$key][$row_id] += ($row[$food->worker_age_id]*$row[$food->worker_age_id.'-children']) / $row[$food->worker_age_id.'div'];
                        $nakproducts[$key]['product_name'] = $row['product_name'];
                        $nakproducts[$key]['sort'] = $row[$food->worker_age_id.'sort'];
                        $nakproducts[$key]['size_name'] = $row['size_name'];
                    }
    
                }
                
            }
        }

        $costs = bycosts::where('day_id', $request->cost_id)
                ->where('region_name_id', $request->region_id)
                ->orderBy('day_id', 'DESC')->get();

        foreach($costs as $cost){
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.svod', compact('nakproducts', 'kindgardens', 'over', 'nds')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		$dompdf->setPaper('A3',  'landscape');
		$name = "svod.pdf";
		$dompdf->render();
		$dompdf->stream($name, ['Attachment' => 0]); 
    }
    // Daromad

    public function income(Request $request, $id){
        $months = Month::all();
        $il = $id;
        if($id == 0){
            $il = Month::where('month_active', 1)->first()->id;
        }
        $daysofmonth = $this->daysofmonth($il);
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $daysofmonth->first()->id)
            ->where('add_groups.day_id', '<=', $daysofmonth->last()->id)
            ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
            ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
            ->get();
        
        $incomes = [];
        foreach($addlarch as $product){
            if(!isset($incomes[$product->product_id])){
                // $alladd[$t++.'id'] = $row->product_id;
                $incomes[$product->product_id]['weight'] = 0;
                $incomes[$product->product_id]['minusweight'] = 0;
                $incomes[$product->product_id]['p_cost'] = 0;
                $incomes[$product->product_id]['p_id'] = $product->product_id;
                $incomes[$product->product_id]['p_sum'] = 0;
                $incomes[$product->product_id]['count'] = 0;
                $incomes[$product->product_id]['p_name'] = $product->product_name;
                $incomes[$product->product_id]['p_sort'] = $product->sort;
            }
            $incomes[$product->product_id]['weight'] += $product->weight;
            $incomes[$product->product_id]['p_sum'] += $product->cost * $product->weight;
            $incomes[$product->product_id]['p_cost'] += $product->cost;
            $incomes[$product->product_id]['count'] += 1;
        }

        $minuslarch = order_product_structure::where('order_products.day_id', '>=', $daysofmonth->first()->id)
                    ->where('order_products.day_id', '<=', $daysofmonth->last()->id)
                    ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();

        foreach($minuslarch as $row){
            if(!isset($incomes[$row->product_name_id])){
                $incomes[$row->product_name_id]['weight'] = 0;
                $incomes[$row->product_name_id]['minusweight'] = 0;
                $incomes[$row->product_name_id]['p_cost'] = 0;
                $incomes[$row->product_name_id]['p_id'] = $row->product_name_id;
                $incomes[$row->product_name_id]['p_sum'] = 0;
                $incomes[$row->product_name_id]['count'] = 0;
                $incomes[$row->product_name_id]['p_name'] = $row->product_name;
                $incomes[$row->product_name_id]['p_sort'] = $row->sort;
            }
            $incomes[$row->product_name_id]['minusweight'] += $row->product_weight;
        }

        $regions = Region::all();
        $kindgardens = Kindgarden::all();
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

        foreach($regions as $region){
            $inregions[$region->id] = [];
            foreach($allproducts as $day){
                foreach($day as $product){
                    if($product->region_id == $region->id){
                        if(!isset($inregions[$region->id][$product->product_name_id."kg"])){
                            $inregions[$region->id][$product->product_name_id."kg"] = 0;
                            $cost = bycosts::where('day_id', bycosts::where('day_id', '<', $daysofmonth->last()->id)->where('region_name_id', $region->id)->orderBy('day_id', 'DESC')->first()->day_id)
                                    ->where('region_name_id', $region->id)
                                    ->where('praduct_name_id', $product->product_name_id)
                                    ->first()->price_cost;
                            $inregions[$region->id][$product->product_name_id."cost"] = $cost;
                        }
                        $inregions[$region->id][$product->product_name_id."kg"] += $product->weight/$product->div * $product->kingar_children_number;
                    }
                }
            }
        }
        $mods = $this->multimods();
        // dd($mods);
        usort($incomes, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });

        return view('accountant.income', compact('incomes', 'inregions', 'months', 'id', 'regions', 'mods'));
    }

    public function bigbase(Request $request)
    {
        $month_days = $this->activmonth();
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $month_days->first()->id)
                    ->where('add_groups.day_id', '<=', $month_days->last()->id)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        
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


        $minuslarch = order_product_structure::where('order_products.day_id', '>=', $month_days->first()->id)
                    ->where('order_products.day_id', '<=', $month_days->last()->id)
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

        usort($alladd, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });
        return view('accountant.bigbase', ['products' => $alladd]);
    }

    public function multibase(Request $request)
    {
        $kingar = Kindgarden::all();
        return view('accountant.multibase', ['kingardens' => $kingar]);
    }

    public function getmodproduct(Request $request, $kid){
        $king = Kindgarden::where('id', $kid)->first();
        $days = $this->activmonth();
        // dd($days);
        $minusproducts = [];
        foreach($days as $day){
            $minus = minus_multi_storage::where('day_id', $day->id)
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
            // echo $minus->count()." ";
            foreach($minus as $row){
                if(!isset($minusproducts[$row->product_name_id])){
                    $minusproducts[$row->product_name_id] = 0;
                }
                $minusproducts[$row->product_name_id] += $row->product_weight;
                // $minusproducts[$row->product_name_id]['productname'] = $row->product_name;
            }
        }
        // dd($minusproducts);
        $plusproducts = [];
        foreach($days as $day){
            $plus = plus_multi_storage::where('day_id', $day->id)
                ->where('kingarden_name_d', $kid)
                ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                ->get([
                    'plus_multi_storages.id',
                    'plus_multi_storages.product_name_id',
                    'plus_multi_storages.day_id',
                    'plus_multi_storages.kingarden_name_d',
                    'plus_multi_storages.product_weight',
                    'products.product_name',
                    'products.size_name_id',
                    'products.div',
                    'products.sort'
                ]);
            foreach($plus as $row){
                if(!isset($plusproducts[$row->product_name_id])){
                    $plusproducts[$row->product_name_id] = 0;
                }
                $plusproducts[$row->product_name_id] += $row->product_weight;
            }
        }

        $products = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')
                ->orderBy('products.sort', 'DESC')
                ->get(['products.id', 'products.product_name', 'sizes.size_name']);
        
        $html = "<table class='table table-light table-striped table-hover'>
                <thead>
                    <tr>
                        <th scope='col'>Maxsulot</th>
                        <th scope='col'>Кирим</th>
                        <th scope='col'>Чиқим</th>
                        <th scope='col'>Қолдиқ</th>
                    </tr>
                </thead>
                <tbody>";
                foreach($products as $product){
                    if(isset($minusproducts[$product->id]) or isset($plusproducts[$product->id])){
                        $html = $html."<tr>
                            <td>". $product->product_name ."</td>
                            <td>";
                            if(isset($plusproducts[$product->id])){ 
                                $countin = $plusproducts[$product->id];
                            }
                            else
                                $countin = 0;
                                $html = $html.$countin."
                            </td>
                            <td>";
                            if(isset($minusproducts[$product->id])){ 
                                $countout = $minusproducts[$product->id];
                            }
                            else
                                $countout = 0;
                            $html = $html.$countout."
                            </td>
                            <td>". sprintf('%0.1f', $countin - $countout) .' '.$product->size_name."</td>
                        </tr>";
                    }
                }
        $html = $html."</tbody>
            </table>
            ";
        
        return $html;
    }

    public function multimods(){
        $kinds = Kindgarden::all();
        $mods = [];
        $days = $this->activmonth();
        $plusproducts = [];
        $minusproducts = [];
        foreach($kinds as $kind){
            foreach($days as $day){
                $plus = plus_multi_storage::where('day_id', $day->id)
                    ->where('kingarden_name_d', $kind->id)
                    ->join('products', 'plus_multi_storages.product_name_id', '=', 'products.id')
                    ->get([
                        'plus_multi_storages.id',
                        'plus_multi_storages.product_name_id',
                        'plus_multi_storages.day_id',
                        'plus_multi_storages.kingarden_name_d',
                        'plus_multi_storages.product_weight',
                        'products.product_name',
                        'products.size_name_id',
                        'products.div',
                        'products.sort'
                    ]);
                foreach($plus as $row){
                    if(!isset($plusproducts[$row->product_name_id])){
                        $plusproducts[$row->product_name_id] = 0;
                    }
                    $plusproducts[$row->product_name_id] += $row->product_weight;
                }

                $minus = minus_multi_storage::where('day_id', $day->id)
                    ->where('kingarden_name_id', $kind->id)
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
                foreach($minus as $row){
                    if(!isset($minusproducts[$row->product_name_id])){
                        $minusproducts[$row->product_name_id] = 0;
                    }
                    $minusproducts[$row->product_name_id] += $row->product_weight;
                }
            }
        }

        foreach($minusproducts as $key => $value){
            if(!isset($plusproducts[$key])){
                $mods[$key] = 0;
            }
            else{
                $mods[$key] = $plusproducts[$key] - $value;
            }
        }

        return $mods;
    }

}
