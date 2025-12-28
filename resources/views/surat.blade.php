@extends('layout')
@section('title', 'Surat Tugas')

@section('content')
<div class="col-lg-10 col-md-9 content">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span>📄 Surat Tugas Mengajar</span>
      <button class="btn btn-light btn-sm text-primary fw-semibold" data-bs-toggle="modal"
          data-bs-target="#addModal">
        <i class="bi bi-plus-circle"></i> Tambah
      </button>
      <form action="/surat" method="GET" class="d-flex gap-2 align-items-center">
          <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari ..."
              value="{{ request('search') }}">

          <select name="user_id" class="form-select form-select-sm">
            <option value="">by dosen ...</option>
              @foreach ($dosen as $r)
                <option value="{{ $r->id }}" {{ request('dosen_id') == $r->id ? 'selected' : '' }}>{{ $r->user->biodata->nama }}</option>
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
                    <span>{{ $kls->dekan->user->biodata->nama }}</span>
                    <span class="italic opacity-70" style="font-style:italic;color:#888">{{ "@" }}dekan</span>
                  </div>
                </td>
                <td > 
                  <div class="flex flex-col">
                    <span>{{ $kls->dosen->user->biodata->nama }}</span>
                    <span class="italic opacity-70" style="font-style:italic;color:#888">{{ "@" }}dosen</span>
                  </div>
                </td>
                <td >
                  <div class="flex flex-col">
                    <span>SK: {{ $kls->nomor_sk }}</span>
                    <span class="italic opacity-70" style="font-style:italic;color:#888">Surat: {{ $kls->nomor_surat }}</span>
                  </div>
                </td>
                <td >
                  <div class="flex flex-col">
                    <span>{{ $kls->tanggal->format('d/m/Y') }}</span>
                    <span class="italic opacity-70" style="font-style:italic;color:#888">Semester {{ $kls->semester->semester }} {{ $kls->semester->jenis }} {{ $kls->semester->tahun_akademik }}</span>
                  </div>
                </td>
                <td>
                  <div class="flex gap-2 items-center justify-center">
                    <button type="button" class="btn btn-sm btn-info btn-cetak-pdf"
                        title="Lihat PDF" data-surat-id="{{ $kls->id }}">
                       <i class="bi bi-printer-fill"></i></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-info btn-view-pdf"
                        title="Lihat PDF" data-surat-id="{{ $kls->id }}">
                        <i class="bi bi-file-earmark"></i>
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
                      data-nomor_surat="{{ $kls->nomor_surat }}"
                      data-tanggal="{{ $kls->tanggal->format('Y-m-d') }}"
                      data-semester_id="{{ $kls->semester_id }}"
                      data-dosen_id="{{ $kls->dosen_id }}"
                      data-file="{{ $kls->file }}"
                      data-pengampu_mk='@json($kls->pengampu_mk)'
                      > 
                      <i class="bi bi-pencil"></i>
                  </button>
                  <button type="button" class="btn btn-outline-danger btn-sm btn-delete"
                      data-bs-toggle="modal" data-bs-target="#deleteModal"
                      data-id="{{ $kls->id }}" data-nama="{{ $kls->nomor_surat }}">
                      <i class="bi bi-trash"></i>
                  </button>
                  <button type="button" class="btn btn-outline-success btn-sm btn-action"
                      data-bs-toggle="modal" data-bs-target="#actionModal"
                      data-id="{{ $kls->id }}" data-kelas-id="{{ $kls->kelas_id }}"
                      data-dosen-id="{{ $kls->dosen_id }}"
                      data-matakuliah-id="{{ $kls->matakuliah_id }}" data-action="approve"
                      title="Setujui Tugas">
                      <i class="bi bi-check-circle"></i>
                  </button>

                  <button type="button" class="btn btn-outline-danger btn-sm btn-action"
                      data-bs-toggle="modal" data-bs-target="#actionModal"
                      data-id="{{ $kls->id }}" data-kelas-id="{{ $kls->kelas_id }}"
                      data-dosen-id="{{ $kls->dosen_id }}"
                      data-matakuliah-id="{{ $kls->matakuliah_id }}" data-action="reject"
                      title="Tolak Tugas">
                      <i class="bi bi-x-circle"></i>
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
    <form class="modal-content" action="/surat" method="POST" enctype="multipart/form-data">
      @csrf
      @method('POST')
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addRuanganModalLabel">Tambah Surat Mengajar</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-1">
        <div class="col-md-4">
          <label class="form-label">Tanggal</label>
          <input type="date" class="form-control form-control-sm" name="tanggal">
        </div>
        <div class="col-md-4">
          <label class="form-label">Nomor SK</label>
          <input type="text" class="form-control form-control-sm" name="nomor_sk">
        </div>
        <div class="col-md-4">
          <label class="form-label">Nomor Surat</label>
          <input type="text" class="form-control form-control-sm" name="nomor_surat">
        </div>
        <div class="col-md-6">
          <label class="form-label">Dosen</label>
          <select name="dosen_id" class="form-select form-select-sm">
              @foreach ($dosen as $item)
                <option value="{{ $item->id }}">{{ $item->user->biodata->nama }}</option>
              @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Semester</label>
          <select name="semester_id" class="form-select form-select-sm">
              @foreach ($semester as $item)
                <option value="{{ $item->id }}">{{ $item->nama }} {{ $item->jenis }} {{ $item->tahun_akademik }}</option>
              @endforeach
          </select>
        </div>
        <div class="mt-2 col-md-12" id="pengampu-row">
          <div class="modal-body row g-1 pengampu-item border rounded">
            <div class="col-md-3">
              <label class="form-label">Matakuliah</label>
              <select name="pengampu[0][matakuliah_id]" class="form-select form-select-sm">
                  @foreach ($matakuliah as $item)
                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                  @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Kelas</label>
              <div class="border rounded p-2" style="height: 100px; overflow-y: auto; background-color: #fff;">
                  @foreach ($kelas as $k)
                      <div class="form-check">
                          <input 
                              class="form-check-input" 
                              type="checkbox" 
                              name="pengampu[0][kelas][]" 
                              value="{{ $k->id }}" 
                              id="kelas_{{ $k->id }}"
                          >
                          <label class="form-check-label" for="kelas_{{ $k->id }}">
                              {{ $k->nama }}
                          </label>
                      </div>
                  @endforeach
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">SKS</label>
              <input type="number" class="form-control form-control-sm" name="pengampu[0][sks]">
            </div>
          </div>
        </div>
        <button type="button" id="add-pengampu" class="btn btn-success btn-sm">+ Tambah Matakuliah</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form class="modal-content" action="/surat" method="POST" enctype="multipart/form-data" id="editForm">
      @csrf
      @method('PUT')
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addRuanganModalLabel">Edit Surat Mengajar</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body row g-1">
        <input type="hidden" name="id" id="edit-id">
        <div class="col-md-4">
          <label class="form-label">Tanggal</label>
          <input type="date" class="form-control form-control-sm" name="tanggal" id="edit-tanggal">
        </div>
        <div class="col-md-4">
          <label class="form-label">Nomor SK</label>
          <input type="text" class="form-control form-control-sm" name="nomor_sk" id="edit-nomor_sk">
        </div>
        <div class="col-md-4">
          <label class="form-label">Nomor Surat</label>
          <input type="text" class="form-control form-control-sm" name="nomor_surat" id="edit-nomor_surat">
        </div>
        <div class="col-md-6">
          <label class="form-label">Dosen</label>
          <select name="dosen_id" class="form-select form-select-sm" id="edit-dosen_id">
              @foreach ($dosen as $item)
                <option value="{{ $item->id }}">{{ $item->user->biodata->nama }}</option>
              @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Semester</label>
          <select name="semester_id" class="form-select form-select-sm" id="edit-semester_id">
              @foreach ($semester as $item)
                <option value="{{ $item->id }}">{{ $item->nama }} {{ $item->jenis }} {{ $item->tahun_akademik }}</option>
              @endforeach
          </select>
        </div>
        <div class="col-md-6">
          <input type="file" name="file" class="form-control form-control-sm" id="edit-file"/>
        </div>
        <div class="mt-2 col-md-12" id="pengampu-row-edit">
        </div>
        <button type="button" id="add-pengampu-edit" class="btn btn-success btn-sm">+ Tambah Matakuliah</button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
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

