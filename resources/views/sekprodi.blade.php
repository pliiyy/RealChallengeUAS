@extends('layout')
@section('title', 'Sekprodi')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>👨‍🏫 Data Sekprodi</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah
      </button>
      <form action="/sekprodi" method="GET" class="d-flex gap-2 align-items-center">
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
              <th>Program Studi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($sekprodi as $index => $kls)
              <tr>
                <td>{{ $sekprodi->firstItem() + $index }}</td>
                <td >{{ $kls->user->biodata->nama }}</td>
                <td>{{ $kls->user->email }}</td>
                <td class="flex flex-col">
                    <span>{{ $kls->prodi->nama }}</span>
                    <span style="font-style:italic;font-size:12px;">{{ $kls->periode_mulai->format("d/m/Y") }} - {{ $kls->periode_selesai->format("d/m/Y") }}</span>
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
                      data-nama="{{ $kls->user->biodata->nama }}"
                      data-email="{{ $kls->user->email }}"
                      data-tempat_lahir="{{ $kls->user->biodata->tempat_lahir }}"
                      data-tanggal_lahir="{{ $kls->user->biodata->tanggal_lahir }}"
                      data-jenis_kelamin="{{ $kls->user->biodata->jenis_kelamin }}"
                      data-agama="{{ $kls->user->biodata->agama }}"
                      data-prov_id="{{ $kls->user->biodata->prov_id }}"
                      data-kab_id="{{ $kls->user->biodata->kab_id }}"
                      data-kec_id="{{ $kls->user->biodata->kec_id }}"
                      data-kelurahan="{{ $kls->user->biodata->kelurahan }}"
                      data-alamat="{{ $kls->user->biodata->alamat }}"
                      data-periode_mulai="{{ $kls->periode_mulai->format('Y-m-d') }}"
                      data-periode_selesai="{{ $kls->periode_selesai->format('Y-m-d') }}"
                      data-fakultas_id="{{ $kls->prodi_id }}"
                      > 
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
            {{ $sekprodi->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="/sekprodi" method="POST">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addRuanganModalLabel">Tambah Data</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
            <label for="user_selection_type" class="form-label">Tindakan User:</label>
            <select id="user_selection_type" class="form-select">
                <option value="existing">Pilih User yang Sudah Ada</option>
                <option value="new">Buat User Baru</option>
            </select>
        </div>

        <div id="existing-user-block">
            <div class="mb-3">
                <label for="user_id" class="form-label">Pilih User:</label>
                <select name="user_id" id="user_id"
                    class="form-select @error('user_id') is-invalid @enderror">
                    <option value="">-- Pilih User --</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                            {{ old('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->biodata->nama }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="exs_periode_mulai" class="form-label">Periode Mulai:</label>
                <input type="date" name="exs_periode_mulai" id="exs_periode_mulai"
                    class="form-control form-control-sm @error('exs_periode_mulai') is-invalid @enderror" value="{{ old('exs_periode_mulai') }}">
                @error('exs_periode_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="exs_periode_selesai" class="form-label">Periode Selesai:</label>
                <input type="date" name="exs_periode_selesai" id="exs_periode_selesai"
                    class="form-control form-control-sm @error('exs_periode_selesai') is-invalid @enderror" value="{{ old('exs_periode_selesai') }}">
                @error('exs_periode_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="exs_fakultas_id" class="form-label">Program Studi:</label>
                <select name="exs_fakultas_id" class="form-select form-select-sm" id="exs_fakultas_id">
                    @foreach ($fakultas as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('exs_fakultas_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div id="new-user-block" class="d-none">
            <h5 class="mt-4 mb-3 text-primary">Data Baru</h5>
            <div class="mb-3">
                <label for="periode_mulai" class="form-label">Periode Mulai:</label>
                <input type="date" name="periode_mulai" id="periode_mulai"
                    class="form-control form-control-sm @error('periode_mulai') is-invalid @enderror" value="{{ old('periode_mulai') }}">
                @error('periode_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="periode_selesai" class="form-label">Periode Selesai:</label>
                <input type="date" name="periode_selesai" id="periode_selesai"
                    class="form-control form-control-sm @error('periode_selesai') is-invalid @enderror" value="{{ old('periode_selesai') }}">
                @error('periode_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="fakultas_id" class="form-label">Program Studi:</label>
                <select name="fakultas_id" id="fakultas_id" class="form-select form-select-sm">
                    @foreach ($fakultas as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('fakultas_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="user_name" class="form-label">Nama Lengkap:</label>
                <input type="text" name="user_name" id="user_name"
                    class="form-control @error('user_name') is-invalid @enderror"
                    value="{{ old('user_name') }}">
                @error('user_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="fakultas_id" class="form-label">Fakultas:</label>
                <select name="fakultas_id" id="fakultas_id" class="form-select form-select-sm">
                    @foreach ($fakultas as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                    @endforeach
                </select>
                @error('fakultas_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="user_email" class="form-label">Email User:</label>
                <input type="email" name="user_email" id="user_email"
                    class="form-control @error('user_email') is-invalid @enderror"
                    value="{{ old('user_email') }}">
                @error('user_email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="user_password" class="form-label">Password:</label>
                <input type="password" name="user_password" id="user_password"
                    class="form-control @error('user_password') is-invalid @enderror">
                @error('user_password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="tempat_lahir" class="form-label">Tempat Lahir:</label>
                <input name="tempat_lahir" id="tempat_lahir"
                    class="form-control @error('tempat_lahir') is-invalid @enderror"
                    value="{{ old('tempat_lahir') }}">
                @error('tempat_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="tanggal_lahir" class="form-label">Tanggal Lahir:</label>
                <input name="tanggal_lahir" id="tanggal_lahir"
                    class="form-control @error('tanggal_lahir') is-invalid @enderror"
                    value="{{ old('tanggal_lahir') }}" type="date">
                @error('tanggal_lahir')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="jenis_kelamin" class="form-label">Jenis Kelamin:</label>
                <select name="jenis_kelamin" id="jenis_kelamin"
                    class="form-select @error('jenis_kelamin') is-invalid @enderror">
                    <option value="">-- Pilih JK --</option>
                    <option value="L">Laki - Laki</option>
                    <option value="P">Perempuan</option>
                </select>
                @error('jenis_kelamin')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
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
            <div class="mb-3">
                <label for="prov_id" class="form-label">Provinsi:</label>
                <select name="prov_id" id="prov_id"
                    class="form-select @error('prov_id') is-invalid @enderror"
                    onchange="RubahProvinsi(this.value)">
                </select>
                @error('prov_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="kab_id" class="form-label">Kota/Kabupaten:</label>
                <select name="kab_id" id="kab_id"
                    class="form-select @error('kab_id') is-invalid @enderror"
                    onchange="RubahKota(this.value)">
                </select>
                @error('kab_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="kec_id" class="form-label">Kecamatan:</label>
                <select name="kec_id" id="kec_id"
                    class="form-select @error('kec_id') is-invalid @enderror">
                </select>
                @error('kec_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="kelurahan" class="form-label">Kelurahan:</label>
                <input name="kelurahan" id="kelurahan"
                    class="form-control @error('kelurahan') is-invalid @enderror"
                    value="{{ old('kelurahan') }}">
                @error('kelurahan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat:</label>
                <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror"
                    value="{{ old('alamat') }}"></textarea>
                @error('alamat')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
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
        <input type="hidden" name="id" id="edit-id"> {{-- ID role yang akan diupdate --}}
        <div class="mb-3">
        <label for="periode_mulai" class="form-label">Periode Mulai:</label>
        <input type="date" name="periode_mulai" id="edit-periode_mulai"
            class="form-control form-control-sm @error('periode_mulai') is-invalid @enderror" value="{{ old('periode_mulai') }}">
        @error('periode_mulai')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        </div>
        <div class="mb-3">
            <label for="periode_selesai" class="form-label">Periode Selesai:</label>
            <input type="date" name="periode_selesai" id="edit-periode_selesai"
                class="form-control form-control-sm @error('periode_selesai') is-invalid @enderror" value="{{ old('periode_selesai') }}">
            @error('periode_selesai')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="nama" class="form-label">Nama Lengkap:</label>
            <input type="text" name="nama" id="edit-nama"
                class="form-control @error('nama') is-invalid @enderror"
                value="{{ old('nama') }}">
            @error('nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="fakultas_id" class="form-label">Program Studi:</label>
            <select name="fakultas_id" id="edit-fakultas_id" class="form-select form-select-sm">
                @foreach ($fakultas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                @endforeach
            </select>
            @error('fakultas_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
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
        var periode_mulai = $(this).data('periode_mulai');
        var periode_selesai = $(this).data('periode_selesai');
        var fakultas_id = $(this).data('fakultas_id');
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
        // var izinAksesJson = $(this).data('izin_akses');
        RubahProvinsi(prov_id);
        RubahKota(kab_id);

        // 2. Isi data Role ke dalam form modal
        $('#edit-id').val(id);
        $('#edit-periode_mulai').val(periode_mulai);
        $('#edit-periode_selesai').val(periode_selesai);
        $('#edit-fakultas_id').val(fakultas_id);
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
        $('#editForm').attr('action', '/sekprodi/' + id);

    });
    $('.btn-delete').on('click', function() {
        var id = $(this).data('id');
        var nama = $(this).data('nama');

        // Isi data ke dalam form modal
        $('#delete-id').val(id);
        $('#delete-role-name').text(nama);

        // Atur action form
        // Ganti '/role/' dengan URL route Anda yang benar, misal '/roles' atau sejenisnya
        $('#deleteForm').attr('action', '/sekprodi/' + id);
    });
});
  document.addEventListener('DOMContentLoaded', function() {
    const typeSelector = document.getElementById('user_selection_type');
    const existingBlock = document.getElementById('existing-user-block');
    const newUserBlock = document.getElementById('new-user-block');
    const existingUserId = document.getElementById('user_id');
    const exsPeriodeMulai = document.getElementById('exs_periode_mulai');
    const exsPeriodeSelesai = document.getElementById('exs_periode_selesai');
    const exsFakultas = document.getElementById('exs_fakultas_id');

    const newUserFields = {
        periode_mulai: document.getElementById('periode_mulai'),
        periode_selesai: document.getElementById('periode_selesai'),
        fakultas_id: document.getElementById('fakultas_id'),
        user_name: document.getElementById('user_name'),
        user_email: document.getElementById('user_email'),
        user_password: document.getElementById('user_password'),
        jenis_kelamin: document.getElementById('jenis_kelamin'),
        agama: document.getElementById('agama'),
        tempat_lahir: document.getElementById('tempat_lahir'),
        tanggal_lahir: document.getElementById('tanggal_lahir'),
        alamat: document.getElementById('alamat'),
        kelurahan: document.getElementById('kelurahan'),
        kec_id: document.getElementById('kec_id'), // Tambahkan ini
        kab_id: document.getElementById('kab_id'), // Tambahkan ini
        prov_id: document.getElementById('prov_id'), // Tambahkan ini
    };

    function toggleUserInputs() {
          if (typeSelector.value === 'new') {
              // Skenario 1: Buat User Baru
              existingBlock.classList.add('d-none');
              newUserBlock.classList.remove('d-none');

              // Menonaktifkan user_id agar tidak terkirim
              existingUserId.value = "";
              existingUserId.setAttribute('disabled', 'disabled');

              // Mengaktifkan field User Baru agar terkirim
              Object.values(newUserFields).forEach(field => field.removeAttribute('disabled'));

          } else {
              // Skenario 2: Pilih User Existing
              existingBlock.classList.remove('d-none');
              newUserBlock.classList.add('d-none');

              // Mengaktifkan user_id
              existingUserId.removeAttribute('disabled');
              exsPeriodeMulai.removeAttribute('disabled');
              exsPeriodeSelesai.removeAttribute('disabled');
              exsFakultas.removeAttribute('disabled');

              // Menonaktifkan field User Baru dan membersihkan nilainya
              Object.values(newUserFields).forEach(field => {
                  field.setAttribute('disabled', 'disabled');
                  field.value = '';
              });
          }
      }

      const hasNewUserError = newUserFields.name && newUserFields.name.classList.contains('is-invalid');

      if (hasNewUserError) {
          // Jika ada error pada input 'new user', set selector ke 'new'
          typeSelector.value = 'new';
          // Tampilkan Modal (penting agar user bisa melihat errornya)
          var dekanModal = new bootstrap.Modal(document.getElementById('dekanFormModal'));
          dekanModal.show();
      }

      // 2. Terapkan toggle saat halaman dimuat atau setelah validasi gagal
      toggleUserInputs();

      // 3. Terapkan toggle saat pilihan berubah
      typeSelector.addEventListener('change', toggleUserInputs);

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