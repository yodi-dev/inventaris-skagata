<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = Barang::with(['lokasiPenyimpanan', 'bengkel'])
            ->where('bengkel_id', $bengkelId);

        // Pencarian Nama / Kode Barang
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        // Filter Tipe Barang
        if ($jenis = $request->input('jenis')) {
            if (in_array($jenis, ['inventaris', 'bhp'])) {
                $query->where('jenis_barang', $jenis);
            }
        }

        // Filter Status Barang
        if ($status = $request->input('status')) {
            if ($status === 'tersedia') {
                $query->where('stok_tersedia', '>', 0);
            } elseif ($status === 'dipinjam') {
                $query->where('stok_dipinjam', '>', 0);
            } elseif ($status === 'rusak') {
                $query->where('stok_rusak', '>', 0);
            } elseif ($status === 'limit') {
                $query->whereColumn('stok_tersedia', '<=', 'minimum_stok');
            }
        }

        $barangs = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('toolman.barang.index', compact('barangs', 'bengkel'));
    }

    public function create()
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);
        $lokasiPenyimpanans = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->get();

        return view('toolman.barang.create', compact('bengkel', 'lokasiPenyimpanans'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        // Validasi input
        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barangs', 'kode_barang')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                }),
            ],
            'nama_barang' => 'required|string|max:255',
            'tipe' => 'required|in:inventaris,bahan,bhp',
            'lokasi_penyimpanan_id' => [
                'required',
                Rule::exists('lokasi_penyimpanans', 'id')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                }),
            ],
            'satuan' => 'required|string|max:50',
            'spesifikasi' => 'nullable|string',
            // Field khusus inventaris
            'stok_baik' => 'nullable|integer|min:0',
            'stok_rusak_ringan' => 'nullable|integer|min:0',
            'stok_rusak_berat' => 'nullable|integer|min:0',
            // Field khusus bahan habis pakai
            'stok_bahan' => 'nullable|numeric|min:0',
            'batas_minimum' => 'nullable|numeric|min:0',
        ], [
            'kode_barang.unique' => 'Kode barang sudah digunakan di bengkel ini.',
            'lokasi_penyimpanan_id.exists' => 'Lokasi penyimpanan tidak valid untuk bengkel Anda.',
        ]);

        $jenisBarang = ($request->input('tipe') === 'bahan' || $request->input('tipe') === 'bhp') ? 'bhp' : 'inventaris';

        return DB::transaction(function () use ($request, $user, $bengkelId, $jenisBarang) {
            if ($jenisBarang === 'inventaris') {
                $stokBaik = (int) $request->input('stok_baik', 0);
                $stokRusakRingan = (int) $request->input('stok_rusak_ringan', 0);
                $stokRusakBerat = (int) $request->input('stok_rusak_berat', 0);
                $stokRusak = $stokRusakRingan + $stokRusakBerat;

                $stokTersedia = $stokBaik;
                $stokDipinjam = 0;
                $stokTotal = $stokTersedia + $stokDipinjam + $stokRusak;
                $minimumStok = (int) $request->input('batas_minimum', 1);
            } else {
                $stokBahan = (int) $request->input('stok_bahan', 0);
                $stokTersedia = $stokBahan;
                $stokDipinjam = 0;
                $stokRusak = 0;
                $stokTotal = $stokBahan;
                $minimumStok = (int) $request->input('batas_minimum', 0);
            }

            $barang = Barang::create([
                'bengkel_id' => $bengkelId,
                'lokasi_penyimpanan_id' => $request->input('lokasi_penyimpanan_id'),
                'kode_barang' => strtoupper(trim($request->input('kode_barang'))),
                'nama' => trim($request->input('nama_barang')),
                'jenis_barang' => $jenisBarang,
                'satuan' => trim($request->input('satuan')),
                'stok_total' => $stokTotal,
                'stok_tersedia' => $stokTersedia,
                'stok_dipinjam' => $stokDipinjam,
                'stok_rusak' => $stokRusak,
                'minimum_stok' => $minimumStok,
                'deskripsi' => $request->input('spesifikasi'),
            ]);

            // Catat riwayat stok awal ke stock_movements jika stok > 0
            if ($stokTotal > 0) {
                StockMovement::create([
                    'barang_id' => $barang->id,
                    'user_id' => $user->id,
                    'jenis' => 'stok_masuk',
                    'jumlah' => $stokTotal,
                    'keterangan' => 'Pendaftaran stok awal barang baru oleh Toolman',
                ]);
            }

            if ($request->input('action') === 'save_and_add') {
                return redirect()->route('toolman.barang.create')
                    ->with('success', "Barang {$barang->nama} ({$barang->kode_barang}) berhasil disimpan! Silakan input barang selanjutnya.");
            }

            return redirect()->route('toolman.barang.index')
                ->with('success', "Barang {$barang->nama} ({$barang->kode_barang}) berhasil ditambahkan ke inventaris bengkel.");
        });
    }

    public function edit($id = null)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $barang = null;
        if ($id) {
            $barang = Barang::with(['lokasiPenyimpanan', 'detailPeminjamans' => function ($q) {
                $q->whereHas('peminjaman', function ($p) {
                    $p->whereIn('status', ['active', 'terlambat']);
                })->with('peminjaman.user');
            }])
                ->where('bengkel_id', $bengkelId)
                ->findOrFail($id);
        } else {
            $barang = Barang::with('lokasiPenyimpanan')
                ->where('bengkel_id', $bengkelId)
                ->firstOrFail();
        }

        $lokasiPenyimpanans = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->get();

        return view('toolman.barang.edit', compact('barang', 'bengkel', 'lokasiPenyimpanans'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $barang = Barang::where('bengkel_id', $bengkelId)->findOrFail($id);

        $validated = $request->validate([
            'kode_barang' => [
                'required',
                'string',
                'max:50',
                Rule::unique('barangs', 'kode_barang')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                })->ignore($barang->id),
            ],
            'nama_barang' => 'required|string|max:255',
            'lokasi_penyimpanan_id' => [
                'required',
                Rule::exists('lokasi_penyimpanans', 'id')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                }),
            ],
            'satuan' => 'required|string|max:50',
            'spesifikasi' => 'nullable|string',
            'stok_baik' => 'nullable|integer|min:0',
            'stok_rusak_ringan' => 'nullable|integer|min:0',
            'stok_rusak_berat' => 'nullable|integer|min:0',
            'stok_bahan' => 'nullable|numeric|min:0',
            'batas_minimum' => 'nullable|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request, $barang, $user) {
            $oldStokTotal = $barang->stok_total;

            if ($barang->jenis_barang === 'inventaris') {
                $stokBaik = (int) $request->input('stok_baik', $barang->stok_tersedia);
                $stokRusakRingan = (int) $request->input('stok_rusak_ringan', 0);
                $stokRusakBerat = (int) $request->input('stok_rusak_berat', 0);
                $stokRusak = $stokRusakRingan + $stokRusakBerat;

                $stokTersedia = $stokBaik;
                $stokDipinjam = $barang->stok_dipinjam;
                $stokTotal = $stokTersedia + $stokDipinjam + $stokRusak;
                $minimumStok = (int) $request->input('batas_minimum', $barang->minimum_stok);
            } else {
                $stokBahan = (int) $request->input('stok_bahan', $barang->stok_tersedia);
                $stokTersedia = $stokBahan;
                $stokDipinjam = 0;
                $stokRusak = 0;
                $stokTotal = $stokBahan;
                $minimumStok = (int) $request->input('batas_minimum', $barang->minimum_stok);
            }

            $barang->update([
                'lokasi_penyimpanan_id' => $request->input('lokasi_penyimpanan_id'),
                'kode_barang' => strtoupper(trim($request->input('kode_barang'))),
                'nama' => trim($request->input('nama_barang')),
                'satuan' => trim($request->input('satuan')),
                'stok_total' => $stokTotal,
                'stok_tersedia' => $stokTersedia,
                'stok_rusak' => $stokRusak,
                'minimum_stok' => $minimumStok,
                'deskripsi' => $request->input('spesifikasi'),
            ]);

            // Jika ada perubahan pada total stok, catat penyesuaian
            if ($oldStokTotal !== $stokTotal) {
                $selisih = $stokTotal - $oldStokTotal;
                StockMovement::create([
                    'barang_id' => $barang->id,
                    'user_id' => $user->id,
                    'jenis' => 'penyesuaian',
                    'jumlah' => abs($selisih),
                    'keterangan' => 'Penyesuaian stok master oleh Toolman (' . ($selisih > 0 ? "+{$selisih}" : "{$selisih}") . ')',
                ]);
            }

            return redirect()->route('toolman.barang.index')
                ->with('success', "Data barang {$barang->nama} ({$barang->kode_barang}) berhasil diperbarui.");
        });
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $barang = Barang::where('bengkel_id', $bengkelId)->findOrFail($id);

        // PRD Rule: Barang yang sedang dipinjam tidak boleh dihapus
        if ($barang->stok_dipinjam > 0) {
            return back()->with('error', "Barang {$barang->nama} tidak dapat dihapus karena saat ini terdapat {$barang->stok_dipinjam} {$barang->satuan} yang sedang aktif dipinjam!");
        }

        // PRD Rule: Cek relasi tiket peminjaman aktif / menunggu
        $hasActiveLoans = $barang->detailPeminjamans()
            ->whereHas('peminjaman', function ($q) {
                $q->whereIn('status', ['pending', 'active', 'terlambat', 'menunggu_pengecekan']);
            })->exists();

        if ($hasActiveLoans) {
            return back()->with('error', "Barang {$barang->nama} tidak dapat dihapus karena masih terhubung dengan antrean/transaksi peminjaman yang belum selesai!");
        }

        $nama = $barang->nama;
        $kode = $barang->kode_barang;

        $barang->delete();

        return redirect()->route('toolman.barang.index')
            ->with('success', "Barang {$nama} ({$kode}) berhasil dihapus dari inventaris bengkel.");
    }
}
