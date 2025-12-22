@extends('layout')
@section('title', 'Surat Tugas')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>📄 Surat Tugas Mengajar</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah User
      </button>
      <form action="/surat" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari ..."
              value="{{ request('search') }}">

          <select name="user_id" class="form-select form-select-sm">
            <option >by dekan/dosen ...</option>
              @foreach ($user as $r)
                <option value="{{ $r->id }}" {{ request('user_id') == $r->id ? 'selected' : '' }}>{{ $r->biodata->nama }}</option>
              @endforeach
          </select>

          <select name="status" class="form-select form-select-sm">
              <option value="AKTIF" {{ request('status') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
              <option value="NONAKTIF" {{ request('status') == 'NONAKTIF' ? 'selected' : '' }}>Nonaktif</option>
              <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Approved</option>
              <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected</option>
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
              <th>Pembuat</th>
              <th>Kepada</th>
              <th>Nomor</th>
              <th>Tgl & Semester</th>
              <th>Surat</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($surat as $index => $kls)
              <tr>
                <td>{{ $surat->firstItem() + $index }}</td>
                <td >
                  <div class="flex flex-col">
                    <span>{{ $kls->dekan->biodata->nama }}</span>
                    <span class="italic opacity-70">{{ "@" }}dekan</span>
                  </div>
                </td>
                <td >
                  <div class="flex flex-col">
                    <span>{{ $kls->donsen->biodata->nama }}</span>
                    <span class="italic opacity-70">{{ "@" }}dosen</span>
                  </div>
                </td>
                <td >
                  <div class="flex flex-col">
                    <span>SK: {{ $kls->nomor_sk }}</span>
                    <span class="italic opacity-70">Surat: {{ $kls->nomor_surat }}</span>
                  </div>
                </td>
                <td >
                  <div class="flex flex-col">
                    <span>{{ $kls->tanggal->format('d/m/Y') }}</span>
                    <span class="italic opacity-70">Semester {{ $kls->semester->jenis }} {{ $kls->semester->tahun_akademik }}</span>
                  </div>
                </td>
                <td>
                  <div class="flex gap-2 items-center justify-center">
                    <button type="button" class="btn btn-sm btn-info btn-tampilkan-pdf"
                        title="Lihat PDF" data-surat-id="{{ $kls->id }}">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-info btn-tampilkan-pdf"
                        title="Lihat PDF" data-surat-id="{{ $kls->id }}">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                    </button>
                  </div>
                </td>
                <td>
                    @if ($kls->status == 'AKTIF')
                        <span class="badge bg-warning">{{ ucfirst(strtolower($kls->status)) }}</span>
                    @elseif($kls->status == "APPROVED")
                      <span class="badge bg-success">{{ ucfirst(strtolower($kls->status)) }}</span>
                    @elseif($kls->status == "REJECTED")
                      <span class="badge bg-danger">{{ ucfirst(strtolower($kls->status)) }}</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst(strtolower($kls->status)) }}</span>
                    @endif
                </td>
                <td>
                  <button type="button" class="btn btn-outline-primary btn-sm btn-edit"
                      data-bs-toggle="modal" data-bs-target="#editModal"
                      data-id="{{ $kls->id }}"
                      data-nomor_sk="{{ $kls->nomor_sk }}"
                      > 
                      <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $kls->id }}" data-nama="{{ $kls->nomor_surat }}">
                      <i class="bi bi-trash"></i>
                  </button>
                </td>
              </tr>
          @endforeach
          </tbody>
        </table>
        <div class="mt-3">
            {{ $surat->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" action="/user" method="POST" enctype="multipart/form-data">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addRuanganModalLabel">Tambah User</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-3">
        <div class="col-md-6">
          <label class="form-label">Nama lengkap</label>
          <input type="text" class="form-control form-control-sm" name="nama">
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="text" class="form-control form-control-sm"  name="email">
        </div>
        <div class="col-md-6">
          <label class="form-label">Password</label>
          <input type="password" class="form-control form-control-sm" name="password">
        </div>
        <div class="col-md-6">
          <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
          <select name="jenis_kelamin" id="jenis_kelamin"
              class="form-select form-select-sm @error('jenis_kelamin') is-invalid @enderror">
              <option value="">-- Pilih JK --</option>
              <option value="L">Laki - Laki</option>
              <option value="P">Perempuan</option>
          </select>
        </div>
        <div class="col-md-6">
          <label for="agama" class="form-label">Agama:</label>
          <select name="agama" id="agama"
              class="form-select form-select-sm @error('agama') is-invalid @enderror">
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
          <textarea type="textarea" class="form-control form-control-sm" name="alamat"></textarea>
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
        <h5 class="modal-title" id="editModalLabel">Edit User: <span id="edit-name"></span></h5>
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

          $('#edit-id').val(id);
          $('#edit-nama').val(nama);

          $('#editForm').attr('action', '/user/' + id);


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var nama = $(this).data('nama');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/user/' + id);
      });
  });
</script>
@endsection