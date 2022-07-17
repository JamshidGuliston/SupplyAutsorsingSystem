<?php

namespace App\Exports;

use App\Models\Age_range;
use App\Models\bycosts;
use App\Models\Day;
use App\Models\Kindgarden;
use App\Models\Number_children;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class FakturaExport implements FromView
{
    protected $request, $id, $ageid, $start, $end, $costid;
    public function __construct(Request $request, $id, $ageid, $start, $end, $costid)
    {
        $this->request = $request;
        $this->id = $id;
        $this->ageid =  $ageid;
        $this->start = $start;
        $this->end = $end;
        $this->costid = $costid;
    } 

    public function view(): View
    {
        $kindgar = Kindgarden::where('id', $this->id)->first();
        $nakproducts = [];
        $age = Age_range::where('id', $this->ageid)->first();
        $days = Day::where('id', '>=', $this->start)->where('id', '<=', $this->end)->get();
        
        foreach($days as $day){
            $join = Number_children::where('number_childrens.day_id', $day->id)
                    ->where('kingar_name_id', $this->id)
                    ->where('king_age_name_id', $this->ageid)
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
                if(!isset($productscount[$row->product_name_id][$this->ageid])){
                    $productscount[$row->product_name_id][$this->ageid] = 0;
                }
                $productscount[$row->product_name_id][$this->ageid] += $row->weight;
                $productscount[$row->product_name_id][$this->ageid.'-children'] = $row->kingar_children_number;
                $productscount[$row->product_name_id][$this->ageid.'div'] = $row->div;
                $productscount[$row->product_name_id][$this->ageid.'sort'] = $row->sort;
                $productscount[$row->product_name_id]['product_name'] = $row->product_name;
                $productscount[$row->product_name_id]['size_name'] = $row->size_name;
            }
            dd($productscount);
            foreach($productscount as $key => $row){
                if(isset($row['product_name'])){
                    
                    $nakproducts[$key][$day->id] = ($row[$this->ageid]*$row[$this->ageid.'-children']) / $row[$this->ageid.'div'];;
                    $nakproducts[$key]['product_name'] = $row['product_name'];
                    $nakproducts[$key]['size_name'] = $row['size_name'];
                    $nakproducts[$key]['sort'] = $row[$this->ageid.'sort'];
                }
            }
            // dd($nakproducts);
            $costs = bycosts::where('day_id', $this->costid)->where('region_name_id', Kindgarden::where('id', $this->id)->first()->region_id)
                    ->orderBy('day_id', 'DESC')->get();
            
            foreach($costs as $cost){
                if(isset($nakproducts[$cost->praduct_name_id]['product_name'])){
                    $nakproducts[$cost->praduct_name_id][0] = $cost->price_cost;
                }
            }

            $costsdays = bycosts::where('region_name_id', Kindgarden::where('id', $this->id)->first()->region_id)
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
        
        return view('pdffile.accountant.schotfakturexcel', compact('age', 'days', 'nakproducts', 'costsdays', 'costs', 'kindgar'));
        
    }
}
