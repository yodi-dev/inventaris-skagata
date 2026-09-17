<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\Pengadaan;
use App\Models\DetailPengadaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengadaanController extends Controller
{
    /**
     * Tampilkan daftar pengajuan RAB dari seluruh bengkel.
     */
    public function index(Request $request)
    {
        $filterStatus = $request->query('status', '');
        $filterBengkel = $request->query('bengkel_id', '');
        $search = $request->query('search', '');

        // Base query for non-draft submissions (Waka only reviews submitted RABs)
        $baseQuery = Pengadaan::whereIn('status', ['pending', 'revisi', 'approved', 'rejected']);

        // KPI Calculations (global statistics for non-draft RABs)
        $totalRAB = (clone $baseQuery)->count();
        $totalPending = (clone $baseQuery)->where('status', 'pending')->count();
        $totalRevisi = (clone $baseQuery)->where('status', 'revisi')->count();
        $totalApproved = (clone $baseQuery)->where('status', 'approved')->count();
        $totalRejected = (clone $baseQuery)->where('status', 'rejected')->count();

        // Total anggaran disetujui (akumulasi nominal detail pengadaan berstatus approved)
        $totalAnggaranDisetujui = DetailPengadaan::whereHas('pengadaan', function ($q) {
            $q->where('status', 'approved');
        })->selectRaw('SUM(jumlah * harga_satuan) as total')->value('total') ?? 0;

        // Query with filters
        $query = Pengadaan::with(['bengkel', 'dibuatOleh', 'direviewOleh', 'detailPengadaans.barang'])
            ->whereIn('status', ['pending', 'revisi', 'approved', 'rejected']);

        if ($filterStatus && in_array($filterStatus, ['pending', 'revisi', 'approved', 'rejected'])) {
            $query->where('status', $filterStatus);
        }

        if ($filterBengkel) {
            $query->where('bengkel_id', $filterBengkel);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%")
                    ->orWhereHas('bengkel', function ($bq) use ($search) {
                        $bq->where('nama', 'like', "%{$search}%")
                           ->orWhere('kode', 'like', "%{$search}%");
                    })
                    ->orWhereHas('dibuatOleh', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('detailPengadaans', function ($dq) use ($search) {
                        $dq->where('nama_barang', 'like', "%{$search}%")
                           ->orWhere('spesifikasi', 'like', "%{$search}%");
                    });
            });
        }

        // Urutan: pending pertama, lalu revisi, approved, rejected. Kemudian waktu pengajuan terbaru.
        $query->orderByRaw("FIELD(status, 'pending', 'revisi', 'approved', 'rejected')")
            ->orderByDesc('diajukan_pada')
            ->orderByDesc('created_at');

        $pengadaans = $query->paginate(10)->withQueryString();
        $bengkels = Bengkel::orderBy('nama')->get();

        return view('superadmin.pengadaan.index', compact(
            'pengadaans',
            'bengkels',
            'filterStatus',
            'filterBengkel',
            'search',
            'totalRAB',
            'totalPending',
            'totalRevisi',
            'totalApproved',
            'totalRejected',
            'totalAnggaranDisetujui'
        ));
    }

    /**
     * Tampilkan detail lengkap pengajuan RAB untuk peninjauan mendalam.
     */
    public function show($id)
    {
        $pengadaan = Pengadaan::with(['bengkel', 'dibuatOleh', 'direviewOleh', 'detailPengadaans.barang'])
            ->whereIn('status', ['pending', 'revisi', 'approved', 'rejected'])
            ->findOrFail($id);

        $totalAnggaran = $pengadaan->detailPengadaans->sum(fn($d) => $d->jumlah * $d->harga_satuan);
        $totalItems = $pengadaan->detailPengadaans->sum('jumlah');
        $terbilang = trim($this->terbilang($totalAnggaran)) . ' Rupiah';

        return view('superadmin.pengadaan.show', compact('pengadaan', 'totalAnggaran', 'totalItems', 'terbilang'));
    }

    /**
     * Tampilkan pratinjau dokumen cetak resmi RAB (A4 Portrait).
     */
    public function print($id)
    {
        $pengadaan = Pengadaan::with(['bengkel', 'dibuatOleh', 'direviewOleh', 'detailPengadaans.barang'])
            ->whereIn('status', ['pending', 'revisi', 'approved', 'rejected'])
            ->findOrFail($id);

        $totalAnggaran = $pengadaan->detailPengadaans->sum(fn($d) => $d->jumlah * $d->harga_satuan);
        $totalItems = $pengadaan->detailPengadaans->sum('jumlah');
        $terbilang = trim($this->terbilang($totalAnggaran)) . ' Rupiah';

        return view('superadmin.pengadaan.print', compact('pengadaan', 'totalAnggaran', 'totalItems', 'terbilang'));
    }

    /**
     * Helper pembaca bilangan mata uang ke teks (terbilang).
     */
    private function terbilang($angka)
    {
        $angka = abs((int) $angka);
        $baca = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if ($angka < 12) {
            return ' ' . $baca[$angka];
        } elseif ($angka < 20) {
            return $this->terbilang($angka - 10) . ' Belas';
        } elseif ($angka < 100) {
            return $this->terbilang((int)($angka / 10)) . ' Puluh' . $this->terbilang($angka % 10);
        } elseif ($angka < 200) {
            return ' Seratus' . $this->terbilang($angka - 100);
        } elseif ($angka < 1000) {
            return $this->terbilang((int)($angka / 100)) . ' Ratus' . $this->terbilang($angka % 100);
        } elseif ($angka < 2000) {
            return ' Seribu' . $this->terbilang($angka - 1000);
        } elseif ($angka < 1000000) {
            return $this->terbilang((int)($angka / 1000)) . ' Ribu' . $this->terbilang($angka % 1000);
        } elseif ($angka < 1000000000) {
            return $this->terbilang((int)($angka / 1000000)) . ' Juta' . $this->terbilang($angka % 1000000);
        } elseif ($angka < 1000000000000) {
            return $this->terbilang((int)($angka / 1000000000)) . ' Miliar' . $this->terbilang(fmod($angka, 1000000000));
        }
        return '';
    }

    /**
     * Proses keputusan review Waka Sarpras (Approve, Revisi, Reject).
     */
    public function review(Request $request, $id)
    {
        $request->validate([
            'action'         => 'required|in:approved,revisi,rejected',
            'catatan_review' => 'nullable|string|max:2000',
        ]);

        // Catatan review wajib diisi jika status revisi atau rejected sesuai PRD
        if (in_array($request->action, ['revisi', 'rejected']) && empty(trim($request->catatan_review ?? ''))) {
            return back()->with('error', 'Catatan/alasan review wajib diisi apabila meminta revisi atau menolak pengajuan.')
                         ->withInput();
        }

        $pengadaan = Pengadaan::whereIn('status', ['pending', 'revisi', 'approved', 'rejected'])
            ->findOrFail($id);

        $user = $request->user() ?? Auth::user();
        $userId = $user ? $user->id : null;

        $pengadaan->update([
            'status'         => $request->action,
            'catatan_review' => $request->catatan_review,
            'direview_oleh'  => $userId,
            'direview_pada'  => now(),
        ]);

        $label = match ($request->action) {
            'approved' => 'disetujui',
            'revisi'   => 'dikembalikan untuk revisi',
            'rejected' => 'ditolak',
        };

        return back()->with('success', "Pengajuan RAB \"{$pengadaan->judul}\" berhasil {$label}.");
    }
}
