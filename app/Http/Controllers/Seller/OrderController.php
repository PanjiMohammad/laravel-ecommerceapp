<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Order;
use App\OrderDetail;
use App\Mail\OrderMail;
use Mail;
use Carbon\Carbon;
use PDF;

class OrderController extends Controller
{
    public function index() 
    {
        $orders = Order::with(['customer.district.city.province'])->withCount('return')->orderBy('created_at', 'DESC');

        if(request()->q != '') {
            $orders = $orders->where(function($q) {
                $q->where('customer_name', 'LIKE', '%' . request()->q . '%')
                ->orWhere('invoice', 'LIKE', '%' . request()->q . '%')
                ->orWhere('customer_address', 'LIKE', '%' . request()->q . '%');
            });
        }

        if (request()->status != '') {
            $orders = $orders->where('status', request()->status);
        }

        $orders = $orders->paginate(10); 

        $order_id = [];
        $shippingCost = [];
        foreach($orders->all() as $row){
            $order_id[] = $row['id'];
            $shippingCost[] = $row['cost'];
        }

        $detailOrder = OrderDetail::whereIn('order_id', $order_id)->orderBy('created_at', 'DESC')->get();

        $groups = $detailOrder->where('seller_id', auth()->guard('seller')->user()->id)->groupBy('order_id'); 

        // we will use map to cumulate each group of rows into single row.
        // $group is a collection of rows that has the same opposition_id.
        $groupwithcount = $groups->map(function ($group) {
            return [
                'id' => $group->first()['order_id'],
                'kuantiti' => $group->sum('qty'),
                'harga' => $group->sum('price'),
            ];
        });

        $subtotal = $groupwithcount->map(function ($q) {
            return $q['kuantiti'] * $q['harga'];
        });

        // convert
        $sub = [];
        foreach($subtotal as $row){
            $sub[] = $row;
        }

        $combine = collect([$shippingCost, $sub])->pipe(function ($q) {
            return collect([
                'subtotal' => $q[1],
                'shippingCost' => $q[0],
            ]);
        });

        $totalOmset = array_fill(0, count($combine), 0);
        foreach ($combine as $collection) {
            foreach ($collection as $key => $value) {
                $totalOmset[$key] += $value;
            }
        }

        // create array
        $result = array($totalOmset);

        return view('seller.orders.index', compact('orders', 'totalOmset'));   
    }

    public function view($invoice) 
    {
        if (Order::where('invoice', $invoice)->exists()){
            $order = Order::with(['customer.district.city.province', 'payment', 'details.product'])->withCount('return')->where('invoice', $invoice)->first();

            $details = $order->details->where('order_id', $order->id)->where('seller_id', auth()->guard('seller')->user()->id);

            $preSubtotal = collect($details)->pipe(function($q){
                return collect([
                    'harga' => $q->sum('price'),
                    'kuantiti' => $q->sum('qty'),
                ]);
            });
            
            $subtotal = $preSubtotal->pipe(function($q){
                return $q['harga'] * $q['kuantiti'];
            });

            $total = collect([$order->cost, $subtotal])->pipe(function($q){
                return $q[0] + $q[1];
            });

            return view('seller.orders.view', compact('order', 'details', 'subtotal', 'total'));
        }else {
            return redirect()->back();
        }    
    }

    public function acceptPayment($invoice)
    {
        $order = Order::with(['payment'])->where('invoice', $invoice)->first();

        $order->payment()->update(['status' => 1]);
        $order->update(['status' => 2]);
        return redirect(route('orders.newView', $order->invoice))->with(['success' => 'Pembayaran Sudah dikonfirmasi']);
    }

    public function shippingOrder(Request $request)
    {
        $order = Order::with(['customer'])->find($request->order_id);
        $order->update(['tracking_number' => $request->tracking_number, 'status' => 3]);

        Mail::to($order->customer->email)->send(new OrderMail($order));
        return redirect()->back();
    }

    public function return($invoice) 
    {
        if (Order::where('invoice', $invoice)->exists()){
            $order = Order::with(['return', 'customer'])->where('invoice', $invoice)->first();

            $detailOrder = OrderDetail::where('order_id', $order->id)->get();

            $orders = $detailOrder->where('seller_id', auth()->guard('seller')->user()->id);
                
            $subtotal = $orders->sum(function($q){
                return $q['price'] * $q['qty'];
            });

            $totalReturn = collect([$order->cost, $subtotal])->pipe(function($q){
                return $q[0] + $q[1];
            });

            return view('seller.orders.return', compact('order', 'totalReturn'));
        }else {
            return redirect()->back();
        }
    }

