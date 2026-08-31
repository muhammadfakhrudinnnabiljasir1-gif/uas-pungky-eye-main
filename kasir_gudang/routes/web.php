<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\TransaksiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Contoh rute login (asumsikan Anda menggunakan Auth standar bawaan Laravel)
// Auth::routes();

// Semua rute di bawah ini wajib login
Route::middleware(['auth'])->group(function () {

    // -------------------------------------------------------------
    // RUTE GUDANG (Dapat diakses oleh Admin & Petugas Gudang)
    // -------------------------------------------------------------
    // Di Laravel, Anda bisa membuat custom middleware 'role:admin,gudang' 
    // atau menggunakan closure middleware sederhana di sini.
    Route::middleware('role:admin,gudang')->group(function () {
        
        // Rute untuk mengelola Produk di Gudang
        Route::get('/produk', [ProdukController::class, 'index'])->name('produk.index');
        Route::get('/produk/tambah', [ProdukController::class, 'create'])->name('produk.create');
        Route::post('/produk', [ProdukController::class, 'store'])->name('produk.store');
        Route::get('/produk/{produk}/edit', [ProdukController::class, 'edit'])->name('produk.edit');
        Route::put('/produk/{produk}', [ProdukController::class, 'update'])->name('produk.update');
        Route::delete('/produk/{produk}', [ProdukController::class, 'destroy'])->name('produk.destroy');

    });

    // -------------------------------------------------------------
    // RUTE KASIR (Dapat diakses oleh Admin & Kasir)
    // -------------------------------------------------------------
    Route::middleware('role:admin,kasir')->group(function () {
        
        // Rute untuk Point of Sale (POS) Kasir
        Route::get('/kasir', [TransaksiController::class, 'index'])->name('kasir.index');
        Route::post('/kasir/transaksi', [TransaksiController::class, 'store'])->name('kasir.store');

    });

});
