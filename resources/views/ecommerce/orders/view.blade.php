@extends('layouts.ecommerce')

@section('title')
    <title>Order {{ $order->invoice }} - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Pesanan <span class="text-uppercase">{{ $order->invoice }}</span></h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Beranda</a>
                        <a href="{{ route('customer.orders') }}">Pesanan <span class="text-uppercase">{{ $order->invoice }}</span></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Login Box Area =================-->
	<section class="login_box_area p_120">
		<div class="container">
			<div class="row">
				<div class="col-md-3">
					@include('layouts.ecommerce.module.sidebar')
                </div>
				<div class="col-md-9">
                    @if (session('success')) 
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    <div class="row">
						<div class="col-md-6">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Data Pelanggan</h4>
                                </div>
								<div class="card-body">
									<table>
                                        <tr>
                                            <td width="30%">InvoiceID</td>
                                            <td width="5%">:</td>
                                            <th><a href="{{ route('customer.order_pdf', $order->invoice) }}" target="_blank" class="font-weight-bold text-uppercase">{{ $order->invoice }}</a></th>
                                        </tr>
                                        <tr>
                                            <td width="30%">Nama Penerima</td>
                                            <td width="5%">:</td>
                                            <th>{{ $order->customer_name }}</th>
                                        </tr>
                                        <tr>
                                            <td>No Telp</td>
                                            <td>:</td>
                                            <th>{{ $order->customer_phone }}</th>
                                        </tr>
                                        <tr>
                                            <td>Alamat</td>
                                            <td>:</td>
                                            <th>{{ $order->customer_address }}, <span class="district">{{ $order->district->name }} {{ $order->district->city->name }}</span>, {{ $order->district->city->province->name }}</th>
                                        </tr>
                                    </table>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">
                                        Pembayaran

                                        @if ($order->status == 0)
                                        <a href="{{ route('customer.paymentForm', $order->invoice) }}" class="btn btn-primary btn-sm float-right">Konfirmasi</a>
                                        @endif
                                    </h4>
                                </div>
								<div class="card-body">
                                    @if ($order->payment)
                                        <table>
                                        <tr>
                                            <td width="30%">Nama Pengirim</td>
                                            <td width="5%"></td>
                                            <td>{{ $order->payment->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal Transfer</td>
                                            <td></td>
                                            <td>{{ $order->payment->transfer_date }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Transfer</td>
                                            <td></td>
                                            <td>Rp {{ number_format($order->payment->amount) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Tujuan Transfer</td>
                                            <td></td>
                                            <td>{{ $order->payment->transfer_to }}</td>
                                        </tr>
                                        <tr>
                                            <td>Bukti Transfer</td>
                                            <td></td>
                                            <td>
                                                <img src="{{ asset('/proof/payment/' . $order->payment->proof) }}" width="100px" height="100px" alt="">
                                                <a href="{{ asset('/proof/payment/' . $order->payment->proof) }}" target="_blank">Lihat Detail</a>
                                            </td>
                                        </tr>
                                    </table>
                                    @else
                                    <h4 class="text-center">Belum ada data pembayaran</h4>
                                    @endif
								</div>
							</div>
                        </div>
                        <div class="col-md-12 mt-4">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">Detail</h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Nama Produk</th>
                                                    <th>Harga</th>
                                                    <th>Kuantiti</th>
                                                    <th>Berat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($order->details as $row)
                                                <tr>
                                                    <td>{{ $row->product->name }}</td>
                                                    <td class="price">{{ $row->price }}</td>
                                                    <td>{{ $row->qty }} Item</td>
                                                    <td>{{ $row->weight }} <span class="text-capitalize">gram</span></td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Tidak ada data</td>
                                                </tr>
                                                @endforelse
                                                <tr>
                                                    <td class="text-center font-weight-bold">Subtotal</td>
                                                    <td colspan="3" class="text-center subtotal font-weight-bold">{{$order->subtotal}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center font-weight-bold">Kurir: <span class="text-uppercase font-weight-bold">{{ $order->shipping }}</span></td>
                                                    <td colspan="3" class="text-center font-weight-bold cost">{{$order->cost}}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center font-weight-bold text-uppercase">Total</td>
                                                    <td colspan="3" class="text-center total font-weight-bold">{{ $order->total }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            var data = []
            $.each({!! $order->details !!}, function(key, value) {
                var price = value.price
                const formatPrice = new Intl.NumberFormat("id-ID", {
                    style: "currency", 
                    currency: "IDR",
                    maximumSignificantDigits: "3"
                }).format(price)
                data.push(formatPrice)
            });

            var price = data
            console.log(price)

            var cells = $(this).find('td.price')

            $.each(cells, function(i, val){
                console.log(val)
                var td = $('<td>');
                $.each(price, function(key, item) {
                    $('.price').html(cells[item]);  
                });
            })

            var total = '{{ $order->total }}';
            const formatTotal = new Intl.NumberFormat("id-ID", {
                style: "currency", 
                currency: "IDR",
                maximumSignificantDigits: "3"
            }).format(total)
            console.log(JSON.stringify(formatTotal))
            $('.total').text(formatTotal)

            var subtotal = '{{ $order->subtotal }}';
            const formatSubtotal = new Intl.NumberFormat("id-ID", {
                style: "currency", 
                currency: "IDR", 
                maximumSignificantDigits: "3"
            }).format(subtotal)
            console.log(JSON.stringify(formatSubtotal))
            $('.subtotal').text(formatSubtotal)

            var harga = '{{ $order->cost }}';
            const formatHarga = new Intl.NumberFormat("id-ID", {
                style: "currency", 
                currency: "IDR",
                maximumSignificantDigits: "3"
            }).format(harga)
            console.log(JSON.stringify(formatHarga))
            $('.cost').text(formatHarga)

        });
    </script>
@endsection