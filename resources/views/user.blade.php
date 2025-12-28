@extends('layout')
@section('title', 'Dekan')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>👨‍🏫 Data Pengguna</span>
      <form action="/dekan" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari ..."
              value="{{ request('search') }}">

          <select name="status" class="form-select form-select-sm">
              <option value="AKTIF" {{ request('status') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
              <option value="NONAKTIF" {{ request('status') == 'NONAKTIF' ? 'selected' : '' }}>Nonaktif</option>
          </select>

          <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel-fill"></i></button>
      </form>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>#</th>
              <th>Nama Lengkap</th>
              <th>Email</th>
              <th>Roles</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($user as $index => $kls)
              <tr>
                <td>{{ $user->firstItem() + $index }}</td>
                <td >{{ $kls->biodata->nama }}</td>
                <td>{{ $kls->email }}</td>
                <td >
                    @foreach ($kls->role as $item)
                        <span class="badge bg-warning">{{ ucfirst(strtolower($item->nama)) }}</span>
                    @endforeach
                </td>
                <td>
                    @if ($kls->status == 'AKTIF')
                        <span class="badge bg-success">{{ ucfirst(strtolower($kls->status)) }}</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst(strtolower($kls->status)) }}</span>
                    @endif
                </td>
                <td>
                  <button type="button" class="btn btn-outline-primary btn-sm btn-edit"
                      data-bs-toggle="modal" data-bs-target="#editModal"
                      data-id="{{ $kls->id }}"
                      data-nama="{{ $kls->biodata->nama }}"
                      data-email="{{ $kls->email }}"
                      data-tempat_lahir="{{ $kls->biodata->tempat_lahir }}"
                      data-tanggal_lahir="{{ $kls->biodata->tanggal_lahir }}"
                      data-jenis_kelamin="{{ $kls->biodata->jenis_kelamin }}"
                      data-agama="{{ $kls->biodata->agama }}"
                      data-prov_id="{{ $kls->biodata->prov_id }}"
                      data-kab_id="{{ $kls->biodata->kab_id }}"
                      data-kec_id="{{ $kls->biodata->kec_id }}"
                      data-kelurahan="{{ $kls->biodata->kelurahan }}"
                      data-alamat="{{ $kls->biodata->alamat }}"
                      > 
                      <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $kls->id }}" data-nama="{{ $kls->biodata->nama }}">
                      <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
          @endforeach
          </tbody>
        </table>
        <div class="mt-3">
            {{ $user->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="editForm" action="" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editModalLabel">Edit Data: <span id="edit-name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id"> {{-- ID role yang akan diupdate --}}
        <div class="mb-3">
            <label for="edit-nama" class="form-label">Nama Lengkap</label>
            <input type="text" class="form-control" id="edit-nama" name="nama" required />
        </div>
        <div class="mb-3">
            <label for="edit-email" class="form-label">Email</label>
            <input type="text" class="form-control" id="edit-email" name="email" required />
        </div>
        <div class="mb-3">
            <label for="edit-tempat_lahir" class="form-label">Tempat Lahir</label>
            <input type="text" class="form-control" id="edit-tempat_lahir" name="tempat_lahir"
                required />
        </div>
        <div class="mb-3">
            <label for="edit-tanggal_lahir" class="form-label">Tanggal Lahir</label>
            <input type="date" class="form-control" id="edit-tanggal_lahir" name="tanggal_lahir"
                required />
        </div>
        <div class="mb-3">
            <label for="edit-jenis_kelamin" class="form-label">Jenis Kelamin</label>
            <select name="jenis_kelamin" id="edit-jenis_kelamin"
                class="form-select @error('jenis_kelamin') is-invalid @enderror">
                <option value="">-- Pilih JK --</option>
                <option value="L">Laki - Laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="edit-agama" class="form-label">Agama</label>
            <select name="agama" id="edit-agama" class="form-select @error('agama') is-invalid @enderror">
                <option value="">-- Pilih Agama --</option>
                <option value="ISLAM">ISLAM</option>
                <option value="HINDU">HINDU</option>
                <option value="BUDHA">BUDHA</option>
                <option value="PROTESTAN">PROTESTAN</option>
                <option value="KATOLIK">KATOLIK</option>
                <option value="KONGHUCU">KONGHUCU</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="edit-prov_id" class="form-label">Provinsi:</label>
            <select name="prov_id" id="edit-prov_id"
                class="form-select @error('prov_id') is-invalid @enderror"
                onchange="RubahProvinsi(this.value)">
            </select>
            @error('prov_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit-kab_id" class="form-label">Kota/Kabupaten:</label>
            <select name="kab_id" id="edit-kab_id"
                class="form-select @error('kab_id') is-invalid @enderror" onchange="RubahKota(this.value)">
            </select>
            @error('kab_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="kec_id" class="form-label">Kecamatan:</label>
            <select name="kec_id" id="edit-kec_id"
                class="form-select @error('kec_id') is-invalid @enderror">
            </select>
            @error('kec_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit-kelurahan" class="form-label">Kelurahan:</label>
            <input name="kelurahan" id="edit-kelurahan"
                class="form-control @error('kelurahan') is-invalid @enderror"
                value="{{ old('edit-kelurahan') }}">
            @error('kelurahan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="edit-alamat" class="form-label">Alamat:</label>
            <textarea name="alamat" id="edit-alamat" class="form-control @error('alamat') is-invalid @enderror"
                value="{{ old('alamat') }}"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <form class="modal-content" id="deleteForm" action="" method="POST">
      @csrf
      @method('DELETE')
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <p>Apakah Anda yakin ingin menghapus data ini? **<span id="delete-name"></span>**?</p>
          <input type="hidden" name="id" id="delete-id">
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Ya, Hapus</button>
      </div>
    </form>
  </div>
</div>

<script>
  $(document).ready(function() {
            // Tangkap saat tombol edit diklik
    $('.btn-edit').on('click', function() {
        // 1. Ambil data dari data-attributes
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        var email = $(this).data('email');
        var jenis_kelamin = $(this).data('jenis_kelamin');
        var agama = $(this).data('agama');
        var tempat_lahir = $(this).data('tempat_lahir');
        var tanggal_lahir = $(this).data('tanggal_lahir');
        var prov_id = $(this).data('prov_id');
        var kec_id = $(this).data('kec_id');
        var kab_id = $(this).data('kab_id');
        var kecamatan = $(this).data('kecamatan');
        var kelurahan = $(this).data('kelurahan');
        var alamat = $(this).data('alamat');
        RubahProvinsi(prov_id);
        RubahKota(kab_id);

        // 2. Isi data Role ke dalam form modal
        $('#edit-id').val(id);
        $('#edit-nama').val(nama);
        $('#edit-email').val(email);
        $('#edit-tempat_lahir').val(tempat_lahir);
        $('#edit-tanggal_lahir').val(tanggal_lahir);
        $('#edit-jenis_kelamin').val(jenis_kelamin);
        $('#edit-agama').val(agama);
        $('#edit-prov_id').val(prov_id);
        $('#edit-kab_id').val(kab_id);
        $('#edit-kec_id').val(kec_id);
        $('#edit-kecamatan').val(kecamatan);
        $('#edit-kelurahan').val(kelurahan);
        $('#edit-alamat').val(alamat);
        $('#edit-role-name').text(nama); // Tampilkan nama role di header modal

        // 3. Atur action form
        // Ganti '/role/' dengan URL route Anda yang benar, misal '/roles' atau sejenisnya
        $('#editForm').attr('action', '/user/' + id);

    });
    $('.btn-delete').on('click', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');

        // Isi data ke dalam form modal
        $('#delete-id').val(id);
        $('#delete-role-name').text(nama);

        // Atur action form
        // Ganti '/role/' dengan URL route Anda yang benar, misal '/roles' atau sejenisnya
        $('#deleteForm').attr('action', '/user/' + id);
    });
});
  document.addEventListener('DOMContentLoaded', function() {
      function Add() {
          fetch("/api/provinces").then(res => res.json()).then(res => {
              const data = res.data;
              const temp = data.map((d) => `
                  <option  value="${d.code}" >${d.name}</option>
      `);
              document.getElementById("prov_id").innerHTML = temp.join("");
              document.getElementById("edit-prov_id").innerHTML = temp.join("");
          })
      }


      Add();
  });
  function RubahProvinsi(id) {
      fetch(`/api/regencies/${id}`).then(res => res.json()).then(res => {
          const data = res.data;
          const temp = data.map((d) => `
                  <option value="${d.code}" >${d.name}</option>
      `);
          document.getElementById("kab_id").innerHTML = temp.join("");
          document.getElementById("edit-kab_id").innerHTML = temp.join("");
      })
  }

  function RubahKota(id) {
      fetch(`/api/districts/${id}`).then(res => res.json()).then(res => {
          const data = res.data;
          const temp = data.map((d) => `
                  <option value="${d.code}" >${d.name}</option>
      `);
          document.getElementById("kec_id").innerHTML = temp.join("");
          document.getElementById("edit-kec_id").innerHTML = temp.join("");
      })
  }
</script>
@endsection