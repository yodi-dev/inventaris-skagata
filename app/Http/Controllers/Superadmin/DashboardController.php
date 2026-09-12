<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Stat Cards ─────────────────────────────────────────────────────
        // Total aset: jumlah seluruh stok_total semua barang inventaris
        $totalAset = Barang::where('jenis_barang', 'inventaris')->sum('stok_total');

        // Sedang dipinjam: jumlah tiket dengan status active atau terlambat
        $sedangDipinjam = Peminjaman::whereIn('status', ['active', 'terlambat'])->count();

        // Kondisi rusak: jumlah seluruh stok_rusak semua barang inventaris
        $kondisiRusak = Barang::where('jenis_barang', 'inventaris')->sum('stok_rusak');

        // Stok BHP menipis: jumlah barang BHP dengan stok_tersedia <= minimum_stok
        $stokBhpMenipis = Barang::where('jenis_barang', 'bhp')
            ->whereColumn('stok_tersedia', '<=', 'minimum_stok')
            ->count();

        // ── Kondisi Inventaris (persentase) ────────────────────────────────
        $totalUnit   = Barang::where('jenis_barang', 'inventaris')->sum('stok_total');
        $totalRusak  = Barang::where('jenis_barang', 'inventaris')->sum('stok_rusak');
        // Dipinjam dianggap "dalam peredaran" — sisanya adalah baik
        // Kategori: baik = tersedia + dipinjam, perlu perbaikan = rusak yang bisa diperbaiki (pakai 50% dari rusak sbg estimasi), afkir = selebihnya
        // Namun karena tidak ada data kondisi rusak detail, kita hitung: tersedia = baik, dipinjam = sedang dipakai, rusak = rusak
        $totalTersedia = Barang::where('jenis_barang', 'inventaris')->sum('stok_tersedia');
        $totalDipinjam = Barang::where('jenis_barang', 'inventaris')->sum('stok_dipinjam');

        // Persentase berdasarkan total unit
        if ($totalUnit > 0) {
            $pctBaik     = round((($totalTersedia + $totalDipinjam) / $totalUnit) * 100);
            $pctRusak    = round(($totalRusak / $totalUnit) * 100);
            // Sisa selisih pembulatan masuk ke "baik"
            $pctPerluPerbaikan = max(0, 100 - $pctBaik - $pctRusak);
        } else {
            $pctBaik           = 0;
            $pctPerluPerbaikan = 0;
            $pctRusak          = 0;
        }

        // ── Alert Stok BHP Menipis ─────────────────────────────────────────
        $alertStokBhp = Barang::where('jenis_barang', 'bhp')
            ->whereColumn('stok_tersedia', '<=', 'minimum_stok')
            ->with('bengkel')
            ->orderBy('stok_tersedia')
            ->get();

        // ── Barang Paling Sering Dipinjam (bulan ini) ─────────────────────
        $startOfMonth = Carbon::now()->startOfMonth();

        $topDipinjam = DetailPeminjaman::select('barang_id', DB::raw('SUM(jumlah) as total_dipinjam'))
            ->whereHas('peminjaman', function ($q) use ($startOfMonth) {
                $q->where('tanggal_pinjam', '>=', $startOfMonth)
                    ->whereNotIn('status', ['pending', 'ditolak']);
            })
            ->with(['barang.bengkel'])
            ->groupBy('barang_id')
            ->orderByDesc('total_dipinjam')
            ->limit(5)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalAset',
            'sedangDipinjam',
            'kondisiRusak',
            'stokBhpMenipis',
            'pctBaik',
            'pctPerluPerbaikan',
            'pctRusak',
            'alertStokBhp',
            'topDipinjam',
        ));
    }
}
