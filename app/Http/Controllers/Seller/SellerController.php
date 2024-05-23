<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\Customer;
use App\Product;
use App\Category;
use App\User;
use App\Seller;
use App\Province;
use App\OrderDetail;


class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('seller');
        $this->middleware(function ($request, $next) {

            $this->user = Auth::user();

            return $next($request);
        });
    }

    public function verifySellerRegistration($token)
    {
        $seller = Seller::where('activate_token', $token)->first();
        if ($seller) {

            $seller->update([
                'activate_token' => null,
                'status' => 1
            ]);
            return redirect(route('login'))->with(['success' => 'Verifikasi Berhasil, Silahkan Login']);
        }
        return redirect(route('login'))->with(['error' => 'Invalid Verifikasi Token']);
    }

    public function index()
    {
        $orders = Order::selectRaw('id,
            COALESCE(sum(CASE WHEN status = 4 THEN cost END), 0) as shippingCost, 
            COALESCE(sum(CASE WHEN status = 4 THEN subtotal + cost END), 0) as turnover, 
            COALESCE(count(CASE WHEN status = 0 THEN subtotal END), 0) as newOrder,
            COALESCE(count(CASE WHEN status = 2 THEN subtotal END), 0) as processOrder,
            COALESCE(count(CASE WHEN status = 3 THEN subtotal END), 0) as shipping,
            COALESCE(count(CASE WHEN status = 4 THEN subtotal END), 0) as completeOrder')->groupBy('id')->get();
        
        // convert to array
        $order_id = [];
        foreach($orders as $row){
            $order_id[] = $row['id'];
        }

        $detailOrder = OrderDetail::whereIn('order_id', $order_id)->get();

        $groups = $detailOrder->where('seller_id', auth()->guard('seller')->user()->id)->groupBy('order_id');

        // get order id by seller id
        $temp = [];
        foreach ($detailOrder as $row) {
            $temp[] = $row['order_id'];
        }  

        if($temp != null){
            // get cost
            foreach($orders as $row){
                $shippingCost = $row['turnover'];
            }

            // count quantity & harga produk
            $groupwithcount = $groups->map(function ($group) {
                return [
                    'id' => $group->first()['order_id'],
                    'kuantiti' => $group->sum('qty'),
                    'harga' => $group->sum('price'),
                ];
            });

            // get subtotal (kuantiti dikali harga)
            $subtotal = $groupwithcount->sum(function($q){
                return $q['kuantiti'] * $q['harga'];
            });

            // get total omset
            $totalOmset = collect([$shippingCost, $subtotal])->pipe(function($q){
                return $q[0] - $q[1];
            });
        } else {
            $totalOmset = '0';
        }
       
        $customers = Customer::get();
        $categories = Category::get();
        $products = Product::where('seller_id', auth()->guard('seller')->user()->id)->get();
        
        return view('seller.home', compact('orders','customers', 'categories', 'products', 'totalOmset'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        return view('seller.seller.create', compact('provinces')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'customer_address' => 'required|string',
            'province_id' => 'required|exists:provinces,id',
            'city_id' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        if(Seller::where('email', $request->email)->exists()){
            return redirect()->back()->with(['error' => 'Email Sudah Ada']);
        } else {
            try {
                if (!auth()->guard('seller')->check()) {
                    $password = Str::random(8); 
                    $seller = Seller::create([
                        'name' => $request->customer_name,
                        'email' => $request->email,
                        'password' => $password, 
                        'phone_number' => $request->customer_phone,
                        'address' => $request->customer_address,
                        'district_id' => $request->district_id,
                        'activate_token' => Str::random(30),
                        'status' => false
                    ]);
                }

                // return redirect(route('seller.index'))->with(['success' => 'Registrasi Member Berhasil, Silahkan Cek Email.']);
                if (!auth()->guard('seller')->check()) {
                    Mail::to($request->email)->send(new SellerRegisterMail($seller, $password));
                }
                return redirect(route('seller.index'))->with(['success' => 'Registrasi Member Berhasil, Silahkan Cek Email.']);

            } catch (\Exception $e) {
                return redirect()->back()->with(['error' => $e->getMessage()]);
            }
        }
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
        $customer = Customer::find($id);
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('customer.edit', compact('customer', 'provinces'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'phone_number' => 'required|max:15',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string'
        ]);


        // $user = auth()->guard('customer')->user();

        $customer = Customer::find($id);

        $data = $request->only('name', 'phone_number', 'address', 'district_id');

        if ($request->password != '') {
            $data['password'] = $request->password;
        }

        $customer->update([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'district_id' => $request->district_id,
        ]);

        return redirect(route('customer.index'))->with(['success' => 'Customer Berhasil Diperbaharui']);

        // return redirect(route('customer.index'))->with(['success' => 'Data Produk Diperbaharui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id); 
        $customer->delete();
        return redirect(route('customer.index'))->with(['success' => 'Customer Berhasil Dihapus']);
    }

    public function accountSetting($id){
        $seller = auth()->guard('seller')->user()->load('district');
        // dd($seller);
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('seller.setting.setting', compact('seller', 'provinces'));
    }

    public function postAccountSetting(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'phone_number' => 'required|max:15',
            'address' => 'required|string',
            'district_id' => 'required|exists:districts,id',
            'password' => 'nullable|string|min:5'
        ]);

        $user = Auth::guard('seller')->user();
        $data = $request->only('name', 'phone_number', 'address', 'district_id');

        if ($request->password != '') {
            $data['password'] = $request->password;
        }
        $user->update($data);
        return redirect()->back()->with(['success' => 'Profil berhasil diperbaharui']);
    }
}
