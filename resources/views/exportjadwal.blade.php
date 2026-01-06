<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <style>
    /* 1. CSS Reset & Base Styles (Pengganti Bootstrap Dasar) */
    .container-local {
        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
        padding: 5px;
        font-family: system-ui, -apple-system, sans-serif;
    }

    /* 2. Form & Select Styles */
    .header-controls {
        text-align: center;
        margin-bottom: 10px;
    }
    .header-controls h4, .header-controls h5 {
        margin: 5px 0;
        color: #333;
    }

    .btn-download:hover {
        background-color: #157347;
    }
    .d-flex-center {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    /* 4. Table Styles */
    .schedule-wrapper {
        overflow-x: auto; /* Agar tabel bisa discroll jika lebar */
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 5px;
    }
    .day-header {
        font-weight: bold;
        margin-top: 10px;
        margin-bottom: 10px;
        font-size: .9em;
        color: #000;
    }
    .table-local {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }
    .table-local th, .table-local td {
        border: 1px solid #dee2e6;
        padding: 5px;
        vertical-align: middle;
    }
    .table-local thead th {
        background-color: #f8f9fa;
        text-align: center;
        font-weight: bold;
        position: sticky;
        top: 0;
    }
    
    /* Warna Row Khusus */
    .row-isoma td {
        background-color: #0dcaf0; /* Bootstrap Info Color */
        color: white;
        font-style: italic;
        text-align: center;
    }
    .cell-time {
        white-space: nowrap;
        text-align: center;
        background-color: #fcfcfc;
    }
    .schedule-item {
        margin-bottom: 4px;
        padding: 4px;
        background-color: #f0f8ff;
        border: 1px solid #cce5ff;
        border-radius: 4px;
        font-size: 10px;
    }
</style>
    </head>
<body>
<div class="container-local">
    <div class="header-controls">
        <h4>
            UNIVERSITAS MA'SOEM FAKULTAS {{ strtoupper($selectedFakultas->nama) }}
        </h4>
        
        <h5>
            JADWAL MATA KULIAH 
            {{ $selectedFakultas->prodi
                ->map(fn ($p) => $p->kode.' '.$p->jenjang)
                ->implode(', ')
            }}
        </h5>
        
        <h5 style="color: #6c757d;">
            SEMESTER {{ $selectedSemester->semester }} {{ strtoupper($selectedSemester->jenis) }} {{ $selectedSemester->tahun_akademik }}
        </h5>
    </div>

    <div class="schedule-wrapper">
        @foreach ($hari as $h)
            <div class="day-header">{{ strtoupper($h) }}</div>
            <table class="table-local">
                <thead >
                    <tr>
                        <th style="width: 120px;">WAKTU</th>
                        @foreach ($selectedFakultas->prodi as $s)
                            <th>{{ $s->kode }} {{ $s->jenjang }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shift as $s)
                    @if ($s->tipe == "ISOMA")
                            <tr class="row-isoma">
                                <td >{{ $s->jam_mulai }} - {{ $s->jam_selesai }}</td>
                                <td colspan="{{ count($selectedFakultas->prodi) }}">Istirahat, Sholat & Makan</td>
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
                                    @endphp
                                    @if ($jadwalProdi->isNotEmpty())
                                        @foreach ($jadwalProdi as $jd)
                                            <td>
                                                <div>
                                                    <div class="fw-bold">{{ $jd->pengampu_mk->matakuliah->nama }}</div>
                                                    <div class="opacity-80" style="font-style:italic;">
                                                        Dosen : {{ $jd->pengampu_mk->surat_tugas->dosen->user->biodata->nama }}
                                                    </div>
                                                    <div class="opacity-80" style="font-style:italic;">
                                                        Ruang : {{ $jd->ruangan->nama }} ({{ $jd->ruangan->kode }})
                                                    </div>
                                                </div>
                                            </td>
                                        @endforeach
                                    @else
                                    <td></td>
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
</div>
</body>
</html>