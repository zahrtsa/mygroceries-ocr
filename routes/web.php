<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Belanja\DaftarBelanjaController;
use App\Http\Controllers\Belanja\ItemBelanjaController;
use App\Http\Controllers\Belanja\PolaBelanjaController;
use App\Http\Controllers\Belanja\PengeluaranBulananController;

// Halaman awal → redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard → hanya untuk user login
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// Routes profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes Belanja → group dengan middleware auth
Route::middleware('auth')->prefix('belanja')->name('belanja.')->group(function () {
    // Daftar Belanja
    Route::resource('daftar', DaftarBelanjaController::class);

    // Item Belanja
    Route::resource('item', ItemBelanjaController::class);

    // Pola Belanja
    Route::resource('pola', PolaBelanjaController::class);

    // Pengeluaran Bulanan
    Route::resource('pengeluaran', PengeluaranBulananController::class);
});

require __DIR__.'/auth.php';
