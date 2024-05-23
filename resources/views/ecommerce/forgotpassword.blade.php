@extends('layouts.ecommerce')

@section('title')
    <title>Lupa Password</title>
@endsection

@section('content')
    <!--================Home Banner Area =================-->
	<section class="banner_area">
		<div class="banner_inner d-flex align-items-center">
			<div class="container">
				<div class="banner_content text-center">
					<h2>Lupa Password</h2>
					<div class="page_link">
                        <a href="{{ url('/') }}">Beranda</a>
                        <a href="{{ route('customer.login') }}">Lupa Password</a>
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
				<div class="offset-md-3 col-lg-6">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

					<div class="login_form_inner">
						<h3>Lupa Password</h3>
						<form class="row login_form" action="{{ route('customer.resetPassword') }}" method="post" id="contactForm" novalidate="novalidate">
                            @csrf
							<div class="col-md-12 form-group">
								<input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
								<span class="text-danger">{{ $errors->first('email') }}</span>
							</div>
							<!-- <div class="col-md-12 form-group">
								<div class="creat_account">
									<input type="checkbox" id="f-option2" name="remember">
									<label for="f-option2">Keep me logged in</label>
								</div>
							</div> -->
							<div class="col-md-12 form-group">
								<button type="submit" value="submit" class="btn submit_btn">Reset</button>
								<a href="{{ route('customer.login') }}">Kembali</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>
@endsection