@extends('layout')
@section('title', 'Semester')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>📘 Data Semester</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah Semester
      </button>
      <form action="/semester" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari TA..."
              value="{{ request('search') }}">

          <select name="status" class="form-select form-select-sm">
              <option ></option>
              <option value="AKTIF" {{ request('status') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
              <option value="NONAKTIF" {{ request('status') == 'NONAKTIF' ? 'selected' : '' }}>Nonaktif</option>
          </select>
          <select name="jenis" class="form-select form-select-sm">
              <option ></option>
              <option value="Ganjil" {{ request('jenis') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
              <option value="Genal" {{ request('jenis') == 'Genap' ? 'selected' : '' }}>Genap</option>
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
              <th>Semester</th>
              <th>Tahun Akademik</th>
              <th>Periode</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($semester as $index => $kls)
              <tr>
                <td>{{ $semester->firstItem() + $index }}</td>
                <td>
                  <div class="flex flex-col">
                    <span>Semester {{ $kls->semester }}</span>
                    <span class="opacity-70 text-xs italic" style="opacity:80px;font-size:12px;font-style: italic">{{ $kls->jenis }}</span>
                  </div>
                </td>
                <td>{{ $kls->tahun_akademik }}</td>
                <td>{{ $kls->tanggal_mulai->format('d/m/Y') }} - {{ $kls->tanggal_mulai->format('d/m/Y') }}</td>
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
                      data-jenis="{{ $kls->jenis }}"
                      data-semester="{{ $kls->semester }}"
                      data-tahun_akademik="{{ $kls->tahun_akademik }}"
                      data-tanggal_mulai="{{ $kls->tanggal_mulai->format("Y-m-d") }}"
                      data-tanggal_selesai="{{ $kls->tanggal_selesai->format("Y-m-d") }}"> 
                      <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $kls->id }}" data-nama="{{ $kls->semester }} {{ $kls->tahun_akademik }}">
                      <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
          @endforeach
          </tbody>
        </table>
        <div class="mt-3">
            {{ $semester->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="/semester" method="POST">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addRuanganModalLabel">Tambah Semester Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Jenis Semester</label>
            <select class="form-select" name="jenis" id="jenis" required>
                <option value="">-- Pilih Semester --</option>
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Semester</label>
          <select class="form-select" name="semester">
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
              <option value="3">Semester 3</option>
              <option value="4">Semester 4</option>
              <option value="5">Semester 5</option>
              <option value="6">Semester 6</option>
              <option value="7">Semester 7</option>
              <option value="8">Semester 8</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Tahun Akademik</label>
          <input type="text" class="form-control" placeholder="Contoh: 2025/2026" name="tahun_akademik">
        </div>
        <div class="mb-3">
          <label class="form-label">Periode Mulai</label>
          <input type="date" class="form-control"  name="tanggal_mulai">
        </div>
        <div class="mb-3">
          <label class="form-label">Periode Selesai</label>
          <input type="date" class="form-control"  name="tanggal_selesai">
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
        <h5 class="modal-title" id="editModalLabel">Edit Semester: <span id="edit-name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
          <label class="form-label">Jenis Semester</label>
            <select class="form-select" name="jenis" id="edit-jenis" required>
                <option value="">-- Pilih Semester --</option>
                <option value="Ganjil">Ganjil</option>
                <option value="Genap">Genap</option>
            </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Semester</label>
          <select class="form-select" name="semester" id="edit-semester">
              <option value="1">Semester 1</option>
              <option value="2">Semester 2</option>
              <option value="3">Semester 3</option>
              <option value="4">Semester 4</option>
              <option value="5">Semester 5</option>
              <option value="6">Semester 6</option>
              <option value="7">Semester 7</option>
              <option value="8">Semester 8</option>
          </select>
        </div>
        <div class="mb-3">
          <label for="edit-tahun_akademik" class="form-label">Tahun Akademik</label>
          <input type="text" class="form-control" id="edit-tahun_akademik" name="tahun_akademik" required />
        </div>
        <div class="mb-3">
          <label for="edit-tanggal_mulai" class="form-label">Periode Mulai</label>
          <input type="date" class="form-control" id="edit-tanggal_mulai" name="tanggal_mulai"></textarea>
        </div>
        <div class="mb-3">
          <label for="edit-tanggal_selesai" class="form-label">Periode Selesai</label>
          <input type="date" class="form-control" id="edit-tanggal_selesai" name="tanggal_selesai"></textarea>
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
          var jenis = $(this).data('jenis');
          var semester = $(this).data('semester');
          var tahun_akademik = $(this).data('tahun_akademik');
          var tanggal_mulai = $(this).data('tanggal_mulai');
          var tanggal_selesai = $(this).data('tanggal_selesai');

          $('#edit-id').val(id);
          $('#edit-jenis').val(jenis);
          $('#edit-semester').val(semester);
          $('#edit-tahun_akademik').val(tahun_akademik);
          $('#edit-tanggal_mulai').val(tanggal_mulai);
          $('#edit-tanggal_selesai').val(tanggal_selesai);

          $('#editForm').attr('action', '/semester/' + id);


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var semester = $(this).data('semester');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/semester/' + id);
      });
  });
</script>
@endsection