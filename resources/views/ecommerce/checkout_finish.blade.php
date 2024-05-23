@extends('layouts.ecommerce')

@section('title')
    <title>Keranjang Belanja - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
    <section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Pesanan Diterima</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
						<a href="">Faktur</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Order Details Area =================-->
	<section class="order_details p_120">
		<div class="container">
			<h3 class="title_confirmation">Terima kasih, pesanan anda telah kami terima.</h3>
			<div class="row order_d_inner">
				<div class="col-lg-6">
					<div class="details_item">
						<h4>Informasi Pesanan</h4>
						<ul class="list">
							<li>
								<a href="">
                                <span>Invoice</span> : <span class="text-uppercase" style="color: black;">{{ $order->invoice }}</span></a>
							</li>
							<li>
								<a href="" >
                                <span>Tanggal</span> : {{ $order->created_at }}</a>
							</li>
							<li>
								<a href="">
									<span>Subtotal</span>
									<span id="subtotal" style="color: black;">: {{ $order->subtotal }}
								</a>
							</li>
							<li>
								<a href="">
									<span>Ongkos Kirim</span>
									<span id="cost" style="color: black;">: {{ $order->cost }}
								</a>
							</li>
							<li>
								<a href="">
									<span>Total</span>
									<span id="total" style="color: black;">: {{ $order->total }}</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="details_item">
						<h4>Informasi Pemesan</h4>
						<ul class="list">
							<li>
								<a href="">
                                    <span>Nama</span> : {{ $order->customer_name }}</a>
							</li>
							<li>
								<a href="">
                                    <span>Alamat</span> : {{ $order->customer_address }}</a>
							</li>
							<li>
								<a href="">
                                <span>Kota</span> : {{ $order->district->city->name }}</a>
							</li>
							<li>
								<a href="">
								<span>Negara</span> : Indonesia</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="col-lg-12">
				<div class="row float-right">
					@if(auth()->guard('customer')->check())
						<br>
						<a class="main_btn btn-md" href="{{ route('customer.dashboard') }}">Pergi ke Dashboard</a>
					@else
						<br>
						<h3 class="title_confirmation text-center">Silahkan Periksa Email Anda Untuk Verifikasi</h3>
					@endif
				</div>
			</div>
		</div>
	</section>
  <!--================End Order Details Area =================-->
    
@endsection

@section('js')
	<script>
		$(document).ready(function() {

			var subtotal = '{{ $order->subtotal }}';
			const formatSubtotal = new Intl.NumberFormat("id-ID", {
				style: "currency", 
				currency: "IDR",
				maximumSignificantDigits: "3"
			}).format(subtotal)
            console.log(JSON.stringify('Subtotal = ' + formatSubtotal))
            $('#subtotal').text(':' + ' ' + formatSubtotal)

            var ongkir = '{{ $order->cost }}';
			const formatOngkir = new Intl.NumberFormat("id-ID", {
				style: "currency", 
				currency: "IDR",
				maximumSignificantDigits: "3"
			}).format(ongkir)
            console.log(JSON.stringify('Ongkos kirim = ' + formatOngkir))
            $('#cost').text(':' + ' ' + formatOngkir)

            var total = '{{ $order->total }}';
			const formatTotal = new Intl.NumberFormat("id-ID", {
				style: "currency", 
				currency: "IDR",
				maximumSignificantDigits: "3"
			}).format(total)
            console.log(JSON.stringify('Total = ' + formatTotal))
            $('#total').text(':' + ' ' + formatTotal)

		});
	</script>
@endsection