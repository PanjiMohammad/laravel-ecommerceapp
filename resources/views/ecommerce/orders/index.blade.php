@extends('layouts.ecommerce')

@section('title')
    <title>List Pesanan - Ecommerce</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>List Pesanan</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Dashboard</a>
                        <a href="{{ route('customer.orders') }}">List Pesanan</a>
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
                    <div class="row">
						<div class="col-md-12">
							<div class="card">
                                <div class="card-header">
                                    <h4 class="card-title">List Pesanan</h4>
                                </div>
								<div class="card-body">
                                    @if (session('success'))
                                        <div class="alert alert-success">{{ session('success') }}</div>
                                    @endif 
                                    
                                    @if(session('error'))
                                        <div class="alert alert-danger">{{ session('error') }}</div>
                                    @endif
									<div class="table-responsive">
                                    <table class="table table-hover table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Penerima</th>
                                                <th>Nomor Telepon</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Tanggal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($orders as $row) 
                                            <tr>
                                                <td>
                                                    <span class="font-weight-bold text-uppercase">{{ $row->invoice }}</span><br>
                                                    @if ($row->return_count == 1)
                                                    <small>Return: {!! $row->return->status_label !!}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $row->customer_name }}</td>
                                                <td>{{ $row->customer_phone }}</td>
                                                <td class="total">Rp {{ number_format($row->total) }}</td>
                                                <td>{!! $row->status_label !!}</td>
                                                <td>{{ $row->created_at }}</td>
                                                <td>
                                                    <form action="{{ route('customer.order_accept') }}" 
                                                        class="form-inline"
                                                        onsubmit="return confirm('Kamu Yakin Menerima Pesanan ?');" method="post">
                                                        @csrf
                                                        <a href="{{ route('customer.view_order', $row->invoice) }}" class="btn btn-primary btn-sm mr-1">Detail</a>
                                                    
                                                        <input type="hidden" name="order_id" value="{{ $row->id }}">
                                                        @if ($row->status == 3 && $row->return_count == 0)
                                                            <button class="btn btn-success btn-sm">Terima</button>
                                                            <a href="{{ route('customer.order_return', $row->invoice) }}" class="btn btn-danger btn-sm mt-1">Return</a>
                                                        @endif
                                                    </form>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Tidak ada pesanan</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="float-right">
                                    {!! $orders->links() !!}
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

            var array = {!! json_encode($orders) !!}
            console.log(JSON.stringify('Test = ' + array.data))

            var result = Array.prototype.map.call(document.getElementsByClassName("total"), function(value) {
                return value.innerHTML.trim();
            });
            var result1 = $('.total').find('td');
            var id = $('.total').val();
            console.log(JSON.stringify(id))

            for (i = 0; i < result.length; ++i) {
                console.log(JSON.stringify('Test = ' + result[i]))
                const formatTotal = new Intl.NumberFormat("id-ID", {
                    style: "currency", 
                    currency: "IDR", 
                    maximumSignificantDigits: "3"
                }).format(result[i])
                console.log(JSON.stringify('total : ' + formatTotal))

                total1 = formatTotal
            }

            $(result1).each(function() {
                total2 = total1
                console.log(total2 + 'halooooo')
                $('.total').html(total2);
            });   

        });
    </script>
@endsection