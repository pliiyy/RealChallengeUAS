@extends('layout')
@section('title', 'Shift')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>🕒 Data Shift Mengajar</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah Shift
      </button>
      <form action="/shift" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama ruangan"
              value="{{ request('search') }}">

          <select name="tipe" class="form-select form-select-sm">
              <option value="KELAS" {{ request('tipe') == 'KELAS' ? 'selected' : '' }}>Aktif</option>
              <option value="ISOMA" {{ request('tipe') == 'ISOMA' ? 'selected' : '' }}>Nonaktif</option>
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
              <th>Nama Shift</th>
              <th>Waktu Shift</th>
              <th>Tipe</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($shift as $index => $kls)
              <tr>
                <td>{{ $shift->firstItem() + $index }}</td>
                <td>{{ $kls->nama }}</td>
                <td>
                  <p>{{ $kls->jam_mulai }}</p>
                  <p class="border-top opacity-80">{{ $kls->jam_selesai }}</p>
                </td>
                <td>
                    @if ($kls->tipe == 'KELAS')
                        <span class="badge bg-warning">{{ ucfirst(strtolower($kls->tipe)) }}</span>
                    @else
                        <span class="badge bg-secondary">{{ ucfirst(strtolower($kls->tipe)) }}</span>
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
                      data-jam_mulai="{{ $kls->jam_mulai }}"
                      data-jam_selesai="{{ $kls->jam_selesai }}"
                      data-tipe="{{ $kls->tipe }}"> 
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
            {{ $shift->links() }}
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" action="/shift" method="POST">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="addRuanganModalLabel">Tambah Shift Baru</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label">Nama Shift</label>
            <select class="form-select" name="nama" id="add-nama" required>
                <option value="">-- Pilih Shift --</option>
                <option value="PAGI">Pagi</option>
                <option value="SIANG">Siang</option>
                <option value="MALAM">Malam</option>
            </select>
            <small class="text-muted">Pilih waktu shift mengajar</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Jam Mulai</label>
          <input type="time" class="form-control" name="jam_mulai" id="add-jam_mulai" required />
          <div class="invalid-feedback" id="add-jam_mulai-error"></div>
        </div>
        <div class="mb-3">
          <label class="form-label">Jam Selesai</label>
          <input type="time" class="form-control" name="jam_selesai" id="add-jam_selesai" required />
          <div class="invalid-feedback" id="add-jam_selesai-error"></div>
        </div>
        <!-- Info Aturan Shift -->
        <div class="alert alert-warning alert-sm" id="add-shift-info" style="display:none;">
          <small>
            <i class="bi bi-exclamation-triangle me-1"></i>
            <span id="add-shift-info-text"></span>
          </small>
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
        <h5 class="modal-title" id="editModalLabel">Edit Ruangan: <span id="edit-name"></span></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="edit-id">
        <div class="mb-3">
            <label for="edit-nama" class="form-label">Nama Shift</label>
            <select class="form-select" id="edit-nama" name="nama" required>
              <option value="">-- Pilih Shift --</option>
              <option value="PAGI">Pagi</option>
              <option value="SIANG">Siang</option>
              <option value="MALAM">Malam</option>
            </select>
            <small class="text-muted">Pilih waktu shift mengajar</small>
        </div>
        <div class="mb-3">
            <label for="edit-jam_mulai" class="form-label">Jam Mulai</label>
            <input class="form-control" id="edit-jam_mulai" name="jam_mulai" type="time" required />
            <div class="invalid-feedback" id="edit-jam_mulai-error"></div>
        </div>
        <div class="mb-3">
            <label for="edit-jam_selesai" class="form-label">Jam Selesai</label>
            <input class="form-control" id="edit-jam_selesai" name="jam_selesai" type="time" required />
            <div class="invalid-feedback" id="edit-jam_selesai-error"></div>
        </div>

        <!-- Info Aturan Shift -->
        <div class="alert alert-warning alert-sm" id="edit-shift-info" style="display:none;">
            <small><i class="bi bi-exclamation-triangle me-1"></i><span
                    id="edit-shift-info-text"></span></small>
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
    const shiftRules = {
        'pagi': {
            minTime: '07:00',
            maxTime: '12:00',
            label: 'Shift Pagi (07:00 - 12:00)'
        },
        'siang': {
            minTime: '12:00',
            maxTime: '18:00',
            label: 'Shift Siang (12:00 - 18:00)'
        },
        'malam': {
            minTime: '18:00',
            maxTime: '23:59',
            label: 'Shift Malam (18:00 - 00:00)'
        }
    };

    // Fungsi untuk mendapatkan tipe shift dari nama
    function getShiftType(namaShift) {
        const nama = namaShift.toLowerCase();
        if (nama === 'pagi') return 'pagi';
        if (nama === 'siang') return 'siang';
        if (nama === 'malam') return 'malam';
        return null;
    }

    // Fungsi validasi waktu shift
    function validateShiftTime(namaShift, jamMulai, jamSelesai, prefix) {
        const shiftType = getShiftType(namaShift);

        // Reset error states
        $(`#${prefix}-jam_mulai`).removeClass('is-invalid');
        $(`#${prefix}-jam_selesai`).removeClass('is-invalid');
        $(`#${prefix}-shift-info`).hide();
        $(`#${prefix}-submit-btn`).prop('disabled', false);

        if (!shiftType) {
            // Jika tidak ada aturan khusus, hanya validasi jam selesai > jam mulai
            if (jamSelesai && jamMulai && jamSelesai <= jamMulai) {
                $(`#${prefix}-jam_selesai`).addClass('is-invalid');
                $(`#${prefix}-jam_selesai-error`).text('Jam selesai harus lebih besar dari jam mulai');
                $(`#${prefix}-submit-btn`).prop('disabled', true);
                return false;
            }
            return true;
        }

        const rules = shiftRules[shiftType];
        let isValid = true;
        let errorMsg = '';

        // Tampilkan info aturan shift
        $(`#${prefix}-shift-info-text`).text(`Aturan: ${rules.label}`);
        $(`#${prefix}-shift-info`).show();

        // Validasi jam mulai
        if (jamMulai) {
            if (jamMulai < rules.minTime || jamMulai > rules.maxTime) {
                $(`#${prefix}-jam_mulai`).addClass('is-invalid');
                $(`#${prefix}-jam_mulai-error`).text(
                    `Jam mulai ${shiftType} harus antara ${rules.minTime} - ${rules.maxTime}`
                );
                isValid = false;
            }
        }

        // Validasi jam selesai
        if (jamSelesai) {
            if (jamSelesai < rules.minTime || jamSelesai > rules.maxTime) {
                $(`#${prefix}-jam_selesai`).addClass('is-invalid');
                $(`#${prefix}-jam_selesai-error`).text(
                    `Jam selesai ${shiftType} harus antara ${rules.minTime} - ${rules.maxTime}`
                );
                isValid = false;
            }
        }

        // Validasi jam selesai > jam mulai
        if (jamMulai && jamSelesai && jamSelesai <= jamMulai) {
            $(`#${prefix}-jam_selesai`).addClass('is-invalid');
            $(`#${prefix}-jam_selesai-error`).text('Jam selesai harus lebih besar dari jam mulai');
            isValid = false;
        }

        $(`#${prefix}-submit-btn`).prop('disabled', !isValid);
        return isValid;
    }

    // Validasi untuk form ADD
    $('#add-nama, #add-jam_mulai, #add-jam_selesai').on('input change', function() {
        const nama = $('#add-nama').val();
        const jamMulai = $('#add-jam_mulai').val();
        const jamSelesai = $('#add-jam_selesai').val();
        validateShiftTime(nama, jamMulai, jamSelesai, 'add');
    });

    // Validasi untuk form EDIT
    $('#edit-nama, #edit-jam_mulai, #edit-jam_selesai').on('input change', function() {
        const nama = $('#edit-nama').val();
        const jamMulai = $('#edit-jam_mulai').val();
        const jamSelesai = $('#edit-jam_selesai').val();
        validateShiftTime(nama, jamMulai, jamSelesai, 'edit');
    });

    // Submit form ADD dengan validasi
    $('#addShiftForm').on('submit', function(e) {
        const nama = $('#add-nama').val();
        const jamMulai = $('#add-jam_mulai').val();
        const jamSelesai = $('#add-jam_selesai').val();

        if (!validateShiftTime(nama, jamMulai, jamSelesai, 'add')) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali waktu shift Anda!',
                confirmButtonColor: '#4f46e5'
            });
            return false;
        }
    });

    // Submit form EDIT dengan validasi
    $('#editRoleForm').on('submit', function(e) {
        const nama = $('#edit-nama').val();
        const jamMulai = $('#edit-jam_mulai').val();
        const jamSelesai = $('#edit-jam_selesai').val();

        if (!validateShiftTime(nama, jamMulai, jamSelesai, 'edit')) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: 'Mohon periksa kembali waktu shift Anda!',
                confirmButtonColor: '#4f46e5'
            });
            return false;
        }
    });

      // Tangkap saat tombol edit diklik
      $('.btn-edit').on('click', function() {
          // 1. Ambil data dari data-attributes
          var id = $(this).data('id');
          var nama = $(this).data('nama');
          var jam_mulai = $(this).data('jam_mulai');
          var jam_selesai = $(this).data('jam_selesai');
          var tipe = $(this).data('tipe');

          $('#edit-id').val(id);
          $('#edit-nama').val(nama);
          $('#edit-jam_mulai').val(jam_mulai);
          $('#edit-jam_selesai').val(jam_selesai);
          $('#edit-tipe').val(tipe);

          $('#editForm').attr('action', '/shift/' + id);


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var nama = $(this).data('nama');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/shift/' + id);
      });
  });
</script>
@endsection