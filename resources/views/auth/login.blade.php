@extends('layouts.auth')

@section('title')
<title>Login</title>
@endsection

@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href=#><b>E-</b>COMMERCE</a>
    </div>
    
    <!-- /.login-logo -->
    <div class="card">
        <div class="card-body login-card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form action="{{ route('post.newLogin') }}" method="post">
            @csrf
                <div class="input-group mb-3">
                    <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" type="password" name="password" placeholder="Password">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-8">
                        <p class="mt-2">
                            <a href="{{ route('forgotPassword') }}">Lupa Password</a>
                        </p>
                    </div>

                    <div class="col-4">
                        <div class="mt-1">
                            <button type="submit" class="btn btn-primary btn-block">Masuk</button>
                        </div>
                    </div>
                </div>

                <div class="mt-2 text-center">
                    <span>Tidak punya akun? <a href="{{ route('register') }}">Daftar</a></span>
                </div>
            </form>
        </div>
    <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
@endsection
