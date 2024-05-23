@extends('layouts.admin')

@section('title')
    <title>Edit Konsumen</title>
@endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Konsumen</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('consumen.index') }}">Konsumen</a></li>
                        <li class="breadcrumb-item active">Edit Konsumen</li>
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
            <form action="{{ route('consumen.update', $customer->id) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Edit Konsumen</h4>
                            </div>
                            <div class="card-body">
                            
                            <form action="{{ route('consumen.update', $customer->id) }}" method="post">
                                @csrf
                                @method('PUT')
                                <div class="form-group">
                                    <label for="">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" required value="{{ $customer->name }}">
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Email</label>
                                    <input type="email" name="email" class="form-control" required value="{{ $customer->email }}" readonly>
                                    <span class="text-danger">{{ $errors->first('email') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="******">
                                    <span class="text-danger">{{ $errors->first('password') }}</span>
                                    <span style="color: red;">* Biarkan kosong jika tidak ingin mengganti password</span>
                                </div>
                                <div class="form-group">
                                    <label for="">No Telp</label>
                                    <input type="text" name="phone_number" class="form-control" required value="{{ $customer->phone_number }}">
                                    <span class="text-danger">{{ $errors->first('phone_number') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Alamat</label>
                                    <input type="text" name="address" class="form-control" required value="{{ $customer->address }}">
                                    <span class="text-danger">{{ $errors->first('address') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="1" {{ $customer->status == '1' ? 'selected':'' }}>Aktif</option>
                                        <option value="0" {{ $customer->status == '0' ? 'selected':'' }}>Tidak Aktif</option>
                                    </select>
                                    <span class="text-danger">{{ $errors->first('province_id') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="">Provinsi</label>
                                    <select class="form-control" name="province_id" id="province_id" required>
                                        <option value="">Pilih Propinsi</option>
                                        @foreach ($provinces as $row)
                                        <option value="{{ $row->id }}" {{ $customer->district->province_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger">{{ $errors->first('province_id') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="">Kabupaten / Kota</label>
                                    <select class="form-control" name="city_id" id="city_id" required>
                                        <option value="">Pilih Kabupaten/Kota</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('city_id') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="">Kecamatan</label>
                                    <select class="form-control" name="district_id" id="district_id" required>
                                        <option value="">Pilih Kecamatan</option>
                                    </select>
                                    <p class="text-danger">{{ $errors->first('district_id') }}</p>
                                </div>
                                <div class="float-right">
                                    <button class="btn btn-primary btn-md">Simpan</button>
                                </div>
                            </form>
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
        //JADI KETIKA HALAMAN DI-LOAD
        $(document).ready(function(){
            //MAKA KITA MEMANGGIL FUNGSI LOADCITY() DAN LOADDISTRICT()
            //AGAR SECARA OTOMATIS MENGISI SELECT BOX YANG TERSEDIA
            loadCity($('#province_id').val(), 'bySelect').then(() => {
                loadDistrict($('#city_id').val(), 'bySelect');
            })
        })

        $('#province_id').on('change', function() {
            loadCity($(this).val(), '');
        })

        $('#city_id').on('change', function() {
            loadDistrict($(this).val(), '')
        })

        function loadCity(province_id, type) {
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: "{{ url('/api/city') }}",
                    type: "GET",
                    data: { province_id: province_id },
                    success: function(html){
                        $('#city_id').empty()
                        $('#city_id').append('<option value="">Pilih Kabupaten/Kota</option>')
                        $.each(html.data, function(key, item) {
                            
                            // KITA TAMPUNG VALUE CITY_ID SAAT INI
                            let city_selected = {{ $customer->district->city_id }};
                           //KEMUDIAN DICEK, JIKA CITY_SELECTED SAMA DENGAN ID CITY YANG DOLOOPING MAKA 'SELECTED' AKAN DIAPPEND KE TAG OPTION
                            let selected = type == 'bySelect' && city_selected == item.id ? 'selected':'';
                            //KEMUDIAN KITA MASUKKAN VALUE SELECTED DI ATAS KE DALAM TAG OPTION
                            $('#city_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                            resolve()
                        })
                    }
                });
            })
        }

        //CARA KERJANYA SAMA SAJA DENGAN FUNGSI DI ATAS
        function loadDistrict(city_id, type) {
            $.ajax({
                url: "{{ url('/api/district') }}",
                type: "GET",
                data: { city_id: city_id },
                success: function(html){
                    $('#district_id').empty()
                    $('#district_id').append('<option value="">Pilih Kecamatan</option>')
                    $.each(html.data, function(key, item) {
                        let district_selected = {{ $customer->district->id }};
                        let selected = type == 'bySelect' && district_selected == item.id ? 'selected':'';
                        $('#district_id').append('<option value="'+item.id+'" '+ selected +'>'+item.name+'</option>')
                    })
                }
            });
        }
    </script>
@endsection