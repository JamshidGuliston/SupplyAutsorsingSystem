<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    protected function redirectTo(){
        if(Auth()->user()->role_id == 4){
            return route('storage.home');
        }
        elseif(Auth()->user()->role_id == 3){
            return route('technolog.home');
        }
        elseif(Auth()->user()->role_id == 5){
            return route('accountant.home');
        }
        elseif(Auth()->user()->role_id == 6){
            return route('chef.home');
        }
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request){
        $input = $request->all();
        $this->validate($request, [
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if(auth()->attempt(array('email'=>$input['email'], 'password'=>$input['password']))){
            if(auth()->user()->role_id == 4){
                return redirect()->route('storage.home');
            }
            elseif(auth()->user()->role_id == 3){
                return redirect()->route('technolog.home');
            }
            elseif(auth()->user()->role_id == 5){
                return redirect()->route('accountant.home');
            }
            elseif(auth()->user()->role_id == 6){
                return redirect()->route('chef.home');
            }
        }
        else{
            return redirect()->route('login')->with('error', 'Email and password are wrong');
        }
    }
}