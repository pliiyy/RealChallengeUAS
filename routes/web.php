<?php

use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DekanController;
use App\Http\Controllers\DosenController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SuratTugasController;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return view('login');
})->middleware('guest');
Route::post('/login',[AuthController::class,"login"])->middleware('guest');

// Master Data
Route::middleware('auth.user')->group(function () {
    Route::resource("fakultas",FakultasController::class)->middleware('auth.user');
    Route::resource("prodi",ProdiController::class)->middleware('auth.user');
    Route::resource("matakuliah",MatakuliahController::class)->middleware('auth.user');
    Route::resource("semester",SemesterController::class)->middleware('auth.user');
    Route::resource("kelas",KelasController::class)->middleware('auth.user');
    Route::resource("shift",ShiftController::class)->middleware('auth.user');
    Route::resource("ruangan",RuanganController::class)->middleware('auth.user');
    Route::resource("angkatan",AngkatanController::class)->middleware('auth.user');
});

// User Data
Route::middleware('auth.user')->group(function () {
    Route::resource("dekan",DekanController::class)->middleware('auth.user');
    Route::resource("dosen",DosenController::class)->middleware('auth.user');
});

// Jadwal Data
Route::middleware('auth.user')->group(function () {
    Route::resource("surat",SuratTugasController::class)->middleware('auth.user');
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