@extends('layout')
@section('title', 'Kelas')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>🏫 Data Kelas</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah Kelas
      </button>
      <form action="/kelas" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari Kelas ..."
              value="{{ request('search') }}">

          <select name="prodi_id" class="form-select form-select-sm">
            <option>by prodi ...</option>
              @foreach ($prodi as $item)
                <option value="{{ $item->id }}" {{ request('prodi_id') == $item->id ? 'selected' : '' }}>{{ $item->nama }}</option>
              @endforeach
          </select>
          <select name="angkatan_id" class="form-select form-select-sm">
            <option>by angkatan ...</option>
            @foreach ($angkatan as $item)
                <option value="{{ $item->id }}" {{ request('angkatan_id') == $item->id ? 'selected' : '' }}>{{ $item->tahun }}</option>
              @endforeach
          </select>
          <select name="angkatan_id" class="form-select form-select-sm">
            <option>by tipe ...</option>
            <option value="R" {{ request('tipe') == 'R' ? 'selected' : '' }}>Reguller</option>
              <option value="NR" {{ request('tipe') == 'NR' ? 'selected' : '' }}>Non Reguller</option>
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
              <th>Kelas</th>
              <th>Angkatan</th>
              <th>Program Studi</th>
              <th>Kapasitas</th>
              <th>Tipe</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($kelas as $index => $kls)
              <tr>
                <td>{{ $kelas->firstItem() + $index }}</td>
                <td><span class="badge bg-info">{{ $kls->fakultas->nama }}</span></td>
                <td class="flex flex-col">
                  <span>{{ $kls->nama }}</span>
                  <span class="opacity-70">{{ $kls->kode }}</span>
                </td>
                <td>{{ $kls->angkatan->tahun }}</td>
                <td class="flex flex-col">
                  <span>{{ $kls->prodi->nama }}</span>
                  <span class="opacity-70">smstr {{ $kls->semester }}</span>
                </td>
                <td>{{ $kl->kapasitas }}</td>
                <td>
                  @if ($kls->tipe == 'R')
                      <span class="badge bg-info">{{ $kls->tipe }}</span>
                  @else
                      <span class="badge bg-warning">{{ $kls->tipe }}</span>
                  @endif
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
                      data-nama="{{ $kls->nama }}"
                      data-kode="{{ $kls->kode }}"
                      data-angkatan_id="{{ $kls->angkatan_id }}"
                      data-prodi_id="{{ $kls->prodi_id }}"
                      data-semester="{{ $kls->semester }}"
                      data-tipe="{{ $kls->tipe }}"
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
    <form class="modal-content" action="/kelas" method="POST">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addModalLabel">Tambah Kelas</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Kelas</label>
          <input type="text" class="form-control" placeholder="Contoh: AB11" name="nama">
        </div>
        <div class="mb-3">
          <label class="form-label">Kode Kelas</label>
          <input type="text" class="form-control" placeholder="Contoh: AB" name="kode">
        </div>
        <div class="mb-3">
          <label class="form-label">Semester</label>
          <input type="text" class="form-control" placeholder="Contoh: 1" name="semester">
        </div>
        <div class="mb-3">
          <label class="form-label">Program Studi</label>
          <select name="prodi_id" class="form-select">
              @foreach ($prodi as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
              @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Angkatan</label>
          <select name="angkatan_id" class="form-select">
              @foreach ($angkatan as $item)
                <option value="{{ $item->id }}">{{ $item->tahun }}</option>
              @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tipe</label>
          <select name="tipe" class="form-select">
            <option value="R">Reguller</option>
            <option value="NR">Non Reguller</option>
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
        <h5 class="modal-title" id="editModalLabel">Edit Kelas: <span id="edit-name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Nama Kelas</label>
          <input type="text" class="form-control" placeholder="Contoh: AB11" name="nama" id="edit-nama">
        </div>
        <div class="mb-3">
          <label class="form-label">Kode Kelas</label>
          <input type="text" class="form-control" placeholder="Contoh: AB" name="kode" id="edit-kode">
        </div>
        <div class="mb-3">
          <label class="form-label">Semester</label>
          <input type="text" class="form-control" placeholder="Contoh: 1" name="semester" id="edit-semester">
        </div>
        <div class="mb-3">
          <label class="form-label">Program Studi</label>
          <select name="prodi_id" class="form-select" id="edit-prodi_id">
              @foreach ($prodi as $item)
                <option value="{{ $item->id }}">{{ $item->nama }}</option>
              @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Angkatan</label>
          <select name="angkatan_id" class="form-select" id="edit-angkatan_id">
              @foreach ($angkatan as $item)
                <option value="{{ $item->id }}">{{ $item->tahun }}</option>
              @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tipe</label>
          <select name="tipe" class="form-select" id="edit-tipe">
            <option value="R">Reguller</option>
            <option value="NR">Non Reguller</option>
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
          var kode = $(this).data('kode');
          var angkatan_id = $(this).data('angkatan_id');
          var prodi_id = $(this).data('prodi_id');
          var semester = $(this).data('semester');
          var tipe = $(this).data('tipe');

          $('#edit-id').val(id);
          $('#edit-nama').val(nama);
          $('#edit-kode').val(kode);
          $('#edit-angkatan_id').val(angkatan_id);
          $('#edit-prodi_id').val(prodi_id);
          $('#edit-semester').val(semester);
          $('#edit-tipe').val(tipe);

          $('#editForm').attr('action', '/kelas/' + id);


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var nama = $(this).data('nama');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/kelas/' + id);
      });
  });
</script>
@endsection