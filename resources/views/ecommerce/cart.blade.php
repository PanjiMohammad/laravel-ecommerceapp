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
					<h2>Keranjang Belanja</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('front.list_cart') }}">Cart</a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--================End Home Banner Area =================-->

	<!--================Cart Area =================-->
	<section class="cart_area">
		<div class="container">
			<div class="cart_inner">
        
                <form action="{{ route('front.update_cart') }}" method="post">
                    @csrf              
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th scope="col">Produk</th>
								<th scope="col">Harga</th>
								<th scope="col">Kuantiti</th>
								<th scope="col">Total</th>
							</tr>
						</thead>
						<tbody>
              				<!-- LOOPING DATA DARI VARIABLE CARTS -->
                            @forelse ($carts as $row)
							<tr>
								<td>
									<div class="media">
										<div class="d-flex">
                                            <img src="{{ asset('/products/' . $row['product_image']) }}" width="150px" height="125px" alt="{{ $row['product_name'] }}">
										</div>
										<div class="media-body">
                                            <p class="font-weight-bold" style="color: #000;">{{ $row['product_name'] }}</p>
                                            <p class="font-weight-bold" style="color: #000;">{{ $row['seller_name'] }}</p>
                                            <p class="font-weight-bold" style="color: #000;">Dikirim Dari : {{ $row['origin_details']['address'] }}, {{ $row['origin_details']['city_name'] }}</p>
										</div>
									</div>
								</td>
								<td>
                                    <h5>Rp {{ number_format($row['product_price']) }}</h5>
								</td>
								<td>
									<div class="product_count">
                    					<!-- PERHATIKAN BAGIAN INI, NAMENYA KITA GUNAKAN ARRAY AGAR BISA MENYIMPAN LEBIH DARI 1 DATA -->
                                        <input type="text" name="qty[]" id="sst{{ $row['product_id'] }}" maxlength="12" value="{{ $row['qty'] }}" title="Quantity:" class="input-text qty">
                                        <input type="hidden" name="product_id[]" value="{{ $row['product_id'] }}" class="form-control">
                    					<!-- PERHATIKAN BAGIAN INI, NAMENYA KITA GUNAKAN ARRAY AGAR BISA MENYIMPAN LEBIH DARI 1 DATA -->
                    					<button onclick="var result = document.getElementById('sst{{ $row['product_id'] }}'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;"
										 class="increase items-count" type="button">
											<i class="lnr lnr-chevron-up"></i>
										</button>
										<button onclick="var result = document.getElementById('sst{{ $row['product_id'] }}'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;return false;"
										 class="reduced items-count" type="button">
											<i class="lnr lnr-chevron-down"></i>
										</button>
									</div>
								</td>
								<td>
                                    <h5>Rp {{ number_format($row['product_price'] * $row['qty']) }}</h5>
								</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4">Tidak ada belanjaan</td>
                            </tr>
                            @endforelse
							<tr class="bottom_button">
								<td></td>
								<td></td>
								<td></td>
								<td>
									<button class="gray_btn">Ubah</button>
								</td>
                            </tr>
                            </form>
							<tr>
								<td></td>
								<td></td>
								<td><h5>Subtotal</h5></td>
								<td>
                                    <h5 class="subtotal">Rp {{ number_format($subtotal) }}</h5>
								</td>
							</tr>

							<tr class="out_button_area">
								<td></td>
								<td></td>
								<td></td>
								<td>
									<div class="row">
										<div class="col-md-12">
											<div class="checkout_btn_inner">
	                                        @if(Auth::guard('customer')->check())
	                                        	@if($carts != null)
	                                        		<a class="gray_btn" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
		                                        	<a class="main_btn" href="{{ route('front.checkout') }}">Proses Selanjutnya</a>
		                                        @else
		                                        <a class="gray_btn float-right" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
		                                        @endif
	                                        @else
	                                        	<a class="float-right btn btn-primary" href="{{ route('front.product') }}">Lanjut Berbelanja</a>
	                                        @endif
										</div>
										</div>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</section>
	<!--================End Cart Area =================-->
@endsection

@section('js')
	<script>
		$(document).ready(function() {

			var subtotal = '{{ $subtotal }}';
			const formatSubtotal = new Intl.NumberFormat("id-ID", {
                style: "currency", 
                currency: "IDR",
                maximumSignificantDigits: "3"
            }).format(subtotal)
            $('.subtotal').text(formatSubtotal)
            $('input[name="cost"]').val(total2)
            console.log(JSON.stringify(formatTotal))

		});
	</script>
@endsection

