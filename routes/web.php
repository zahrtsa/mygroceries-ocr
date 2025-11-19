<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Belanja\DashboardController;
use App\Http\Controllers\Belanja\BelanjaController;
use App\Http\Controllers\Belanja\ItemBelanjaController;
use App\Http\Controllers\Belanja\PolaBelanjaController;
use App\Http\Controllers\Belanja\PengeluaranBulananController;

// Halaman awal → redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Belanja: group RESTful nested resource (BEST PRACTICE)
Route::middleware('auth')->prefix('belanja')->name('belanja.')->group(function () {
    // Daftar & Item nested resource (item di dalam daftar)
    // Route::resource('daftar', DaftarBelanjaController::class);
    // Route::resource('daftar.item', ItemBelanjaController::class)->shallow(); // RESTful, agar route item edit/delete cukup /item/{id}
    Route::resource('item', BelanjaController::class);
    Route::get('/rekapanharian', [BelanjaController::class, 'rekapHarian'])->name('rekapanharian');
    // Pola, Pengeluaran Bulanan tetap
    Route::resource('pola', PolaBelanjaController::class);
    Route::resource('pengeluaran', PengeluaranBulananController::class);
});

require __DIR__.'/auth.php';
