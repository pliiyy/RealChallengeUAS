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

Route::get('/', function () {
    return view('login');
});
Route::post('/login',[AuthController::class,"login"]);

// Master Data
Route::resource("fakultas",FakultasController::class);
Route::resource("prodi",ProdiController::class);
Route::resource("matakuliah",MatakuliahController::class);
Route::resource("semester",SemesterController::class);
Route::resource("kelas",KelasController::class);
Route::resource("shift",ShiftController::class);
Route::resource("ruangan",RuanganController::class);
Route::resource("angkatan",AngkatanController::class);

// User Data
Route::resource("dekan",DekanController::class);
Route::resource("dosen",DosenController::class);

// Jadwal Data
Route::resource("surat",SuratTugasController::class);

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