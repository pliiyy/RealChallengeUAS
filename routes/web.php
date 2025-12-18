<?php

use App\Http\Controllers\DekanController;
use App\Http\Controllers\FakultasController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\ShiftController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::resource("dekan",DekanController::class);
Route::resource("fakultas",FakultasController::class);
Route::resource("shift",ShiftController::class);
Route::resource("ruangan",RuanganController::class);
