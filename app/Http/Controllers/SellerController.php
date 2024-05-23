<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Order;
use App\Customer;
use App\Product;
use App\Category;
use App\User;
use App\Seller;
use App\Province;

use App\Mail\SellerRegisterMail;
use App\Mail\CustomerRegisterMail;
use Mail;

class SellerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function index()
    {
        $seller = Seller::orderBy('created_at', 'DESC');

        if (request()->q != '') {
            $seller = $seller->where('name', 'LIKE', '%' . request()->q . '%');
        }

        // if (auth()->guard('customer')->check()) return redirect(route('customer.dashboard'));

        $provinces = Province::orderBy('created_at', 'DESC')->get();

        $seller = $seller->paginate(10);
        
        return view('admin.seller.index', compact('seller', 'provinces'));
    }

    public function create()
    {
        $provinces = Province::orderBy('created_at', 'DESC')->get();
        return view('admin.seller.create', compact('provinces')); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
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


        if(Seller::where('email', $request->email)->exists()){
            return redirect()->back()->with(['error' => 'Email Sudah Ada']);
        } else {
            try {
                if (!auth()->guard('seller')->check()) {
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
                }

                dd($seller);

                if (!auth()->guard('seller')->check()) {
                    Mail::to($request->email)->send(new SellerRegisterMail($seller, $password));
                }
                return redirect()->back()->with(['success' => 'Registrasi Berhasil, Silahkan Cek Email.']);

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
        $seller = Seller::find($id);
        $provinces = Province::orderBy('name', 'ASC')->get();
        return view('admin.seller.edit', compact('seller', 'provinces'));
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

        $seller = Seller::find($id);

        $seller->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'district_id' => $request->district_id,
            'activate_token' => null,
            'status' => $request->status,
        ]);

        return redirect(route('seller.newIndex'))->with(['success' => 'Data Berhasil Diperbaharui']);

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
}
