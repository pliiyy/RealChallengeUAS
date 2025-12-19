<?php

use App\Http\Controllers\DekanController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

// Master Data
Route::resource("fakultas",FakultasController::class);
Route::resource("prodi",ProdiController::class);
Route::resource("semester",SemesterController::class);
Route::resource("shift",ShiftController::class);
Route::resource("ruangan",RuanganController::class);

// User Data
Route::resource("dekan",DekanController::class);
