<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        // 1. Metrik Statistik Ringkas
        $sedangDipinjam = Barang::where('bengkel_id', $bengkelId)->sum('stok_dipinjam');
        $requestBaru = Peminjaman::where('bengkel_id', $bengkelId)
            ->where('status', 'menunggu_acc')
            ->count();
        $barangRusak = Barang::where('bengkel_id', $bengkelId)->sum('stok_rusak');
        $stokMenipis = Barang::where('bengkel_id', $bengkelId)
            ->whereColumn('stok_tersedia', '<=', 'minimum_stok')
            ->count();

        // 2. Jadwal Pengembalian Hari Ini / Sedang Dipinjam
        $jadwalPengembalian = Peminjaman::with(['user', 'detailPeminjamans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->whereIn('status', ['aktif', 'terlambat'])
            ->orderBy('batas_kembali')
            ->take(6)
            ->get();

        // 3. Peringatan Stok Habis Pakai & Limit
        $peringatanStok = Barang::where('bengkel_id', $bengkelId)
            ->whereColumn('stok_tersedia', '<=', 'minimum_stok')
            ->orderBy('stok_tersedia')
            ->take(6)
            ->get();

        return view('toolman.dashboard', compact(
            'bengkel',
            'sedangDipinjam',
            'requestBaru',
            'barangRusak',
            'stokMenipis',
            'jadwalPengembalian',
            'peringatanStok'
        ));
    }
}
