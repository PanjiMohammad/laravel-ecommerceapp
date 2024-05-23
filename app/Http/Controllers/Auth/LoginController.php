<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

use App\Province;
use App\Mail\SellerRegisterMail;
use App\Mail\CustomerRegisterMail;
use App\Seller;
use Mail;



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

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    // Default
    // protected $redirectTo = RouteServiceProvider::HOME;
    
    // Custom
    // protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('guest')->except('logout');
        
    // }

    public function showLoginForm(){
        return view('auth.login');
    }

    public function newRegister(){
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        return view('auth.register', compact('provinces'));

        dd(Seller::get());
    }

    public function postRegister(Request $request)
    {   
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'phone_number' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        try {
            if(Seller::where('email', $request->email)->exists()){
                return redirect()->back()->with(['error' => 'Email Sudah Ada, Silahkan Coba Lagi.']);
            }

            $password = Str::random(8); 
            $seller = Seller::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password, 
                'phone_number' => $request->phone_number,
                'address' => $request->address,
                'district_id' => $request->district_id,
                'activate_token' => Str::random(30),
                'status' => false
            ]);

            Mail::to($request->email)->send(new SellerRegisterMail($seller, $password));

            return redirect()->back()->with(['success' => 'Registrasi Berhasil, Silahkan Cek Email.']);

        } catch (\Exception $e) {
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    

    public function postLogin(Request $request){   
        
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $auth = $request->only('email', 'password');
        $auth['status'] = 1; 

        $data = $request->only('email', 'password');

        if (auth()->guard('seller')->attempt($data)) {
            if(auth()->guard('seller')->user()->status == '0'){
                return redirect()->back()->with(['error' => 'Email Belum Diverifikasi']);
            }
            // dd('berhasil');
            // return redirect()->intended(route('customer.dashboard'));
            return redirect(route('seller.dashboard'));
        }

        if (auth()->guard('web')->attempt($data)) {
            // return redirect()->intended(route('customer.dashboard'));
            return redirect(route('home'));
        }

        return redirect()->back()->with(['error' => 'Email / Password Salah']);
          
    }

    public function logout(Request $request){
        Auth::logout();
        return redirect(route('login'));
    }

}
