<?php

use Illuminate\Support\Facades\Route;

// Halaman awal langsung arahkan ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// 1. ROUTE AUTHENTICATION (PROTOTYPE)
// ==========================================
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/logout', function () {
    return redirect()->route('login');
})->name('logout');


// ==========================================
// 2. ROUTE SUPER ADMIN (WAKA SARPRAS)
// ==========================================
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('dashboard');

    // Pengadaan (RAB)
    Route::get('/pengadaan', function () {
        return view('superadmin.pengadaan.index');
    })->name('pengadaan.index');

    Route::get('/pengadaan/{id}', function ($id) {
        return view('superadmin.pengadaan.show', compact('id'));
    })->name('pengadaan.show');

    // Laporan
    Route::get('/laporan/mutasi', function () {
        return view('superadmin.laporan.mutasi');
    })->name('laporan.mutasi');

    Route::get('/laporan/konsumsi', function () {
        return view('superadmin.laporan.konsumsi');
    })->name('laporan.konsumsi');

    // Master Data Bengkel
    Route::get('/bengkel', function () {
        return view('superadmin.bengkel.index');
    })->name('bengkel.index');

    Route::get('/bengkel/create', function () {
        return view('superadmin.bengkel.create');
    })->name('bengkel.create');

    Route::get('/bengkel/edit', function () {
        return view('superadmin.bengkel.edit');
    })->name('bengkel.edit');

    // Master Data Toolman
    Route::get('/toolman', function () {
        return view('superadmin.toolman.index');
    })->name('toolman.index');

    Route::get('/toolman/create', function () {
        return view('superadmin.toolman.create');
    })->name('toolman.create');

    Route::get('/toolman/edit', function () {
        return view('superadmin.toolman.edit');
    })->name('toolman.edit');

    // Backward-compatible aliases for legacy master routes
    Route::get('/master/bengkel', function () {
        return view('superadmin.bengkel.index');
    })->name('master.bengkel');
    Route::get('/master/bengkel/create', function () {
        return view('superadmin.bengkel.create');
    })->name('master.bengkel.create');
    Route::get('/master/bengkel/edit', function () {
        return view('superadmin.bengkel.edit');
    })->name('master.bengkel.edit');
    Route::get('/master/toolman', function () {
        return view('superadmin.toolman.index');
    })->name('master.toolman');
    Route::get('/master/toolman/create', function () {
        return view('superadmin.toolman.create');
    })->name('master.toolman.create');
    Route::get('/master/toolman/edit', function () {
        return view('superadmin.toolman.edit');
    })->name('master.toolman.edit');

    // Profil Waka Sarpras
    Route::get('/profile', function () {
        return redirect()->route('profile.edit', ['role' => 'superadmin']);
    })->name('profile');
});


// ==========================================
// 3. ROUTE ADMIN BENGKEL (TOOLMAN)
// ==========================================
Route::prefix('toolman')->name('toolman.')->group(function () {
    Route::get('/dashboard', function () {
        return view('toolman.dashboard');
    })->name('dashboard');

    // Manajemen Barang
    Route::get('/barang', function () {
        return view('toolman.barang.index');
    })->name('barang.index');

    Route::get('/barang/create', function () {
        return view('toolman.barang.create');
    })->name('barang.create');

    Route::get('/barang/edit', function () {
        return view('toolman.barang.edit');
    })->name('barang.edit');

    // Sirkulasi Peminjaman
    Route::get('/peminjaman', function () {
        return view('toolman.peminjaman.index');
    })->name('peminjaman.index');

    Route::get('/peminjaman/{id}', function ($id) {
        return view('toolman.peminjaman.show', compact('id'));
    })->name('peminjaman.show');

    // Sirkulasi Pengembalian
    Route::get('/pengembalian', function () {
        return view('toolman.pengembalian.index');
    })->name('pengembalian.index');

    Route::get('/pengembalian/{id}/check', function ($id) {
        return view('toolman.pengembalian.check', compact('id'));
    })->name('pengembalian.check');

    // Pengadaan (RAB)
    Route::get('/pengadaan', function () {
        return view('toolman.pengadaan.index');
    })->name('pengadaan.index');

    Route::get('/pengadaan/create', function () {
        return view('toolman.pengadaan.create');
    })->name('pengadaan.create');

    Route::get('/pengadaan/{id}', function ($id) {
        return view('toolman.pengadaan.show', compact('id'));
    })->name('pengadaan.show');

    Route::get('/pengadaan/{id}/edit', function ($id) {
        return view('toolman.pengadaan.edit', compact('id'));
    })->name('pengadaan.edit');

    // Manajemen Peminjam
    Route::get('/peminjam', function () {
        return view('toolman.peminjam.index');
    })->name('peminjam.index');

    Route::get('/peminjam/{id}', function ($id) {
        return view('toolman.peminjam.show', compact('id'));
    })->name('peminjam.show');

    // Riwayat Stok / Mutasi
    Route::get('/mutasi', function () {
        return view('toolman.mutasi.index');
    })->name('mutasi.index');

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
        return redirect()->route('profile.edit', ['role' => 'toolman']);
    })->name('profile');
});


// ==========================================
// 4. ROUTE PEMINJAM (GURU & SISWA)
// ==========================================
Route::prefix('peminjam')->name('peminjam.')->group(function () {
    Route::get('/katalog', function () {
        return view('peminjam.katalog.index');
    })->name('katalog.index');

    Route::get('/pengajuan/create', function () {
        return view('peminjam.pengajuan.create');
    })->name('pengajuan.create');

    Route::get('/tiket', function () {
        return view('peminjam.tiket.index');
    })->name('tiket.index');

    Route::get('/tiket/{id}', function ($id) {
        return view('peminjam.tiket.show', compact('id'));
    })->name('tiket.show');

    Route::get('/profile', function () {
        return redirect()->route('profile.edit', ['role' => 'peminjam']);
    })->name('profile');
});

// Route untuk halaman edit profil dengan deteksi role dinamis
Route::get('/profile', function () {
    $role = request('role');
    if (!$role) {
        $referer = request()->header('referer') ?? '';
        if (str_contains($referer, 'superadmin')) {
            $role = 'superadmin';
        } elseif (str_contains($referer, 'toolman')) {
            $role = 'toolman';
        } else {
            $role = 'peminjam';
        }
    }
    return view('profile.edit', compact('role'));
})->name('profile.edit');