    public function approveReturn(Request $request)
    {
        $this->validate($request, ['status' => 'required']);

        $order = Order::find($request->order_id);
        $order->return()->update(['status' => $request->status]);
        $order->update(['status' => 4]);
        return redirect()->back();
    }

    public function orderReport()
    {
        $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        if (request()->date != '') {
            $date = explode(' - ' ,request()->date);
            $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';
        }

        $orders = Order::with(['customer.district'])->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC')->get();

        // get order id, cost
        $order_id = [];
        $shippingCost = [];
        foreach($orders as $row){
            $order_id[] = $row['id'];
            $shippingCost[] = $row['cost'];
        }

        $detailOrder = OrderDetail::whereIn('order_id', $order_id)->orderBy('created_at', 'DESC')->get();

        $groups = $detailOrder->where('seller_id', auth()->guard('seller')->user()->id)->groupBy('order_id'); 

        // we will use map to cumulate each group of rows into single row.
        // $group is a collection of rows that has the same opposition_id.
        $groupwithcount = $groups->map(function ($group) {
            return [
                'id' => $group->first()['order'],
                'kuantiti' => $group->sum('qty'),
                'harga' => $group->sum('price'),
            ];
        });

        // get subtotal (count price * kuantiti)
        $subtotal = $groupwithcount->map(function($q){
            return $q['harga'] * $q['kuantiti'];
        });

        // convert
        $sub = [];
        foreach($subtotal as $row){
            $sub[] = $row;
        }

        $combine = collect([$shippingCost, $sub])->pipe(function($q){
            return collect([
                'shipping cost' => $q[0],
                'subtotal' => $q[1],
            ]);
        });


        if(count($combine) > 1){
            $totalOmset = array_fill(0, count($combine), 0);
            foreach($combine as $collection){
                foreach($collection as $key => $value){
                    $totalOmset[$key] += $value;
                }
            }
        } else {
            $totalOmset = $combine->sum(function($q){
                return $q[0] + $q[1];
            });
        }


        return view('seller.report.index', compact('orders', 'totalOmset'));
    }

    public function orderReportPdf($daterange)
    {
        $date = explode('+', $daterange); 

        $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

        $orders = Order::with(['customer.district'])->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC')->get();

        // get order id, cost
        $order_id = [];
        $shippingCost = [];
        foreach($orders as $row){
            $order_id[] = $row['id'];
            $shippingCost[] = $row['cost'];
        }

        $detailOrder = OrderDetail::whereIn('order_id', $order_id)->orderBy('created_at', 'DESC')->get();

        $groups = $detailOrder->where('seller_id', auth()->guard('seller')->user()->id)->groupBy('order_id'); 

        // we will use map to cumulate each group of rows into single row.
        // $group is a collection of rows that has the same opposition_id.
        $groupwithcount = $groups->map(function ($group) {
            return [
                'id' => $group->first()['order'],
                'kuantiti' => $group->sum('qty'),
                'harga' => $group->sum('price'),
            ];
        });

        // get subtotal (count price * kuantiti)
        $subtotal = $groupwithcount->map(function($q){
            return $q['harga'] * $q['kuantiti'];
        });

        // convert
        $sub = [];
        foreach($subtotal as $row){
            $sub[] = $row;
        }

        $combine = collect([$shippingCost, $sub])->pipe(function($q){
            return collect([
                'shipping cost' => $q[0],
                'subtotal' => $q[1],
            ]);
        });

        if(count($combine) > 1) {
            // $totalOmset = true;

            $totalOmset = array_fill(0, count($combine), 0);
            foreach($combine as $collection){
                foreach($collection as $key => $value){
                    $totalOmset[$key] += $value;
                }
            }

            $result = array_sum($totalOmset);

        } else {
            $totalOmset = $combine->sum(function($q) {
                return $q[0] + $q[1];
            });

            $result = $totalOmset;
        }

        $pdf = PDF::loadView('seller.report.orderpdf', compact('orders', 'totalOmset', 'result', 'date'));

        $startpdf = Carbon::parse($date[0])->format('d-F-Y');
        $endpdf = Carbon::parse($date[1])->format('d-F-Y');
        return $pdf->download('Laporan Order '.$startpdf.' sampai '.$endpdf.'.pdf');
    }

