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
        JADWAL MATA KULIAH 
        {{ $selectedFakultas->prodi
            ->map(fn ($p) => $p->kode.' '.$p->jenjang)
            ->implode(', ')
        }}
    </h5>
    
    <h5 class="text-center text-secondary mb-4">
        SEMESTER {{ $selectedSemester->semester }} {{ strtoupper($selectedSemester->jenis) }} {{ $selectedSemester->tahun_akademik }}
    </h5>

    <div class="d-flex justify-content-center mb-3 gap-2">
        @foreach ($selectedFakultas->prodi->flatMap->kelas as $kls)
            <a href="{{ route('jadwal', [
                'fakultas_id' => request('fakultas_id'),
                'kelas' => $kls->tipe,
                'semester_id' => $kls->semester->id
            ]) }}"
               class="btn btn-sm {{ request('kelas') == $kls->tipe ? 'btn-primary' : 'btn-info' }}">
                {{ $kls->tipe }}{{ $kls->semester->semester }}
            </a>
        @endforeach

        <a href="/ruang" class="btn btn-sm btn-warning">
            RUANG
        </a>
        <a href="{{ route('jadwal.download', [
            'fakultas_id' => request('fakultas_id'),
            'kelas' => request('kelas'),
            'semester_id' => request('semester_id')
        ]) }}" class="btn btn-sm btn-success" target="_blank">
            <i class="bi bi-file-pdf"></i> DOWNLOAD JADWAL
        </a>
    </div>

    <div class="card-body">
        @foreach ($hari as $h)
            <div class="fw-bold mb-2">{{ strtoupper($h) }}</div>
            <table class="table table-bordered table-striped align-middle text-sm">
                <thead class="table-light">
                    <tr>
                        <th>WAKTU</th>
                        @foreach ($selectedFakultas->prodi as $s)
                            <th>{{ $s->kode }} {{ $s->jenjang }}</th>
                        @endforeach
                    </tr>
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
                                            $j->pengampu_mk?->matakuliah->prodi_id == $k->id && 
                                            $j->pengampu_mk?->surat_tugas->semester_id == $selectedSemester->id && 
                                            $j->hari == $h && 
                                            $j->shift_id == $s->id
                                        );
                                        $pengampu = $selectedSemester->surat_tugas
                                        ->filter(fn ($st) =>
                                            $st->status == 'APPROVED' && $st->dosen_id == auth()->user()->dosen->id
                                            && $st->pengampu_mk->contains(fn ($pmk) =>
                                                $pmk->matakuliah->prodi_id == $k->id
                                                && is_null($pmk->jadwal)
                                            )
                                        )
                                        ->flatMap(fn ($st) =>
                                            $st->pengampu_mk->filter(fn ($pmk) =>
                                                $pmk->matakuliah->prodi_id == $k->id
                                                && is_null($pmk->jadwal)
                                            )
                                        )
                                        ->values();
                                        @endphp
                                    @if ($jadwalProdi->isNotEmpty())
                                        @foreach ($jadwalProdi as $jd)
                                            <td>
                                                <div class="d-flex gap-1 items-center">
                                                    <div>
                                                        <div class="fw-bold">{{ $jd->pengampu_mk->matakuliah->nama }}</div>
                                                        <div class="opacity-80" style="font-style:italic;">
                                                            Dosen : {{ $jd->pengampu_mk->surat_tugas->dosen->user->biodata->nama }}
                                                        </div>
                                                        <div class="opacity-80" style="font-style:italic;">
                                                            Ruang : {{ $jd->ruangan->nama }} ({{ $jd->ruangan->kode }})
                                                        </div>
                                                    </div>
                                                    {{-- @if (auth()->user()->dosen->id !== $jd->pengampu_mk->surat_tugas->dosen_id) --}}
                                                        <button class="btn btn-sm btn-info btn-barter py-0 px-1 opacity-50"
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#barterJadwalModal"
                                                            data-hari="{{ $h }}"
                                                            data-shift_id="{{ $s->id }}" 
                                                            data-jam="{{ $s->jam_mulai }} - {{ $s->jam_selesai }}" 
                                                            data-ruangan_id="{{ $jd->ruangan_id }}" 
                                                            data-jadwal_tujuan_id="{{ $jd->id }}" 
                                                            data-jadwal_tujuan_show="{{ $jd->pengampu_mk->surat_tugas->dosen->user->biodata->nama }} - {{ $jd->pengampu_mk->matakuliah->nama }} ({{ $jd->hari }} {{ $jd->shift->jam_mulai }}-{{ $jd->shift->jam_selesai }})" 
                                                        >Barter</button>
                                                    {{-- @endif --}}
                                                </div>
                                            </td>
                                        @endforeach
                                    @else
                                        <td>
                                            @foreach ($pengampu as $pg)
                                                <div class="text-xs opacity-50">
                                                    {{ $pg->matakuliah->nama }}
                                                    <button 
                                                        class="btn btn-sm btn-success btn-charter py-0 px-1"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#addJadwalModal"
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
                                            <div>
                                                <button class="btn btn-sm btn-warning btn-pindah py-0 px-1 opacity-50"
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#pindahJadwalModal"
                                                    data-hari="{{ $h }}"
                                                    data-shift_id="{{ $s->id }}" 
                                                    data-jam="{{ $s->jam_mulai }} - {{ $s->jam_selesai }}" 
                                                >Pindah</button>
                                            </div>
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
<div class="modal fade" id="pindahJadwalModal" tabindex="-1" aria-labelledby="pindahJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" id="pindahJadwal" action="/pindah">
            @csrf
            @method('POST')
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pindahJadwalModalLabel">Pindah Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body row g-3">
                <input type="text" name="status_jadwal" value="PINDAH" hidden class="form-control form-control-sm">
                <div class="col-md-6">
                    <label class="form-label">Hari</label>
                    <input type="text" id="pindah_hari" disabled class="form-control form-control-sm">
                    <input type="text" id="pindah_hari_id" name="hari" hidden class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jam</label>
                    <input type="text" id="pindah_jam" disabled class="form-control form-control-sm">
                    <input type="text" id="pindah_shift_id" name="shift_id" hidden class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ruangan</label>
                    <select class="form-select form-select-sm" name="ruangan_id" required>
                        @foreach ($ruangan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jadwal Asal</label>
                    <select class="form-select form-select-sm" name="jadwal_asal_id" required>
                        @foreach ($jadwal as $item)
                            <option value="{{ $item->id }}">{{ $item->pengampu_mk->matakuliah->nama }} ({{ $item->hari }} {{ $item->shift->jam_mulai }} - {{ $item->shift->jam_selesai }})</option>
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
<div class="modal fade" id="barterJadwalModal" tabindex="-1" aria-labelledby="barterJadwalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" method="POST" id="barterJadwal" action="/pindah">
            @csrf
            @method('POST')
            
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="pindahJadwalModalLabel">Barter Jadwal</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body row g-3">
                <input type="text" name="status_jadwal" value="BARTER" hidden class="form-control form-control-sm">
                <div class="col-md-12">
                    <label class="form-label">Jadwal tujuan</label>
                    <input type="text" id="jadwal_tujuan_show" disabled class="form-control form-control-sm">
                    <input type="text" id="jadwal_tujuan_id" name="jadwal_tujuan_id" hidden class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hari</label>
                    <input type="text" id="barter_hari" disabled class="form-control form-control-sm">
                    <input type="text" id="barter_hari_id" name="barter_hari_id" hidden class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jam</label>
                    <input type="text" id="barter_jam" disabled class="form-control form-control-sm">
                    <input type="text" id="barter_shift_id" name="shift_id" hidden class="form-control form-control-sm">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ruangan</label>
                    <select class="form-select form-select-sm" name="ruangan_id" id="barter_ruangan_id" required>
                        @foreach ($ruangan as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }} ({{ $item->kode }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jadwal Asal</label>
                    <select class="form-select form-select-sm" name="jadwal_asal_id" required>
                        @foreach ($jadwal as $item)
                            <option value="{{ $item->id }}">{{ $item->pengampu_mk->matakuliah->nama }} ({{ $item->hari }} {{ $item->shift->jam_mulai }} - {{ $item->shift->jam_selesai }})</option>
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

<script>
    $(document).ready(function() {
        // Handle fakultas select change
        $('#fakultasSelect').on('change', function() {
            const fakultas = $(this).val();
            window.location.href = `/jadwal?fakultas_id=${fakultas}`;
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
        $('.btn-pindah').on('click', function() {
            const hari = $(this).data('hari');
            const shift_id = $(this).data('shift_id');
            const jam = $(this).data('jam');

            $('#pindah_hari').val(hari);
            $('#pindah_hari_id').val(hari);
            $('#pindah_shift_id').val(shift_id);
            $('#pindah_jam').val(jam);
        });
        $('.btn-barter').on('click', function() {
            const hari = $(this).data('hari');
            const shift_id = $(this).data('shift_id');
            const ruangan_id = $(this).data('ruangan_id');
            const jam = $(this).data('jam');
            const jadwal_tujuan_id = $(this).data('jadwal_tujuan_id');
            const jadwal_tujuan_show = $(this).data('jadwal_tujuan_show');

            $('#barter_hari').val(hari);
            $('#barter_hari_id').val(hari);
            $('#barter_shift_id').val(shift_id);
            $('#barter_ruangan_id').val(ruangan_id);
            $('#barter_jam').val(jam);
            $('#jadwal_tujuan_id').val(jadwal_tujuan_id);
            $('#jadwal_tujuan_show').val(jadwal_tujuan_show);
        });
    });
</script>
@endsection