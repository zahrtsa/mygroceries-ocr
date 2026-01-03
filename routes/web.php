<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\UserSettingController;
use App\Http\Controllers\Belanja\BelanjaController;
use App\Http\Controllers\Belanja\DashboardController;
use App\Http\Controllers\Belanja\ItemBelanjaController;
use App\Http\Controllers\Belanja\PengeluaranBulananController;
use App\Http\Controllers\Belanja\PolaBelanjaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

// Halaman awal → redirect ke login
Route::get('/', function () {
    return redirect()->route('login');
});
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::resource('settings', UserSettingController::class)
        ->only(['edit', 'update']);
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
    Route::get('/rekapanharian', [BelanjaController::class, 'rekapanHarian'])->name('rekapanharian');
    // Pola, Pengeluaran Bulanan tetap
    Route::resource('pola', PolaBelanjaController::class);
    Route::resource('pengeluaran', PengeluaranBulananController::class);

    Route::resource('receipts', ReceiptController::class);
    Route::post('receipts/{receipt}/reset-ocr', [ReceiptController::class, 'resetToOCR'])
        ->name('receipts.reset-ocr');
});

require __DIR__.'/auth.php';
