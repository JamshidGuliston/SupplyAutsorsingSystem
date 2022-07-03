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

    public function activyear(){
        $year = Year::orderBy('id', 'DESC')->first();
        $days = Day::where('year_id', $year->id)
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
        $regions = Region::all();
        $days = $this->activyear();
        // dd($regions);
        return view('accountant.reports', compact('days', 'kinds', 'regions'));
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

    public function nakapit(Request $request, $id, $ageid, $start, $end, $costid){
        $kindgar = Kindgarden::where('id', $id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $ageid)->first();
        $days = Day::where('id', '>=', $start)->where('id', '<=', $end)->get();
        $allproducts = [];
        $t = 0;
        foreach($days as $day){
            $t++;
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
            foreach($join as $row){
                array_push($allproducts, $row);
            }
            if($t == 40){
                break;
            }
        }
        // dd($allproducts);
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
            dd($nakproducts);
            usort($nakproducts, function ($a, $b){
                return $a["sort"] > $b["sort"];
            });

            dd($nakproducts);
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
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.nakapit', compact('age', 'days', 'nakproducts', 'costsdays', 'costs', 'kindgar')), 'HTML-ENTITIES', 'UTF-8');
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

    public function nakapitexcel(Request $request, $id, $ageid, $start, $end, $costid){
        // Excel::store(new NakapitelExport($request, $id, $ageid, $start, $end, $costid), "nakapitel.xlsx");
        return Excel::download(new NakapitelExport($request, $id, $ageid, $start, $end, $costid), 'excellist.xlsx');
        // return response(Storage::get('nakapitel.xlsx'))->header('Content-Type', Storage::mimeType('nakapitel.xlsx'));
    }

    public function schotfaktur(Request $request, $id, $ageid, $start, $end, $costid){
        function arr_sort($a, $b){
            return $a['sort'] <=> $b['sort'];
        }
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


        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.schotfaktur', compact('age', 'days', 'nakproducts', 'costsdays', 'costs', 'kindgar')), 'HTML-ENTITIES', 'UTF-8');
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
  
    public function schotfakturexcel(Request $request, $id, $ageid, $start, $end, $costid){
        return Excel::download(new FakturaExport($request, $id, $ageid, $start, $end, $costid), 'Fakturaexcellist.xlsx');
    }

    public function norm(Request $request, $id, $ageid, $start, $end, $costid){
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
                    ->join('norm_categories', 'products.norm_cat_id', '=', 'norm_categories.id')
                    ->join('norms', 'products.norm_cat_id', '=', 'norms.norm_cat_id')
                    ->where('norms.norm_age_id', $ageid)
                    ->where('norms.noyuk_id', 1)
                    ->get();
            // dd($join);
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
                $productscount[$row->norm_cat_id]['product_name'] = $row->norm_name;
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
                    $nakproducts[$key]['div'] = $row[$ageid.'div'];
                }
            }
            
        }
        // dd($nakproducts);

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
                        $productscount[$row->product_name_id]['size_name'] = $row->size_name;
                    }
                    
                    foreach($productscount as $key => $row){
                        if(!isset($nakproducts[$key][$row_id])){
                            $nakproducts[$key][$row_id] = 0;
                        }
                        $nakproducts[$key][$row_id] += ($row[$age->id]*$row[$age->id.'-children']) / $row[$age->id.'div'];
                        $nakproducts[$key]['product_name'] = $row['product_name'];
                        $nakproducts[$key]['size_name'] = $row['size_name'];
                    }
    
                }
                
            }
        }

        $costs = bycosts::where('day_id', bycosts::where('day_id', '<=', $first)->where('region_name_id', $request->region_id)->orderBy('day_id', 'DESC')->first()->day_id)
                ->where('region_name_id', $request->region_id)
                ->orderBy('day_id', 'DESC')->get();

        foreach($costs as $cost){
            if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
            }
        }
        // dd($nakproducts);
        $dompdf = new Dompdf('UTF-8');
		$html = mb_convert_encoding(view('pdffile.accountant.svod', compact('age', 'nakproducts', 'kindgardens')), 'HTML-ENTITIES', 'UTF-8');
		$dompdf->loadHtml($html);

		$dompdf->setPaper('A3',  'landscape');
		$name = "svod.pdf";
		$dompdf->render();
		$dompdf->stream($name, ['Attachment' => 0]); 
    }


}
