@extends('layouts.seller')

@section('title')
    <title>Edit Kategori</title>
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
                        <li class="breadcrumb-item">
                            <a href="{{ route('category.newIndex') }}">Kategori</a>
                        </li>
                        <li class="breadcrumb-item active">Edit Kategori</li>
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
                <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT CATEGORY  -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Edit Kategori</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('category.newUpdate', $category->id) }}" method="post">
                                @csrf
                                @method('PUT')
                                @if($category->parent_id == null)
                                <div class="form-group">
                                    <label for="name">Kategori</label>
                                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                                @else
                                <div class="form-group">
                                    <label for="name">Kategori</label>
                                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                </div>
                                <div class="form-group">
                                    <label for="parent_id">Kategori</label>
                                    <select name="parent_id" class="form-control">
                                        <option value="">None</option>
                                        @foreach ($parent as $row)
                                      	<!-- TERDAPAT TERNARY OPERATOR UNTUK MENGECEK JIKA PARENT_ID SAMA DENGAN ID CATEGORY PADA LIST PARENT, MAKA OTOMATIS SELECTED -->
                                        <option value="{{ $row->id }}" {{ $category->parent_id == $row->id ? 'selected':'' }}>{{ $row->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger">{{ $errors->first('parent_id') }}</span>
                                </div>
                                @endif
                                <div class="form-group float-right">
                                    <button class="btn btn-primary btn-md">Ubah</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- BAGIAN INI AKAN MENG-HANDLE FORM EDIT CATEGORY  -->

            </div>
        </div>
    </section>
  </div>
  <!-- /.content-wrapper -->
@endsection