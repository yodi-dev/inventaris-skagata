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
     * Ekspor riwayat mutasi stok ke file CSV untuk pelaporan & audit.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = $this->buildMovementQuery($request, $bengkelId);
        $movements = $query->get();

        $bengkelSlug = $bengkel ? str_replace(' ', '_', strtolower($bengkel->nama)) : 'bengkel';
        $filename = "log_mutasi_{$bengkelSlug}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($movements) {
            $file = fopen('php://output', 'w');
            // Menambahkan UTF-8 BOM agar terbaca sempurna di Microsoft Excel
            fputs($file, "\xEF\xBB\xBF");

            // Header Kolom CSV
            fputcsv($file, [
                'No',
                'ID Log',
                'Tanggal',
                'Waktu',
                'Kode Barang',
                'Nama Barang',
                'Tipe Barang',
                'Jenis Mutasi',
                'Perubahan Qty',
                'Satuan',
                'Petugas / Pemroses',
                'Keterangan',
                'Referensi Transaksi',
            ]);

            $no = 1;
            foreach ($movements as $m) {
                $sign = match ($m->jenis) {
                    'stok_masuk', 'pengembalian_baik' => '+',
                    'peminjaman', 'bhp_keluar', 'barang_hilang' => '-',
                    default => '',
                };

                $jenisLabel = match ($m->jenis) {
                    'stok_masuk' => 'Stok Masuk',
                    'peminjaman' => 'Peminjaman',
                    'pengembalian_baik' => 'Pengembalian (Baik)',
                    'pengembalian_rusak' => 'Pengembalian (Rusak)',
                    'barang_hilang' => 'Barang Hilang',
                    'bhp_keluar' => 'BHP Keluar',
                    'perbaikan' => 'Perbaikan Alat',
                    'penyesuaian' => 'Penyesuaian Stok',
                    default => ucfirst(str_replace('_', ' ', $m->jenis)),
                };

                $refText = ($m->referensi_tipe && $m->referensi_id)
                    ? ucfirst($m->referensi_tipe) . " #{$m->referensi_id}"
                    : '-';

                fputcsv($file, [
                    $no++,
                    '#LOG-' . str_pad($m->id, 5, '0', STR_PAD_LEFT),
                    $m->created_at->format('Y-m-d'),
                    $m->created_at->format('H:i') . ' WIB',
                    $m->barang->kode_barang ?? '-',
                    $m->barang->nama ?? 'Barang Terhapus',
                    ($m->barang && $m->barang->jenis_barang === 'bhp') ? 'BHP' : 'Inventaris',
                    $jenisLabel,
                    $sign . $m->jumlah,
                    $m->barang->satuan ?? 'unit',
                    $m->user->name ?? 'Sistem Otomatis',
                    $m->keterangan ?? '-',
                    $refText,
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    /**
     * Membangun query builder untuk StockMovement dengan semua parameter filter.
     */
    protected function buildMovementQuery(Request $request, $bengkelId)
    {
        $query = StockMovement::with(['barang', 'user'])
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
