@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Data Master Dinas</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.master-dinas.update', $masterDina) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nama_dinas" class="form-label">Nama Dinas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="nama_dinas" name="nama_dinas" value="{{ $masterDina->nama_dinas }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <input type="text" class="form-control" id="alamat" name="alamat" value="{{ $masterDina->alamat }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="no_telp" class="form-label">No Telp</label>
                                <input type="text" class="form-control" id="no_telp" name="no_telp" value="{{ $masterDina->no_telp }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="{{ route('admin.master-dinas.index') }}" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
