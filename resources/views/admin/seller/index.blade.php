@extends('layouts.admin')

@section('title')
    <title>Data Penjual</title>
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
                  <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                  <li class="breadcrumb-item active">Penjual</li>
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
                <!-- BAGIAN INI AKAN MENG-HANDLE TABLE LIST PRODUCT  -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Daftar Penjual</h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <div class="float-left">
                                <!-- <a href="{{ route('product.bulk') }}" class="btn btn-danger">Mass Upload</a> -->
                                <a href="{{ route('seller.create') }}" class="btn btn-primary">Tambah</a>
                            </div>
                            <!-- BUAT FORM UNTUK PENCARIAN, METHODNYA ADALAH GET -->
                            <form action="{{ route('seller.newIndex') }}" method="get">
                                <div class="input-group mb-3 col-md-3 float-right">
                                    <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">Cari</button>
                                    </div>
                                </div>
                            </form>

                            <div class="table table-responsive table-striped">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Nama Penjual</th>
                                            <th>Email</th>
                                            <th>Nomor Telepon</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <?php 
                                        $no = 1;
                                     ?>
                                    <tbody>
                                        <!-- LOOPING DATA TERSEBUT MENGGUNAKAN FORELSE -->
                                        <!-- ADAPUN PENJELASAN ADA PADA ARTIKEL SEBELUMNYA -->
                                        @forelse ($seller as $row)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ $row->phone_number }}</td>
                                            <td class="text-center">
                                                @if($row->status == 1)
                                                <span class="badge badge-success">Aktif</span>
                                                @else
                                                <span class="badge badge-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                <!-- FORM UNTUK MENGHAPUS DATA PRODUK -->
                                                <form onsubmit="return confirm('Yakin Hapus Produk ?')" action="{{ route('seller.destroy', $row->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="{{ route('seller.edit', $row->id) }}" class="btn btn-warning btn-sm"><span class="fas fa-pen"></span></a>
                                                    <button class="btn btn-danger btn-sm"><span class="fas fa-trash"></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- FUNGSI INI AKAN SECARA OTOMATIS MEN-GENERATE TOMBOL PAGINATION  -->
                            {!! $seller->links() !!}
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