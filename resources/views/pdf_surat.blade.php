<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Surat Tugas Pengajaran</title>
    <style>
        /* CSS Umum dan Reset */
        body {
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 17cm;
            /* Ukuran mendekati A4 */
            margin: 1cm auto;
            padding: 0;
        }

        /* Header (Kop Surat) */
        .header {
            border-bottom: 3px solid #000;
            font-family: 'Times New Roman', Times, serif;
            padding-bottom: 10px;
        }

        .header h2,
        .header h3 {
            margin: 0;
            padding: 0;
        }

        .kop-img {
            float: left;
            margin-right: 5px;
            width: 150px;
            /* Sesuaikan ukuran logo */
        }

        /* Judul Surat */
        .judul-surat {
            text-align: center;
            margin: 25px 0 15px 0;
        }

        .judul-surat h4 {
            text-decoration: underline;
        }

        /* Isi Surat */
        .isi-surat p {
            text-align: justify;
            line-height: 1.5;
            margin-bottom: 15px;
        }

        .data-list {
            width: 100%;
            margin: 10px 0 20px 0;
            padding-left: 20px;
        }

        .data-list td {
            padding-bottom: 5px;
        }

        /* Tanda Tangan */
        .ttd-section {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }

        .ttd-left {
            width: 50%;
            float: left;
            text-align: center;
        }

        .ttd-right {
            width: 50%;
            float: right;
            text-align: center;
        }

        .ttd-right p {
            margin: 0;
        }

        .clear {
            clear: both;
        }
        .row-total {
            font-weight: bold;
            text-transform: capitalize;
        }

        .total-label {
            text-align: center;
            letter-spacing: 1px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div style="position: relative; width:100%; border-bottom:3px solid black; padding-bottom:10px;">

    <!-- LOGO -->
            <div style="position:absolute; left:0; top:0;">
                <img src="{{ public_path('images/masoem.png') }}" width="120">
            </div>

            <!-- TEKS (FULL & CENTER) -->
            <div style="text-align:center;font-family: Times New Roman">
                <h3 style="margin:0;font-size:25px;">UNIVERSITAS MA'SOEM</h3>
                <div>SK. No. : {{ $surat->nomor_sk }}</div>
                <div style="font-weight:bold;">Terakreditasi BAN-PT</div>

                <div style="font-size:11px; margin-top:10px;">
                    <div>Kampus : Jl. Raya Cipacing No. 22 Jatinangor - Sumedang 45363</div>
                    <div>Telp. (022) 7798340 Fax. (022) 7798243</div>
                    <div>
                        email :
                        <span style="color:blue; text-decoration: underline;">
                            info@masoemuniversity.ac.id
                        </span>
                        Web :
                        <span style="color:blue; text-decoration: underline;">
                            www.masoemuniversity.ac.id
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div style="text-align: center;margin-top: 20px;">
            <div style="font-weight: bold; font-size: 14px; text-decoration: underline;">SURAT TUGAS PENGAJARAN</div>
            <div style="font-size: 12x;">Nomor: {{ $surat->nomor_surat }}</div>
        </div>

        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini Dekan Fakultas {{ $surat->dekan->fakultas->nama }}, menugaskan kepada :</p>
            <table class="data-list">
                <tr>
                    <td style="width: 25%;">Nama</td>
                    <td style="width: 5%;">:</td>
                    <td>{{ $surat->dosen->user->biodata->nama }}</td>
                </tr>
                <tr>
                    <td>NIDN</td>
                    <td>:</td>
                    <td>{{ $surat->dosen->nidn ?? '-' }}</td>
                </tr>
            </table>

            <p>Untuk mengampu matakuliah sebagai berikut :</p>
        </div>

        <div class="detail-tugas">
            <table border="1" style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px;">No.</th>
                        <th style="padding: 8px;">Mata Kuliah</th>
                        <th style="padding: 8px;">SKS</th>
                        <th style="padding: 8px;">Program</th>
                        <th style="padding: 8px;">SMT</th>
                        <th style="padding: 8px;">Kelas</th>
                        <th style="padding: 8px;">Jml. Kelas</th>
                        <th style="padding: 8px;">Jml. SKS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $grandTotalSks = 0; @endphp
                    @foreach ($surat->pengampu_mk as $index => $kls)
                    @php
                        $jumlahKelas = $kls->kelas->count();
                        $subTotalSks = $jumlahKelas * $kls->sks;
                        $grandTotalSks += $subTotalSks;
                    @endphp
                        <tr>
                            <td style="padding: 8px; text-align: center;">{{  $index+1 }}</td>
                            <td style="padding: 8px;">{{ $kls->matakuliah->nama }}</td>
                            <td style="padding: 8px; text-align: center;">{{ $kls->sks }}</td>
                            <td style="padding: 8px; text-align: center;">{{ $kls->matakuliah->prodi->jenjang }} {{ $kls->matakuliah->prodi->kode }}</td>
                            <td style="padding: 8px; text-align: center;">{{ $surat->semester->tahun_akademik }}</td>
                            <td style="padding: 8px; text-align: center;">{{ $kls->kelas->pluck('tipe')->unique()->implode('/') }}</td>
                            <td style="padding: 8px; text-align: center;">{{ count($kls->kelas) }}</td>
                            <td style="padding: 8px; text-align: center;">{{ count($kls->kelas) * $kls->sks }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="row-total">
                        <td colspan="7" class="total-label">Total SKS</td>
                        <td style="text-align: center">{{$grandTotalSks}}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="text-align: justify;line-height: 1.3;">
            <p>Perlu kami sampaikan bahwa perkuliahan semester {{ $surat->semester->jenis }} tahun akademik {{ $surat->semester->tahun_akademik }} insya Allah akan dimulai tanggal 15 September 2025, untuk itu kaml mohon Bapak dapat mempersiapkan <span style="font-weight:bold;">Rencana Pembelaiaran Semester (RPSI)</span> dan Bahan Ajar untuk matakuliah di atas dan menyampaikannya ke Fakultas {{ $surat->dekan->fakultas->nama }} Universitas Ma'soem <span style="font-weight:bold;">paling lambat diminggu pertama perkuliahan</span>.</p>
            <p>Atas perhatiannya kami ucapkan terimakasih.</p>
        </div>

        <div class="ttd-section">
            <div class="ttd-right">
                <p>Jatinangor, {{ \Carbon\Carbon::parse($surat->tanggal ?? now())->isoFormat('D MMMM Y') }}</p>
                <p>Dekan Fakultas {{ $surat->dekan->Fakultas->nama }}</p>

                <br><br><br><br><br><br>

                <p style="text-decoration: underline; margin-bottom: 0;font-weight: bold;">
                    {{ $surat->dekan->user->biodata->nama ?? '                                ' }}
                </p>
            </div>
            <div class="clear"></div>
        </div>

    </div>
</body>

</html>