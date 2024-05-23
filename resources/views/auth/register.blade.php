@extends('layouts.auth')

@section('title')
<title>Register</title>
@endsection

@section('content')
    
    <div class="login-box">
        <div class="login-logo">
            <h2>Registrasi</h2>
        </div>
        
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <div class="mb-3">
                <div class="mb-3">
                    <a href="{{ route('login')}}" style="color: black;"><span class="fa fa-arrow-left" style="color: black;"></span> Kembali</a>
                </div>
            </div>
            <form action="{{ route('post.newRegister') }}" method="post" novalidate="novalidate">
            @csrf
                <div class="form-group mt-3">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Full Name" required>
                    <span class="text-danger">{{ $errors->first('name') }}</span>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="email" name="email" placeholder="Email" required>
                    <span class="text-danger">{{ $errors->first('email') }}</span>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Kata Sandi" required>
                    <span class="text-danger">{{ $errors->first('password') }}</span>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Masukkan Nomor Telepon" required>
                    <span class="text-danger">{{ $errors->first('phone_number') }}</span>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="address" name="address" placeholder="Masukkan Alamat" required>
                    <span class="text-danger">{{ $errors->first('address') }}</span>
                </div>
                <div class="form-group">
                    <select class="form-control" name="province_id" id="province_id" required>
                        <option value="">Pilih Provinsi</option>
                        <!-- LOOPING DATA PROVINCE UNTUK DIPILIH OLEH CUSTOMER -->
                        @foreach ($provinces as $row)
                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger">{{ $errors->first('province_id') }}</span>
                </div>
                <div class="form-group">
                    <select class="form-control" name="city_id" id="city_id" required>
                        <option value="">Pilih Kabupaten/Kota</option>
                    </select>
                    <span class="text-danger">{{ $errors->first('city_id') }}</span>
                </div>
                <div class="form-group">
                    <select class="form-control" name="district_id" id="district_id" required>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                    <span class="text-danger">{{ $errors->first('district_id') }}</span>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function(){

            //KETIKA SELECT BOX DENGAN ID province_id DIPILIH
            $('#province_id').on('change', function() {
                //MAKA AKAN MELAKUKAN REQUEST KE URL /API/CITY DENGAN MENGIRIM PROVINCE_ID
                $.ajax({
                    url: "{{ url('/api/city') }}",
                    type: "GET",
                    data: { province_id: $(this).val() },
                    success: function(html){
                        //SETELAH DATA DITERIMA, SELECTBOX DENGAN ID CITY_ID DI KOSONGKAN
                        $('#city_id').empty()
                        //KEMUDIAN APPEND DATA BARU YANG DIDAPATKAN DARI HASIL REQUEST VIA AJAX
                        //UNTUK MENAMPILKAN DATA KABUPATEN / KOTA
                        $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                        $.each(html.data, function(key, item) {
                            console.log(item)
                            $('#city_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                        })
                    }
                });
            })

            //LOGICNYA SAMA DENGAN CODE DIATAS HANYA BERBEDA OBJEKNYA SAJA
            $('#city_id').on('change', function() {
                $.ajax({
                    url: "{{ url('/api/district') }}",
                    type: "GET",
                    data: { city_id: $(this).val() },
                    success: function(html){
                        $('#district_id').empty()
                        $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                        $.each(html.data, function(key, item) {
                            $('#district_id').append('<option value="'+item.id+'">'+item.name+'</option>')
                        })
                    }
                });
            })

        })
    </script>
@endsection