@extends('layout')
@section('title', 'Permohonan Pindah Jadwal')

@section('content')
    <div class="col-lg-10 col-md-9 content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>🔄 Permohonan Pindah Jadwal Mengajar</span>
                <form action="/pindah" method="GET" class="d-flex gap-2 align-items-center">

                <select name="status" class="form-select form-select-sm">
                    <option value="PINDAH" {{ request('status') == 'PINDAH' ? 'selected' : '' }}>Pindah</option>
                    <option value="BARTER" {{ request('status') == 'BARTER' ? 'selected' : '' }}>Barter</option>
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
                                <th>Jadwal Sebelumnya</th>
                                <th>Jadwal yang Diminta</th>
                                <th>Metode</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pindah_jadwal as $index => $kls)
                                <tr>
                                    <td>{{ $pindah_jadwal->firstItem() + $index }}</td>
                                    </td>
                                    <td>
                                      <div style="line-height: .6;">
                                        <p>{{ $kls->jadwal_asal->pengampu_mk->matakuliah->nama }} ({{ $kls->jadwal_asal->pengampu_mk->surat_tugas->dosen->user->biodata->nama }})</p>
                                        <p style="font-size: .9em; color: gray;">
                                          {{ $kls->jadwal_asal->ruangan->nama }} {{ $kls->jadwal_asal->hari }}
                                          ({{ $kls->jadwal_asal->shift->jam_mulai }} -
                                          {{ $kls->jadwal_asal->shift->jam_selesai }})
                                        </p>
                                      </div>
                                    </td>
                                    @if ($kls->status_jadwal == "PINDAH")
                                      <td>{{ $kls->ruangan->nama }} {{ $kls->hari }}
                                          ({{ $kls->shift->jam_mulai }} -
                                          {{ $kls->shift->jam_selesai }})</td>
                                    @else
                                      <td>
                                      <div style="line-height: .6;">
                                        <p>{{ $kls->jadwal_tujuan->pengampu_mk->matakuliah->nama }} ({{ $kls->jadwal_tujuan->pengampu_mk->surat_tugas->dosen->user->biodata->nama }})</p>
                                        <p style="font-size: .9em; color: gray;">
                                          {{ $kls->jadwal_tujuan->ruangan->nama }} {{ $kls->jadwal_tujuan->hari }}
                                          ({{ $kls->jadwal_tujuan->shift->jam_mulai }} -
                                          {{ $kls->jadwal_tujuan->shift->jam_selesai }})
                                        </p>
                                      </div>
                                    </td>
                                    @endif
                                    <td>
                                        @if ($kls->status_jadwal === 'PINDAH')
                                            <span class="badge bg-success">{{ ucfirst(strtolower($kls->status_jadwal)) }}</span>
                                        @else
                                            <span class="badge bg-info">{{ ucfirst(strtolower($kls->status_jadwal)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($kls->status === 'APPROVED')
                                            <span class="badge bg-success">{{ ucfirst(strtolower($kls->status)) }}</span>
                                        @elseif($kls->status === 'REJECTED')
                                            <span class="badge bg-danger">{{ ucfirst(strtolower($kls->status)) }}</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst(strtolower($kls->status)) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Tombol Edit: Memicu modal dan mengirim data role ke fungsi JS/data attributes --}}

                                        @if ($kls->status == 'ANTRI' && Auth::user()->mahasiswa?->kosma)
                                            <button type="button" class="btn btn-outline-success btn-sm btn-action"
                                                data-bs-toggle="modal" data-bs-target="#actionModal"
                                                data-id="{{ $kls->id }}"
                                                data-ruangan_id="{{ $kls->ruangan_id }}"
                                                data-shift_id="{{ $kls->shift_id }}" data-hari="{{ $kls->hari }}"
                                                data-action="approve" title="Setujui">
                                                <i class="bi bi-check-circle"></i>
                                            </button>

                                            <button type="button" class="btn btn-outline-danger btn-sm btn-action"
                                                data-bs-toggle="modal" data-bs-target="#actionModal"
                                                data-id="{{ $kls->id }}"
                                                data-ruangan_id="{{ $kls->ruangan_id }}"
                                                data-shift_id="{{ $kls->shift_id }}" data-hari="{{ $kls->hari }}"
                                                data-action="reject" title="Tolak">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @else
                                            {{-- Jika status sudah APPROVED/REJECTED, tampilkan tombol edit/delete lama (Opsional) --}}
                                            <button type="button" class="btn btn-outline-secondary btn-sm" disabled>
                                                <i class="bi bi-slash-circle" title="Sudah Diproses"></i>
                                            </button>
                                        @endif

                                    </td>
                                    {{-- ... akhir loop ... --}}
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            {{-- Form akan diisi action URL-nya oleh JavaScript --}}
            <form class="modal-content" method="POST" id="actionForm" action="/pindah">
                @csrf
                {{-- Kita akan menggunakan method PUT/PATCH untuk update status --}}
                @method('PUT')
                <div class="modal-header text-white" id="actionModalHeader">
                    <h5 class="modal-title" id="actionModalLabel">Konfirmasi Aksi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin <strong id="actionVerb">memproses</strong> surat tugas ini?

                    {{-- Input tersembunyi untuk menyimpan status baru --}}
                    <input type="hidden" name="status" id="actionStatusInput">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="hari" id="hari">
                    <input type="hidden" name="shift_id" id="shift_id">
                    <input type="hidden" name="ruangan_id" id="ruangan_id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" id="actionConfirmButton">Ya, Proses</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- LOGIKA APPROVE/REJECT ---
            const actionButtons = document.querySelectorAll('.btn-action');
            const actionModal = document.getElementById('actionModal');
            const actionForm = document.getElementById('actionForm');
            const actionModalLabel = document.getElementById('actionModalLabel');
            const actionModalHeader = document.getElementById('actionModalHeader');
            const actionVerb = document.getElementById('actionVerb');
            const actionStatusInput = document.getElementById('actionStatusInput');
            const inhari = document.getElementById('hari');
            const inid = document.getElementById('id');
            const inruangan_id = document.getElementById('ruangan_id');
            const inshift_id = document.getElementById('shift_id');
            const actionConfirmButton = document.getElementById('actionConfirmButton');

            actionButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const suratId = this.getAttribute('data-id');
                    const hari = this.getAttribute('data-hari');
                    const shift_id = this.getAttribute('data-shift_id');
                    const ruangan_id = this.getAttribute('data-ruangan_id');
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

                    // 1. Update Konten Modal
                    actionModalLabel.textContent = `Konfirmasi ${verb.toUpperCase()}`;
                    actionVerb.textContent = verb;

                    // 2. Update Style Modal
                    actionModalHeader.className = `modal-header text-white ${headerClass}`;
                    actionConfirmButton.className = `btn ${headerClass}`;
                    actionConfirmButton.textContent = buttonText;

                    // 3. Set nilai input tersembunyi
                    actionStatusInput.value = statusValue;
                    inhari.value = hari;
                    inid.value = suratId;
                    inruangan_id.value = ruangan_id;
                    inshift_id.value = shift_id;

                    // Perbaikan: gunakan ID yang baru diambil

                    // 4. Set Action URL Form (Ganti dengan route yang sudah diperbaiki)
                    actionForm.action = `/pindah/${suratId}`;
                });
            });
        });
    </script>
@endsection