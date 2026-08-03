<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\DashboardController;
use App\Models\Satuan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Satuan dikelompokkan per kategori untuk ditampilkan di dropdown login.
    $satuans = Satuan::orderBy('urutan')->get()->groupBy('kategori');

    return view('siberad.landing.welcome', ['satuans' => $satuans]);
});

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');
