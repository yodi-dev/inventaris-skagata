<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MutasiController extends Controller
{
    /**
     * Tampilkan daftar riwayat mutasi stok dengan filter dan statistik ringkasan.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        // 1. Query Data Mutasi Terfilter
        $query = $this->buildMovementQuery($request, $bengkelId);
        $movements = $query->paginate(15)->withQueryString();

        // 2. Metrik Statistik Ringkasan Khusus Bengkel Ini
        $baseBengkelQuery = StockMovement::whereHas('barang', function ($q) use ($bengkelId) {
            $q->where('bengkel_id', $bengkelId);
        });

        $stats = [
            'total_records' => (clone $baseBengkelQuery)->count(),
            'total_masuk' => (clone $baseBengkelQuery)->where('jenis', 'stok_masuk')->sum('jumlah'),
            'total_keluar' => (clone $baseBengkelQuery)->whereIn('jenis', ['peminjaman', 'bhp_keluar'])->sum('jumlah'),
            'total_masalah' => (clone $baseBengkelQuery)->whereIn('jenis', ['pengembalian_rusak', 'barang_hilang'])->sum('jumlah'),
        ];

        return view('toolman.mutasi.index', compact('movements', 'bengkel', 'stats'));
    }

    /**
     * Ekspor riwayat mutasi stok ke file Excel (.xls) untuk pelaporan & audit formal.
     */
    public function export(Request $request)
    {
        return $this->exportExcel($request);
    }

    /**
     * Ekspor riwayat mutasi stok ke file Excel (.xls) dengan styling tabel penuh.
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = $this->buildMovementQuery($request, $bengkelId);
        $movements = $query->get();

        $bengkelSlug = $bengkel ? str_replace(' ', '_', strtolower($bengkel->nama)) : 'bengkel';
        $filename = "Laporan_Mutasi_{$bengkelSlug}_" . date('Ymd_His') . ".xls";

        $filters = $this->resolveFilterLabels($request);
        $totalRecords = $movements->count();
        $totalMasuk = $movements->whereIn('jenis', ['stok_masuk', 'pengembalian_baik'])->sum('jumlah');
        $totalKeluar = $movements->whereIn('jenis', ['peminjaman', 'bhp_keluar'])->sum('jumlah');
        $totalMasalah = $movements->whereIn('jenis', ['pengembalian_rusak', 'barang_hilang'])->sum('jumlah');

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->view('toolman.mutasi.export_excel', compact(
            'movements',
            'bengkel',
            'user',
            'totalRecords',
            'totalMasuk',
            'totalKeluar',
            'totalMasalah',
            'filters'
        ), 200, $headers);
    }

    /**
     * Tampilan cetak ramah printer (A4 Landscape) berstandar laporan resmi instansi.
     */
    public function print(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = $this->buildMovementQuery($request, $bengkelId);
        $movements = $query->get();

        $filters = $this->resolveFilterLabels($request);
        $totalRecords = $movements->count();
        $totalMasuk = $movements->whereIn('jenis', ['stok_masuk', 'pengembalian_baik'])->sum('jumlah');
        $totalKeluar = $movements->whereIn('jenis', ['peminjaman', 'bhp_keluar'])->sum('jumlah');
        $totalMasalah = $movements->whereIn('jenis', ['pengembalian_rusak', 'barang_hilang'])->sum('jumlah');

        return view('toolman.mutasi.print', compact(
            'movements',
            'bengkel',
            'user',
            'totalRecords',
            'totalMasuk',
            'totalKeluar',
            'totalMasalah',
            'filters'
        ));
    }

    /**
     * Menerjemahkan parameter request menjadi label teks yang rapi dan mudah dibaca.
     */
    protected function resolveFilterLabels(Request $request): array
    {
        $period = $request->input('period');
        if ($period === 'today') {
            $periodLabel = 'Hari Ini (' . today()->format('d/m/Y') . ')';
        } elseif ($period === 'week') {
            $periodLabel = '7 Hari Terakhir (' . now()->subDays(7)->format('d/m/Y') . ' s/d ' . now()->format('d/m/Y') . ')';
        } elseif ($period === 'month') {
            $periodLabel = '30 Hari Terakhir (' . now()->subDays(30)->format('d/m/Y') . ' s/d ' . now()->format('d/m/Y') . ')';
        } else {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            if ($startDate && $endDate) {
                $periodLabel = date('d/m/Y', strtotime($startDate)) . ' s/d ' . date('d/m/Y', strtotime($endDate));
            } elseif ($startDate) {
                $periodLabel = 'Sejak ' . date('d/m/Y', strtotime($startDate));
            } elseif ($endDate) {
                $periodLabel = 'Hingga ' . date('d/m/Y', strtotime($endDate));
            } else {
                $periodLabel = 'Semua Riwayat Terdata';
            }
        }

        $jenis = $request->input('jenis');
        $jenisLabel = match ($jenis) {
            'stok_masuk' => 'Stok Masuk (Pengadaan / Tambah)',
            'peminjaman' => 'Peminjaman Aset',
            'pengembalian_baik' => 'Pengembalian (Kondisi Baik)',
            'pengembalian_rusak' => 'Pengembalian (Kondisi Rusak)',
            'barang_hilang' => 'Barang Hilang (Penyusutan)',
            'bhp_keluar' => 'BHP Keluar (Konsumsi)',
            'perbaikan' => 'Perbaikan Alat',
            'penyesuaian' => 'Penyesuaian Stok (Opname)',
            default => 'Semua Jenis Mutasi',
        };

        $tipe = $request->input('tipe');
        $tipeLabel = match ($tipe) {
            'bhp', 'habis_pakai' => 'Bahan Habis Pakai (BHP)',
            'inventaris' => 'Barang Inventaris',
            default => 'Semua Tipe Aset (Inventaris & BHP)',
        };

        $search = trim($request->input('search', ''));

        return [
            'period' => $periodLabel,
            'jenis' => $jenisLabel,
            'tipe' => $tipeLabel,
            'search' => $search !== '' ? "\"{$search}\"" : '-',
        ];
    }

    /**
     * Membangun query builder untuk StockMovement dengan semua parameter filter.
     */
    protected function buildMovementQuery(Request $request, $bengkelId)
    {
        $query = StockMovement::with(['barang.lokasiPenyimpanan', 'user'])
            ->whereHas('barang', function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId);
            })
            ->latest('created_at');

        // Filter 1: Pencarian Teks
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('barang', function ($bq) use ($search) {
                    $bq->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                })
                ->orWhere('keterangan', 'like', "%{$search}%")
                ->orWhereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Filter 2: Jenis Mutasi
        if ($jenis = $request->input('jenis')) {
            $query->where('jenis', $jenis);
        }

        // Filter 3: Tipe Barang (inventaris / bhp)
        if ($tipe = $request->input('tipe')) {
            $dbTipe = ($tipe === 'habis_pakai' || $tipe === 'bhp') ? 'bhp' : 'inventaris';
            $query->whereHas('barang', function ($q) use ($dbTipe) {
                $q->where('jenis_barang', $dbTipe);
            });
        }

        // Filter 4: Rentang Waktu (Preset & Kustom)
        $period = $request->input('period');
        if ($period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($period === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', now()->subDays(30));
        } else {
            if ($startDate = $request->input('start_date')) {
                $query->whereDate('created_at', '>=', $startDate);
            }

            if ($endDate = $request->input('end_date')) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        return $query;
    }
}
