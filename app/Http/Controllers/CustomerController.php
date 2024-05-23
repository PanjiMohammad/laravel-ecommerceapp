<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = Customer::orderBy('created_at', 'DESC');

        if (request()->q != '') {
            $customer = $customer->where('name', 'LIKE', '%' . request()->q . '%');
        }

        // if (auth()->guard('customer')->check()) return redirect(route('customer.dashboard'));

        $provinces = Province::orderBy('created_at', 'DESC')->get();

        $customer = $customer->paginate(10);
        return view('admin.customer.index', compact('customer', 'provinces'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        return view('admin.customer.edit', compact('customer', 'provinces'));
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
            'email' => $request->email,
            'password' => $request->password,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'district_id' => $request->district_id,
            'activate_token' => null,
            'status' => $request->status,
        ]);

        return redirect(route('consumen.index'))->with(['success' => 'Customer Berhasil Diperbaharui']);

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
