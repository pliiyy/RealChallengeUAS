@extends('layout')
@section('title', 'Jadwal')

@section('content')
<div class="col-md-9 col-lg-10 content">
    <h4 class="text-center mb-2 fw-bold">
        UNIVERSITAS MA'SOEM FAKULTAS 
        <select id="fakultasSelect" >
            @foreach ($fakultas as $f)
                <option value="{{ $f->id }}" {{ request('fakultas_id') == $f->id ? 'selected' : '' }}>
                    {{ strtoupper($f->nama) }}
                </option>
            @endforeach
        </select>
    </h4>
    
    <h5 class="text-center mb-2 fw-bold">
        JADWAL PEMAKAIAN RUANGAN
    </h5>
    <h5 class="text-center text-secondary mb-4">
        SEMESTER 
        <select id="semesterSelect" >
            @foreach ($semester as $f)
                <option value="{{ $f->id }}" {{ request('semester_id') == $f->id ? 'selected' : '' }}>
                    {{ strtoupper($f->semester) }} {{ strtoupper($f->jenis) }} {{ $f->tahun_akademik }}
                </option>
            @endforeach
        </select>
    </h5>
    <div class="d-flex justify-center">
        <a href="{{ route('jadwal.ruang', [
            'fakultas_id' => request('fakultas_id'),
            'kelas' => request('kelas'),
            'semester_id' => request('semester_id')
        ]) }}" class="btn btn-sm btn-success" target="_blank">
            <i class="bi bi-file-pdf"></i> DOWNLOAD
        </a>
    </div>
    <div class="card-body">
        @foreach ($hari as $h)
            <div class="fw-bold mb-2">{{ strtoupper($h) }}</div>
            <table class="table table-bordered table-striped align-middle text-sm">
                <thead class="table-light">
                    <tr>
                        <th>WAKTU</th>
                        @foreach ($ruangan as $s)
                            <th>{{ $s->kode }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shift as $s)
                    @if ($s->tipe == "ISOMA")
                            <tr style="font-style:italic;">
                                <td class="bg-info text-white">{{ $s->jam_mulai }} - {{ $s->jam_selesai }}</td>
                                @foreach ($ruangan as $k)
                                    <td class="bg-info text-white text-center">Istirahat, Sholat & Makan</td>
                                @endforeach
                            </tr>
                        @else
                            <tr>
                                <td>{{ $s->jam_mulai }} - {{ $s->jam_selesai }}</td>
                                @foreach ($ruangan as $r)
                                    @foreach ($selectedFakultas->prodi as $k)
                                        @php
                                            $jadwalProdi = $s->jadwal->filter(fn ($j) =>
                                                $j->pengampu_mk?->matakuliah->prodi_id == $k->id && 
                                                $j->pengampu_mk?->surat_tugas->semester_id == $selectedSemester->id && 
                                                $j->hari == $h && 
                                                $j->shift_id == $s->id &&
                                                $j->ruangan_id == $r->id
                                            );
                                        @endphp
                                        @if ($jadwalProdi->isNotEmpty())
                                            @foreach ($jadwalProdi as $jd)
                                                <td>
                                                    <div style="font-style:italic;">{{ $jd->pengampu_mk->matakuliah->prodi->kode }}{{ $jd->pengampu_mk->surat_tugas->semester->semester }}{{ $jd->pengampu_mk->kelas->pluck('tipe')->implode('/') }}</div>
                                                </td>
                                            @endforeach
                                        @else
                                            <td></td>
                                        @endif
                                    @endforeach
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addJadwalModal" tabindex="-1" aria-labelledby="addJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" id="addJadwal" action="{{ route('jadwal.store') }}">
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
                    <select class="form-select form-select-sm" name="ruangan_id" required>
                        @foreach ($ruangan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->kode }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-12 text-xs mt-2" id="preferensiDosen" style="display:none;">
                    <div class="text-danger" id="preferensiText" style="font-size: 0.85rem;">
                        {{ Auth::user()->dosen->referensi ?? '' }}
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Hari</label>
                    <input type="text" disabled id="hari_show" class="form-control form-control-sm">
                    <input type="hidden" name="hari" id="hari" class="form-control form-control-sm" required>
                </div>
                
                <div class="col-md-6">
                    <label class="form-label">Jam</label>
                    <input type="hidden" name="shift_id" id="shift_id" class="form-control form-control-sm" required>
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
    $(document).ready(function() {
        // Handle fakultas select change
        $('#fakultasSelect').on('change', function() {
            const fakultas = $(this).val();
            window.location.href = `/ruang?fakultas_id=${fakultas}`;
        });
        $('#semesterSelect').on('change', function() {
            const fakultas = $(this).val();
            window.location.href = `/ruang?semester_id=${fakultas}`;
        });

        // Handle charter button click
        $('.btn-charter').on('click', function() {
            const hari = $(this).data('hari');
            const pengampu_mk_id = $(this).data('pengampu_mk_id');
            const matakuliah = $(this).data('matakuliah');
            const shift_id = $(this).data('shift_id');
            const jam = $(this).data('jam');

            $('#hari').val(hari);
            $('#hari_show').val(hari);
            $('#pengampu_mk_id').val(pengampu_mk_id);
            $('#matakuliah').val(matakuliah);
            $('#shift_id').val(shift_id);
            $('#jam').val(jam);
        });
    });
</script>
@endsection