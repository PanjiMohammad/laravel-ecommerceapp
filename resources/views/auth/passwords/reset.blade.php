@extends('layouts.auth')

@section('title')
<title>Login</title>
@endsection

@section('content')
<div class="login-box">
    <div class="login-logo">
        <p>Lupa Password</p>
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
            <div class="mb-3">
                <a href="{{ route('login')}}" style="color: black;"><span class="fa fa-arrow-left" style="color: black;"></span> Kembali</a>
            </div>
            <form action="{{ route('post.newLogin') }}" method="post">
            @csrf
                <div class="form-group">
                    <div class="input-group mb-3">
                        <input class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" type="email" name="email" placeholder="Email" value="{{ old('email') }}" autofocus>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="float-right">
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Reset</button>
                    </div>
                </div>
            </form>
        </div>
    <!-- /.login-card-body -->
    </div>
</div>
<!-- /.login-box -->
@endsection
