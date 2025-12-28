@extends('layout')
@section('title', 'Jadwal')

@section('content')

<div class="col-md-9 col-lg-10 content">
  <h4 class="text-center mb-2 fw-bold">UNIVERSITAS MA'SOEM FAKULTAS 
    <select  id="fakultasSelect">
      @foreach ($fakultas as $f)
        <option value="{{ $f->id }}" {{ request('fakultas_id') == $f->id ? 'selected' : '' }}>{{ strtoupper($f->nama) }}</option>
      @endforeach
    </select>
  </h4>
  <h5 class="text-center mb-2 fw-bold">JADWAL MATA KULIAH @foreach ($selectedFakultas->prodi as $p)
    {{ $p->kode }} {{ $p->jenjang }}
  @endforeach </h5>
  <h5 class="text-center text-secondary mb-4">SEMESTER {{ $selectedSemester->semester }} {{ strtoupper($selectedSemester->jenis) }} {{ $selectedSemester->tahun_akademik }}</h5>

  <div class="d-flex justify-content-center mb-3 gap-2">
    @foreach ($kelas->unique(fn($kls) => $kls->tipe.'-'.$kls->semester_id) as $kls)
        <a href="{{ route('jadwal', [
            'fakultas_id' => request('fakultas_id'),
            'kelas'       => $kls->tipe,
            'semester_id' => $kls->semester_id
        ]) }}"
          class="btn btn-sm
          {{ request('kelas') == $kls->tipe && request('semester_id') == $kls->semester_id
                ? 'btn-primary'
                : 'btn-info' }}">
          
            {{ $kls->tipe }}{{ $kls->semester->semester }}
        </a>
    @endforeach
    <a href="/" target="_blank" class="btn btn-sm btn-success">
        <i class="bi bi-file-pdf"></i> DOWNLOAD JADWAL
    </a>
  </div>

  <div class="card-body">
    @foreach ($hari as $h)
      <div class="fw-bold">
          {{ strtoupper($h) }}
      </div>
      <table class="table table-bordered table-striped align-middle text-sm">
        <thead class="table-light">
          <tr>
            <th>WAKTU</th>
              @foreach ($selectedFakultas->prodi as $s)
                <th>{{ $s->kode }} {{ $s->jenjang }}</th>
              @endforeach
        </thead>
        <tbody>
          @foreach ($shift as $s)
            @if ($s->tipe == "ISOMA")
            <tr style="font-style:italic;">
              <td class="bg-info text-white">{{ $s->jam_mulai }} - {{ $s->jam_selesai }}</td>
              @foreach ($selectedFakultas->prodi as $k)
                <td class="bg-info text-white text-center">Istirahat, Sholat & Makan</td>
              @endforeach
            </tr>
            @else
            <tr>
              <td>{{ $s->jam_mulai }} - {{ $s->jam_selesai }}</td>
              @foreach ($selectedFakultas->prodi as $k)
                @php
                    $jadwalProdi = $s->jadwal->filter(fn ($j) =>
                        $j->pengampu_mk?->kelas?->contains('prodi_id', $k->id) && $j->hari == $h && $j->shift_id == $s->id
                    )->first();
                @endphp
                @if ($jadwalProdi)
                  <td>
                    <div >
                      <div class="fw-bold">{{ $jadwalProdi->pengampu_mk->matakuliah->nama }}</div>
                      <div class="opacity-80" style="font-style:italic;">Dosen : {{ $jadwalProdi->pengampu_mk->surat_tugas->dosen->user->biodata->nama }}</div>
                      <div class="opacity-80" style="font-style:italic;">Ruang : {{ $jadwalProdi->ruangan->nama }}</div>
                    </div>
                  </td>
                @else
                <td>
                  @foreach ($pengampu as $pg)
                    <div class="text-xs opacity-50">
                      {{ $pg->matakuliah->nama }}
                        <button class="btn btn-sm btn-success btn-charter py-0 px-1"
                        data-bs-toggle="modal" data-bs-target="#addJadwalModal"
                        data-hari="{{ $h }}"
                        data-shift_id="{{ $s->id }}" 
                        data-jam="{{ $s->jam_mulai }} - {{ $s->jam_selesai }}" 
                        data-matakuliah="{{ $pg->matakuliah->nama }}" 
                        data-pengampu_mk_id="{{ $pg->id }}" 
                        >
                        Charter
                      </button>
                    </div>
                    @endforeach
                </td>
                @endif
              @endforeach
            </tr>
            @endif
          @endforeach
        </tbody>
      </table>
    @endforeach
  </div>
</div>

<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" id="addJadwal">
            @csrf
            @method('POST')
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addJadwalModalLabel">Charter Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
              <div class="col-md-4">
                    <label class="form-label">Matakuliah</label>
                    <input type="text" disabled id="matakuliah" class="form-control form-control-sm">
                    <input type="hidden" name="pengampu_mk_id" id="pengampu_mk_id" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ruangan</label>
                    <select class="form-select form-select-sm" name="ruangan_id">
                        @foreach ($ruangan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <!-- Preferensi Dosen akan muncul di sini -->
                <div class="col-md-12 text-xs mt-2" id="preferensiDosen" style="display:none;">
                    <div class="text-danger" id="preferensiText" style="font-size: 0.85rem;">
                      {{ Auth::user()->dosen->referensi }}
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Hari</label>
                    <input type="text" disabled id="hari_show" class="form-control form-control-sm">
                    <input type="hidden" name="hari" id="hari" class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jam</label>
                    <input type="hidden" name="shift_id" id="shift_id"  class="form-control form-control-sm">
                    <input type="text" id="jam" disabled class="form-control form-control-sm">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
  const fakultas = document.getElementById("fakultasSelect").value;

  function loadJadwal(fakultas) {
    window.location.href = `/jadwal?fakultas_id=${fakultas}`;
  }
  document.getElementById('fakultasSelect').addEventListener('change', function () {
    const fakultas = this.value;

    loadJadwal(fakultas);
  });
  $(document).ready(function() {
      // Tangkap saat tombol edit diklik
      $('.btn-charter').on('click', function() {
          var hari = $(this).data('hari');
          var pengampu_mk_id = $(this).data('pengampu_mk_id');
          var matakuliah = $(this).data('matakuliah');
          var shift_id = $(this).data('shift_id');
          var jam = $(this).data('jam');

          $('#hari').val(hari);
          $('#hari_show').val(hari);
          $('#pengampu_mk_id').val(pengampu_mk_id);
          $('#matakuliah').val(matakuliah);
          $('#shift_id').val(shift_id);
          $('#jam').val(jam);

          $('#addJadwal').attr('action', '/jadwal');


      });
      $('.btn-delete').on('click', function() {
          var id = $(this).data('id');
          var nama = $(this).data('nama');

          $('#delete-id').val(id);
          $('#delete-name').text(nama);

          $('#deleteForm').attr('action', '/ruangan/' + id);
      });
  });
</script>
@endsection