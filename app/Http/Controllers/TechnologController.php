<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TechnologController extends Controller
{
    public function index(Request $request){
        return view('technolog.home');
    }
}
