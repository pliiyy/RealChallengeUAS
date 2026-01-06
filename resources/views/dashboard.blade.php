@extends('layout')
@section('title', 'Dashboard')

@section('content')
    <div class="col-md-9 col-lg-10 content">

        {{-- Kartu Pengguna, Notifikasi, dan Pengaturan --}}
        <div class="row g-4 ">
            <!-- Card Pengguna -->
            <div class="col-md-4">
                <a href="/user" style="text-decoration: none">
                    <div class="card p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">Pengguna</h5>
                                <p class="card-text text-muted mb-0">{{ $user }} terdaftar</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Card Notifikasi -->
            <div class="col-md-4">
                <div class="card p-4">
                    <div class="d-flex align-items-center">
                        <div class="icon bg-warning bg-opacity-10 text-warning rounded-3 p-3 me-3">
                            <i class="bi bi-bell fs-4"></i>
                        </div>
                        <div>
                            <h5 class="card-title mb-1">Notifikasi</h5>
                            <p class="card-text text-muted mb-0">{{ $total }} Perpindahan jadwal</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Pengaturan -->
            <div class="col-md-4">
                <a href="/settings" style="text-decoration: none">
                    <div class="card p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                                <i class="bi bi-gear fs-4"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-1">Pengaturan</h5>
                                <p class="card-text text-muted mb-0">Kelola preferensi akun</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Kartu Data Mahasiswa --}}
        <div class="card p-4 mt-4">
            <h5 class="fw-semibold text-secondary mb-3">Biodata</h5>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-3">
                    <p class="fw-semibold mb-1 text-muted">Nama</p>
                    <p class="mb-0">{{ auth()->user()->biodata->nama ?? 'Rafly Adrian Firmansyah' }}</p>
                    <p class="mb-0" style="font-style:italic;">{{ auth()->user()->email ?? 'Rafly Adrian Firmansyah' }}</p>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <p class="fw-semibold mb-1 text-muted">NIDN/NIM</p>
                    <p class="mb-0">NIDN : {{ auth()->user()->dosen->nidn ?? '-' }}</p>
                    <p class="mb-0">NIM : {{ auth()->user()->mahasiswa->nim ?? '-' }}</p>
                </div>
                <div class="col-md-6 col-lg-3 mb-3">
                    <p class="fw-semibold mb-1 text-muted">Roles</p>
                    @foreach (auth()->user()->role as $item)
                        <span class="badge bg-info">{{ ucfirst(strtolower($item->nama)) }}</span>
                    @endforeach
                </div>
            </div>
        </div>
        <div style="margin-top: 20px"></div>
        <div id='calendar' style="height:500px;width: 80%;margin: auto"></div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                events: '/rekap', // ambil semua jadwal dari controller
                timeZone: 'local',
                locale: 'id', // <-- tambahkan ini
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                editable: false,
                selectable: false,
                dayMaxEvents: true // otomatis membuat “+N more” jika terlalu banyak event
            });

            calendar.render();
        });
    </script>
@endsection