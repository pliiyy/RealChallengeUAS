<?php

use App\Http\Controllers\DekanController;
use App\Http\Controllers\RuanganController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::resource("ruangan",RuanganController::class);
Route::resource("dekan",DekanController::class);
