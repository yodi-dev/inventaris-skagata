<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\DashboardController as SuperadminDashboardController;
use App\Http\Controllers\Superadmin\PengadaanController as SuperadminPengadaanController;
use App\Http\Controllers\Toolman\DashboardController as ToolmanDashboardController;
use App\Http\Controllers\Toolman\BarangController as ToolmanBarangController;
use App\Http\Controllers\Toolman\PeminjamanController as ToolmanPeminjamanController;
use App\Http\Controllers\Toolman\PengembalianController as ToolmanPengembalianController;
use App\Http\Controllers\Toolman\PengadaanController as ToolmanPengadaanController;
use App\Http\Controllers\Toolman\PeminjamController as ToolmanPeminjamController;
use App\Http\Controllers\Toolman\MutasiController as ToolmanMutasiController;
use App\Http\Controllers\Peminjam\KatalogController as PeminjamKatalogController;
use App\Http\Controllers\Peminjam\TiketController as PeminjamTiketController;
use App\Http\Controllers\Peminjam\PengajuanController as PeminjamPengajuanController;


// Halaman awal langsung arahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// 1. ROUTE AUTHENTICATION (LARAVEL BREEZE)
// ==========================================
require __DIR__ . '/auth.php';


// ==========================================
// 2. ROUTE SUPER ADMIN (WAKA SARPRAS)
// ==========================================
Route::prefix('superadmin')->name('superadmin.')->middleware(['auth', 'role:waka'])->group(function () {
    Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');


    // Pengadaan (RAB)
    Route::get('/pengadaan', [SuperadminPengadaanController::class, 'index'])->name('pengadaan.index');
    Route::get('/pengadaan/{id}', [SuperadminPengadaanController::class, 'show'])->name('pengadaan.show');
    Route::post('/pengadaan/{id}/review', [SuperadminPengadaanController::class, 'review'])->name('pengadaan.review');


    // Laporan
    Route::get('/laporan/mutasi', [\App\Http\Controllers\Superadmin\LaporanController::class, 'mutasi'])->name('laporan.mutasi');
    Route::get('/laporan/konsumsi', [\App\Http\Controllers\Superadmin\LaporanController::class, 'konsumsi'])->name('laporan.konsumsi');

    // Master Data Bengkel
    Route::get('/bengkel', [\App\Http\Controllers\Superadmin\BengkelController::class, 'index'])->name('bengkel.index');
    Route::get('/bengkel/create', [\App\Http\Controllers\Superadmin\BengkelController::class, 'create'])->name('bengkel.create');
    Route::post('/bengkel', [\App\Http\Controllers\Superadmin\BengkelController::class, 'store'])->name('bengkel.store');
    Route::get('/bengkel/edit/{id}', [\App\Http\Controllers\Superadmin\BengkelController::class, 'edit'])->name('bengkel.edit');
    Route::put('/bengkel/{id}', [\App\Http\Controllers\Superadmin\BengkelController::class, 'update'])->name('bengkel.update');
    Route::delete('/bengkel/{id}', [\App\Http\Controllers\Superadmin\BengkelController::class, 'destroy'])->name('bengkel.destroy');

    // Master Data Toolman
    Route::get('/toolman', [\App\Http\Controllers\Superadmin\ToolmanController::class, 'index'])->name('toolman.index');
    Route::get('/toolman/create', [\App\Http\Controllers\Superadmin\ToolmanController::class, 'create'])->name('toolman.create');
    Route::post('/toolman', [\App\Http\Controllers\Superadmin\ToolmanController::class, 'store'])->name('toolman.store');
    Route::get('/toolman/edit/{id}', [\App\Http\Controllers\Superadmin\ToolmanController::class, 'edit'])->name('toolman.edit');
    Route::put('/toolman/{id}', [\App\Http\Controllers\Superadmin\ToolmanController::class, 'update'])->name('toolman.update');
    Route::delete('/toolman/{id}', [\App\Http\Controllers\Superadmin\ToolmanController::class, 'destroy'])->name('toolman.destroy');
    Route::post('/toolman/{id}/reset-password', [\App\Http\Controllers\Superadmin\ToolmanController::class, 'resetPassword'])->name('toolman.reset-password');

    // Backward-compatible aliases for legacy master routes
    Route::get('/master/bengkel', function () {
        return redirect()->route('superadmin.bengkel.index');
    })->name('master.bengkel');
    Route::get('/master/bengkel/create', function () {
        return redirect()->route('superadmin.bengkel.create');
    })->name('master.bengkel.create');
    Route::get('/master/bengkel/edit/{id}', function ($id) {
        return redirect()->route('superadmin.bengkel.edit', $id);
    })->name('master.bengkel.edit');
    Route::get('/master/toolman', function () {
        return redirect()->route('superadmin.toolman.index');
    })->name('master.toolman');
    Route::get('/master/toolman/create', function () {
        return redirect()->route('superadmin.toolman.create');
    })->name('master.toolman.create');
    Route::get('/master/toolman/edit/{id}', function ($id) {
        return redirect()->route('superadmin.toolman.edit', $id);
    })->name('master.toolman.edit');

    // Profil Waka Sarpras
    Route::get('/profile', function () {
        return redirect()->route('profile.edit');
    })->name('profile');
});


