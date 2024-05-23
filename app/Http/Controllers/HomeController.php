<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Customer;
use App\Product;
use App\Province;
use App\Category;
use App\User;
use App\Seller;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $orders = Order::selectRaw('COALESCE(sum(CASE WHEN status = 4 THEN subtotal + cost END), 0) as turnover, 
        COALESCE(count(CASE WHEN status = 0 THEN subtotal END), 0) as newOrder,
        COALESCE(count(CASE WHEN status = 2 THEN subtotal END), 0) as processOrder,
        COALESCE(count(CASE WHEN status = 3 THEN subtotal END), 0) as shipping,
        COALESCE(count(CASE WHEN status = 4 THEN subtotal END), 0) as completeOrder')->get();

        $customers = Customer::get();
        $categories = Category::get();
        $products = Product::get();
        $sellers = Seller::get();
        
        return view('admin.home', compact('orders','customers', 'categories', 'products', 'sellers'));
    }

    public function accountSetting($id){
        $admin = auth()->guard('web')->user();
        return view('admin.setting.setting', compact('admin'));
    }

    public function postAccountSetting(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'password' => 'nullable|string|min:5'
        ]);

        $user = Auth::guard('web')->user();
        $data = $request->only('name');

        if ($request->password != '') {
            $data['password'] = $request->password;
        }
        $user->update($data);
        return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
    }
}
