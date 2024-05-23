<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Customer;
use App\Order;
use App\User;
use App\Mail\CustomerResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Mail;


class LoginController extends Controller
{
    public function loginForm()
    {
        if (auth()->guard('customer')->check()) return redirect(route('customer.dashboard'));
        return view('ecommerce.login');
    }

    public function forgotPassword()
    {
        return view('ecommerce.forgotpassword');
    }

    public function resetPassword(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $data = Customer::where('email', $request->email)->first();

        if($data != null){
            $customer = Customer::find($data->id);
            $password = Str::random(8); 
            $customer->update([
                'name' => $customer->name,
                'email' => $customer->email,
                'password' => $password,
                'phone_number' => $customer->phone_number,
                "address" => $customer->address,
                "district_id" => $customer->district_id,
                "activate_token" => Str::random(30),
                'status' => 0
                
            ]);

            Mail::to($request->email)->send(new CustomerResetPasswordMail($customer, $password));

            return redirect()->back()->with(['success' => 'Atur Ulang Kata Sandi Berhasil, Silahkan Cek Email.']);
        } else {
            return redirect()->back()->with(['error' => 'Atur Ulang Kata Sandi Gagal, Email Tidak Terdaftar.']);
        }

        return redirect()->back()->with(['error' => 'Atur Ulang Kata Sandi Gagal. Silahkan Kembali']);
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $auth = $request->only('email', 'password');
        $auth['status'] = 1; 
    
        if (auth()->guard('customer')->attempt($auth)) {
            // return redirect()->intended(route('customer.dashboard'));
            return redirect(route('customer.dashboard'));
        } else {
            return redirect()->back()->with(['error' => 'Email / Password Salah']);
        }

        return redirect()->back()->with(['error' => 'Gagal Login']);
    }

    public function dashboard()
    {
        //Terdapat kondisi dengan menggunakan CASE, dimana jika kondisinya terpenuhi dalam hal ini status 
        //maka subtotal akan di-sum, kemudian untuk shipping dan complete hanya di count order

        $orders = Order::selectRaw('
                COALESCE(sum(CASE WHEN status = 0 THEN subtotal + cost END), 0) as pending, 
                COALESCE(count(CASE WHEN status = 3 THEN subtotal END), 0) as shipping,
                COALESCE(count(CASE WHEN status = 4 THEN subtotal END), 0) as completeOrder')
                ->where('customer_id', auth()->guard('customer')->user()->id)->get();

        return view('ecommerce.dashboard', compact('orders'));
    }

    public function logout()
    {
        auth()->guard('customer')->logout(); 
        return redirect(route('customer.login'));
    }
}
