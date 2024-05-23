<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\Province;
use App\City;
use App\District;
use App\Customer;
use App\Order;
use App\Seller;
use App\OrderDetail;
use Illuminate\Support\Str;
use DB;
use App\Mail\CustomerRegisterMail;
use Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class CartController extends Controller
{
    private function getCarts()
    {
        $carts = json_decode(request()->cookie('e-carts'), true);
        $carts = $carts != '' ? $carts:[];

        return $carts;
    }

    public function addToCart(Request $request)
    {
        $this->validate($request, [
            'product_id' => 'required|exists:products,id', 
            'qty' => 'required|integer' 
        ]);

        $carts = json_decode($request->cookie('e-carts'), true); 
    
        if ($carts && array_key_exists($request->product_id, $carts)) {
            $carts[$request->product_id]['qty'] += $request->qty;
        } else {
            $product = Product::find($request->product_id);

            // get district id by
            $seller = Seller::where('id', $product->seller_id)->first();

            $temp = $seller->load('district');

            // get province id
            $provinceName = Province::where('id', $temp->district->province_id)->first();

            // get city id
            $cityName = City::where('id', $temp->district->city_id)->first();

            $carts[$request->product_id] = [
                'qty' => $request->qty,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $product->price,
                'product_image' => $product->image,
                'weight' => $product->weight,
                'seller_id' => $product->seller_id,
                'seller_name' => $product->seller_name,
                'origin_details' => [
                    'pronvice_id' => $temp->district->province_id,
                    'province_name' => $provinceName->name,
                    'city_id' => $temp->district->city_id,
                    'city_name' => $cityName->name,
                    'district_id' => $temp->district->id,
                    'address' => $temp->district->name,
                ],
                'destination_details' => [
                    'pronvice_id' => null,
                    'city_id' => null,
                    'district_id' => null,
                    'address' => null,
                ],
                'courier' => null,
                'shipping_cost' => null,
            ];
        }

        $cookie = cookie('e-carts', json_encode($carts), 2880);
        return redirect()->back()->with(['success' => 'Produk Ditambahkan ke Keranjang'])->cookie($cookie);
    }

    public function listCart()
    {
        $carts = $this->getCarts();

        // get city name by city id
        $city_id = [];
        foreach($carts as $row){
            $city_id[] = $row['origin_details']['city_id'];
        }

        $originCity = City::whereIn('id', $city_id)->get();

        $cityName = [];
        foreach($originCity as $row){
            $cityName[] = $row['name']; 
        }

        $subtotal = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['product_price']; 
        });

        return view('ecommerce.cart', compact('carts', 'subtotal', 'cityName'));
    }

    public function updateCart(Request $request)
    {
        $carts = $this->getCarts();

        if($request->product_id == ''){
            return redirect()->route('front.product');
        }else{
            foreach ($request->product_id as $key => $row) {
                if ($request->qty[$key] == 0) {
                    unset($carts[$row]);
                } else {
                    $carts[$row]['qty'] = $request->qty[$key];
                }
            }
            $cookie = cookie('e-carts', json_encode($carts), 2880);
            return redirect()->back()->cookie($cookie);
        }
    }

    // public function updateCheckout($courier, $shippingCost, $destination){

    // }

    public function getOngkir($origin, $destination, $weight, $courier)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_POSTFIELDS => "origin=501&destination=114&weight=1700&courier=jne",
            CURLOPT_POSTFIELDS => "origin=$origin&destination=$destination&weight=$weight&courier=$courier",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: 79f2b835940489c164654cc868d70936"
            ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        
        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $response = json_decode($response, true);
            $data_ongkir = $response['rajaongkir']['results'];
            return json_encode($data_ongkir);
        }
    }

    public function checkout(Request $request)
    {

        if(auth()->guard('customer')->check()){
            $customer = auth()->guard('customer')->user()->load('district');
        } else {
            return redirect(route('customer.login'));
        }

        $provinces = Province::orderBy('name', 'ASC')->get();
        // $provinces = Province::orderBy('created_at', 'DESC')->get();
        
        $carts = $this->getCarts();

        // get subtotal
        $subtotal = collect($carts)->sum(function($q) {
            return $q['qty'] * $q['product_price'];
        });

        // get origin
        $origin_id = collect($carts)->map(function($q) {
            return $q['origin_details'];
        });

        // $origin_id = [];
        // foreach($origin as $row){
        //     $origin_id[] = $row['city_id'];
        // }

        // get weight
        $weight = collect($carts)->map(function($q) {
            return $q['qty'] * $q['weight'];
        });

        return view('ecommerce.checkout', compact('provinces', 'customer', 'carts', 'subtotal', 'origin_id', 'weight'));
    }

    public function getCity()
    {
        $cities = City::where('province_id', request()->province_id)->get();
        return response()->json(['status' => 'success', 'data' => $cities]);
    }

    public function getDistrict()
    {
        $districts = District::where('city_id', request()->city_id)->get();
        return response()->json(['status' => 'success', 'data' => $districts]);
    }

    public function processCheckout(Request $request)
    {
        $this->validate($request, [
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required',
            'email' => 'required|email',
            'customer_address' => 'required',
            'origin' => 'required|exists:provinces,id',
            'destination' => 'required|exists:cities,id',
            'district_id' => 'required|exists:districts,id'
        ]);

        // dd($request->all());
        //DATABASE TRANSACTION BERFUNGSI UNTUK MEMASTIKAN SEMUA PROSES SUKSES UNTUK KEMUDIAN DI COMMIT AGAR DATA BENAR BENAR DISIMPAN, JIKA TERJADI ERROR MAKA KITA ROLLBACK AGAR DATANYA SELARAS
        DB::beginTransaction();
        try {
            $customer = Customer::where('email', $request->email)->first();
            //JIKA DIA TIDAK LOGIN DAN DATA CUSTOMERNYA ADA
            if (!auth()->guard('customer')->check() && $customer) {
                return redirect()->back()->with(['error' => 'Silahkan Login Terlebih Dahulu']);
            }

            $carts = $this->getCarts();
            $data = $request->all();

            // get cost
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                // CURLOPT_POSTFIELDS => "origin=501&destination=114&weight=1700&courier=jne",
                CURLOPT_POSTFIELDS => "origin=$request->origin&destination=$request->destination&weight=$request->weight&courier=$request->courier",
                CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                    "key: 79f2b835940489c164654cc868d70936"
                ),
            ));
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            
            // Ambil produk id
            $product = collect($carts)->map(function($q) {
                return $q['product_id'];
            });

            $sellerId = collect($carts)->map(function($q) {
                return $q['seller_id'];
            });

            $sellerName = collect($carts)->map(function($q) {
                return $q['seller_name'];
            });

            $subtotal = collect($carts)->sum(function($q) {
                return $q['qty'] * $q['product_price'];
            });

            if (!auth()->guard('customer')->check()) {
                $password = Str::random(8); 
                $customer = Customer::create([
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

            $shipping = explode('-', $request->courier);

            $order = Order::create([
                'invoice' => Str::random(4) . '-' . time(), 
                'customer_id' => $customer->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'district_id' => $request->destination,
                'subtotal' => $subtotal,
                'cost' => $request->cost,
                'shipping' => $request->courier,
                // 'shipping' => $shipping[0] . '-' . $shipping[1]
            ]);
            

            foreach ($carts as $row) {
                //AMBIL DATA PRODUK BERDASARKAN PRODUCT_ID
                $product = Product::find($row['product_id']);
                //SIMPAN DETAIL ORDER
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $row['product_id'],
                    'seller_id' => $row['seller_id'],
                    'price' => $row['product_price'],
                    'qty' => $row['qty'],
                    'weight' => $product->weight
                ]);
            }
            
            //TIDAK TERJADI ERROR, MAKA COMMIT DATANYA UNTUK MENINFORMASIKAN BAHWA DATA SUDAH FIX UNTUK DISIMPAN
            DB::commit();

            $carts = [];
            $cookie = cookie('e-carts', json_encode($carts), 2880);
            
            if (!auth()->guard('customer')->check()) {
                Mail::to($request->email)->send(new CustomerRegisterMail($customer, $password));
            }
            return redirect(route('front.finish_checkout', $order->invoice))->cookie($cookie);
        } catch (\Exception $e) {
            //JIKA TERJADI ERROR, MAKA ROLLBACK DATANYA
            DB::rollback();
            //DAN KEMBALI KE FORM TRANSAKSI SERTA MENAMPILKAN ERROR
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }
    }

    public function checkoutFinish($invoice)
    {
        $order = Order::with(['district.city.province'])->where('invoice', $invoice)->first();
        if (Order::where('invoice', $invoice)->exists()){
            return view('ecommerce.checkout_finish', compact('order'));
        }else {
            return redirect()->back();
        }    
    }
}
