<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChefController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Tashkent');
        return view('chef.home');
    }
}
