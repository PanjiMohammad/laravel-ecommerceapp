@extends('layouts.admin')

@section('title')
    <title>Tambah Produk</title>
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark">Penjual</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="#">Penjual</a></li>
                  <li class="breadcrumb-item active">Tambah Penjual</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <!-- TAMBAHKAN ENCTYPE="" KETIKA MENGIRIMKAN FILE PADA FORM -->
            <form action="{{ route('seller.store') }}" method="post" enctype="multipart/form-data" >
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Tambah Penjual</h4>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                @if (session('error'))
                                    <div class="alert alert-danger">{{ session('error') }}</div>
                                @endif
                                <div class="form-group">
                                    <label for="name" class="form-label">Nama</label>
                                    <input type="text" name="name" class="form-control" placeholder="Masukan Nama Lengkap" required>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control" placeholder="Masukan Email" required>
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="password" class="form-label">Kata Sandi</label>
                                    <input type="password" name="password" class="form-control" placeholder="Masukan Kata Sandi" required>
                                    <p class="text-danger">{{ $errors->first('password') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="address" class="form-label">Alamat</label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Masukkan Alamat" required>
                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="province_id" class="form-label">Pilih Provinsi</label>
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
                                    <label for="city_id" class="form-label">Pilih Kabupaten</label>
                                    <select class="form-control" name="city_id" id="city_id" required>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <span class="text-danger">{{ $errors->first('city_id') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="district_id" class="form-label">Pilih Kecamatan</label>
                                    <select class="form-control" name="district_id" id="district_id" required>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <span class="text-danger">{{ $errors->first('district_id') }}</span>
                                </div>
                                <div class="form-group float-right">
                                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection

@section('js')
    <script>
        $(document).ready(function (){

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

        });
    </script>
@endsection