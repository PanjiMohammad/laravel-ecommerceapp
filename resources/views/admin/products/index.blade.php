@extends('layouts.admin')

@section('title')
    <title>Daftar Produk</title>
@endsection

@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Produk</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
                        <li class="breadcrumb-item active">Produk</li>
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
                            <h4 class="card-title">Daftar Produk</h4>
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
                                <a href="{{ route('product.create') }}" class="btn btn-primary">Tambah</a>
                            </div>
                            <!-- BUAT FORM UNTUK PENCARIAN, METHODNYA ADALAH GET -->
                            <form action="{{ route('product.index') }}" method="get">
                                <div class="col-md-12">
                                    <div class="row float-right mb-3">
                                        <div class="mr-1">
                                            <input type="text" id="q" name="q" class="form-control" placeholder="Cari..." value="{{ request()->q }}">
                                        </div>
                                        <div class="mr-1">
                                            <button class="btn btn-secondary" type="submit">Cari</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th>Gambar</th>
                                            <th>Deskripsi</th>
                                            <th>Harga</th>
                                            <th>Created At</th>
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
                                        @forelse ($product as $row)
                                        <tr>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">
                                                <!-- TAMPILKAN GAMBAR DARI FOLDER PUBLIC/STORAGE/PRODUCTS -->
                                                <img src="{{ asset('/products/' . $row->image) }}" width="150px" height="100px" alt="{{ $row->name }}">
                                            </td>
                                            <td>
                                                <strong>{{ $row->name }}</strong><br>
                                                <!-- ADAPUN NAMA KATEGORINYA DIAMBIL DARI HASIL RELASI PRODUK DAN KATEGORI -->
                                                <label>Kategori: <span class="badge badge-info">{{ $row->category->name ?? '-' }}</span></label><br>
                                                <label>Berat: <span class="badge badge-info">{{ $row->weight }} gr</span></label>
                                            </td>
                                            <td id="harga">Rp {{ number_format($row->price) }}</td>
                                            <td>{{ $row->created_at->format('d-m-Y') }}</td>
                                            
                                            <!-- KARENA BERISI HTML MAKA KITA GUNAKAN { !! UNTUK MENCETAK DATA -->
                                            <td>{!! $row->status_label !!}</td>

                                            <!-- <td>
                                                @if($row->status == '1')
                                                <span class="badge badge-success">Publish</span>
                                                @else
                                                <span class="badge badge-secondary">Draft</span>
                                                @endif
                                            </td>
 -->
                                            <td>
                                                <!-- FORM UNTUK MENGHAPUS DATA PRODUK -->
                                                <form onsubmit="return confirm('Yakin Hapus Produk ?')" action="{{ route('product.destroy', $row->id) }}" method="post">
                                                    @csrf
                                                    @method('DELETE')
                                                    <a href="{{ route('product.edit', $row->id) }}" class="btn btn-warning btn-sm"><span class="fas fa-pen"></span></a>
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
                            {!! $product->links() !!}
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

@section('js')
    <script>
        $(document).ready(function() {

            
        });
    </script>
@endsection