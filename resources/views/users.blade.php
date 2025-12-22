@extends('layout')
@section('title', 'Users')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>👨‍🏫 Data Pengguna</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah User
      </button>
      <form action="/user" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari ..."
              value="{{ request('search') }}">

          <select name="role_id" class="form-select form-select-sm">
            <option value="">by role ...</option>
              @foreach ($role as $r)
                <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->nama }}</option>
              @endforeach
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
              <th>Nama Lengkap</th>
              <th>Email</th>
              <th>Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($user as $index => $kls)
              <tr>
                <td>{{ $user->firstItem() + $index }}</td>
                <td >
                  <div class="flex flex-col">
                    <span>{{ $kls->biodata->nama }}</span>
                    <span class="italic opacity-70">@ {{ $kls->dosen?->nidn ?? $kls->mahasiswa?->nim }}</span>
                  </div>
                </td>
                <td >
                  <div class="flex flex-col">
                    <span>{{ $kls->email }}</span>
                    <span class="italic opacity-70">{{ $kls->biodata->jenis_kelamin }}</span>
                  </div>
                </td>
                <td >
                  <div class="flex flex-wrap gap-1 text-xs">
                    @foreach ($kls->role as $r)
                      <span class="badge bg-warning">{{ $r->nama }}</span>
                    @endforeach
                  </div>
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
        <div id="role-container">
        </div>
        <button type="button" id="add-role" class="btn btn-sm btn-success">+ Tambah Jabatan</button>
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
  let roleIndex = 1;
  const availableRoles = @json($role);
  const availableFakultas = @json($fakultas);
  const availableProdi = @json($prodi);
  const availableKelas = @json($kelas);
  
  console.log({availableRoles,availableFakultas,availableProdi,availableKelas});

    function getRoleOptions() {
        return availableRoles.map(role => 
            `<option value="${role.id}">${role.nama}</option>`
        ).join('');
    }
    function getFakultasOptions() {
        return availableFakultas.map(role => 
            `<option value="${role.id}">${role.nama}</option>`
        ).join('');
    }
    function getProdiOptions() {
        return availableProdi.map(role => 
            `<option value="${role.id}">${role.nama}</option>`
        ).join('');
    }
    function getKelasOptions() {
        return availableKelas.map(role => 
            `<option value="${role.id}">${role.nama}</option>`
        ).join('');
    }

    function handleRoleChange(selectElement, index) {
    const selectedText = selectElement.options[selectElement.selectedIndex].text.toLowerCase();
    fakultasOptions = document.getElementByClassName("addFakultasRole");
    prodiOptions = document.getElementByClassName("addProdiRole");
    kelasOptions = document.getElementByClassName("addKelasRole");
    startOptions = document.getElementByClassName("addStartRole");
    endOptions = document.getElementByClassName("addEndRole");

    // Jika yang dipilih adalah 'dekan'
    if (selectedText.includes('dekan')) {
      fakultasOptions.classList.remove("hidden")
    }
}


    document.getElementById('add-role').addEventListener('click', function() {
        let container = document.getElementById('role-container');
        let html = `
            <div class="role-item mb-3 border p-3" data-index="${roleIndex}">
                <div class="row">
                    <div class="col-md-4">
                        <select name="role[${roleIndex}][nama]" class="form-select form-select-sm" onchange="handleRoleChange(this, ${roleIndex})">
                          ${getRoleOptions()}
                        </select>
                    </div>
                    <div class="col-md-4 hidden" id="addFakultasRole">
                        <select name="role[${roleIndex}][fakultas_id]" class="form-select form-select-sm">
                          ${getFakultasOptions()}
                        </select>
                    </div>
                    <div class="col-md-4 hidden" id="addProdiRole">
                        <select name="role[${roleIndex}][prodi_id]" class="form-select form-select-sm">
                          ${getProdiOptions()}
                        </select>
                    </div>
                    <div class="col-md-4 hidden" id="addKelasRole">
                        <select name="role[${roleIndex}][kelas_id]" class="form-select form-select-sm">
                          ${getKelasOptions()}
                        </select>
                    </div>
                    <div class="col-md-3 hidden" id="addStartRole">
                        <input type="date" name="role[${roleIndex}][periode_mulai]" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-3 hidden" id="addEndRole">
                        <input type="date" name="role[${roleIndex}][periode_selesai]" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-remove btn-sm">Hapus</button>
                    </div>
                </div>
            </div>`;
        container.insertAdjacentHTML('beforeend', html);
        roleIndex++;
    });

    // Delegasi event untuk tombol hapus
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-remove')) {
            e.target.closest('.role-item').remove();
        }
    });
</script>
@endsection