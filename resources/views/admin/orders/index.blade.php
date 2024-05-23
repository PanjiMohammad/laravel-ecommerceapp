@extends('layouts.admin')

@section('title')
    <title>Daftar Pesanan</title>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0 text-dark">Pesanan</h1>
              </div><!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <li class="breadcrumb-item"><a href="{{ route('home')}}">Beranda</a></li>
                  <li class="breadcrumb-item active">Pesanan</li>
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
                            <h4 class="card-title">Daftar Pesanan</h4>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            <form action="{{ route('orders.index') }}" method="get">
                                <div class="col-md-12 mb-3">
                                    <div class="row float-right mb-3">
                                        <div class="mr-1">
                                            <select name="status" class="form-control">
                                                <option value="">Pilih Status</option>
                                                <option value="0">Baru</option>
                                                <option value="1">Confirm</option>
                                                <option value="2">Proses</option>
                                                <option value="3">Dikirim</option>
                                                <option value="4">Selesai</option>
                                            </select>
                                        </div>
                                        <div class="mr-1">
                                            <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                        </div>
                                        <div class="mr-1">
                                            <button class="btn btn-secondary" type="submit">Cari</button>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="input-group mb-3 col-md-6 float-right">
                                    <select name="status" class="form-control">
                                        <option value="">Pilih Status</option>
                                        <option value="0">Baru</option>
                                        <option value="1">Confirm</option>
                                        <option value="2">Proses</option>
                                        <option value="3">Dikirim</option>
                                        <option value="4">Selesai</option>
                                    </select>
                                    <input type="text" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-secondary" type="submit">Cari</button>
                                    </div>
                                </div> -->
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Nomor Resi</th>
                                            <th>Nama Pelanggan</th>
                                            <th>Nomor Telepon</th>
                                            <th>Total</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($orders as $row)
                                        <tr>
                                            <td>{{ $row->invoice }}</td>
                                            <td>{{ $row->customer_name }}</td>
                                            <td>{{ $row->customer_phone }}</td>
                                            <td>Rp. {{ number_format($row->total) }}</td>
                                            <td>{{ $row->created_at->format('d-m-Y') }}</td>
                                            <td>
                                                {!! $row->status_label !!}
                                                @if ($row->return_count == 1)
                                                    <a href="{{ route('orders.return', $row->invoice) }}">Permintaan Return</a>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('orders.destroy', $row->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="{{ route('orders.view', $row->invoice) }}" class="btn btn-warning btn-sm"><span class="fa fa-eye"></span></a>
                                                    <button class="btn btn-danger btn-sm"><span class="fa fa-trash"></span></button>
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
                            {!! $orders->links() !!}
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
