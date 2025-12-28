@extends('layout')
@section('title', 'Profil')

@section('content')
    <div class="col-lg-10 col-md-9 p-4">
        <div class="profile-card mx-auto" style="max-width: 800px;">
            <!-- Header -->
            <div class="profile-header text-center py-4 bg-primary text-white rounded-top">
                {{-- Lingkaran foto bisa diklik untuk upload --}}
                <form id="formUploadFoto" method="POST" 
                    enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <input type="file" name="foto" id="fotoInput" accept="image/*" style="display: none;"
                        onchange="previewFoto(event)">

                    <div class="d-inline-block mb-3 position-relative" style="cursor: pointer;"
                        onclick="document.getElementById('fotoInput').click()">
                        <div id="previewContainer"
                            class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto"
                            style="width: 120px; height: 120px; border: 3px solid #fff; overflow: hidden;">
                            @if (auth()->user()->biodata->foto_profil)
                                <img id="fotoPreview"
                                    src="{{ auth()->user()->biodata->foto_profil }}"
                                    class="w-100 h-100" style="object-fit: cover;">
                            @else
                                <i id="iconDefault" class="bi bi-person text-secondary" style="font-size: 60px;"></i>
                            @endif
                        </div>
                        <div class="position-absolute bottom-0 end-0 translate-middle p-2 bg-white rounded-circle border shadow-sm"
                            style="width: 36px; height: 36px;">
                            <i class="bi bi-camera-fill text-primary"></i>
                        </div>
                    </div>
                </form>

                <h4 class="mb-1">{{ auth()->user()->biodata->nama ?? 'Nama Pengguna' }}</h4>
                <p class="text-white-50 mb-0">{{ auth()->user()->email ?? 'email@contoh.com' }}</p>

                <div class="mt-2">
                    @foreach (auth()->user()->role as $role)
                      <span class="badge rounded-pill text-uppercase bg-light text-primary fw-semibold px-3 py-2">
                        {{ $role->nama }}
                      </span>
                    @endforeach
                </div>
            </div>

            <!-- Body -->
            <div class="p-4">
                <h5 class="mb-3 text-primary fw-semibold">Informasi Pribadi</h5>
                <form method="POST" action="/profil">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="info-label mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control"
                                value="{{ auth()->user()->biodata->nama ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="info-label mb-1">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="{{ auth()->user()->email ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="info-label mb-1">No. Telepon</label>
                            <input type="text" name="no_telepon" class="form-control" placeholder="0812-3456-7890"
                                value="{{ auth()->user()->biodata->no_telepon ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="info-label mb-1">Alamat</label>
                            <input type="text" name="alamat" class="form-control" placeholder="Jl. Mawar No. 123"
                                value="{{ auth()->user()->biodata->alamat ?? '' }}">
                        </div>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script preview foto --}}
    <script>
        function previewFoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    let previewContainer = document.getElementById('previewContainer');
                    previewContainer.innerHTML =
                        `<img id="fotoPreview" src="${e.target.result}" class="w-100 h-100" style="object-fit: cover;">`;
                    document.getElementById('formUploadFoto').submit(); // auto submit setelah pilih foto
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection