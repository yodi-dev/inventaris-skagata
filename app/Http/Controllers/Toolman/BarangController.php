<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use App\Models\StockMovement;
use App\Models\SumberDana;
use App\Models\Satuan;
use App\Http\Controllers\Toolman\SumberDanaController;
use App\Http\Controllers\Toolman\SatuanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);

        SumberDanaController::ensureSchemaReady();

        $query = Barang::with(['lokasiPenyimpanan', 'bengkel', 'sumberDana'])
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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);
        $lokasiPenyimpanans = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->get();

        SumberDanaController::ensureSchemaReady();
        $sumberDanas = SumberDana::withCount('barangs')->orderBy('nama', 'asc')->get();

        SatuanController::ensureDefaults();
        $satuans = Satuan::orderBy('nama', 'asc')->get();

        return view('toolman.barang.create', compact('bengkel', 'lokasiPenyimpanans', 'sumberDanas', 'satuans'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }

        SumberDanaController::ensureSchemaReady();

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
            'sumber_dana_id' => 'nullable|exists:sumber_danas,id',
            'satuan' => 'required|string|max:50',
            'estimasi_harga' => 'nullable|numeric|min:0',
            'harga' => 'nullable|numeric|min:0',
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
            'sumber_dana_id.exists' => 'Sumber dana yang dipilih tidak valid.',
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
                'sumber_dana_id' => $request->input('sumber_dana_id'),
                'kode_barang' => strtoupper(trim($request->input('kode_barang'))),
                'nama' => trim($request->input('nama_barang')),
                'jenis_barang' => $jenisBarang,
                'satuan' => trim($request->input('satuan')),
                'harga' => $request->input('estimasi_harga') ?? $request->input('harga') ?? 0,
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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);

        SumberDanaController::ensureSchemaReady();

        $barang = null;
        if ($id) {
            $barang = Barang::with(['lokasiPenyimpanan', 'sumberDana', 'detailPeminjamans' => function ($q) {
                $q->whereHas('peminjaman', function ($p) {
                    $p->whereIn('status', ['active', 'terlambat']);
                })->with('peminjaman.user');
            }])
                ->where('bengkel_id', $bengkelId)
                ->findOrFail($id);
        } else {
            $barang = Barang::with(['lokasiPenyimpanan', 'sumberDana'])
                ->where('bengkel_id', $bengkelId)
                ->firstOrFail();
        }

        $lokasiPenyimpanans = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->get();
        $sumberDanas = SumberDana::withCount('barangs')->orderBy('nama', 'asc')->get();

        SatuanController::ensureDefaults();
        $satuans = Satuan::orderBy('nama', 'asc')->get();

        return view('toolman.barang.edit', compact('barang', 'bengkel', 'lokasiPenyimpanans', 'sumberDanas', 'satuans'));
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }

        SumberDanaController::ensureSchemaReady();

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
            'sumber_dana_id' => 'nullable|exists:sumber_danas,id',
            'satuan' => 'required|string|max:50',
            'estimasi_harga' => 'nullable|numeric|min:0',
            'harga' => 'nullable|numeric|min:0',
            'spesifikasi' => 'nullable|string',
            'stok_baik' => 'nullable|integer|min:0',
            'stok_rusak_ringan' => 'nullable|integer|min:0',
            'stok_rusak_berat' => 'nullable|integer|min:0',
            'stok_bahan' => 'nullable|numeric|min:0',
            'batas_minimum' => 'nullable|numeric|min:0',
        ], [
            'sumber_dana_id.exists' => 'Sumber dana yang dipilih tidak valid.',
        ]);

        return DB::transaction(function () use ($request, $barang, $user) {
            $oldStokTotal = (int) $barang->stok_total;
            $oldStokTersedia = (int) $barang->stok_tersedia;
            $oldStokRusak = (int) $barang->stok_rusak;

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
                'sumber_dana_id' => $request->input('sumber_dana_id'),
                'kode_barang' => strtoupper(trim($request->input('kode_barang'))),
                'nama' => trim($request->input('nama_barang')),
                'satuan' => trim($request->input('satuan')),
                'harga' => $request->input('estimasi_harga') ?? $request->input('harga') ?? 0,
                'stok_total' => $stokTotal,
                'stok_tersedia' => $stokTersedia,
                'stok_rusak' => $stokRusak,
                'minimum_stok' => $minimumStok,
                'deskripsi' => $request->input('spesifikasi'),
            ]);

            // Periksa perubahan stok per kompartemen (tersedia vs rusak)
            $diffTersedia = $stokTersedia - $oldStokTersedia;
            $diffRusak = $stokRusak - $oldStokRusak;

            if ($barang->jenis_barang === 'inventaris') {
                // Evaluasi apakah terjadi transfer antar kondisi (berlawanan tanda)
                if ($diffTersedia > 0 && $diffRusak < 0) {
                    // Kasus Perbaikan: sebagian/seluruh stok rusak dialihkan ke tersedia
                    $repaired = min($diffTersedia, abs($diffRusak));
                    $extraTersedia = $diffTersedia - $repaired;
                    $discardedRusak = abs($diffRusak) - $repaired;

                    StockMovement::create([
                        'barang_id' => $barang->id,
                        'user_id' => $user->id,
                        'jenis' => 'perbaikan',
                        'jumlah' => $repaired,
                        'referensi_tipe' => 'alih_kondisi_baik',
                        'keterangan' => "Perbaikan alat: Pengalihan kondisi dari rusak ke baik (+{$repaired} baik, -{$repaired} rusak)",
                    ]);

                    if ($extraTersedia > 0) {
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'penyesuaian',
                            'jumlah' => $extraTersedia,
                            'referensi_tipe' => 'penyesuaian_tambah',
                            'keterangan' => "Penyesuaian stok master oleh Toolman (+{$extraTersedia})",
                        ]);
                    }

                    if ($discardedRusak > 0) {
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'penyesuaian',
                            'jumlah' => $discardedRusak,
                            'referensi_tipe' => 'penyesuaian_rusak_kurang',
                            'keterangan' => "Penghapusan/afkir barang rusak oleh Toolman (-{$discardedRusak} rusak)",
                        ]);
                    }
                } elseif ($diffTersedia < 0 && $diffRusak > 0) {
                    // Kasus Kerusakan: sebagian/seluruh pengurangan tersedia dialihkan menjadi rusak
                    $transferredDamaged = min(abs($diffTersedia), $diffRusak);
                    $lostAvailable = abs($diffTersedia) - $transferredDamaged;
                    $extraDamaged = $diffRusak - $transferredDamaged;

                    StockMovement::create([
                        'barang_id' => $barang->id,
                        'user_id' => $user->id,
                        'jenis' => 'penyesuaian',
                        'jumlah' => $transferredDamaged,
                        'referensi_tipe' => 'alih_kondisi_rusak',
                        'keterangan' => "Pengalihan kondisi stok: Baik ke Rusak (-{$transferredDamaged} baik, +{$transferredDamaged} rusak)",
                    ]);

                    if ($lostAvailable > 0) {
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'penyesuaian',
                            'jumlah' => $lostAvailable,
                            'referensi_tipe' => 'penyesuaian_kurang',
                            'keterangan' => "Penyesuaian stok master oleh Toolman (-{$lostAvailable})",
                        ]);
                    }

                    if ($extraDamaged > 0) {
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'penyesuaian',
                            'jumlah' => $extraDamaged,
                            'referensi_tipe' => 'penyesuaian_rusak_tambah',
                            'keterangan' => "Penyesuaian fisik barang rusak oleh Toolman (+{$extraDamaged} rusak)",
                        ]);
                    }
                } else {
                    // Tanda sama atau salah satu bernilai 0 (tidak ada alih kondisi langsung)
                    if ($diffTersedia !== 0) {
                        $isNegTersedia = $diffTersedia < 0;
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'penyesuaian',
                            'jumlah' => abs($diffTersedia),
                            'referensi_tipe' => $isNegTersedia ? 'penyesuaian_kurang' : 'penyesuaian_tambah',
                            'keterangan' => 'Penyesuaian stok master oleh Toolman (' . ($diffTersedia > 0 ? "+{$diffTersedia}" : "{$diffTersedia}") . ')',
                        ]);
                    }

                    if ($diffRusak !== 0) {
                        $isNegRusak = $diffRusak < 0;
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'penyesuaian',
                            'jumlah' => abs($diffRusak),
                            'referensi_tipe' => $isNegRusak ? 'penyesuaian_rusak_kurang' : 'penyesuaian_rusak_tambah',
                            'keterangan' => $isNegRusak
                                ? 'Penghapusan/afkir barang rusak oleh Toolman (-' . abs($diffRusak) . ' rusak)'
                                : "Penyesuaian fisik barang rusak oleh Toolman (+{$diffRusak} rusak)",
                        ]);
                    }
                }
            } else {
                // Tipe BHP/Bahan (hanya stok tersedia)
                if ($diffTersedia !== 0) {
                    $isNegTersedia = $diffTersedia < 0;
                    StockMovement::create([
                        'barang_id' => $barang->id,
                        'user_id' => $user->id,
                        'jenis' => 'penyesuaian',
                        'jumlah' => abs($diffTersedia),
                        'referensi_tipe' => $isNegTersedia ? 'penyesuaian_kurang' : 'penyesuaian_tambah',
                        'keterangan' => 'Penyesuaian stok master oleh Toolman (' . ($diffTersedia > 0 ? "+{$diffTersedia}" : "{$diffTersedia}") . ')',
                    ]);
                }
            }

            return redirect()->route('toolman.barang.index')
                ->with('success', "Data barang {$barang->nama} ({$barang->kode_barang}) berhasil diperbarui.");
        });
    }

    public function destroy($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }

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

        // Pengecekan integritas data: cegah penghapusan barang yang memiliki rekam jejak historis
        if ($barang->detailPeminjamans()->exists()) {
            return back()->with('error', "Barang {$barang->nama} ({$barang->kode_barang}) tidak dapat dihapus karena memiliki riwayat transaksi peminjaman di sistem!");
        }

        if ($barang->stockMovements()->exists()) {
            return back()->with('error', "Barang {$barang->nama} ({$barang->kode_barang}) tidak dapat dihapus karena memiliki riwayat mutasi kartu stok!");
        }

        if ($barang->detailPengadaans()->exists()) {
            return back()->with('error', "Barang {$barang->nama} ({$barang->kode_barang}) tidak dapat dihapus karena terhubung dengan riwayat usulan pengadaan (RAB)!");
        }

        $nama = $barang->nama;
        $kode = $barang->kode_barang;

        try {
            $barang->delete();

            return redirect()->route('toolman.barang.index')
                ->with('success', "Barang {$nama} ({$kode}) berhasil dihapus dari inventaris bengkel.");
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error("Gagal menghapus barang ID {$id}: " . $e->getMessage());
            return back()->with('error', "Gagal menghapus barang {$nama} ({$kode}) karena data masih terikat dengan catatan sistem lain.");
        }
    }

    /**
     * Cetak Lembar Kartu Barang / Kartu Persediaan Barang Resmi
     * Sesuai format standar Kartu Barang.md
     */
    public function printKartu($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }

        $barang = Barang::with([
            'bengkel',
            'lokasiPenyimpanan',
            'sumberDana',
            'stockMovements' => function ($q) {
                $q->with('user')->orderBy('created_at', 'asc');
            }
        ])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        $bengkel = $barang->bengkel ?? ($user->bengkel ?? Bengkel::findOrFail($bengkelId));

        // Kalkulasi mutasi kartu barang dan saldo berjalannya
        $movementRows = [];
        $saldoBaik = 0;
        $saldoRusak = 0;

        foreach ($barang->stockMovements as $m) {
            $masukBaik = 0;
            $masukRusak = 0;
            $keluarBaik = 0;
            $keluarRusak = 0;

            switch ($m->jenis) {
                case 'stok_masuk':
                    $masukBaik = (int) $m->jumlah;
                    $saldoBaik += $masukBaik;
                    break;
                case 'peminjaman':
                case 'bhp_keluar':
                    $keluarBaik = (int) $m->jumlah;
                    $saldoBaik = max(0, $saldoBaik - $keluarBaik);
                    break;
                case 'pengembalian_baik':
                    $masukBaik = (int) $m->jumlah;
                    $saldoBaik += $masukBaik;
                    break;
                case 'pengembalian_rusak':
                    $masukRusak = (int) $m->jumlah;
                    $saldoRusak += $masukRusak;
                    break;
                case 'barang_hilang':
                    // Pencegahan double deduction: barang hilang saat peminjaman telah
                    // dicatat keluar pada saat peminjaman ('keluar_baik'). Pada saat pengembalian,
                    // barang hilang mengurangi stok_total sistemik dan tidak memotong saldo_baik lagi.
                    $masukBaik = 0;
                    $masukRusak = 0;
                    $keluarBaik = 0;
                    $keluarRusak = 0;
                    break;
                case 'perbaikan':
                case 'rusak_ke_baik':
                    $jml = abs((int) $m->jumlah);
                    $keluarRusak = $jml;
                    $masukBaik = $jml;
                    $saldoRusak = max(0, $saldoRusak - $keluarRusak);
                    $saldoBaik += $masukBaik;
                    break;
                case 'kondisi_rusak':
                case 'baik_ke_rusak':
                    $jml = abs((int) $m->jumlah);
                    $keluarBaik = $jml;
                    $masukRusak = $jml;
                    $saldoBaik = max(0, $saldoBaik - $keluarBaik);
                    $saldoRusak += $masukRusak;
                    break;
                case 'penyesuaian':
                default:
                    $jml = (int) $m->jumlah;
                    $rawKet = strtolower($m->keterangan ?? '');
                    $refTipe = strtolower($m->referensi_tipe ?? '');

                    if (
                        $refTipe === 'alih_kondisi_rusak'
                        || str_contains($rawKet, 'baik ke rusak')
                        || preg_match('/ditemukan dalam kondisi rusak/i', $rawKet)
                        || str_contains($rawKet, 'kondisi rusak berat saat inventarisasi')
                    ) {
                        // Alih kondisi: Tersedia/Baik ke Rusak tanpa mengubah stok total
                        $qty = abs($jml);
                        $keluarBaik = $qty;
                        $masukRusak = $qty;
                        $saldoBaik = max(0, $saldoBaik - $keluarBaik);
                        $saldoRusak += $masukRusak;
                    } elseif ($refTipe === 'alih_kondisi_baik' || str_contains($rawKet, 'rusak ke baik')) {
                        // Alih kondisi: Rusak ke Baik/Tersedia (perbaikan) tanpa mengubah stok total
                        $qty = abs($jml);
                        $keluarRusak = $qty;
                        $masukBaik = $qty;
                        $saldoRusak = max(0, $saldoRusak - $keluarRusak);
                        $saldoBaik += $masukBaik;
                    } elseif (
                        $refTipe === 'penyesuaian_rusak_kurang'
                        || (str_contains($rawKet, 'rusak') && (str_contains($rawKet, 'afkir') || str_contains($rawKet, 'dihapus') || str_contains($rawKet, 'penghapusan')))
                    ) {
                        // Pengurangan stok rusak (misal pembuangan/afkir barang rusak)
                        $qty = abs($jml);
                        $keluarRusak = $qty;
                        $saldoRusak = max(0, $saldoRusak - $keluarRusak);
                    } elseif ($refTipe === 'penyesuaian_rusak_tambah') {
                        // Penambahan stok rusak secara langsung
                        $qty = abs($jml);
                        $masukRusak = $qty;
                        $saldoRusak += $masukRusak;
                    } elseif (
                        $jml < 0
                        || $refTipe === 'penyesuaian_kurang'
                        || preg_match('/(?:^| oleh Toolman )\(-(\d+)\)$/', trim($m->keterangan ?? ''))
                        || str_contains($rawKet, 'pengurangan')
                        || str_contains($rawKet, 'penurunan')
                        || str_contains($rawKet, 'penyusutan')
                    ) {
                        // Penyesuaian stok baik berkurang (stok opname minus pada stok tersedia)
                        $qty = abs($jml);
                        $keluarBaik = $qty;
                        $saldoBaik = max(0, $saldoBaik - $keluarBaik);
                    } else {
                        // Penyesuaian stok baik bertambah (stok opname plus pada stok tersedia)
                        $qty = abs($jml);
                        $masukBaik = $qty;
                        $saldoBaik += $masukBaik;
                    }
                    break;
            }

            $movementRows[] = [
                'tanggal'      => $m->created_at ? $m->created_at->translatedFormat('d/m/Y') : '-',
                'masuk_baik'   => $masukBaik > 0 ? $masukBaik : '',
                'masuk_rusak'  => $masukRusak > 0 ? $masukRusak : '',
                'keluar_baik'  => $keluarBaik > 0 ? $keluarBaik : '',
                'keluar_rusak' => $keluarRusak > 0 ? $keluarRusak : '',
                'sisa_baik'    => $saldoBaik,
                'sisa_rusak'   => $saldoRusak,
                'paraf'        => $m->user ? strtoupper(substr($m->user->name, 0, 3)) : 'TLM',
                'keterangan'   => $m->keterangan ?? '-',
            ];
        }

        // Jika riwayat pergerakan kosong namun barang memiliki stok terdaftar
        if (empty($movementRows) && ($barang->stok_total > 0 || $barang->stok_tersedia > 0 || $barang->stok_rusak > 0)) {
            $movementRows[] = [
                'tanggal'      => $barang->created_at ? $barang->created_at->translatedFormat('d/m/Y') : date('d/m/Y'),
                'masuk_baik'   => $barang->stok_tersedia > 0 ? $barang->stok_tersedia : '',
                'masuk_rusak'  => $barang->stok_rusak > 0 ? $barang->stok_rusak : '',
                'keluar_baik'  => '',
                'keluar_rusak' => '',
                'sisa_baik'    => $barang->stok_tersedia,
                'sisa_rusak'   => $barang->stok_rusak,
                'paraf'        => 'TLM',
                'keterangan'   => 'Stok Awal Terdaftar di Sistem',
            ];
        }

        return view('toolman.barang.print_kartu', compact('barang', 'bengkel', 'movementRows'));
    }

    /**
     * Unduh file template Microsoft Excel (.xlsx) resmi untuk Import Data Barang.
     * Menggunakan format native OpenXML (.xlsx) multi-sheet:
     * Sheet 1: Template data barang dengan kolom terpisah & styling rapi
     * Sheet 2: Panduan pengisian & data referensi (Lokasi, Sumber Dana, Satuan)
     */
    public function downloadTemplate()
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);
        $bengkelCode = $bengkel ? strtoupper($bengkel->kode ?? 'BENGKEL') : 'BENGKEL';

        SumberDanaController::ensureSchemaReady();
        SatuanController::ensureDefaults();

        $lokasis = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->orderBy('nama')->get();
        $sumberDanas = SumberDana::orderBy('nama')->get();
        $satuans = Satuan::orderBy('nama')->get();

        $sampleLokasi = $lokasis->first()?->nama ?? 'Gudang Utama';
        $sampleSumberDana = $sumberDanas->first()?->nama ?? 'BOS Reguler';

        $filename = "Template_Import_Barang_{$bengkelCode}.xlsx";

        if (class_exists(\ZipArchive::class)) {
            $tempFile = tempnam(sys_get_temp_dir(), 'skagata_tpl_') . '.xlsx';
            $zip = new \ZipArchive();

            if ($zip->open($tempFile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $xmlEsc = function (string $str): string {
                    return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                };

                // 1. [Content_Types].xml
                $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
  <Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>
  <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/xl/worksheets/sheet2.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
  <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
  <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
</Types>');

                // 2. _rels/.rels
                $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>');

                // 3. docProps/app.xml & docProps/core.xml
                $zip->addFromString('docProps/app.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties">
  <Application>Inventaris Skagata</Application>
</Properties>');

                $zip->addFromString('docProps/core.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
  <dc:creator>Inventaris Skagata</dc:creator>
  <cp:lastModifiedBy>Inventaris Skagata</cp:lastModifiedBy>
</cp:coreProperties>');

                // 4. xl/_rels/workbook.xml.rels
                $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
  <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet2.xml"/>
  <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>');

                // 5. xl/workbook.xml
                $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
  <sheets>
    <sheet name="Template Barang" sheetId="1" r:id="rId1"/>
    <sheet name="Panduan &amp; Referensi" sheetId="2" r:id="rId2"/>
  </sheets>
</workbook>');

                // 6. xl/styles.xml
                $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">
  <fonts count="3">
    <font><name val="Calibri"/><sz val="11"/></font>
    <font><b/><color rgb="FFFFFFFF"/><name val="Calibri"/><sz val="11"/></font>
    <font><b/><color rgb="FF1F2937"/><name val="Calibri"/><sz val="11"/></font>
  </fonts>
  <fills count="4">
    <fill><patternFill patternType="none"/></fill>
    <fill><patternFill patternType="gray125"/></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FF059669"/></patternFill></fill>
    <fill><patternFill patternType="solid"><fgColor rgb="FFF3F4F6"/></patternFill></fill>
  </fills>
  <borders count="2">
    <border><left/><right/><top/><bottom/></border>
    <border>
      <left style="thin"><color rgb="FFD1D5DB"/></left>
      <right style="thin"><color rgb="FFD1D5DB"/></right>
      <top style="thin"><color rgb="FFD1D5DB"/></top>
      <bottom style="thin"><color rgb="FFD1D5DB"/></bottom>
    </border>
  </borders>
  <cellStyleXfs count="1">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="0"/>
  </cellStyleXfs>
  <cellXfs count="4">
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0"/>
    <xf numFmtId="0" fontId="1" fillId="2" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>
    <xf numFmtId="0" fontId="2" fillId="3" borderId="1" xfId="0" applyFont="1" applyFill="1" applyBorder="1"/>
    <xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"/>
  </cellXfs>
</styleSheet>');

                // 7. Sheet 1: Template Barang
                $s1Cols = '
  <cols>
    <col min="1" max="1" width="24" customWidth="1"/>
    <col min="2" max="2" width="36" customWidth="1"/>
    <col min="3" max="3" width="20" customWidth="1"/>
    <col min="4" max="4" width="14" customWidth="1"/>
    <col min="5" max="5" width="26" customWidth="1"/>
    <col min="6" max="6" width="26" customWidth="1"/>
    <col min="7" max="7" width="18" customWidth="1"/>
    <col min="8" max="8" width="18" customWidth="1"/>
    <col min="9" max="9" width="18" customWidth="1"/>
    <col min="10" max="10" width="16" customWidth="1"/>
    <col min="11" max="11" width="20" customWidth="1"/>
    <col min="12" max="12" width="42" customWidth="1"/>
  </cols>';

                $s1Rows = '
    <row r="1" ht="26" customHeight="1">
      <c r="A1" t="inlineStr" s="1"><is><t>Kode Barang (Opsional)</t></is></c>
      <c r="B1" t="inlineStr" s="1"><is><t>Nama Barang</t></is></c>
      <c r="C1" t="inlineStr" s="1"><is><t>Tipe (inventaris/bhp)</t></is></c>
      <c r="D1" t="inlineStr" s="1"><is><t>Satuan</t></is></c>
      <c r="E1" t="inlineStr" s="1"><is><t>Lokasi Penyimpanan</t></is></c>
      <c r="F1" t="inlineStr" s="1"><is><t>Sumber Dana</t></is></c>
      <c r="G1" t="inlineStr" s="1"><is><t>Stok Baik / Bahan</t></is></c>
      <c r="H1" t="inlineStr" s="1"><is><t>Stok Rusak Ringan</t></is></c>
      <c r="I1" t="inlineStr" s="1"><is><t>Stok Rusak Berat</t></is></c>
      <c r="J1" t="inlineStr" s="1"><is><t>Batas Minimum</t></is></c>
      <c r="K1" t="inlineStr" s="1"><is><t>Harga Satuan (Rp)</t></is></c>
      <c r="L1" t="inlineStr" s="1"><is><t>Spesifikasi / Keterangan</t></is></c>
    </row>
    <row r="2" ht="20" customHeight="1">
      <c r="A2" t="inlineStr" s="3"><is><t>' . $xmlEsc("INV-{$bengkelCode}-101") . '</t></is></c>
      <c r="B2" t="inlineStr" s="3"><is><t>Laptop ASUS ExpertBook B1400</t></is></c>
      <c r="C2" t="inlineStr" s="3"><is><t>inventaris</t></is></c>
      <c r="D2" t="inlineStr" s="3"><is><t>Unit</t></is></c>
      <c r="E2" t="inlineStr" s="3"><is><t>' . $xmlEsc($sampleLokasi) . '</t></is></c>
      <c r="F2" t="inlineStr" s="3"><is><t>' . $xmlEsc($sampleSumberDana) . '</t></is></c>
      <c r="G2" s="3"><v>10</v></c>
      <c r="H2" s="3"><v>1</v></c>
      <c r="I2" s="3"><v>0</v></c>
      <c r="J2" s="3"><v>2</v></c>
      <c r="K2" s="3"><v>8500000</v></c>
      <c r="L2" t="inlineStr" s="3"><is><t>Intel Core i5, RAM 16GB, SSD 512GB, Windows 11 Pro</t></is></c>
    </row>
    <row r="3" ht="20" customHeight="1">
      <c r="A3" t="inlineStr" s="3"><is><t>' . $xmlEsc("BHP-{$bengkelCode}-201") . '</t></is></c>
      <c r="B3" t="inlineStr" s="3"><is><t>Kabel UTP Cat6 Belden (305m)</t></is></c>
      <c r="C3" t="inlineStr" s="3"><is><t>bhp</t></is></c>
      <c r="D3" t="inlineStr" s="3"><is><t>Roll</t></is></c>
      <c r="E3" t="inlineStr" s="3"><is><t>' . $xmlEsc($sampleLokasi) . '</t></is></c>
      <c r="F3" t="inlineStr" s="3"><is><t>' . $xmlEsc($sampleSumberDana) . '</t></is></c>
      <c r="G3" s="3"><v>5</v></c>
      <c r="H3" s="3"><v>0</v></c>
      <c r="I3" s="3"><v>0</v></c>
      <c r="J3" s="3"><v>1</v></c>
      <c r="K3" s="3"><v>1650000</v></c>
      <c r="L3" t="inlineStr" s="3"><is><t>Kabel LAN Cat6 original panjang 305 meter</t></is></c>
    </row>
    <row r="4" ht="20" customHeight="1">
      <c r="A4" t="inlineStr" s="3"><is><t></t></is></c>
      <c r="B4" t="inlineStr" s="3"><is><t>Obeng Set Presisi 32 in 1</t></is></c>
      <c r="C4" t="inlineStr" s="3"><is><t>inventaris</t></is></c>
      <c r="D4" t="inlineStr" s="3"><is><t>Set</t></is></c>
      <c r="E4" t="inlineStr" s="3"><is><t>' . $xmlEsc($sampleLokasi) . '</t></is></c>
      <c r="F4" t="inlineStr" s="3"><is><t>' . $xmlEsc($sampleSumberDana) . '</t></is></c>
      <c r="G4" s="3"><v>15</v></c>
      <c r="H4" s="3"><v>0</v></c>
      <c r="I4" s="3"><v>0</v></c>
      <c r="J4" s="3"><v>3</v></c>
      <c r="K4" s="3"><v>125000</v></c>
      <c r="L4" t="inlineStr" s="3"><is><t>Mata obeng magnetik lengkap dengan pinset presisi</t></is></c>
    </row>';

                $sheet1Xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . $s1Cols . '<sheetData>' . $s1Rows . '</sheetData></worksheet>';

                $zip->addFromString('xl/worksheets/sheet1.xml', $sheet1Xml);

                // 8. Sheet 2: Panduan & Referensi
                $rules = [
                    "1. Kolom 'Nama Barang' WAJIB diisi.",
                    "2. Kolom 'Tipe' diisi 'inventaris' atau 'bhp'.",
                    "3. Kolom 'Kode Barang' opsional (kosongkan agar dibuat otomatis sistem).",
                    "4. Kolom 'Satuan' disarankan memakai satuan baku di Kolom D.",
                    "5. Kolom 'Lokasi' sebaiknya mengacu pada daftar di Kolom B.",
                    "6. Kolom 'Sumber Dana' sebaiknya mengacu pada daftar di Kolom C.",
                    "7. Lokasi, Satuan, dan Sumber Dana harus mengacu pada master data yang telah terdaftar.",
                    "8. Untuk tipe 'bhp', jumlah stok diisi pada 'Stok Baik / Bahan'.",
                    "9. 'Stok Rusak Ringan' dan 'Rusak Berat' khusus untuk barang inventaris.",
                    "10. 'Batas Minimum' menentukan ambang batas peringatan stok menipis.",
                    "11. 'Harga Satuan' diisi estimasi harga per unit dalam rupiah (opsional, contoh: 1500000)."
                ];

                $lokasiList = $lokasis->map(fn($l) => $l->nama . ($l->kode ? " ({$l->kode})" : ''))->toArray();
                $sumberDanaList = $sumberDanas->map(fn($s) => $s->nama . ($s->kode ? " ({$s->kode})" : ''))->toArray();
                $satuanList = $satuans->map(fn($st) => $st->nama . ($st->singkatan ? " ({$st->singkatan})" : ''))->toArray();

                $maxRows = max(count($rules), count($lokasiList), count($sumberDanaList), count($satuanList));

                $s2Cols = '
  <cols>
    <col min="1" max="1" width="46" customWidth="1"/>
    <col min="2" max="2" width="30" customWidth="1"/>
    <col min="3" max="3" width="30" customWidth="1"/>
    <col min="4" max="4" width="22" customWidth="1"/>
  </cols>';

                $s2Rows = '
    <row r="1" ht="26" customHeight="1">
      <c r="A1" t="inlineStr" s="2"><is><t>Petunjuk &amp; Aturan Pengisian</t></is></c>
      <c r="B1" t="inlineStr" s="2"><is><t>Lokasi di Bengkel Ini</t></is></c>
      <c r="C1" t="inlineStr" s="2"><is><t>Sumber Dana Terdaftar</t></is></c>
      <c r="D1" t="inlineStr" s="2"><is><t>Satuan Baku Terdaftar</t></is></c>
    </row>';

                for ($i = 0; $i < $maxRows; $i++) {
                    $rNum = $i + 2;
                    $ruleVal = $rules[$i] ?? '';
                    $lokVal = $lokasiList[$i] ?? '';
                    $sdVal = $sumberDanaList[$i] ?? '';
                    $stVal = $satuanList[$i] ?? '';

                    $s2Rows .= "
    <row r=\"{$rNum}\" ht=\"20\" customHeight=\"1\">
      <c r=\"A{$rNum}\" t=\"inlineStr\" s=\"3\"><is><t>" . $xmlEsc($ruleVal) . "</t></is></c>
      <c r=\"B{$rNum}\" t=\"inlineStr\" s=\"3\"><is><t>" . $xmlEsc($lokVal) . "</t></is></c>
      <c r=\"C{$rNum}\" t=\"inlineStr\" s=\"3\"><is><t>" . $xmlEsc($sdVal) . "</t></is></c>
      <c r=\"D{$rNum}\" t=\"inlineStr\" s=\"3\"><is><t>" . $xmlEsc($stVal) . "</t></is></c>
    </row>";
                }

                $sheet2Xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">' . $s2Cols . '<sheetData>' . $s2Rows . '</sheetData></worksheet>';

                $zip->addFromString('xl/worksheets/sheet2.xml', $sheet2Xml);
                $zip->close();

                return response()->download($tempFile, $filename, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                    'Pragma' => 'public',
                ])->deleteFileAfterSend(true);
            }
        }

        // Fallback ke CSV jika ZipArchive tidak aktif
        $filenameCsv = "Template_Import_Barang_{$bengkelCode}.csv";
        $headersCsv = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filenameCsv}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = [
            'Kode Barang (Opsional)',
            'Nama Barang',
            'Tipe (inventaris/bhp)',
            'Satuan',
            'Lokasi Penyimpanan',
            'Sumber Dana',
            'Stok Baik / Bahan',
            'Stok Rusak Ringan',
            'Stok Rusak Berat',
            'Batas Minimum',
            'Harga Satuan (Rp)',
            'Spesifikasi / Keterangan',
        ];

        $sampleRows = [
            [
                "INV-{$bengkelCode}-101",
                'Laptop ASUS ExpertBook B1400',
                'inventaris',
                'Unit',
                $sampleLokasi,
                $sampleSumberDana,
                '10',
                '1',
                '0',
                '2',
                '8500000',
                'Intel Core i5, RAM 16GB, SSD 512GB, Windows 11 Pro',
            ],
            [
                "BHP-{$bengkelCode}-201",
                'Kabel UTP Cat6 Belden (305m)',
                'bhp',
                'Roll',
                $sampleLokasi,
                $sampleSumberDana,
                '5',
                '0',
                '0',
                '1',
                '1650000',
                'Kabel LAN Cat6 original panjang 305 meter',
            ],
            [
                '',
                'Obeng Set Presisi 32 in 1',
                'inventaris',
                'Set',
                $sampleLokasi,
                $sampleSumberDana,
                '15',
                '0',
                '0',
                '3',
                '125000',
                'Mata obeng magnetik lengkap dengan pinset presisi',
            ],
        ];

        $callback = function () use ($columns, $sampleRows) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $columns, ';');
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row, ';');
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headersCsv);
    }

    /**
     * Proses import bulk data barang dari file Excel (.xlsx, .xls, .csv, .txt).
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240|mimes:xlsx,xls,csv,txt',
        ], [
            'file.required' => 'Silakan pilih file Excel atau CSV yang akan diunggah.',
            'file.mimes' => 'Format file yang didukung adalah .xlsx, .xls, atau .csv.',
            'file.max' => 'Ukuran file maksimal yang diperbolehkan adalah 10MB.',
        ]);

        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);
        $bengkelCode = $bengkel ? strtoupper($bengkel->kode ?? 'BGK') : 'BGK';

        SumberDanaController::ensureSchemaReady();
        SatuanController::ensureDefaults();

        $file = $request->file('file');
        $rows = $this->parseSpreadsheet($file);

        if (empty($rows) || count($rows) < 2) {
            return back()->with('error', 'File yang diunggah kosong atau tidak memiliki baris data barang.')->withInput();
        }

        // 1. Petakan header kolom
        $headerRow = $rows[0];
        $colMap = [];

        foreach ($headerRow as $idx => $headerText) {
            $norm = $this->normalizeHeader((string) $headerText);
            if (empty($norm)) continue;

            if (str_contains($norm, 'kode')) {
                $colMap['kode'] = $idx;
            } elseif (str_contains($norm, 'nama')) {
                $colMap['nama'] = $idx;
            } elseif (str_contains($norm, 'tipe') || str_contains($norm, 'jenis')) {
                $colMap['tipe'] = $idx;
            } elseif (str_contains($norm, 'harga') || str_contains($norm, 'biaya') || str_contains($norm, 'tarif') || str_contains($norm, 'price')) {
                $colMap['harga'] = $idx;
            } elseif ((str_contains($norm, 'satuan') || str_contains($norm, 'unit') || str_contains($norm, 'uom')) && !str_contains($norm, 'harga')) {
                $colMap['satuan'] = $idx;
            } elseif (str_contains($norm, 'lokasi') || str_contains($norm, 'tempat') || str_contains($norm, 'rak')) {
                $colMap['lokasi'] = $idx;
            } elseif (str_contains($norm, 'sumber') || str_contains($norm, 'dana') || str_contains($norm, 'anggaran')) {
                $colMap['sumber_dana'] = $idx;
            } elseif (str_contains($norm, 'ringan')) {
                $colMap['stok_rusak_ringan'] = $idx;
            } elseif (str_contains($norm, 'berat')) {
                $colMap['stok_rusak_berat'] = $idx;
            } elseif (str_contains($norm, 'baik') || str_contains($norm, 'stok_bahan') || $norm === 'stok' || $norm === 'jumlah' || $norm === 'qty') {
                $colMap['stok_baik'] = $idx;
            } elseif (str_contains($norm, 'minimum') || str_contains($norm, 'batas') || str_contains($norm, 'min')) {
                $colMap['batas_minimum'] = $idx;
            } elseif (str_contains($norm, 'spesifikasi') || str_contains($norm, 'deskripsi') || str_contains($norm, 'keterangan')) {
                $colMap['spesifikasi'] = $idx;
            }
        }

        if (!isset($colMap['nama'])) {
            return back()->with('error', "Kolom 'Nama Barang' tidak ditemukan pada baris header file. Pastikan menggunakan format template yang disediakan.")->withInput();
        }

        // Cache referensi master data untuk validasi baris
        $lokasiBengkelList = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->get();
        $defaultLokasi = $lokasiBengkelList->first();

        $allLokasis = LokasiPenyimpanan::all();
        $satuanMasterList = Satuan::all();
        $sumberDanaMasterList = SumberDana::all();

        $existingDbCodes = Barang::where('bengkel_id', $bengkelId)
            ->pluck('kode_barang')
            ->map(fn($k) => strtoupper(trim($k)))
            ->flip()
            ->all();

        $seenCodesInFile = [];
        $validatedRows = [];
        $rowErrors = [];

        // 2. Pre-validasi seluruh baris sebelum transaksi
        for ($i = 1; $i < count($rows); $i++) {
            $rowNum = $i + 1; // 1-indexed baris Excel (baris 1 = header)
            $row = $rows[$i];

            // Abaikan baris yang seluruh kolomnya kosong
            $hasContent = false;
            foreach ($row as $cell) {
                if (trim((string) $cell) !== '') {
                    $hasContent = true;
                    break;
                }
            }
            if (!$hasContent) continue;

            $nama = isset($colMap['nama']) ? trim((string) ($row[$colMap['nama']] ?? '')) : '';
            if (empty($nama)) {
                $rowErrors[] = "Baris {$rowNum}: Kolom 'Nama Barang' wajib diisi.";
                continue;
            }

            // Tipe / jenis barang
            $rawTipe = isset($colMap['tipe']) ? strtolower(trim((string) ($row[$colMap['tipe']] ?? ''))) : '';
            $jenisBarang = (str_contains($rawTipe, 'bhp') || str_contains($rawTipe, 'bahan')) ? 'bhp' : 'inventaris';

            // Kode barang
            $rawKode = isset($colMap['kode']) ? strtoupper(trim((string) ($row[$colMap['kode']] ?? ''))) : '';
            if (!empty($rawKode)) {
                if (strlen($rawKode) > 50) {
                    $rowErrors[] = "Baris {$rowNum}: Kode barang '{$rawKode}' melebihi batas maksimal 50 karakter.";
                } elseif (isset($seenCodesInFile[$rawKode])) {
                    $rowErrors[] = "Baris {$rowNum}: Kode barang '{$rawKode}' duplikat di dalam file impor (sama dengan baris {$seenCodesInFile[$rawKode]}).";
                } elseif (isset($existingDbCodes[$rawKode])) {
                    $rowErrors[] = "Baris {$rowNum}: Kode barang '{$rawKode}' sudah terdaftar di bengkel ini.";
                } else {
                    $seenCodesInFile[$rawKode] = $rowNum;
                }
            }

            // Validasi Satuan terhadap master satuan yang ada
            $satuanRaw = isset($colMap['satuan']) ? trim((string) ($row[$colMap['satuan']] ?? '')) : '';
            $finalSatuan = null;
            if (empty($satuanRaw)) {
                $defaultUnitName = ($jenisBarang === 'bhp') ? 'Pcs' : 'Unit';
                $matchedDefault = $satuanMasterList->first(function ($s) use ($defaultUnitName) {
                    return strcasecmp($s->nama, $defaultUnitName) === 0;
                });
                $finalSatuan = $matchedDefault?->nama ?? $defaultUnitName;
            } else {
                $matchedSatuan = $satuanMasterList->first(function ($s) use ($satuanRaw) {
                    return strcasecmp($s->nama, $satuanRaw) === 0
                        || (!empty($s->singkatan) && strcasecmp($s->singkatan, $satuanRaw) === 0);
                });

                if (!$matchedSatuan) {
                    $rowErrors[] = "Baris {$rowNum}: Satuan '{$satuanRaw}' tidak terdaftar dalam master data satuan.";
                } else {
                    $finalSatuan = $matchedSatuan->nama;
                }
            }

            // Validasi Lokasi Penyimpanan (wajib dalam lingkup bengkel Toolman)
            $lokasiRaw = isset($colMap['lokasi']) ? trim((string) ($row[$colMap['lokasi']] ?? '')) : '';
            $finalLokasiId = null;
            if (!empty($lokasiRaw)) {
                $matchedLokasi = $lokasiBengkelList->first(function ($l) use ($lokasiRaw) {
                    return strcasecmp($l->nama, $lokasiRaw) === 0
                        || strcasecmp($l->kode, $lokasiRaw) === 0;
                });

                if ($matchedLokasi) {
                    $finalLokasiId = $matchedLokasi->id;
                } else {
                    // Cek apakah lokasi ini milik bengkel lain
                    $foreignLoc = $allLokasis->first(function ($l) use ($bengkelId, $lokasiRaw) {
                        return $l->bengkel_id != $bengkelId
                            && (strcasecmp($l->nama, $lokasiRaw) === 0 || strcasecmp($l->kode, $lokasiRaw) === 0);
                    });

                    if ($foreignLoc) {
                        $rowErrors[] = "Baris {$rowNum}: Lokasi '{$lokasiRaw}' milik bengkel lain dan tidak dapat digunakan.";
                    } else {
                        $rowErrors[] = "Baris {$rowNum}: Lokasi '{$lokasiRaw}' tidak ditemukan pada bengkel ini.";
                    }
                }
            } else {
                if ($defaultLokasi) {
                    $finalLokasiId = $defaultLokasi->id;
                } else {
                    $rowErrors[] = "Baris {$rowNum}: Lokasi penyimpanan wajib diisi karena bengkel belum memiliki lokasi penyimpanan terdaftar.";
                }
            }

            // Validasi Sumber Dana terhadap master data
            $sumberDanaRaw = isset($colMap['sumber_dana']) ? trim((string) ($row[$colMap['sumber_dana']] ?? '')) : '';
            $finalSumberDanaId = null;
            if (!empty($sumberDanaRaw)) {
                $matchedSd = $sumberDanaMasterList->first(function ($sd) use ($sumberDanaRaw) {
                    return strcasecmp($sd->nama, $sumberDanaRaw) === 0
                        || (!empty($sd->kode) && strcasecmp($sd->kode, $sumberDanaRaw) === 0);
                });

                if ($matchedSd) {
                    $finalSumberDanaId = $matchedSd->id;
                } else {
                    $rowErrors[] = "Baris {$rowNum}: Sumber dana '{$sumberDanaRaw}' tidak terdaftar dalam master data sumber dana.";
                }
            }

            // Validasi Stok & Harga
            if ($jenisBarang === 'inventaris') {
                $stokBaik = isset($colMap['stok_baik']) ? max(0, (int) ($row[$colMap['stok_baik']] ?? 1)) : 1;
                $stokRusakRingan = isset($colMap['stok_rusak_ringan']) ? max(0, (int) ($row[$colMap['stok_rusak_ringan']] ?? 0)) : 0;
                $stokRusakBerat = isset($colMap['stok_rusak_berat']) ? max(0, (int) ($row[$colMap['stok_rusak_berat']] ?? 0)) : 0;
                $stokRusak = $stokRusakRingan + $stokRusakBerat;
                $stokTersedia = $stokBaik;
                $stokTotal = $stokTersedia + $stokRusak;
                $minimumStok = isset($colMap['batas_minimum']) ? max(0, (int) ($row[$colMap['batas_minimum']] ?? 1)) : 1;
            } else {
                $stokBahan = isset($colMap['stok_baik']) ? max(0, (int) ($row[$colMap['stok_baik']] ?? 1)) : 1;
                $stokTersedia = $stokBahan;
                $stokRusak = 0;
                $stokTotal = $stokBahan;
                $minimumStok = isset($colMap['batas_minimum']) ? max(0, (int) ($row[$colMap['batas_minimum']] ?? 0)) : 0;
            }

            $deskripsi = isset($colMap['spesifikasi']) ? trim((string) ($row[$colMap['spesifikasi']] ?? '')) : null;

            $harga = 0;
            if (isset($colMap['harga'])) {
                $rawHarga = (string) ($row[$colMap['harga']] ?? '0');
                $cleaned = trim($rawHarga);
                if ($cleaned !== '') {
                    $cleaned = preg_replace('/[^\d.,]/', '', $cleaned);
                    if (str_contains($cleaned, ',') && str_contains($cleaned, '.')) {
                        $cleaned = str_replace('.', '', $cleaned);
                        $cleaned = str_replace(',', '.', $cleaned);
                    } elseif (str_contains($cleaned, ',')) {
                        $cleaned = str_replace(',', '.', $cleaned);
                    } elseif (preg_match('/^\d{1,3}(\.\d{3})+$/', $cleaned)) {
                        $cleaned = str_replace('.', '', $cleaned);
                    }
                    $harga = max(0, (float) $cleaned);
                }
            }

            $validatedRows[] = [
                'row_num' => $rowNum,
                'kode_barang' => $rawKode,
                'nama' => $nama,
                'jenis_barang' => $jenisBarang,
                'satuan' => $finalSatuan,
                'lokasi_penyimpanan_id' => $finalLokasiId,
                'sumber_dana_id' => $finalSumberDanaId,
                'harga' => $harga,
                'stok_total' => $stokTotal,
                'stok_tersedia' => $stokTersedia,
                'stok_dipinjam' => 0,
                'stok_rusak' => $stokRusak,
                'minimum_stok' => $minimumStok,
                'deskripsi' => $deskripsi,
            ];
        }

        // Jika terdapat kesalahan validasi pada baris manapun, batalkan impor seluruhnya
        if (!empty($rowErrors)) {
            $errorSummary = "Import dibatalkan. Ditemukan " . count($rowErrors) . " kesalahan pada baris data:\n" . implode("\n", $rowErrors);
            return back()->with('error', $errorSummary)
                ->with('import_errors', $rowErrors)
                ->withInput();
        }

        if (empty($validatedRows)) {
            return back()->with('error', 'Tidak ada data barang yang valid untuk diimpor. Pastikan file berisi baris data barang.')->withInput();
        }

        // 3. Eksekusi penyimpanan atomik dalam database transaction
        $importedCount = 0;
        try {
            DB::transaction(function () use ($validatedRows, $bengkelId, $bengkelCode, $user, &$importedCount) {
                $autoGenCounter = 1;

                foreach ($validatedRows as $item) {
                    $kodeBarang = $item['kode_barang'];
                    if (empty($kodeBarang)) {
                        $prefix = ($item['jenis_barang'] === 'bhp') ? 'BHP' : 'INV';
                        do {
                            $kodeBarang = "{$prefix}-{$bengkelCode}-" . str_pad((string) $autoGenCounter++, 4, '0', STR_PAD_LEFT);
                        } while (Barang::where('bengkel_id', $bengkelId)->where('kode_barang', $kodeBarang)->exists());
                    }

                    $barang = Barang::create([
                        'bengkel_id' => $bengkelId,
                        'lokasi_penyimpanan_id' => $item['lokasi_penyimpanan_id'],
                        'sumber_dana_id' => $item['sumber_dana_id'],
                        'kode_barang' => $kodeBarang,
                        'nama' => $item['nama'],
                        'jenis_barang' => $item['jenis_barang'],
                        'satuan' => $item['satuan'],
                        'harga' => $item['harga'],
                        'stok_total' => $item['stok_total'],
                        'stok_tersedia' => $item['stok_tersedia'],
                        'stok_dipinjam' => 0,
                        'stok_rusak' => $item['stok_rusak'],
                        'minimum_stok' => $item['minimum_stok'],
                        'deskripsi' => $item['deskripsi'],
                    ]);

                    if ($item['stok_total'] > 0) {
                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'stok_masuk',
                            'jumlah' => $item['stok_total'],
                            'keterangan' => 'Pendaftaran stok awal melalui Import Excel oleh Toolman',
                        ]);
                    }

                    $importedCount++;
                }
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses transaksi import: ' . $e->getMessage())
                ->withInput();
        }

        return redirect()->route('toolman.barang.index')
            ->with('success', "Berhasil mengimpor {$importedCount} data barang ke inventaris bengkel.");
    }

    /**
     * Parsing file spreadsheet (.xlsx, .xls, .csv, .txt) ke dalam array baris data.
     */
    private function parseSpreadsheet($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();
        $rows = [];

        if (in_array($extension, ['csv', 'txt'])) {
            $handle = fopen($path, 'r');
            if ($handle !== false) {
                // Deteksi delimiter dari baris pertama
                $firstLine = fgets($handle);
                rewind($handle);

                $delimiter = ',';
                $commaCount = substr_count($firstLine, ',');
                $semiCount = substr_count($firstLine, ';');
                $tabCount = substr_count($firstLine, "\t");

                if ($semiCount > $commaCount && $semiCount > $tabCount) {
                    $delimiter = ';';
                } elseif ($tabCount > $commaCount && $tabCount > $semiCount) {
                    $delimiter = "\t";
                }

                $isFirst = true;
                while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
                    if ($isFirst && !empty($data[0])) {
                        $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', $data[0]);
                        $isFirst = false;
                    }
                    $rows[] = array_map('trim', $data);
                }
                fclose($handle);
            }
        } elseif ($extension === 'xlsx') {
            if (class_exists(\ZipArchive::class)) {
                $zip = new \ZipArchive();
                if ($zip->open($path) === true) {
                    // 1. Baca sharedStrings.xml jika ada
                    $sharedStrings = [];
                    $ssIndex = $zip->locateName('xl/sharedStrings.xml');
                    if ($ssIndex !== false) {
                        $ssXml = simplexml_load_string($zip->getFromIndex($ssIndex));
                        if ($ssXml && isset($ssXml->si)) {
                            foreach ($ssXml->si as $si) {
                                if (isset($si->t)) {
                                    $sharedStrings[] = (string) $si->t;
                                } elseif (isset($si->r)) {
                                    $str = '';
                                    foreach ($si->r as $r) {
                                        $str .= (string) $r->t;
                                    }
                                    $sharedStrings[] = $str;
                                } else {
                                    $sharedStrings[] = '';
                                }
                            }
                        }
                    }

                    // 2. Cari sheet1.xml atau sheet pertama
                    $sheetIndex = $zip->locateName('xl/worksheets/sheet1.xml');
                    if ($sheetIndex === false) {
                        for ($i = 0; $i < $zip->numFiles; $i++) {
                            $name = $zip->getNameIndex($i);
                            if (str_starts_with($name, 'xl/worksheets/sheet') && str_ends_with($name, '.xml')) {
                                $sheetIndex = $i;
                                break;
                            }
                        }
                    }

                    if ($sheetIndex !== false) {
                        $sheetXml = simplexml_load_string($zip->getFromIndex($sheetIndex));
                        if ($sheetXml && isset($sheetXml->sheetData->row)) {
                            foreach ($sheetXml->sheetData->row as $row) {
                                $rowData = [];
                                foreach ($row->c as $c) {
                                    $coord = (string) $c['r'];
                                    $colIdx = $this->coordinateToColIndex($coord);
                                    $type = (string) $c['t'];
                                    $val = '';

                                    if ($type === 's') {
                                        $sIdx = (int) $c->v;
                                        $val = $sharedStrings[$sIdx] ?? '';
                                    } elseif ($type === 'inlineStr') {
                                        $val = (string) ($c->is->t ?? '');
                                    } else {
                                        $val = (string) ($c->v ?? '');
                                    }

                                    $rowData[$colIdx] = trim($val);
                                }

                                if (!empty($rowData)) {
                                    $maxCol = max(array_keys($rowData));
                                    $fullRow = [];
                                    for ($c = 0; $c <= $maxCol; $c++) {
                                        $fullRow[] = $rowData[$c] ?? '';
                                    }
                                    $rows[] = $fullRow;
                                }
                            }
                        }
                    }
                    $zip->close();
                }
            }
        } elseif ($extension === 'xls') {
            $content = file_get_contents($path);
            if (stripos($content, '<table') !== false) {
                if (preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $content, $trMatches)) {
                    foreach ($trMatches[1] as $tr) {
                        if (preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $tr, $tdMatches)) {
                            $row = array_map(function ($val) {
                                return trim(html_entity_decode(strip_tags($val)));
                            }, $tdMatches[1]);
                            $rows[] = $row;
                        }
                    }
                }
            } else {
                $handle = fopen($path, 'r');
                if ($handle !== false) {
                    while (($data = fgetcsv($handle, 0, "\t")) !== false) {
                        $rows[] = array_map('trim', $data);
                    }
                    fclose($handle);
                }
            }
        }

        return $rows;
    }

    /**
     * Konversi koordinat sel Excel (seperti "A1", "C2", "AB10") ke indeks kolom 0-based.
     */
    private function coordinateToColIndex(string $coord): int
    {
        preg_match('/^([A-Z]+)/', strtoupper($coord), $matches);
        if (empty($matches[1])) {
            return 0;
        }
        $colStr = $matches[1];
        $col = 0;
        $len = strlen($colStr);
        for ($i = 0; $i < $len; $i++) {
            $col = $col * 26 + (ord($colStr[$i]) - 64);
        }
        return $col - 1;
    }

    /**
     * Normalisasi teks header kolom untuk pencocokan fleksibel.
     */
    private function normalizeHeader(string $header): string
    {
        $h = strtolower(trim($header));
        $h = preg_replace('/[^a-z0-9]/', '_', $h);
        $h = preg_replace('/_+/', '_', $h);
        return trim($h, '_');
    }
}