<div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="pdfModalLabel">Preview PDF</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <iframe id="pdfIframe" style="width: 100%; height: 500px;" frameborder="0"></iframe>
          </div>
      </div>
  </div>
</div>

<div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
    <div class="modal-dialog"> {{-- Form akan diisi action URL-nya oleh JavaScript --}} <form class="modal-content" method="POST" id="actionForm"> @csrf
            {{-- Kita akan menggunakan method PUT/PATCH untuk update status --}} @method('PUT') <div class="modal-header text-white" id="actionModalHeader">
                <h5 class="modal-title" id="actionModalLabel">Konfirmasi Aksi</h5> <button type="button"
                    class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body"> Apakah Anda yakin ingin <strong id="actionVerb">memproses</strong> surat tugas ini?
                {{-- Input tersembunyi untuk menyimpan status baru --}} <input type="hidden" name="status" id="actionStatusInput"> <input
                    type="hidden" name="kelas_id" id="valueKelasId"> <input type="hidden" name="dosen_id"
                    id="valueDosenId"> <input type="hidden" name="matakuliah_id" id="valueMatakuliahId"> </div>
            <div class="modal-footer"> <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Batal</button> <button type="submit" class="btn"
                    id="actionConfirmButton">Ya, Proses</button> </div>
        </form>
    </div>
