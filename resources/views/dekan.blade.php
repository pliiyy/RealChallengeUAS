@extends('layout')
@section('title', 'Data Dekan')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>👨‍🏫 Data Dekan</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah Dekan
      </button>
      <form action="/dekan" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama ..."
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
              <th>Fakultas</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($dekan as $index => $kls)
              <tr>
                <td>{{ $dekan->firstItem() + $index }}</td>
                <td>{{ $kls->user->biodata->nama }}</td>
                <td>{{ $kls->fakultas->nama }}</td>
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
                      data-nama="{{ $kls->user->biodata->nama }}"
                      data-fakultas_id="{{ $kls->fakultas_id }}"> 
                      <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $kls->id }}" data-nama="{{ $kls->user->biodata->nama }}">
                      <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
          @endforeach
          </tbody>
        </table>
        <div class="mt-3">
            {{ $dekan->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" action="/dekan" method="POST" enctype="multipart/form-data">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addRuanganModalLabel">Tambah Dekan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama lengkap</label>
          <input type="text" class="form-control" name="nama">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="text" class="form-control"  name="email">
        </div>
        <div class="col-md-6">
          <label class="form-label">Password</label>
          <input type="password" class="form-control" name="password">
        </div>
        <div class="col-md-6">
          <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
          <select name="jenis_kelamin" id="jenis_kelamin"
              class="form-select @error('jenis_kelamin') is-invalid @enderror">
              <option value="">-- Pilih JK --</option>
              <option value="L">Laki - Laki</option>
              <option value="P">Perempuan</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="agama" class="form-label">Agama:</label>
          <select name="agama" id="agama"
              class="form-select @error('agama') is-invalid @enderror">
              <option value="">-- Pilih Agama --</option>
              <option value="ISLAM">ISLAM</option>
              <option value="HINDU">HINDU</option>
              <option value="BUDHA">BUDHA</option>
              <option value="PROTESTAN">PROTESTAN</option>
              <option value="KATOLIK">KATOLIK</option>
              <option value="KONGHUCU">KONGHUCU</option>
          </select>
          @error('agama')
              <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Alamat</label>
          <input type="textarea" class="form-control" name="alamat">
        </div>
        <div class="col-md-6">
          <label class="form-label">Provinsi</label>
          <input type="text" class="form-control" name="prov_id">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" id="editForm" action="" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editModalLabel">Edit Dekan: <span id="edit-name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label for="edit-nama" class="form-label">Nama Ruangan</label>
          <input type="text" class="form-control" id="edit-nama" name="nama" required />
        </div>
        <div class="mb-3">
          <label for="edit-nama" class="form-label">Nama Ruangan</label>
          <input type="text" class="form-control" id="edit-kode" name="kode" required />
        </div>
        <div class="mb-3">
          <label for="edit-kapasitas" class="form-label">Kapasitas</label>
          <input class="form-control" id="edit-kapasitas" name="kapasitas"></textarea>
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
          <p>Apakah Anda yakin ingin menghapus Ruangan ini? **<span id="delete-name"></span>**?</p>
          <input type="hidden" name="id" id="delete-id">
      </div>
      <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Ya, Hapus Ruangan Ini</button>
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
          var kode = $(this).data('kode');

          $('#edit-id').val(id);
          $('#edit-nama').val(nama);

          $('#editForm').attr('action', '/ruangan/' + id);


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var nama = $(this).data('nama');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/ruangan/' + id);
      });
  });
</script>
@endsection