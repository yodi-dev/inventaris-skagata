<?php

// use App\Http\Controllers\CategoryController;
// use App\Http\Controllers\DashboardController;
// use App\Http\Controllers\ItemController;
// use App\Http\Controllers\ProfileController;
// use App\Http\Controllers\RoomController;
// use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return redirect()->route('login');
// });

// Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware(['auth', 'role:admin'])->group(function () {
// Dasbor admin
// Route::get('/admin/dashboard', function () {
//     return view('dashboard');
// })->name('admin.dashboard');

//     Route::resource('categories', CategoryController::class);
//     Route::resource('rooms', RoomController::class);
//     Route::resource('items', ItemController::class);
// });

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// require __DIR__ . '/auth.php';



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


// ==========================================
// 2. ROUTE SUPER ADMIN (WAKA SARPRAS)
// ==========================================
Route::prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('superadmin.dashboard');
    })->name('dashboard');

    Route::get('/pengadaan', function () {
        return view('superadmin.pengadaan.index');
    })->name('pengadaan.index');

    // Laporan
    Route::get('/laporan/mutasi', function () {
        return view('superadmin.laporan.mutasi');
    })->name('laporan.mutasi');
    Route::get('/laporan/konsumsi', function () {
        return view('superadmin.laporan.konsumsi');
    })->name('laporan.konsumsi');

    // Master Data
    Route::get('/master/bengkel', function () {
        return view('superadmin.master.bengkel');
    })->name('master.bengkel');
    Route::get('/master/toolman', function () {
        return view('superadmin.master.toolman');
    })->name('master.toolman');
});


// ==========================================
// 3. ROUTE ADMIN BENGKEL (TOOLMAN)
// ==========================================
Route::prefix('toolman')->name('toolman.')->group(function () {
    Route::get('/dashboard', function () {
        return view('toolman.dashboard');
    })->name('dashboard');

    Route::get('/barang', function () {
        return view('toolman.barang.index');
    })->name('barang.index');

    // Sirkulasi
    Route::get('/sirkulasi/peminjaman', function () {
        return view('toolman.sirkulasi.peminjaman');
    })->name('sirkulasi.peminjaman');
    Route::get('/sirkulasi/pengembalian', function () {
        return view('toolman.sirkulasi.pengembalian');
    })->name('sirkulasi.pengembalian');

    // Pengadaan (RAB)
    Route::get('/pengadaan/create', function () {
        return view('toolman.pengadaan.create');
    })->name('pengadaan.create');

    // Manajemen Peminjam
    Route::get('/users', function () {
        return view('toolman.users.index');
    })->name('users.index');
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
});
