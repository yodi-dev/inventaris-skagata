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
    /**
     * Menampilkan dashboard Waka Sarpras (Superadmin) dengan data analitik riil dari database.
     */
    public function index()
    {
        // 1. Auto-update status: Sinkronisasi transaksi 'active' yang melewati batas kembali menjadi 'terlambat' (PRD 4.2)
        Peminjaman::whereIn('status', ['active', 'aktif'])
            ->whereNotNull('batas_kembali')
            ->where('batas_kembali', '<', Carbon::now())
            ->update(['status' => 'terlambat']);

        // 2. Stat Cards (PRD 3.4: stok_total = stok_tersedia + stok_dipinjam + stok_rusak)
        // Total Aset: Akumulasi seluruh stok_total barang inventaris dari seluruh bengkel
        $totalAset = (int) Barang::where('jenis_barang', 'inventaris')->sum('stok_total');

        // Sedang Dipinjam: Total unit fisik barang inventaris yang sedang dipinjam
        $sedangDipinjam = (int) Barang::where('jenis_barang', 'inventaris')->sum('stok_dipinjam');

        // Kondisi Rusak: Total unit fisik barang inventaris yang tercatat rusak
        $kondisiRusak = (int) Barang::where('jenis_barang', 'inventaris')->sum('stok_rusak');

        // Stok Bahan Menipis: Jumlah barang BHP dengan stok_tersedia <= minimum_stok
        $stokBhpMenipis = Barang::where('jenis_barang', 'bhp')
            ->whereColumn('stok_tersedia', '<=', 'minimum_stok')
            ->count();

        // 3. Kondisi Inventaris (Persentase)
        $totalUnit     = $totalAset;
        $totalTersedia = (int) Barang::where('jenis_barang', 'inventaris')->sum('stok_tersedia');
        $totalDipinjam = $sedangDipinjam;
        $totalRusak    = $kondisiRusak;

        if ($totalUnit > 0) {
            // Kondisi Baik: stok tersedia + stok sedang dipinjam (dalam kondisi baik & layak operasional)
            $pctBaik = round((($totalTersedia + $totalDipinjam) / $totalUnit) * 100);

            // Pembagian kondisi rusak: estimasi perlu perbaikan vs rusak berat/afkir
            $unitPerluPerbaikan = (int) ceil($totalRusak * 0.6); // 60% estimasi perbaikan
            $unitRusakBerat     = $totalRusak - $unitPerluPerbaikan;

            $pctPerluPerbaikan = round(($unitPerluPerbaikan / $totalUnit) * 100);
            $pctRusak          = round(($unitRusakBerat / $totalUnit) * 100);

            // Normalisasi pembulatan agar total persentase tepat 100%
            if ($totalRusak > 0 && ($pctPerluPerbaikan + $pctRusak) == 0) {
                $pctPerluPerbaikan = 1;
            }
            $pctBaik = max(0, 100 - $pctPerluPerbaikan - $pctRusak);
        } else {
            $pctBaik           = 0;
            $pctPerluPerbaikan = 0;
            $pctRusak          = 0;
        }

        // 4. Alert: Stok Bahan Habis Pakai Menipis (PRD 3.4 & 3.11)
        $alertStokBhp = Barang::where('jenis_barang', 'bhp')
            ->whereColumn('stok_tersedia', '<=', 'minimum_stok')
            ->with('bengkel')
            ->orderBy('stok_tersedia', 'asc')
            ->get();

        // 5. Barang Paling Sering Dipinjam Bulan Ini
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
            'topDipinjam'
        ));
    }
}
