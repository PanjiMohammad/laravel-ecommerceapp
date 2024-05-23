@extends('layouts.admin')

@section('title')
    <title>Data Konsumen</title>
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
                  <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
                  <li class="breadcrumb-item active">Konsumen</li>
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
                            <h4 class="card-title">Daftar Konsumen</h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <div class="float-left">
                                <a href="{{ route('product.create') }}" class="btn btn-primary">Tambah</a>
                            </div>
                            <!-- BUAT FORM UNTUK PENCARIAN, METHODNYA ADALAH GET -->
                            <form action="{{ route('consumen.index') }}" method="get">
                                <div class="col-md-12">
                                    <div class="row float-right">
                                        <div class="form-group mr-1">
                                            <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                        </div>
                                        <div class="form-group mr-1">
                                            <button class="btn btn-secondary" type="submit">Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>No. Telepon</th>
                                            <th>Address</th>
                                            <!-- <th>Created At</th> -->
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <?php 
                                        $no = 1;
                                     ?>
                                    <tbody>
                                        <!-- LOOPING DATA TERSEBUT MENGGUNAKAN FORELSE -->
                                        <!-- ADAPUN PENJELASAN ADA PADA ARTIKEL SEBELUMNYA -->
                                        @forelse ($customer as $row)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ $row->phone_number }}</td>
                                            <td>{{ $row->address }}</td>
                                            <!-- <td>{{ $row->created_at->format('d-m-Y') }}</td> -->
                                            <td>
                                                @if($row->status == '1')
                                                <span class="badge badge-success">Aktif</span>
                                                @else
                                                <span class="badge badge-danger">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <!-- FORM UNTUK MENGHAPUS DATA PRODUK -->
                                                <form onsubmit="return confirm('Yakin Hapus Customer ?')" action="{{ route('consumen.destroy', $row->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="{{ route('consumen.edit', $row->id) }}" class="btn btn-warning btn-sm"><span class="fas fa-pen"></span></a>
                                                    <button class="btn btn-danger btn-sm"><span class="fas fa-trash"></span></button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada data</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- FUNGSI INI AKAN SECARA OTOMATIS MEN-GENERATE TOMBOL PAGINATION  -->
                            {!! $customer->links() !!}
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