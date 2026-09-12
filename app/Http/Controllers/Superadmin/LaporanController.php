<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan mutasi sirkulasi aset inventaris & BHP.
     */
    public function mutasi(Request $request)
    {
        $bengkels = Bengkel::orderBy('nama', 'asc')->get();

        $query = $this->buildMutasiQuery($request);

        // Ringkasan KPI untuk data mutasi yang difilter
        $summaryMovements = (clone $query)->get();
        $totalRecords = $summaryMovements->count();
        $totalMasuk = $summaryMovements->whereIn('jenis', ['stok_masuk', 'pengembalian_baik'])->sum('jumlah');
        $totalKeluar = $summaryMovements->whereIn('jenis', ['peminjaman', 'bhp_keluar'])->sum('jumlah');
        $totalMasalah = $summaryMovements->whereIn('jenis', ['pengembalian_rusak', 'barang_hilang'])->sum('jumlah');

        $movements = $query->paginate(15)->withQueryString();

        return view('superadmin.laporan.mutasi', compact(
            'movements',
            'bengkels',
            'totalRecords',
            'totalMasuk',
            'totalKeluar',
            'totalMasalah'
        ));
    }

    /**
     * Ekspor data mutasi sirkulasi aset ke spreadsheet Excel (.xls).
     */
    public function exportMutasiExcel(Request $request)
    {
        $query = $this->buildMutasiQuery($request);
        $movements = $query->get();

        $selectedBengkel = null;
        if ($request->filled('bengkel')) {
            $selectedBengkel = Bengkel::find($request->bengkel);
        }

        $bengkelSlug = $selectedBengkel 
            ? str_replace([' ', '/', '\\'], '_', strtolower($selectedBengkel->nama)) 
            : 'semua_bengkel';
        $filename = "Laporan_Mutasi_Aset_{$bengkelSlug}_" . date('Ymd_His') . ".xls";

        $filters = $this->resolveFilterLabels($request);
        $totalRecords = $movements->count();
        $totalMasuk = $movements->whereIn('jenis', ['stok_masuk', 'pengembalian_baik'])->sum('jumlah');
        $totalKeluar = $movements->whereIn('jenis', ['peminjaman', 'bhp_keluar'])->sum('jumlah');
        $totalMasalah = $movements->whereIn('jenis', ['pengembalian_rusak', 'barang_hilang'])->sum('jumlah');

        $user = $request->user() ?? Auth::user();

        if ($request->boolean('download')) {
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->view('superadmin.laporan.mutasi_excel', compact(
                'movements',
                'selectedBengkel',
                'user',
                'totalRecords',
                'totalMasuk',
                'totalKeluar',
                'totalMasalah',
                'filters'
            ), 200, $headers);
        }

        return view('superadmin.laporan.mutasi_excel_preview', compact(
            'movements',
            'selectedBengkel',
            'user',
            'totalRecords',
            'totalMasuk',
            'totalKeluar',
            'totalMasalah',
            'filters'
        ));
    }

    /**
     * Tampilan pratinjau cetak dan ekspor PDF resmi laporan mutasi aset.
     */
    public function exportMutasiPdf(Request $request)
    {
        $query = $this->buildMutasiQuery($request);
        $movements = $query->get();

        $selectedBengkel = null;
        if ($request->filled('bengkel')) {
            $selectedBengkel = Bengkel::find($request->bengkel);
        }

        $filters = $this->resolveFilterLabels($request);
        $totalRecords = $movements->count();
        $totalMasuk = $movements->whereIn('jenis', ['stok_masuk', 'pengembalian_baik'])->sum('jumlah');
        $totalKeluar = $movements->whereIn('jenis', ['peminjaman', 'bhp_keluar'])->sum('jumlah');
        $totalMasalah = $movements->whereIn('jenis', ['pengembalian_rusak', 'barang_hilang'])->sum('jumlah');

        $user = $request->user() ?? Auth::user();

        return view('superadmin.laporan.mutasi_pdf', compact(
            'movements',
            'selectedBengkel',
            'user',
            'totalRecords',
            'totalMasuk',
            'totalKeluar',
            'totalMasalah',
            'filters'
        ));
    }

    /**
     * Menampilkan laporan konsumsi bahan habis pakai (BHP) seluruh bengkel.
     */
    public function konsumsi(Request $request)
    {
        $bengkels = Bengkel::orderBy('nama', 'asc')->get();

        $query = $this->buildKonsumsiQuery($request);

        // Ringkasan KPI untuk data yang difilter
        $summaryMovements = (clone $query)->get();
        $totalRecords = $summaryMovements->count();
        $totalQuantity = $summaryMovements->sum(fn($m) => abs($m->jumlah));
        $totalBarangVarian = $summaryMovements->pluck('barang_id')->unique()->count();
        
        $bengkelTerbanyakGroup = $summaryMovements->groupBy('barang.bengkel_id')
            ->sortByDesc(fn($group) => $group->sum(fn($m) => abs($m->jumlah)))
            ->first();
        $namaBengkelTerbanyak = $bengkelTerbanyakGroup ? ($bengkelTerbanyakGroup->first()?->barang?->bengkel?->nama ?? '-') : '-';

        $konsumsi = $query->paginate(15)->withQueryString();

        return view('superadmin.laporan.konsumsi', compact(
            'konsumsi',
            'bengkels',
            'totalRecords',
            'totalQuantity',
            'totalBarangVarian',
            'namaBengkelTerbanyak'
        ));
    }

    /**
     * Ekspor data konsumsi bahan habis pakai ke spreadsheet Excel (.xls).
     */
    public function exportExcel(Request $request)
    {
        $query = $this->buildKonsumsiQuery($request);
        $movements = $query->get();

        $selectedBengkel = null;
        if ($request->filled('bengkel')) {
            $selectedBengkel = Bengkel::find($request->bengkel);
        }

        $bengkelSlug = $selectedBengkel 
            ? str_replace([' ', '/', '\\'], '_', strtolower($selectedBengkel->nama)) 
            : 'semua_bengkel';
        $filename = "Laporan_Konsumsi_BHP_{$bengkelSlug}_" . date('Ymd_His') . ".xls";

        $filters = $this->resolveFilterLabels($request);
        $totalRecords = $movements->count();
        $totalQuantity = $movements->sum(fn($m) => abs($m->jumlah));
        $totalBarangVarian = $movements->pluck('barang_id')->unique()->count();

        $user = $request->user() ?? Auth::user();

        if ($request->boolean('download')) {
            $headers = [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Pragma' => 'no-cache',
                'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                'Expires' => '0',
            ];

            return response()->view('superadmin.laporan.konsumsi_excel', compact(
                'movements',
                'selectedBengkel',
                'user',
                'totalRecords',
                'totalQuantity',
                'totalBarangVarian',
                'filters'
            ), 200, $headers);
        }

        return view('superadmin.laporan.konsumsi_excel_preview', compact(
            'movements',
            'selectedBengkel',
            'user',
            'totalRecords',
            'totalQuantity',
            'totalBarangVarian',
            'filters'
        ));
    }

    /**
     * Tampilan pratinjau cetak dan ekspor PDF resmi laporan konsumsi BHP.
     */
    public function exportPdf(Request $request)
    {
        $query = $this->buildKonsumsiQuery($request);
        $movements = $query->get();

        $selectedBengkel = null;
        if ($request->filled('bengkel')) {
            $selectedBengkel = Bengkel::find($request->bengkel);
        }

        $filters = $this->resolveFilterLabels($request);
        $totalRecords = $movements->count();
        $totalQuantity = $movements->sum(fn($m) => abs($m->jumlah));
        $totalBarangVarian = $movements->pluck('barang_id')->unique()->count();

        $user = $request->user() ?? Auth::user();

        return view('superadmin.laporan.konsumsi_pdf', compact(
            'movements',
            'selectedBengkel',
            'user',
            'totalRecords',
            'totalQuantity',
            'totalBarangVarian',
            'filters'
        ));
    }

    /**
     * Membangun query dasar untuk penarikan riwayat mutasi aset & stok.
     */
    protected function buildMutasiQuery(Request $request)
    {
        $query = StockMovement::with(['barang.bengkel', 'barang.lokasiPenyimpanan', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('bengkel')) {
            $bengkelId = $request->bengkel;
            $query->whereHas('barang', function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId);
            });
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('barang', function ($b) use ($search) {
                    $b->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                })
                ->orWhere('keterangan', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $query;
    }

    /**
     * Membangun query dasar untuk penarikan riwayat konsumsi BHP.
     */
    protected function buildKonsumsiQuery(Request $request)
    {
        $query = StockMovement::with(['barang.bengkel', 'barang.lokasiPenyimpanan', 'user'])
            ->where('jenis', 'bhp_keluar')
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('bengkel')) {
            $bengkelId = $request->bengkel;
            $query->whereHas('barang', function ($q) use ($bengkelId) {
                $q->where('bengkel_id', $bengkelId);
            });
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('barang', function ($b) use ($search) {
                    $b->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode_barang', 'like', "%{$search}%");
                })
                ->orWhere('keterangan', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                });
            });
        }

        return $query;
    }

    /**
     * Mengubah parameter filter menjadi label deskriptif untuk dokumen cetak / ekspor.
     */
    protected function resolveFilterLabels(Request $request): array
    {
        $bengkelNama = 'Semua Bengkel / Kejuruan';
        if ($request->filled('bengkel')) {
            $b = Bengkel::find($request->bengkel);
            if ($b) {
                $bengkelNama = $b->nama . " ({$b->kode})";
            }
        }

        $periode = 'Seluruh Waktu';
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $periode = date('d/m/Y', strtotime($request->start_date)) . ' s/d ' . date('d/m/Y', strtotime($request->end_date));
        } elseif ($request->filled('start_date')) {
            $periode = 'Mulai ' . date('d/m/Y', strtotime($request->start_date));
        } elseif ($request->filled('end_date')) {
            $periode = 'Hingga ' . date('d/m/Y', strtotime($request->end_date));
        }

        $jenisLabel = 'Semua Jenis Mutasi';
        if ($request->filled('jenis')) {
            $jenisMap = [
                'stok_masuk' => 'Barang Masuk Baru',
                'peminjaman' => 'Peminjaman',
                'pengembalian_baik' => 'Kembali (Kondisi Baik)',
                'pengembalian_rusak' => 'Kembali (Kondisi Rusak)',
                'barang_hilang' => 'Barang Hilang',
                'bhp_keluar' => 'BHP Digunakan',
                'perbaikan' => 'Perbaikan / Servis',
                'penyesuaian' => 'Penyesuaian Stok',
            ];
            $jenisLabel = $jenisMap[$request->jenis] ?? ucfirst(str_replace('_', ' ', $request->jenis));
        }

        return [
            'bengkel' => $bengkelNama,
            'periode' => $periode,
            'jenis' => $jenisLabel,
            'search' => $request->filled('search') ? $request->search : null,
        ];
    }
}