    public function returnReport()
    {
        $start = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d H:i:s');

        if (request()->date != '') {
            $date = explode(' - ' ,request()->date);
            $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
            $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';
        }

        $orders = Order::with(['customer.district'])->has('return')->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC')->get();

        // get id & cost
        $order_id = [];
        $shippingCost = [];
        foreach($orders as $row){
            $order_id[] = $row['id'];
            $shippingCost[] = $row['cost'];
        }

        $detailOrder = OrderDetail::whereIn('order_id', $order_id)->orderBy('created_at', 'DESC')->get();

        $groups = $detailOrder->where('seller_id', auth()->guard('seller')->user()->id)->groupBy('order_id'); 

        // we will use map to cumulate each group of rows into single row.
        // $group is a collection of rows that has the same opposition_id.
        $groupwithcount = $groups->map(function ($group) {
            return [
                'id' => $group->first()['order'],
                'kuantiti' => $group->sum('qty'),
                'harga' => $group->sum('price'),
            ];
        });
        
        // get subtotal
        $subtotal = $groupwithcount->map(function($q){
            return $q['harga'] * $q['kuantiti'];
        });

        // Convert to array
        $temp = [];
        foreach($subtotal as $row){
            $temp[] = $row;
        }

        // combine 2 array
        $combine = collect([$shippingCost, $temp]);

        if(count($combine) > 1){
            $totalOmset = array_fill(0, count($combine), 0);
            foreach($combine as $collection){
                foreach($collection as $key => $value){
                    $totalOmset[$key] += $value;
                }
            }
        } else {
            $totalOmset = $combine->pipe(function($q){
                return $q[0] + $q[1];
            });
        }

        return view('seller.report.return', compact('orders', 'totalOmset'));
    }

    public function returnReportPdf($daterange)
    {
        $date = explode('+', $daterange);
        $start = Carbon::parse($date[0])->format('Y-m-d') . ' 00:00:01';
        $end = Carbon::parse($date[1])->format('Y-m-d') . ' 23:59:59';

        $orders = Order::with(['customer.district'])->has('return')->whereBetween('created_at', [$start, $end])->orderBy('created_at', 'DESC')->get();

        // get id & cost
        $order_id = [];
        $shippingCost = [];
        foreach($orders as $row){
            $order_id[] = $row['id'];
            $shippingCost[] = $row['cost'];
        }

        $detailOrder = OrderDetail::whereIn('order_id', $order_id)->orderBy('created_at', 'DESC')->get();

        $groups = $detailOrder->where('seller_id', auth()->guard('seller')->user()->id)->groupBy('order_id'); 

        // we will use map to cumulate each group of rows into single row.
        // $group is a collection of rows that has the same opposition_id.
        $groupwithcount = $groups->map(function ($group) {
            return [
                'id' => $group->first()['order'],
                'kuantiti' => $group->sum('qty'),
                'harga' => $group->sum('price'),
            ];
        });
        
        // get subtotal
        $subtotal = $groupwithcount->map(function($q){
            return $q['harga'] * $q['kuantiti'];
        });

        // Convert to array
        $temp = [];
        foreach($subtotal as $row){
            $temp[] = $row;
        }

        // combine 2 array
        $combine = collect([$shippingCost, $temp]);

        if(count($combine) > 1){
            $totalOmset = array_fill(0, count($combine), 0);
            foreach($combine as $collection){
                foreach($collection as $key => $value){
                    $totalOmset[$key] += $value;
                }
            }

            $result = array_sum($totalOmset);
        } else {
            $totalOmset = $combine->pipe(function($q){
                return $q[0] + $q[1];
            });

            $result = $totalOmset;
        }

        $pdf = PDF::loadView('seller.report.returnpdf', compact('orders', 'date', 'totalOmset', 'result'));
        
        $startpdf = Carbon::parse($date[0])->format('d-F-Y');
        $endpdf = Carbon::parse($date[1])->format('d-F-Y');
        return $pdf->download('Laporan Return Order '.$startpdf.' sampai '.$endpdf.'.pdf');
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        $order->details()->delete();
        $order->payment()->delete();
        $order->delete();
        return redirect(route('orders.newIndex'))->with(['success' => 'Order Sudah Dihapus']);
    }
}
