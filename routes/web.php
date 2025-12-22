<?php

use App\Http\Controllers\AngkatanController;
use App\Http\Controllers\DekanController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MatakuliahController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

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
Route::resource("user",UserController::class);

// Jadwal Data
Route::resource("surat",SuratTugasController::class);