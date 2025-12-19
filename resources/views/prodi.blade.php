@extends('layout')
@section('title', 'Program Studi')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span><i class="bi bi-journal-text me-2"></i> Program Studi</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah Prodi
      </button>
      <form action="/prodi" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Prodi ..."
              value="{{ request('search') }}">

          <select name="fakultas_id" class="form-select form-select-sm">
            <option></option>
              @foreach ($fakultas as $item)
                <option value="{{ $item->id }}" {{ request('fakultas_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
              @endforeach
          </select>
          <select name="jenjang" class="form-select form-select-sm">
            <option></option>
              <option value="D3" {{ request('jenjang') == 'D3' ? 'selected' : '' }}>D3</option>
              <option value="S1" {{ request('jenjang') == 'S1' ? 'selected' : '' }}>S1</option>
              <option value="S2" {{ request('jenjang') == 'S2' ? 'selected' : '' }}>S2</option>
              <option value="S3" {{ request('jenjang') == 'D3' ? 'selected' : '' }}>S3</option>
          </select>
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
              <th>Fakultas</th>
              <th>Program Studi</th>
              <th>Kode</th>
              <th>Jenjang</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($prodi as $index => $kls)
              <tr>
                <td>{{ $prodi->firstItem() + $index }}</td>
                <td><span class="badge bg-info">{{ $kls->fakultas->nama }}</span></td>
                <td>{{ $kls->nama }}</td>
                <td>{{ $kls->kode }}</td>
                <td>{{ $kls->jenjang }}</td>
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
                      data-fakultas_id="{{ $kls->fakultas_id }}"
                      data-nama="{{ $kls->nama }}"
                      data-kode="{{ $kls->kode }}"
                      data-jenjang="{{ $kls->jenjang }}"
                      > 
                      <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $kls->id }}" data-nama="{{ $kls->nama }}">
                      <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
          @endforeach
          </tbody>
        </table>
        <div class="mt-3">
            {{ $prodi->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="/prodi" method="POST">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addModalLabel">Tambah Program Studi</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Fakultas</label>
          <select name="fakultas_id" class="form-select">
              @foreach ($fakultas as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
              @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Program Studi</label>
          <input type="text" class="form-control" placeholder="Contoh: Sistem Informasi" name="nama">
        </div>
        <div class="mb-3">
          <label class="form-label">Kode Prodi</label>
          <input type="text" class="form-control" placeholder="Contoh: Sistem Informasi" name="kode">
        </div>
        <div class="mb-3">
          <label class="form-label">Jenjang</label>
          <select name="jenjang" class="form-select">
            <option value="D3">D3</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
          </select>
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
    <form class="modal-content" id="editForm" action="" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="editModalLabel">Edit Fakultas: <span id="edit-name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Fakultas</label>
          <select name="fakultas_id" id="edit-fakultas_id" class="form-select">
              @foreach ($fakultas as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
              @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Program Studi</label>
          <input type="text" class="form-control" id="edit-nama" placeholder="Contoh: Sistem Informasi" name="nama">
        </div>
        <div class="mb-3">
          <label class="form-label">Kode Prodi</label>
          <input type="text" class="form-control" id="edit-kode" placeholder="Contoh: Sistem Informasi" name="kode">
        </div>
        <div class="mb-3">
          <label class="form-label">Jenjang</label>
          <select name="jenjang" class="form-select" id="edit-jenjang">
            <option value="D3">D3</option>
            <option value="S1">S1</option>
            <option value="S2">S2</option>
            <option value="S3">S3</option>
          </select>
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
          <p>Apakah Anda yakin ingin menghapus Fakultas ini? **<span id="delete-name"></span>**?</p>
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
          var fakultas_id = $(this).data('fakultas_id');
          var nama = $(this).data('nama');
          var kode = $(this).data('kode');
          var jenjang = $(this).data('jenjang');

          $('#edit-id').val(id);
          $('#edit-fakultas_id').val(fakultas_id);
          $('#edit-nama').val(nama);
          $('#edit-kode').val(kode);
          $('#edit-jenjang').val(jenjang);

          $('#editForm').attr('action', '/prodi/' + id);


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var nama = $(this).data('nama');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/prodi/' + id);
      });
  });
</script>
@endsection