</div>

<script>
  const availableMatakuliah = @json($matakuliah);
  const availableKelas = @json($kelas);
  let upIndex = 0;
  $(document).ready(function() {
      const pdfButtons = document.querySelectorAll('.btn-cetak-pdf');
      const pdfView = document.querySelectorAll('.btn-view-pdf');
      const pdfIframe = document.getElementById('pdfIframe');
      const pdfModal = new bootstrap.Modal(document.getElementById('pdfModal'));

      pdfButtons.forEach(button => {
          button.addEventListener('click', function() {
              const suratId = this.getAttribute('data-surat-id');
              const pdfUrl = "{{ route('laporan.pdf.generate') }}" +
                  "?id=" + suratId + "&t=" + new Date().getTime();

              pdfIframe.src = pdfUrl;
              pdfModal.show();
          });
      });
      pdfView.forEach(button => {
          button.addEventListener('click', function() {
              const suratId = this.getAttribute('data-surat-id');
              const pdfUrl = "{{ route('laporan.pdf.show') }}" +
                  "?id=" + suratId + "&t=" + new Date().getTime();

              pdfIframe.src = pdfUrl;
              pdfModal.show();
          });
      });

      document.getElementById('pdfModal').addEventListener('hidden.bs.modal', function() {
          pdfIframe.src = '';
      });


      // Tangkap saat tombol edit diklik
      $('.btn-edit').on('click', function() {
          // 1. Ambil data dari data-attributes
          var id = $(this).data('id');
          var tanggal = $(this).data('tanggal');
          var nomor_surat = $(this).data('nomor_surat');
          var nomor_sk = $(this).data('nomor_sk');
          var semester_id = $(this).data('semester_id');
          var pengampu = $(this).data('pengampu_mk');

          $('#edit-id').val(id);
          $('#edit-tanggal').val(tanggal);
          $('#edit-nomor_surat').val(nomor_surat);
          $('#edit-nomor_sk').val(nomor_sk);
          $('#edit-semester_id').val(semester_id);
          
          upIndex = pengampu.length;
          if (typeof pengampu === 'string') {
              pengampu = JSON.parse(pengampu);
          }

          let container = document.getElementById('pengampu-row-edit');
          let listPengampu = pengampu.map((p,i) => `
            <div class="modal-body row g-1 pengampu-item border rounded" data-index="${i+1}">
              <div class="col-md-3">
                <label class="form-label">Matakuliah</label>
                <select name="pengampu[${i+1}][matakuliah_id]" class="form-select form-select-sm">
                  ${availableMatakuliah.map(m => `<option value="${ m.id }" ${m.id == p.matakuliah_id ? 'selected' : ''}>${ m.nama }</option>`).join("")}
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Kelas</label>
                <div class="border rounded p-2" style="height: 100px; overflow-y: auto; background-color: #fff;">
                  ${availableKelas.map(m => `
                    <div class="form-check">
                      <input  
                          class="form-check-input" 
                          type="checkbox" 
                          name="pengampu[${i+1}][kelas][]" 
                          value="${m.id}" 
                          id="kelas_${m.id}"
                          ${p.kelas.some(k => k.id == m.id) ? 'checked' : ''}
                      >
                      <label class="form-check-label" for="kelas_${m.id}">
                          ${m.nama}
                      </label>
                    </div>
                  `).join("")}
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label">SKS</label>
                <input type="number" class="form-control form-control-sm" name="pengampu[${i+1}][sks]" value="${p.sks}">
              </div>
              <div class="col-md-2">
                  <button type="button" class="btn btn-danger btn-remove btn-sm">Hapus</button>
              </div>
            </div>
          `).join("");

          container.insertAdjacentHTML('beforeend', listPengampu);

          $('#editForm').attr('action', '/surat/' + id);


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var nama = $(this).data('nama');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/surat/' + id);
      });

      const actionButtons = document.querySelectorAll('.btn-action');
      const actionForm = document.getElementById('actionForm');
      const actionModalLabel = document.getElementById('actionModalLabel');
      const actionModalHeader = document.getElementById('actionModalHeader');
      const actionVerb = document.getElementById('actionVerb');
      const actionStatusInput = document.getElementById('actionStatusInput');
      const actionConfirmButton = document.getElementById('actionConfirmButton');

      actionButtons.forEach(button => {
          button.addEventListener('click', function() {
              const suratId = this.getAttribute('data-id');
              const action = this.getAttribute('data-action');

              let verb = '';
              let statusValue = '';
              let headerClass = '';
              let buttonText = '';

              if (action === 'approve') {
                  verb = 'menyetujui';
                  statusValue = 'APPROVED';
                  headerClass = 'bg-success';
                  buttonText = 'Ya, Setujui';
              } else if (action === 'reject') {
                  verb = 'menolak';
                  statusValue = 'REJECTED';
                  headerClass = 'bg-danger';
                  buttonText = 'Ya, Tolak';
              }

              actionModalLabel.textContent = `Konfirmasi ${verb.toUpperCase()}`;
              actionVerb.textContent = verb;
              actionModalHeader.className = `modal-header text-white ${headerClass}`;
              actionConfirmButton.className = `btn ${headerClass}`;
              actionConfirmButton.textContent = buttonText;
              actionStatusInput.value = statusValue;
              actionForm.action = `/surat/${suratId}`;
          });
      });
  });
  
  let roleIndex = 1;
    document.getElementById('add-pengampu').addEventListener('click', function() {
        let container = document.getElementById('pengampu-row');
        let html = `
          <div class="modal-body row g-1 pengampu-item border rounded" data-index="${roleIndex}">
            <div class="col-md-3">
              <label class="form-label">Matakuliah</label>
              <select name="pengampu[${roleIndex}][matakuliah_id]" class="form-select form-select-sm">
                ${availableMatakuliah.map(m => `<option value="${ m.id }">${ m.nama }</option>`).join("")}
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Kelas</label>
              <div class="border rounded p-2" style="height: 100px; overflow-y: auto; background-color: #fff;">
                ${availableKelas.map(m => `
                  <div class="form-check">
                    <input  
                        class="form-check-input" 
                        type="checkbox" 
                        name="pengampu[${roleIndex}][kelas][]" 
                        value="${m.id}" 
                        id="kelas_${m.id}"
                    >
                    <label class="form-check-label" for="kelas_${m.id}">
                        ${m.nama}
                    </label>
                  </div>
                `).join("")}
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">SKS</label>
              <input type="number" class="form-control form-control-sm" name="pengampu[${roleIndex}][sks]">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-remove btn-sm">Hapus</button>
            </div>
          </div>
            `;
        container.insertAdjacentHTML('beforeend', html);
        roleIndex++;
    });

    document.getElementById('add-pengampu-edit').addEventListener('click', function() {
      console.log("clicked")
        let container = document.getElementById('pengampu-row-edit');
        let html = `
          <div class="modal-body row g-1 pengampu-item border rounded" data-index="${upIndex}">
            <div class="col-md-3">
              <label class="form-label">Matakuliah</label>
              <select name="pengampu[${upIndex}][matakuliah_id]" class="form-select form-select-sm">
                ${availableMatakuliah.map(m => `<option value="${ m.id }">${ m.nama }</option>`).join("")}
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Kelas</label>
              <div class="border rounded p-2" style="height: 100px; overflow-y: auto; background-color: #fff;">
                ${availableKelas.map(m => `
                  <div class="form-check">
                    <input  
                        class="form-check-input" 
                        type="checkbox" 
                        name="pengampu[${upIndex}][kelas][]" 
                        value="${m.id}" 
                        id="kelas_${m.id}"
                    >
                    <label class="form-check-label" for="kelas_${m.id}">
                        ${m.nama}
                    </label>
                  </div>
                `).join("")}
              </div>
            </div>
            <div class="col-md-3">
              <label class="form-label">SKS</label>
              <input type="number" class="form-control form-control-sm" name="pengampu[${upIndex}][sks]">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-remove btn-sm">Hapus</button>
            </div>
          </div>
            `;
        container.insertAdjacentHTML('beforeend', html);
        upIndex++;
    });

    // Delegasi event untuk tombol hapus
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('btn-remove')) {
            e.target.closest('.pengampu-item').remove();
        }
    });
</script>
@endsection