// ==========================================
// 3. ROUTE ADMIN BENGKEL (TOOLMAN)
// ==========================================
Route::prefix('toolman')->name('toolman.')->middleware(['auth', 'role:toolman'])->group(function () {
    Route::get('/dashboard', [ToolmanDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Barang
    Route::get('/barang', [ToolmanBarangController::class, 'index'])->name('barang.index');
    Route::get('/barang/create', [ToolmanBarangController::class, 'create'])->name('barang.create');
    Route::post('/barang', [ToolmanBarangController::class, 'store'])->name('barang.store');
    Route::get('/barang/edit/{id?}', [ToolmanBarangController::class, 'edit'])->name('barang.edit');
    Route::put('/barang/{id}', [ToolmanBarangController::class, 'update'])->name('barang.update');
    Route::delete('/barang/{id}', [ToolmanBarangController::class, 'destroy'])->name('barang.destroy');

    // Sirkulasi Peminjaman
    Route::get('/peminjaman', [ToolmanPeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/{id}', [ToolmanPeminjamanController::class, 'show'])->name('peminjaman.show');
    Route::post('/peminjaman/{id}/approve', [ToolmanPeminjamanController::class, 'approve'])->name('peminjaman.approve');
    Route::post('/peminjaman/{id}/reject', [ToolmanPeminjamanController::class, 'reject'])->name('peminjaman.reject');

    // Sirkulasi Pengembalian
    Route::get('/pengembalian', [ToolmanPengembalianController::class, 'index'])->name('pengembalian.index');
    Route::get('/pengembalian/{id}/check', [ToolmanPengembalianController::class, 'check'])->name('pengembalian.check');
    Route::post('/pengembalian/{id}/check', [ToolmanPengembalianController::class, 'processCheck'])->name('pengembalian.process-check');

    // Pengadaan (RAB)
    Route::get('/pengadaan', [ToolmanPengadaanController::class, 'index'])->name('pengadaan.index');
    Route::get('/pengadaan/create', [ToolmanPengadaanController::class, 'create'])->name('pengadaan.create');
    Route::post('/pengadaan', [ToolmanPengadaanController::class, 'store'])->name('pengadaan.store');
    Route::get('/pengadaan/{id}', [ToolmanPengadaanController::class, 'show'])->name('pengadaan.show');
    Route::get('/pengadaan/{id}/edit', [ToolmanPengadaanController::class, 'edit'])->name('pengadaan.edit');
    Route::put('/pengadaan/{id}', [ToolmanPengadaanController::class, 'update'])->name('pengadaan.update');
    Route::post('/pengadaan/{id}/submit', [ToolmanPengadaanController::class, 'submit'])->name('pengadaan.submit');
    Route::delete('/pengadaan/{id}', [ToolmanPengadaanController::class, 'destroy'])->name('pengadaan.destroy');
    Route::post('/pengadaan/{id}/receive', [ToolmanPengadaanController::class, 'receive'])->name('pengadaan.receive');

    // Manajemen Peminjam
    Route::get('/peminjam', [ToolmanPeminjamController::class, 'index'])->name('peminjam.index');
    Route::get('/peminjam/{id}', [ToolmanPeminjamController::class, 'show'])->name('peminjam.show');
    Route::post('/peminjam/{id}/approve', [ToolmanPeminjamController::class, 'approveUser'])->name('peminjam.approve');
    Route::post('/peminjam/{id}/reject', [ToolmanPeminjamController::class, 'rejectUser'])->name('peminjam.reject');
    Route::post('/peminjam/{id}/suspend', [ToolmanPeminjamController::class, 'suspendUser'])->name('peminjam.suspend');
    Route::post('/peminjam/{id}/activate', [ToolmanPeminjamController::class, 'activateUser'])->name('peminjam.activate');

    // Riwayat Stok / Mutasi
    Route::get('/mutasi', [ToolmanMutasiController::class, 'index'])->name('mutasi.index');
    Route::get('/mutasi/export', [ToolmanMutasiController::class, 'export'])->name('mutasi.export');
    Route::get('/mutasi/export-excel', [ToolmanMutasiController::class, 'exportExcel'])->name('mutasi.export-excel');
    Route::get('/mutasi/print', [ToolmanMutasiController::class, 'print'])->name('mutasi.print');

    // Backward-compatible aliases
    Route::get('/sirkulasi/peminjaman', function () {
        return redirect()->route('toolman.peminjaman.index');
    })->name('sirkulasi.peminjaman');

    Route::get('/sirkulasi/pengembalian', function () {
        return redirect()->route('toolman.pengembalian.index');
    })->name('sirkulasi.pengembalian');

    Route::get('/users', function () {
        return redirect()->route('toolman.peminjam.index');
    })->name('users.index');

    // Profil Toolman
    Route::get('/profile', function () {
        return redirect()->route('profile.edit');
    })->name('profile');
});


// ==========================================
// 4. ROUTE PEMINJAM (GURU & SISWA)
// ==========================================
Route::prefix('peminjam')->name('peminjam.')->middleware(['auth', 'role:peminjam'])->group(function () {
    Route::get('/katalog', [PeminjamKatalogController::class, 'index'])->name('katalog.index');

    Route::get('/pengajuan/create', [PeminjamPengajuanController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan', [PeminjamPengajuanController::class, 'store'])->name('pengajuan.store');

    Route::get('/tiket', [PeminjamTiketController::class, 'index'])->name('tiket.index');
    Route::get('/tiket/{id}', [PeminjamTiketController::class, 'show'])->name('tiket.show');
    Route::post('/tiket/{id}/kembalikan', [PeminjamTiketController::class, 'ajukanPengembalian'])->name('tiket.kembalikan');

    Route::get('/profile', function () {
        return redirect()->route('profile.edit');
    })->name('profile');
});

// Route untuk halaman edit profil dengan deteksi role dinamis
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});
