<?php

namespace App\Http\Controllers;

use App\Exports\NakapitelExport;
use App\Exports\FakturaExport;
use App\Exports\RegionSchotFakturaExport;
use App\Exports\TransportationRegionExport;
use App\Exports\ReportRegionExport;
use App\Exports\ReportRegionSecondaryExport;
use App\Exports\NakapitWithoutCostExport;
use App\Exports\NormExport;
use App\Exports\SchotFakturaSecondExport;
use App\Exports\TransportationExcelExport;
use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Active_menu;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Month;
use App\Models\Number_children;
use App\Models\Product;
use App\Models\Protsent;
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
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Foreach_;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
// use PDF;

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

    public function daysthisyear($id){
        $days = Day::where('years.id', $id)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderBy('days.id', 'DESC')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function fullydate($id){
        $day = Day::where('days.id', $id)->join('months', 'months.id', '=', 'days.month_id')
        ->join('years', 'years.id', '=', 'days.year_id')
        ->first(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
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
        //where('year_id', $year->id)
        $days = Day::where('year_id', $year->id)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->get(['days.id', 'days.day_number', 'months.month_name']);
        $costs = bycosts::where('day_id', '>=', $days[0]['id'])
                ->where('region_name_id', $id)
                ->join('products', 'bycosts.praduct_name_id', '=', 'products.id')
                ->orderby('day_id', 'DESC')
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
        
        // Protsents ma'lumotlarini yuklash
        $protsents = \App\Models\Protsent::where('region_id', $id)
                    ->orderBy('start_date', 'DESC')
                    ->get();
        
        // Age ranges ma'lumotlarini yuklash
        $age_ranges = \App\Models\Age_range::all();
        
        return view('accountant.bycosts', compact('region', 'minusproducts', 'costs', 'productall', 'id', 'days', 'protsents', 'age_ranges'));
    }

    public function pluscosts(Request $request){
        // dd($request->all());
        $mid = Day::where('id', $request->dayid)->first()->month_id;

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
                    if($row['month_id'] % 12 == 0){
                        $mth = 12;
                    }else{
                        $mth = $row['month_id'] % 12;
                    }
                    $id = $row['day_id'];
                    $day = $row['day_number'];
                    $year = $row['year_name'];
                    $html .=  "<option value=".$id.">".sprintf("%02d", $day).".".sprintf("%02d", $mth).".".$year."</option>";
                }
        $html .= "</select>";

        return $html;
    }

    public function kindreport(Request $request, $id){
        $days = $this->activmonth();
        $yeardays = $this->activyear();
        $kindgar = Kindgarden::where('id', $id)->with('age_range')->first();
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
            $protsent = Protsent::where('region_id', Kindgarden::where('id', $id)->first()->region_id)->first();
            
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
        return view('accountant.kindreport', compact('days', 'nakproducts', 'yeardays', 'costsdays', 'costs', 'ages', 'kindgar', 'protsent'));
    }

    public function nakapitwithoutcost(Request $request, $id, $ageid, $start, $end){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $ageid)->first();
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('years', 'days.year_id', '=', 'years.id')
            ->get(['days.id', 'days.day_number', 'days.month_id', 'years.year_name']);
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
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['sort'] = $row[$ageid.'sort'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                }
            }
        }
        
        $protsent = Protsent::where('region_id', Kindgarden::where('id', $id)->first()->region_id)->first();
        
        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.nakapitwithoutcost', compact('age', 'days', 'nakproducts', 'kindgar', 'protsent')), 'HTML-ENTITIES', 'UTF-8');
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

    public function nakapit(Request $request, $id, $ageid, $start, $end, $costid){
        $kindgar = Kindgarden::where('id', $id)->first();
        $region = Region::where('id', $kindgar->region_id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $ageid)->first();
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
        $allproducts = [];
        // dd($days);
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
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];
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

        $protsent = Protsent::where('region_id', Kindgarden::where('id', $id)->first()->region_id)
                    ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                    ->where('age_range_id', $ageid)
                    ->first();
        // dd($protsent);

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.nakapit', compact('age', 'days', 'nakproducts', 'costsdays', 'costs', 'kindgar', 'protsent', 'region')), 'HTML-ENTITIES', 'UTF-8');
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

    public function schotfaktur(Request $request, $id, $ageid, $start, $end, $costid){
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
		$html = mb_convert_encoding(view('pdffile.accountant.schotfaktur', compact('age', 'days', 'nakproducts', 'costs', 'kindgar')), 'HTML-ENTITIES', 'UTF-8');
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

    public function schotfaktursecond(Request $request, $id, $start, $end){
        $kindgar = Kindgarden::where('id', $id)->with('age_range')->first();

        $region = Region::where('id', $kindgar->region_id)->first();
        
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
                ->join('years', 'days.year_id', '=', 'years.id')
                ->join('months', 'days.month_id', '=', 'months.id')
                ->get(['days.day_number', 'months.id as month_id', 'years.year_name', 'days.created_at']);
        
        $costs = [];
        // dd($days->last()->year_name.'-'.($days->last()->month_id % 12 == 0 ? 12 : $days->last()->month_id % 12).$days->last()->day_number); 
        foreach($kindgar->age_range as $age){
            $costs[$age->id] = Protsent::where('region_id', $kindgar->region_id)
                        ->where('age_range_id', $age->id)
                        ->where('end_date', '>=', $days->last()->created_at->format('Y-m-d'))
                        ->first();
            if(!isset($total_number_children[$age->id])){
                $total_number_children[$age->id] = 0;
            }
            $total_number_children[$age->id] += Number_children::where('day_id', '>=', $start)->where('day_id', '<=', $end)->where('kingar_name_id', $id)->where('king_age_name_id', $age->id)->sum('kingar_children_number');
        }
        
        // Autsorser ma'lumotlari (kompaniya ma'lumotlari)
        $autorser = config('company.autorser');
        
        // Buyurtmachi ma'lumotlari
        $buyurtmachi = [
            'company_name' => $region->region_name.' ММТБга тасарруфидаги '.$kindgar->number_of_org .'-сонли ДМТТ' ?? '',
            'address' => $region->region_name,
            'inn' => '________________',
            'bank_account' => '___________________________________',
            'mfo' => '00014',
            'account_number' => '23402000300100001010',
            'treasury_account' => '_______________',
            'treasury_inn' => '________________',
            'bank' => 'Марказий банк ХККМ',
            'phone' => '__________________________',
        ];

        if(is_null(env('CONTRACT_DATA'))){
            $contract_data = " ______ '______' ___________ 2025 й";
        }else{
            $contract_data = " 25111006438231       16.07.2025 й";
        }
        
        // Hisob-faktura raqami va sanasi
        if(is_null(env('INVOICE_NUMBER'))){
            $invoice_number = $days->last()->month_id.'-'. $kindgar->number_of_org;
        }else{
            $invoice_number = $days->last()->month_id.'/'.env('INVOICE_NUMBER');
        }
        $invoice_date = $days->last()->created_at->format('d.m.Y');
        
        // Snappy PDF yaratish
        $pdf = \PDF::loadView('pdffile.accountant.schotfaktursecond', compact('contract_data', 'costs', 'days', 'kindgar', 'autorser', 'buyurtmachi', 'invoice_number', 'invoice_date', 'total_number_children'));
        
        // PDF sozlamalari
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('encoding', 'UTF-8');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('print-media-type', true);
        $pdf->setOption('disable-smart-shrinking', false);
        
        $name = $start.$end.$id."schotfaktursecond.pdf";
        
        return $pdf->stream($name);
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
		$name = $start.$end.$id."schotfakturworker.pdf";
		
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
        $numberOfChild = Number_children::where('kingar_name_id', $id)
            ->where('king_age_name_id', $ageid)
            ->where('day_id', '>=', $start)
            ->where('day_id', '<=', $end)->sum('kingar_children_number');
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.norm', compact('numberOfChild', 'age', 'days', 'date', 'nakproducts', 'kindgar')), 'HTML-ENTITIES', 'UTF-8');
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


    public function svod(Request $request){
        // dd($request->all());
        $over = $request->over;
        $nds = $request->nds;
        $days = Day::where('days.id', '>=', $request->start)->where('days.id', '<=', $request->end)
                ->join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        $regions = Region::all();
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
        
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.svod', compact('days', 'age', 'regions', 'nakproducts', 'kindgardens', 'over', 'nds')), 'HTML-ENTITIES', 'UTF-8');
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
        $regions = Region::all();
        $years = Year::all();
        $months = Month::all();
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.svod', compact('nakproducts', 'kindgardens', 'over', 'nds', 'regions', 'days', 'years', 'months')), 'HTML-ENTITIES', 'UTF-8');
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

    // katta sklad qoldiq
    public function modsofproducts(Request $request){
        $yearid = Year::where('year_active', 1)->first()->id;
        $days = $this->daysthisyear($yearid);
        
        return view('accountant.modsofproducts', ['days' => $days]);
    }

    public function getreportlargebase(Request $request){    
        $yearid = Year::where('year_active', 1)->first()->id;
        $start = $this->daysthisyear($yearid)->last()->id;
        $addlarch = Add_large_werehouse::where('add_groups.day_id', '>=', $start)
                    ->where('add_groups.day_id', '<=', $request->lastid)
                    ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                    ->join('products', 'products.id', '=', 'add_large_werehouses.product_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get();
        
        $alladd = [];
        $t = 0;
        foreach($addlarch as $row){
            if(!isset($alladd[$row->product_id])){
                $alladd[$row->product_id]['middlecost'] = 0;
                $mc = Add_large_werehouse::where('add_large_werehouses.product_id', $row->product_id)
                        ->where('add_groups.day_id', '>=', $start)
                        ->where('add_groups.day_id', '<=', $request->lastid)
                        ->join('add_groups', 'add_groups.id', '=', 'add_large_werehouses.add_group_id')
                        ->avg('cost');
                $alladd[$row->product_id]['weight'] = 0;
                $alladd[$row->product_id]['minusweight'] = 0;
                $alladd[$row->product_id]['p_name'] = $row->product_name;
                $alladd[$row->product_id]['size_name'] = $row->size_name;
                $alladd[$row->product_id]['p_sort'] = $row->sort;
                $alladd[$row->product_id]['middlecost'] = $mc;
            }
            $alladd[$row->product_id]['weight'] += $row->weight; 
        }
        $minuslarch = order_product_structure::where('order_products.day_id', '>=', $start)
                    ->where('order_products.day_id', '<=', $request->lastid)
                    ->join('order_products', 'order_products.id', '=', 'order_product_structures.order_product_name_id')
                    ->join('products', 'products.id', '=', 'order_product_structures.product_name_id')
                    ->join('sizes', 'sizes.id', '=', 'products.size_name_id')
                    ->get(["order_product_structures.product_name_id", "order_product_structures.product_weight", "products.product_name", "products.sort", "sizes.size_name" ]);
        
        foreach($minuslarch as $row){
            if(empty($alladd[$row->product_name_id])){
                $alladd[$row->product_name_id]['middlecost'] = 0;
                $alladd[$row->product_name_id]['weight'] = 0;
                $alladd[$row->product_name_id]['minusweight'] = 0;
                $alladd[$row->product_name_id]['p_name'] = $row->product_name;
                $alladd[$row->product_name_id]['size_name'] = $row->size_name;
                $alladd[$row->product_name_id]['p_sort'] = $row->sort;
            }
            $alladd[$row->product_name_id]['minusweight'] += $row->product_weight;
        }
        
        // $nochs = Number_children::where('day_id', '>=', $start)
        //             ->join('kindgardens', 'kindgardens.id', '=', 'number_childrens.kingar_name_id')
        //             ->where('day_id', '<=', $request->lastid)
        //             ->get();
        // $bymenus = Active_menu::where('day_id', '>=', $start)
        //                     ->where('day_id', '<=', $request->lastid)->get();
                            
        // $ages = Age_range::all();
        // $products = Product::all();
        // $totalproducts = [];
        // foreach($ages as $age){
        //     $foundmenu = $bymenus->where('age_range_id', $age->id);
        //     foreach($products as $prd){
        //     	if(!isset($totalproducts[$prd->id])){
        //               $totalproducts[$prd->id] = 0;
        //         } 
        //     	$w = $foundmenu->where('product_name_id', $prd->id)->sum('weight');
        //     	$totalproducts[$prd->id] += ($w * $nochs->where('king_age_name_id', $age->id)->sum('kingar_children_number')) / $prd->div;
        //     }
            // foreach($foundmenu as $menu){
            //     if(!isset($totalproducts[$menu->product_name_id])){
            //         $totalproducts[$menu->product_name_id] = 0;
            //     }               
            //     $totalproducts[$menu->product_name_id] += ($menu->weight * $noch->kingar_children_number) / $products->find($menu->product_name_id)->div;
            // }
        // }
        // return json_encode($totalproducts);
        
                            
        usort($alladd, function ($a, $b){
            if(isset($a["p_sort"]) and isset($b["p_sort"])){
                return $a["p_sort"] > $b["p_sort"];
            }
        });
        
        $html = "<table style='background-color: white' class='table'>
                <thead>
                    <tr>
                        <th rowspan='2'>Махсулот номи</th>
                        <th rowspan='2'>Ул.бир</th>
                        <th colspan='3'>Кирим</th>
                        <th colspan='3'>Чиқим</th>
                        <th colspan='3'>Қолдиқ</th>
                    </tr>
                    <tr>
                        <th>Микдори</th>
                        <th>Уртача нархи</th>
                        <th>Суммаси</th>
                        <th>Микдори</th>
                        <th>Нархи</th>
                        <th>Суммаси</th>
                        <th>Микдори</th>
                        <th>Нархи</th>
                        <th>Суммаси</th>
                    </tr>
                </thead>
                <tbody>";
        $taking = 0;
        $giving = 0;
        $mod = 0;
        foreach($alladd as $key => $row){
            $taking = $taking + $row["weight"] * $row["middlecost"];
            $giving = $giving + $row["minusweight"] * $row["middlecost"];
            $mod = $mod + ($row["weight"]-$row["minusweight"]) * $row["middlecost"];
            $html = $html."
                <tr>
                    <td>".$row["p_name"]."</td>
                    <td>".$row["size_name"]."</td>
                    <td>".sprintf('%0.2f', $row["weight"])."</td>
                    <td>".sprintf('%0.2f', $row["middlecost"])."</td>   
                    <td>".sprintf('%0.2f', $row["weight"] * $row["middlecost"])."</td>
                    <td>".sprintf('%0.2f', $row["minusweight"])."</td>
                    <td>".sprintf('%0.2f', $row["middlecost"])."</td>   
                    <td>".sprintf('%0.2f', $row["minusweight"] * $row["middlecost"])."</td>
                    <td>".sprintf('%0.2f', $row["weight"]-$row["minusweight"])."</td>
                    <td>".sprintf('%0.2f', $row["middlecost"])."</td>   
                    <td>".sprintf('%0.2f', ($row["weight"]-$row["minusweight"]) * $row["middlecost"])."</td>    
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
                <td></td>   
                <td><b>".sprintf('%0.2f', $giving)."</b></td>
                <td></td>
                <td></td>   
                <td><b>".sprintf('%0.2f',$mod)."</b></td>    
            </tr>
        ";
        $html = $html."</tbody>
                </table>
                ";
            
        return $html;
    }
    
    public function editallcosts(Request $request){
    	
    	foreach ($request->orders as $key => $value){
    		bycosts::where('day_id', $request->dayid)
                ->where('region_name_id', $request->regionid)
                ->where('praduct_name_id', $key)
                ->update(['price_cost' => $value]);
    	}
    	
    	return redirect()->route('accountant.bycosts', $request->regionid);
    }
    
    public function getingcosts(Request $request){
    	
    }

    // Protsents CRUD methods
    public function addprotsent(Request $request){
        $request->validate([
            'region_id' => 'required|integer',
            'age_range_id' => 'required|integer|exists:age_ranges,id',
            'eater_cost' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'nds' => 'required|numeric|min:0|max:100',
            'raise' => 'required|numeric|min:0|max:100',
            'protsent' => 'required|numeric|min:0|max:100'
        ]);

        \App\Models\Protsent::create($request->all());

        return redirect()->route('accountant.bycosts', $request->region_id)
                        ->with('success', 'Protsent muvaffaqiyatli qo\'shildi!');
    }

    public function getprotsent($id){
        $protsent = \App\Models\Protsent::findOrFail($id);
        $age_ranges = \App\Models\Age_range::all();
        
        $html = '
            <input type="hidden" name="protsent_id" value="'.$protsent->id.'">
            <div class="mb-3">
                <label for="edit_age_range_id" class="form-label">Yosh guruhi</label>
                <select class="form-select" id="edit_age_range_id" name="age_range_id" required>';
        
        foreach($age_ranges as $age_range) {
            $selected = ($age_range->id == $protsent->age_range_id) ? 'selected' : '';
            $html .= '<option value="'.$age_range->id.'" '.$selected.'>'.$age_range->age_name.'</option>';
        }
        
        $html .= '</select>
            </div>
            <div class="mb-3">
                <label for="edit_eater_cost" class="form-label">Ovqatlanish narxi</label>
                <input type="number" step="0.01" class="form-control" id="edit_eater_cost" name="eater_cost" value="'.$protsent->eater_cost.'" required>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="edit_start_date" class="form-label">Boshlanish sanasi</label>
                    <input type="date" class="form-control" id="edit_start_date" name="start_date" value="'.$protsent->start_date->format('Y-m-d').'" required>
                </div>
                <div class="col-md-6">
                    <label for="edit_end_date" class="form-label">Tugash sanasi</label>
                    <input type="date" class="form-control" id="edit_end_date" name="end_date" value="'.$protsent->end_date->format('Y-m-d').'" required>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <label for="edit_nds" class="form-label">QQS (%)</label>
                    <input type="number" step="0.01" class="form-control" id="edit_nds" name="nds" value="'.$protsent->nds.'" required>
                </div>
                <div class="col-md-4">
                    <label for="edit_raise" class="form-label">Ustama (%)</label>
                    <input type="number" step="0.01" class="form-control" id="edit_raise" name="raise" value="'.$protsent->raise.'" required>
                </div>
                <div class="col-md-4">
                    <label for="edit_protsent" class="form-label">Protsent (%)</label>
                    <input type="number" step="0.01" class="form-control" id="edit_protsent" name="protsent" value="'.$protsent->protsent.'" required>
                </div>
            </div>
        ';
        
        return $html;
    }

    public function editprotsent(Request $request){
        $request->validate([
            'protsent_id' => 'required|integer|exists:protsents,id',
            'age_range_id' => 'required|integer|exists:age_ranges,id',
            'eater_cost' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'nds' => 'required|numeric|min:0|max:100',
            'raise' => 'required|numeric|min:0|max:100',
            'protsent' => 'required|numeric|min:0|max:100'
        ]);

        $protsent = \App\Models\Protsent::findOrFail($request->protsent_id);
        $region_id = $protsent->region_id;
        
        $protsent->update($request->except('protsent_id'));

        return redirect()->route('accountant.bycosts', $region_id)
                        ->with('success', 'Protsent muvaffaqiyatli yangilandi!');
    }

    public function deleteprotsent(Request $request){
        $request->validate([
            'protsent_id' => 'required|integer|exists:protsents,id'
        ]);

        $protsent = \App\Models\Protsent::findOrFail($request->protsent_id);
        $region_id = $protsent->region_id;
        
        $protsent->delete();

        return redirect()->route('accountant.bycosts', $region_id)
                        ->with('success', 'Protsent muvaffaqiyatli o\'chirildi!');
    }

    public function transportation(Request $request, $id, $start, $end){
        $kindgar = Kindgarden::where('id', $id)->first();
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::all();
        $costs = Protsent::where('region_id', $kindgar->region_id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();

        $number_childrens = [];
        foreach($days as $day){
            foreach($ages as $age){
                $number_childrens[$day->id][$age->id] = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->where('king_age_name_id', $age->id)
                    ->leftJoin('titlemenus', 'titlemenus.id', '=', 'number_childrens.kingar_menu_id')
                    ->first();
            }
        }
        // make snappy pdf
        $pdf = \PDF::loadView('pdffile.accountant.transportation', compact('days', 'costs', 'number_childrens', 'kindgar', 'ages'));
        $pdf->setPaper('A3', 'landscape');
        $pdf->setOptions(['dpi' => 150]);
        return $pdf->stream('transportation.pdf');

        // return view('pdffile.accountant.transportation', compact('days', 'costs', 'number_childrens', 'kindgar'));
    }

    public function transportationSecondary(Request $request, $id, $start, $end){
        $kindgar = Kindgarden::where('id', $id)->first();
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::all();
        $costs = Protsent::where('region_id', $kindgar->region_id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();
        
        $number_childrens = [];
        foreach($days as $day){
            foreach($ages as $age){
                $number_childrens[$day->id][$age->id] = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->where('king_age_name_id', $age->id)
                    ->leftJoin('titlemenus', 'titlemenus.id', '=', 'number_childrens.kingar_menu_id')
                    ->first();
            }
        }

        $pdf = \PDF::loadView('pdffile.accountant.transportationSecondary', compact('days', 'costs', 'number_childrens', 'kindgar', 'ages'));
        $pdf->setPaper('A3', 'landscape');
        $pdf->setOptions(['dpi' => 150]);
        return $pdf->stream('transportationSecondary.pdf');

        // return view('pdffile.accountant.transportationSecondary', compact('days', 'costs', 'number_childrens', 'kindgar', 'ages'));

    }


    public function transportationThird(Request $request, $id, $start, $end){
        $kindgar = Kindgarden::where('id', $id)->first();
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::all();
        $costs = Protsent::where('region_id', $kindgar->region_id)
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();

        $number_childrens = [];
        foreach($days as $day){
            foreach($ages as $age){
                $number_childrens[$day->id][$age->id] = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $id)
                    ->where('king_age_name_id', $age->id)
                    ->leftJoin('titlemenus', 'titlemenus.id', '=', 'number_childrens.kingar_menu_id')
                    ->first();
            }
        }

        // return view('pdffile.accountant.transportationThird', compact('days', 'costs', 'number_childrens', 'kindgar', 'ages'));

        $pdf = \PDF::loadView('pdffile.accountant.transportationThird', compact('days', 'costs', 'number_childrens', 'kindgar', 'ages'));
        $pdf->setPaper('A3', 'landscape');
        $pdf->setOptions(['dpi' => 150]);
        return $pdf->stream('transportationThird.pdf');
    }

   

    public function transportationRegion(Request $request, $id, $start, $end){
        $kindgardens = Kindgarden::where('region_id', $id)->get();
        // dd($kindgardens->pluck('id')->toArray());
        $region = Region::where('id', $id)->first();
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::all();
        $costs = Protsent::where('region_id', $id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();

        // dd($costs->where('age_range_id', 4)->first()->raise);

        $number_childrens = [];
        foreach($days as $day){
            foreach($ages as $age){
                $number_childrens[$day->id][$age->id] = Number_children::where('number_childrens.day_id', $day->id)
                    ->whereIn('kingar_name_id', $kindgardens->pluck('id')->toArray())
                    ->where('king_age_name_id', $age->id)
                    ->sum('kingar_children_number');
            }
            $number_childrens[$day->id]["menu"] = Number_children::where('number_childrens.day_id', $day->id)
                    ->leftJoin('titlemenus', 'titlemenus.id', '=', 'number_childrens.kingar_menu_id')
                    ->first();
        }
        // make snappy pdf
        $pdf = \PDF::loadView('pdffile.accountant.transportationRegion', compact('days', 'costs', 'number_childrens', 'region', 'ages'));
        $pdf->setPaper('A3', 'landscape');
        $pdf->setOptions(['dpi' => 150]);
        return $pdf->stream('transportation.pdf');

        // return view('pdffile.accountant.transportation', compact('days', 'costs', 'number_childrens', 'kindgar'));
    }

    public function reportregion(Request $request, $id, $start, $end){
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);

        $costs = Protsent::where('region_id', $id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();

        $region = Region::where('id', $id)->first();
        
        $ages = Age_range::all();
        
        $kindgardens = Kindgarden::where('region_id', $id)->get();
        $number_childrens = [];
        foreach($kindgardens as $kindgarden){
            foreach($ages as $age){
                $number_childrens[$kindgarden->id][$age->id] = Number_children::where('number_childrens.day_id', '>=', $start)
                    ->where('number_childrens.day_id', '<=', $end)
                    ->where('kingar_name_id', $kindgarden->id)
                    ->where('king_age_name_id', $age->id)
                    ->sum('kingar_children_number');
            }
        }


        // return view('pdffile.accountant.reportregion', compact('region', 'days', 'costs', 'number_childrens', 'ages', 'kindgardens'));
        $pdf = \PDF::loadView('pdffile.accountant.reportregion', compact('region', 'days', 'costs', 'number_childrens', 'ages', 'kindgardens'));
        $pdf->setPaper('A3', 'landscape');
        $pdf->setOptions(['dpi' => 150]);
        return $pdf->stream('reportregion.pdf');
    }

    public function regionSchotFaktura(Request $request, $id, $start, $end){
        $kindgardens = Kindgarden::where('region_id', $id)->get();

        $region = Region::where('id', $id)->first();
        
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
                ->join('years', 'days.year_id', '=', 'years.id')
                ->join('months', 'days.month_id', '=', 'months.id')
                ->get(['days.day_number', 'months.id as month_id', 'months.month_name', 'years.year_name', 'days.created_at']);
        
        $costs = [];
        $number_childrens = [];
        $ages = Age_range::all();
        // dd($days->last()->year_name.'-'.($days->last()->month_id % 12 == 0 ? 12 : $days->last()->month_id % 12).$days->last()->day_number); 
        foreach($ages as $age){
            $costs[$age->id] = Protsent::where('region_id', $id)
                        ->where('age_range_id', $age->id)
                        ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                        ->where('end_date', '>=', $days->last()->created_at->format('Y-m-d'))
                        ->first();
            $number_childrens[$age->id] = Number_children::where('number_childrens.day_id', '>=', $start)
                    ->where('number_childrens.day_id', '<=', $end)
                    ->whereIn('kingar_name_id', $kindgardens->pluck('id')->toArray())
                    ->where('king_age_name_id', $age->id)
                    ->sum('kingar_children_number');
        }
        
        // Autsorser ma'lumotlari (kompaniya ma'lumotlari)
        $autorser = config('company.autorser');
        
        // Buyurtmachi ma'lumotlari
        $buyurtmachi = [
            'company_name' => $region->region_name.' ММТБ' ?? '',
            'address' => $region->region_name,
            'inn' => '________________',
            'bank_account' => '___________________________________',
            'mfo' => '00014',
            'account_number' => '23402000300100001010',
            'treasury_account' => '_______________',
            'treasury_inn' => '________________',
            'bank' => 'Марказий банк ХККМ',
            'phone' => '__________________________',
        ];

        if(is_null(env('CONTRACT_DATA'))){
            $contract_data = " ______ '______' ___________ 2025 й";
        }else{
            $contract_data = " 25111006438231       16.07.2025 й";
        }
        
        // Hisob-faktura raqami va sanasi
        if(is_null(env('INVOICE_NUMBER'))){
            $invoice_number = $days->last()->month_id.'-'. $id;
        }else{
            $invoice_number = $days->last()->month_id.'/'.env('INVOICE_NUMBER');
        }
        $invoice_date = $days->last()->created_at->format('d.m.Y');
        
        // Snappy PDF yaratish
        $pdf = \PDF::loadView('pdffile.accountant.regionschotfaktura', compact('contract_data', 'costs', 'ages', 'days', 'kindgardens', 'autorser', 'buyurtmachi', 'invoice_number', 'invoice_date', 'number_childrens', 'region'));
        
        // PDF sozlamalari
        $pdf->setOption('page-size', 'A4');
        $pdf->setOption('orientation', 'landscape');
        $pdf->setOption('margin-top', 10);
        $pdf->setOption('margin-bottom', 10);
        $pdf->setOption('margin-left', 10);
        $pdf->setOption('margin-right', 10);
        $pdf->setOption('encoding', 'UTF-8');
        $pdf->setOption('enable-local-file-access', true);
        $pdf->setOption('print-media-type', true);
        $pdf->setOption('disable-smart-shrinking', false);
        
        $name = $start.$end.$id."schotfaktursecond.pdf";
        
        return $pdf->stream($name);
    }

    public function regionSchotFakturaexcel(Request $request, $id, $start, $end){
        // Execution time oshirish
        set_time_limit(300); // 5 daqiqa
        
        return Excel::download(new RegionSchotFakturaExport($id, $start, $end), 'region_schotfaktura_'.date('Y-m-d').'.xlsx');
    }

    public function transportationRegionexcel(Request $request, $id, $start, $end){
        // Execution time oshirish
        set_time_limit(300); // 5 daqiqa
        
        return Excel::download(new TransportationRegionExport($id, $start, $end), 'transportation_region_'.date('Y-m-d').'.xlsx');
    }

    public function reportRegionSecondary(Request $request, $id, $start, $end){
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name', 'days.created_at']);
        $ages = Age_range::orderBy('id', 'desc')->get();
        $costs = Protsent::where('region_id', $id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();
        $region = Region::where('id', $id)->first();
        $kindgardens = Kindgarden::where('region_id', $id)->get();
        $number_childrens = [];
        foreach($kindgardens as $kindgarden){
            foreach($ages as $age){
                $number_childrens[$kindgarden->id][$age->id] = Number_children::where('number_childrens.day_id', '>=', $start)
                    ->where('number_childrens.day_id', '<=', $end)
                    ->where('kingar_name_id', $kindgarden->id)
                    ->where('king_age_name_id', $age->id)
                    ->sum('kingar_children_number');
            }
        }

        // dd($number_childrens);
        $pdf = \PDF::loadView('pdffile.accountant.reportRegionSecondary', compact('region', 'days', 'costs', 'number_childrens', 'ages', 'kindgardens'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['dpi' => 150]);

        return $pdf->stream('reportRegionSecondary.pdf');
    }

    public function reportProductsOfRegion(Request $request, $id, $start, $end, $ageid){
        $days = Day::where('days.id', '>=', $start)->where('days.id', '<=', $end)
            ->join('months', 'months.id', '=', 'days.month_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->get(['days.id', 'days.day_number', 'months.month_name', 'days.month_id', 'years.year_name', 'days.created_at']);
        $protsent = Protsent::where('region_id', $id)
                ->where('start_date', '<=', $days[0]->created_at->format('Y-m-d'))
                ->where('end_date', '>=', $days[count($days)-1]->created_at->format('Y-m-d'))
                ->get();

        $age = Age_range::where('id', $ageid)->first();
        $products = Product::all();
        $region = Region::where('id', $id)->first();
        $kindgardens = Kindgarden::where('region_id', $id)
                    ->whereHas('age_range', function($query) use ($ageid){
                        $query->where('age_range_id', $ageid);
                    })
                    ->get();
        $nakproducts = [];
        foreach($days as $day){
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->whereIn('kingar_name_id', $kindgardens->pluck('id')->toArray())
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
                    $productscount[$row->product_name_id][$ageid.'-children'] = 0;
                    $productscount[$row->product_name_id][$ageid.'div'] = $row->div;
                    $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                    $productscount[$row->product_name_id][$ageid.'sort'] = $row->sort;
                    $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                }
                $productscount[$row->product_name_id][$ageid] += $row->weight;
                $productscount[$row->product_name_id][$ageid.'-children'] += $row->kingar_children_number;
            }
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    $childs = Number_children::where('day_id', $day->id)
                                    ->whereIn('kingar_name_id', $kindgardens->pluck('id')->toArray())
                                    ->where('king_age_name_id', $ageid)
                                    ->sum('kingar_children_number');    
                    $nakproducts[0][$day->id] = $childs;
                    $nakproducts[0]['product_name'] = "Болалар сони";
                    $nakproducts[0]['size_name'] = "";
                    $nakproducts[$key][$day->id] = ($row[$ageid]*$row[$ageid.'-children']) / $row[$ageid.'div'];
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['sort'] = $row[$ageid.'sort'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                }
            }
        }

        usort($nakproducts, function ($a, $b){
            if(isset($a["sort"]) and isset($b["sort"])){
                return $a["sort"] > $b["sort"];
            }
        });

// dompdf 
        $pdf = new Dompdf();
        $pdf->loadHtml(view('pdffile.accountant.reportProductsOfRegion', compact('region', 'days', 'protsent', 'age', 'products', 'kindgardens', 'nakproducts')));
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();

        return $pdf->stream('reportProductsOfRegion.pdf');
        
        // return view('pdffile.accountant.reportProductsOfRegion', compact('region', 'days', 'protsent', 'age', 'products', 'kindgardens', 'nakproducts'));
    }   

    public function reportProductsOfRegionexcel(Request $request, $id, $start, $end, $ageid){
        set_time_limit(300);
        return Excel::download(new ReportProductsOfRegionExport($id, $start, $end, $ageid), 'report_products_of_region_'.date('Y-m-d').'.xlsx');
    }

    public function reportregionexcel(Request $request, $id, $start, $end){
        // Execution time oshirish
        set_time_limit(300); // 5 daqiqa
        
        return Excel::download(new ReportRegionExport($id, $start, $end), 'report_region_'.date('Y-m-d').'.xlsx');
    }

    public function reportRegionSecondaryexcel(Request $request, $id, $start, $end){
        // Execution time oshirish
        set_time_limit(300); // 5 daqiqa
        
        return Excel::download(new ReportRegionSecondaryExport($id, $start, $end), 'report_region_secondary_'.date('Y-m-d').'.xlsx');
    }

    public function nakapitwithoutcostexcel(Request $request, $id, $ageid, $start, $end){
        set_time_limit(300);
        return Excel::download(new NakapitWithoutCostExport($request, $id, $ageid, $start, $end), 'nakapit_without_cost_'.date('Y-m-d').'.xlsx');
    }

    public function normexcel(Request $request, $id, $ageid, $start, $end, $costid){
        set_time_limit(300);
        return Excel::download(new NormExport($id, $ageid, $start, $end, $costid), 'norm_'.date('Y-m-d').'.xlsx');
    }

    public function schotfaktursecondexcel(Request $request, $id, $start, $end){
        set_time_limit(300);
        return Excel::download(new SchotFakturaSecondExport($id, $start, $end), 'schotfaktura_second_'.date('Y-m-d').'.xlsx');
    }

    public function transportationexcel(Request $request, $id, $start, $end, $costid){
        set_time_limit(300);
        return Excel::download(new TransportationExcelExport($id, $start, $end, $costid), 'transportation_'.date('Y-m-d').'.xlsx');
    }

    public function transportationSecondaryexcel(Request $request, $id, $start, $end, $costid){
        set_time_limit(300);
        return Excel::download(new TransportationExcelExport($id, $start, $end, $costid), 'transportation_secondary_'.date('Y-m-d').'.xlsx');
    }

    public function transportationThirdexcel(Request $request, $id, $start, $end, $costid){
        set_time_limit(300);
        return Excel::download(new TransportationExcelExport($id, $start, $end, $costid), 'transportation_third_'.date('Y-m-d').'.xlsx');
    }

    public function boqchakexcel(Request $request, $id, $start, $end){

    }

}

