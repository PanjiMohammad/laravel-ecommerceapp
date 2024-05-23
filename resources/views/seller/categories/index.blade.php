@extends('layouts.seller')

@section('title')
    <title>Kategori</title>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark">Kategori</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="{{ route('seller.dashboard') }}">Beranda</a></li>
                  <li class="breadcrumb-item active">Kategori</li>
                </ol>
              </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container">
            <div class="row">
                <!-- BAGIAN INI AKAN MENG-HANDLE FORM INPUT NEW CATEGORY  -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Tambah Kategori</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('category.newStore') }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <label for="name">Nama Kategori</label>
                                    <input type="text" name="name" class="form-control" placeholder="Masukkan Nama Kategori" required>
                                    <p class="text-danger">{{ $errors->first('name') }}</p>
                                </div>
                                <div class="form-group">
                                    <label for="parent_id">Kategori</label>
                                    <select name="parent_id" class="form-control">
                                        <option value="">None</option>
                                        @foreach ($parent as $row)
                                        <option value="{{ $row->id }}">{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-danger">{{ $errors->first('parent_id') }}</p>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary btn-sm">Tambah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- BAGIAN INI AKAN MENG-HANDLE FORM INPUT NEW CATEGORY  -->

                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Daftar Kategori</h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif

                            <form action="{{ route('category.newIndex') }}" method="get">
                                <div class="col-md-12">
                                    <div class="row float-right">
                                        <div class="form-group mr-1">
                                            <input type="text" class="form-control" name="q" id="q" value="{{ request()->q }}" placeholder="Cari...">
                                        </div>
                                        <div class="form-group mr-1">
                                            <button type="submit" class="form-control btn btn-secondary">Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Kategori</th>
                                            <th>Parent</th>
                                            <th>Created At</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      	<!-- LOOPING DATA KATEGORI SESUAI JUMLAH DATA YANG ADA DI VARIABLE $CATEGORY -->
                                        @forelse ($category as $cat)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><strong>{{ $cat->name }}</strong></td>        
                                          	<!-- MENGGUNAKAN TERNARY OPERATOR, UNTUK MENGECEK, JIKA $cat->parent ADA MAKA TAMPILKAN NAMA PARENTNYA, SELAIN ITU MAKA TANMPILKAN STRING - -->
                                            <td>{{ $cat->parent ? $cat->parent->name:'-' }}</td>
                                          
                                            <td>{{ $cat->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                <form onsubmit="return confirm('Yakin Hapus Kategori ?')" action="{{ route('category.newDestroy', $cat->id) }}" method="post">
                                                    <!-- KONVERSI DARI @ CSRF & @ METHOD AKAN DIJELASKAN DIBAWAH -->
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="{{ route('category.newEdit', $cat->id) }}" class="btn btn-warning btn-sm"><span class="fas fa-pen"></span></a>
                                                    <button class="btn btn-danger btn-sm"><span class="fas fa-trash"></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                        <!-- JIKA DATA CATEGORY KOSONG, MAKA AKAN DIRENDER KOLOM DIBAWAH INI  -->
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- FUNGSI INI AKAN SECARA OTOMATIS MEN-GENERATE TOMBOL PAGINATION  -->
                            {!! $category->links() !!}
                        </div>
                    </div>
                </div>
                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST CATEGORY  -->
            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection