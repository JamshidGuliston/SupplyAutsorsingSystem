<?php

namespace App\Http\Controllers;

use App\Models\Kindgarden;
use App\Models\Product;
use App\Models\Temporary;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChefController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Tashkent');
        $user = User::where('id', auth()->user()->id)->with('kindgarden')->first();
        $kindgarden = Kindgarden::where('id', $user->kindgarden[0]['id'])->with('age_range')->first();
        $sendchildcount = Temporary::where('kingar_name_id', $user->kindgarden[0]['id'])->get();
        $productall = Product::join('sizes', 'sizes.id', '=', 'products.size_name_id')->get();

        return view('chef.home', compact('productall', 'kindgarden', 'sendchildcount'));
    }

    public function sendnumbers(Request $request)
    {
        $row = Temporary::where('kingar_name_id', $request->kingar_id)->get();
        if($row->count() == 0){
            foreach($request->agecount as  $key => $value){
                Temporary::create([
                    'kingar_name_id' => $request->kingar_id,
                    'age_id' => $key,
                    'age_number' => $value
                ]);
            }
        }
        return redirect()->route('chef.home');
    }
}
