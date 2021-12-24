<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StorageController extends Controller
{
    public function index(Request $request){
        return view('storage.home');
    }
}
