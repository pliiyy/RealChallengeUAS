@extends('layout')
@section('title', 'Dekan')
@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>👨‍🏫 Data Kosma</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah
      </button>
      <form action="/kosma" method="GET" class="d-flex gap-2 align-items-center">
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
              <th>Kosma Kelas</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($kosma as $index => $kls)
              <tr>
                <td>{{ $kosma->firstItem() + $index }}</td>
                <td >
                    <div class="flex flex-col">
                        <div >{{ $kls->mahasiswa->user->biodata->nama }}</div>    
                        <div style="font-style:italic;font-size:12px;">{{ $kls->nim }}</div>    
                    </div>
                </td>
                <td>{{ $kls->mahasiswa->user->email }}</td>
                <td class="flex flex-col">
                    <span>{{ $kls->kelas->nama }} / {{ $kls->kelas->tipe }}</span>
                    <span style="font-style:italic;font-size:12px;">{{ $kls->kelas->prodi->nama }}</span>
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
                      data-mahasiswa_id="{{ $kls->mahasiswa_id }}"
                      data-kelas_id="{{ $kls->kelas_id }}"
                      > 
                      <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $kls->id }}" data-nama="{{ $kls->mahasiswa->user->biodata->nama }}">
                      <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
          @endforeach
          </tbody>
        </table>
        <div class="mt-3">
            {{ $kosma->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="/kosma" method="POST">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addRuanganModalLabel">Tambah Data</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="mahasiswa_id" class="form-label">Mahasiswa:</label>
            <select name="mahasiswa_id" class="form-select form-select-sm">
                @foreach ($mahasiswa as $item)
                    <option value="{{ $item->id }}">{{ $item->user->biodata->nama }} ({{ $item->nim }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="kelas_id" class="form-label">Kelas:</label>
            <select name="kelas_id"  class="form-select form-select-sm">
                @foreach ($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                @endforeach
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
        <h5 class="modal-title" id="editModalLabel">Edit Data: <span id="edit-name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
            <label for="mahasiswa_id" class="form-label">Mahasiswa:</label>
            <select name="mahasiswa_id" id="edit-mahasiswa_id" class="form-select form-select-sm">
                @foreach ($mahasiswa as $item)
                    <option value="{{ $item->id }}">{{ $item->user->biodata->nama }} ({{ $item->nim }})</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="kelas_id" class="form-label">Kelas:</label>
            <select name="kelas_id" id="edit-kelas_id" class="form-select form-select-sm">
                @foreach ($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                @endforeach
            </select>
        </div>
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
        var mahasiswa_id = $(this).data('mahasiswa_id');
        var kelas_id = $(this).data('kelas_id');
        RubahProvinsi(prov_id);
        RubahKota(kab_id);

        // 2. Isi data Role ke dalam form modal
        $('#edit-id').val(id);
        $('#edit-mahasiswa_id').val(mahasiswa_id);
        $('#edit-kelas_id').val(kelas_id);
        $('#edit-role-name').text(nama); // Tampilkan nama role di header modal

        // 3. Atur action form
        // Ganti '/role/' dengan URL route Anda yang benar, misal '/roles' atau sejenisnya
        $('#editForm').attr('action', '/kosma/' + id);

    });
    $('.btn-delete').on('click', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');

        // Isi data ke dalam form modal
        $('#delete-id').val(id);
        $('#delete-role-name').text(nama);

        // Atur action form
        // Ganti '/role/' dengan URL route Anda yang benar, misal '/roles' atau sejenisnya
        $('#deleteForm').attr('action', '/kosma/' + id);
    });
});  
</script>
@endsection