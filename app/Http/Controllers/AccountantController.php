<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountantController extends Controller
{
    public function index(Request $request)
    {
        return view('accountant.home');
    }

    public function costs(Request $request){
            
    }
}
