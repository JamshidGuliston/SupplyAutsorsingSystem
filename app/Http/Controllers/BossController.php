<?php

namespace App\Http\Controllers;

use App\Models\cashes;
use Illuminate\Http\Request;

class BossController extends Controller
{
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
