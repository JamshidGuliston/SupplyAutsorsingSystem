<?php

namespace App\Http\Controllers;

use App\Models\all_costs;
use App\Models\cashes;
use App\Models\costs;
use App\Models\Day;
use Illuminate\Http\Request;

class CasherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function days(){
        $days = Day::join('months', 'months.id', '=', 'days.month_id')
                ->join('years', 'years.id', '=', 'days.year_id')
                ->orderby('days.id', 'DESC')
                ->get(['days.id', 'days.day_number', 'months.month_name', 'years.year_name']);
        return $days;
    }

    public function index()
    {
        $days = $this->days();
        $allcosts = all_costs::where('allcost_hide', 1)->get();
        $cashes = cashes::join('all_costs', 'all_costs.id', '=', 'cashes.allcost_id')
            ->select('cashes.id as cashid', 'cashes.description', 'cashes.summ', 'months.month_name', 'years.year_name', 'all_costs.allcost_name', 'days.day_number', 'cashes.status')
            ->join('days', 'days.id', '=', 'cashes.day_id')
            ->join('years', 'years.id', '=', 'days.year_id')
            ->join('months', 'months.id', '=', 'days.month_id')
            ->orderby('cashes.id', 'DESC')
            ->paginate(50);
        return view('casher.home', compact('cashes', 'days', 'allcosts'));
    }
    
    public function costs(){
        $costs = costs::where('cost_hide', 1)->orderby('id', 'DESC')->paginate(50);
        return view('casher.costs', compact('costs'));
    }

    public function createcost(Request $request){
        costs::create([
            'cost_name' => $request->name,
            'cost_img' => '...',
            'cost_hide' => 1,
        ]);
        return redirect()->route('casher.costs');   
    }
    public function deletecost(Request $request){
        costs::where('id', $request->costid)->update(['cost_hide' => 0]);
        return redirect()->route('casher.costs');
    }

    public function editecost(Request $request){

        return redirect()->route('casher.costs');
    }

    public function allcosts(){
        $allcosts = all_costs::join('costs', 'costs.id', '=', 'all_costs.cost_name_id')
            ->select('all_costs.id as allid', 'costs.cost_name', 'all_costs.allcost_name')
            ->where('allcost_hide', 1)
            ->orderby('all_costs.id', 'DESC')->paginate(50);
        $costs = costs::where('cost_hide', 1)->orderby('id', 'DESC')->get();
        return view('casher.all_costs', compact('costs', 'allcosts'));
    }

    public function allcreatecost(Request $request){
        all_costs::create([
            'cost_name_id' => $request->catid,
            'allcost_name' => $request->name,
            'allcost_hide' => 1,
        ]);
        return redirect()->route('casher.allcosts');   
    }
    public function alldeletecost(Request $request){
        all_costs::where('id', $request->costid)->update(['allcost_hide' => 0]);
        return redirect()->route('casher.allcosts')->with('status', "Faoliyati tugatildi");
    }

    public function alleditecost(Request $request){

        return redirect()->route('casher.allcosts');
    }

    public function createcash(Request $request){
        cashes::create([
            'allcost_id' => $request->catid,
            'day_id' => $request->dayid,
            'summ' => $request->value,
            'description' => $request->description,
            'vid' => 0,
            'status' => 1
        ]);
        return redirect()->route('casher.home');
    }

    public function deletecash(Request $request){
        $r = cashes::where('id', $request->cashid)->first();
        
        if($r->status == 1){
            cashes::where('id', $request->cashid)->delete();
            return redirect()->route('casher.home')->with('status', "Uchirildi");
        }else{
            return redirect()->route('casher.home');
        }
    }

    public function report(Request $request){
        $days = $this->days();
        $costs = costs::where('cost_hide', 1)->get();
        
        return view('casher.report', compact('days', 'costs'));
    }

    public function selectallcost($id)
    {
        $all = all_costs::where('cost_name_id', $id)->where('allcost_hide', 1)->get();

        $html = "<select class='form-select' id='allcostid' aria-label='Default select example'>
                    <option value='0' >Hammasi</option>";
                foreach($all as $row){
                    $id = $row->id;
                    $name = $row->allcost_name;
                    $html .=  "<option value=".$id.">".$name."</option>";
                }
        $html .= "</select>";

        return $html;
    }

    public function selectreport($type, $id, $b, $e){
        $days = $this->days();
        switch ($type) {
            case 0:
                $report = cashes::where('status', 2)->where('day_id', '>=', $b)->where('day_id', '<=', $e)->get();
                break;
            case 1:
                $report = cashes::where('status', 2)
                        ->where('all_costs.cost_name_id', '=', $id)
                        ->where('day_id', '>=', $b)->where('day_id', '<=', $e)
                        ->join('all_costs', 'all_costs.id', '=', 'cashes.allcost_id')
                        ->get();
                break;
            case 2:
                $report = cashes::where('status', 2)->where('allcost_id', '=', $id)->where('day_id', '>=', $b)->where('day_id', '<=', $e)->get();
                break;
        }
        $html = "<table class='table table-light py-4 px-4'>
                    <thead>
                        <tr>
                            <th scope='col'>ID</th>
                            <th scope='col'>Izoh</th>
                            <th scope='col'>Belgilangan sana</th>
                            <th scope='col'>Yaratilgan sana</th>
                            <th scope='col'>Miqdori</th>
                        </tr>
                    </thead>
                    <tbody>";
                    $total = 0;
                    foreach($report as $row){
                        $total = $total + $row->summ;
                        $html = $html."<tr>
                            <td>".$row->id."</td>
                            <td>".$row->description."</td>
                            <td>".$days->find($row->day_id)->day_number.".".$days->find($row->day_id)->month_name.".".$days->find($row->day_id)->year_name."</td>
                            <td>".$row->created_at."</td>
                            <td>".$row->summ."</td>
                            </tr>";
                    }
        $html = $html."<tr><td><b>Jami:</b></td><td colspan='3'></td><td><b>".$total."</b></td></tr></tbody>
                </table>";

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
    public function update(Request $request, $id)
    {
        //
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
