<?php

use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DekanController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KaprodiController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KosmaController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\PindahJadwalController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SekprodiController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
})->middleware('guest');
Route::post('/login',[AuthController::class,"login"])->middleware('guest');
Route::post('/logout',[AuthController::class,"logout"])->middleware('guest');
Route::get('/', [UserController::class, 'dashboard'])->middleware('auth.user');
Route::put('/settings', [UserController::class, 'editPassword'])->middleware('auth.user');
Route::get('/settings', function () {
        return view('setting');
    })->middleware('auth.user');
Route::get('/profil', function () {
        return view('profile');
    })->middleware('auth.user');
Route::PUT('/profil', [UserController::class, 'editProfil'])->middleware('auth.user');
Route::put('/profil/foto', [UserController::class, 'updateFoto'])->name('profil.updateFoto');

// Master Data
Route::middleware('auth.user')->group(function () {
    Route::resource("fakultas",FakultasController::class);
    Route::resource("prodi",ProdiController::class);
    Route::resource("matakuliah",MatakuliahController::class);
    Route::resource("semester",SemesterController::class);
    Route::resource("kelas",KelasController::class);
    Route::resource("shift",ShiftController::class);
    Route::resource("ruangan",RuanganController::class);
    Route::resource("angkatan",AngkatanController::class);
});

// User Data
Route::middleware('auth.user')->group(function () {
    Route::resource("dekan",DekanController::class);
    Route::resource("dosen",DosenController::class);
    Route::resource("kaprodi",KaprodiController::class);
    Route::resource("sekprodi",SekprodiController::class);
    Route::resource("mahasiswa",MahasiswaController::class);
    Route::resource("kosma",KosmaController::class);
    Route::get('/user', [AuthController::class, 'user']);
});

// Jadwal Data
Route::middleware('auth.user')->group(function () {
    Route::resource("surat",SuratTugasController::class);
    Route::resource("jadwal",JadwalController::class)->name('index',"jadwal");
    Route::resource("pindah",PindahJadwalController::class)->name('index',"pindah");
});

// Wilayah Data
Route::get('/api/provinces', function () {
    $response = Http::get('https://wilayah.id/api/provinces.json');
    return response()->json($response->json());
});
Route::get('/api/regencies/{provinceId}', function ($provinceId) {
    $response = Http::get("https://wilayah.id/api/regencies/{$provinceId}.json");
    return response()->json($response->json());
});
Route::get('/api/districts/{regencyId}', function ($regencyId) {
    $response = Http::get("https://wilayah.id/api/districts/{$regencyId}.json");
    return response()->json($response->json());
});

// PDF Data
Route::get('/laporan/pdf/generate', [SuratTugasController::class, 'generateSurat'])->name('laporan.pdf.generate');
Route::get('/laporan/pdf/show', [SuratTugasController::class, 'viewSurat'])->name('laporan.pdf.show');
Route::get('/rekap', [UserController::class, 'jadwal']);
Route::get('/jadwals/download', [JadwalController::class, 'download'])->name('jadwal.download');
Route::get('/ruang', [JadwalController::class, 'ruang']);
Route::get('/ruang/download', [JadwalController::class, 'ruangDownload'])->name('jadwal.ruang');