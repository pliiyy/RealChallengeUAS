@extends('layout')
@section('title', 'Pengaturan')

@section('content')
    <div class="col-lg-10 col-md-9 content">
        <div class="card mb-4">
            <div class="card-header">
                ⚙️ Pengaturan Akun
            </div>
            <div class="card-body">
                <form action="/settings" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" value="{{ auth()->user()->biodata->nama }}"
                                name="nama">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="{{ auth()->user()->email }}" name="email">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Password Lama</label>
                            <input type="password" class="form-control" placeholder="" name="password_lama">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control" placeholder="" name="password_baru">
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
@endsection