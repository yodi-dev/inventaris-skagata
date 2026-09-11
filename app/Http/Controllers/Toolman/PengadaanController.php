<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPengadaan;
use App\Models\Pengadaan;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengadaanController extends Controller
{
    /**
     * Tampilkan daftar usulan RAB pengadaan bengkel dengan filter status dan pencarian.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $filterStatus = $request->query('status', '');
        $search = trim($request->query('search', ''));

        $baseQuery = Pengadaan::with(['dibuatOleh', 'direviewOleh', 'detailPengadaans.barang'])
            ->where('bengkel_id', $bengkelId);

        $query = (clone $baseQuery)->latest('created_at');

        if ($filterStatus && in_array($filterStatus, ['draft', 'pending', 'revisi', 'approved', 'rejected', 'selesai'])) {
            $query->where('status', $filterStatus);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                    ->orWhere('catatan', 'like', "%{$search}%")
                    ->orWhereHas('detailPengadaans', function ($dq) use ($search) {
                        $dq->where('nama_barang', 'like', "%{$search}%");
                    });
            });
        }

        $pengadaans = $query->paginate(10)->withQueryString();

        // Hitungan per status untuk tab badge
        $statusCounts = [
            'all' => (clone $baseQuery)->count(),
            'draft' => (clone $baseQuery)->where('status', 'draft')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'revisi' => (clone $baseQuery)->where('status', 'revisi')->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'selesai' => (clone $baseQuery)->where('status', 'selesai')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        return view('toolman.pengadaan.index', compact('pengadaans', 'bengkel', 'filterStatus', 'statusCounts', 'search'));
    }

    /**
     * Tampilkan form pembuatan usulan RAB baru.
     */
    public function create()
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        // Rekomendasi barang yang menipis atau rusak untuk fitur generate otomatis
        $limitItems = Barang::where('bengkel_id', $bengkelId)
            ->where(function ($q) {
                $q->whereColumn('stok_tersedia', '<=', 'minimum_stok')
                    ->orWhere('stok_rusak', '>', 0);
            })
            ->get();

        return view('toolman.pengadaan.create', compact('bengkel', 'limitItems'));
    }

    /**
     * Simpan usulan RAB baru (baik sebagai draf maupun langsung diajukan).
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'action' => 'required|in:draft,submit',
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|string|max:255',
            'items.*.spesifikasi' => 'nullable|string|max:1000',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.barang_id' => 'nullable|exists:barangs,id',
        ], [
            'items.required' => 'Usulan pengadaan harus mencantumkan minimal 1 item barang.',
            'items.min' => 'Usulan pengadaan harus mencantumkan minimal 1 item barang.',
            'items.*.nama.required' => 'Nama barang pada setiap baris wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
        ]);

        $isSubmit = $request->input('action') === 'submit';
        $status = $isSubmit ? 'pending' : 'draft';
        $diajukanPada = $isSubmit ? now() : null;

        $pengadaan = DB::transaction(function () use ($user, $bengkelId, $request, $status, $diajukanPada) {
            $rab = Pengadaan::create([
                'bengkel_id' => $bengkelId,
                'dibuat_oleh' => $user->id,
                'judul' => trim($request->judul),
                'status' => $status,
                'catatan' => $request->keterangan ? trim($request->keterangan) : null,
                'diajukan_pada' => $diajukanPada,
            ]);

            foreach ($request->items as $item) {
                if (empty(trim($item['nama'] ?? ''))) {
                    continue;
                }

                DetailPengadaan::create([
                    'pengadaan_id' => $rab->id,
                    'barang_id' => !empty($item['barang_id']) ? $item['barang_id'] : null,
                    'nama_barang' => trim($item['nama']),
                    'spesifikasi' => !empty($item['spesifikasi']) ? trim($item['spesifikasi']) : null,
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'unit'),
                    'harga_satuan' => (float) ($item['harga_satuan'] ?? 0),
                ]);
            }

            return $rab;
        });

        $msg = $isSubmit
            ? "Usulan RAB #RAB-" . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) . " berhasil diajukan ke Waka Sarpras!"
            : "Draf usulan RAB #RAB-" . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) . " berhasil disimpan.";

        return redirect()->route('toolman.pengadaan.show', $pengadaan->id)->with('success', $msg);
    }

    /**
     * Tampilkan rincian usulan RAB.
     */
    public function show($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $pengadaan = Pengadaan::with(['bengkel', 'dibuatOleh', 'direviewOleh', 'detailPengadaans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        return view('toolman.pengadaan.show', compact('pengadaan', 'bengkel'));
    }

    /**
     * Tampilkan form edit usulan RAB (hanya untuk status draft atau revisi).
     */
    public function edit($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $pengadaan = Pengadaan::with(['detailPengadaans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        if (!in_array($pengadaan->status, ['draft', 'revisi'])) {
            return redirect()->route('toolman.pengadaan.show', $pengadaan->id)
                ->with('error', "Usulan dengan status {$pengadaan->status} tidak dapat diedit.");
        }

        return view('toolman.pengadaan.edit', compact('pengadaan', 'bengkel'));
    }

    /**
     * Perbarui data usulan RAB (draf atau perbaikan revisi).
     */
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $pengadaan = Pengadaan::where('bengkel_id', $bengkelId)->findOrFail($id);

        if (!in_array($pengadaan->status, ['draft', 'revisi'])) {
            return redirect()->route('toolman.pengadaan.show', $pengadaan->id)
                ->with('error', "Usulan ini tidak dalam status draf atau revisi.");
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'keterangan' => 'nullable|string|max:1000',
            'action' => 'required|in:draft,submit',
            'items' => 'required|array|min:1',
            'items.*.nama' => 'required|string|max:255',
            'items.*.spesifikasi' => 'nullable|string|max:1000',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.satuan' => 'required|string|max:50',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.barang_id' => 'nullable|exists:barangs,id',
        ]);

        $isSubmit = $request->input('action') === 'submit';

        DB::transaction(function () use ($pengadaan, $request, $isSubmit) {
            $updateData = [
                'judul' => trim($request->judul),
                'catatan' => $request->keterangan ? trim($request->keterangan) : null,
            ];

            if ($isSubmit) {
                $updateData['status'] = 'pending';
                $updateData['diajukan_pada'] = now();
            }

            $pengadaan->update($updateData);

            // Re-sync detail items
            $pengadaan->detailPengadaans()->delete();

            foreach ($request->items as $item) {
                if (empty(trim($item['nama'] ?? ''))) {
                    continue;
                }

                DetailPengadaan::create([
                    'pengadaan_id' => $pengadaan->id,
                    'barang_id' => !empty($item['barang_id']) ? $item['barang_id'] : null,
                    'nama_barang' => trim($item['nama']),
                    'spesifikasi' => !empty($item['spesifikasi']) ? trim($item['spesifikasi']) : null,
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'unit'),
                    'harga_satuan' => (float) ($item['harga_satuan'] ?? 0),
                ]);
            }
        });

        $msg = $isSubmit
            ? "Perubahan usulan RAB berhasil diajukan kembali ke Waka Sarpras!"
            : "Perubahan usulan RAB berhasil disimpan.";

        return redirect()->route('toolman.pengadaan.show', $pengadaan->id)->with('success', $msg);
    }

    /**
     * Kirim usulan RAB berstatus draf atau revisi langsung ke Waka Sarpras (Status: Pending).
     */
    public function submit($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $pengadaan = Pengadaan::where('bengkel_id', $bengkelId)->findOrFail($id);

        if (!in_array($pengadaan->status, ['draft', 'revisi'])) {
            return redirect()->back()->with('error', "Usulan dengan status {$pengadaan->status} tidak dapat diajukan.");
        }

        if ($pengadaan->detailPengadaans()->count() === 0) {
            return redirect()->back()->with('error', "Usulan tidak memiliki rincian barang. Tambahkan item terlebih dahulu.");
        }

        $pengadaan->update([
            'status' => 'pending',
            'diajukan_pada' => now(),
        ]);

        return redirect()->route('toolman.pengadaan.show', $pengadaan->id)
            ->with('success', "Usulan RAB #RAB-" . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) . " berhasil diajukan ke Waka Sarpras!");
    }

    /**
     * Hapus usulan RAB (Hanya diperbolehkan jika berstatus draft atau rejected).
     */
    public function destroy($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $pengadaan = Pengadaan::where('bengkel_id', $bengkelId)->findOrFail($id);

        if (!in_array($pengadaan->status, ['draft', 'rejected'])) {
            return redirect()->back()->with('error', "Usulan dengan status {$pengadaan->status} tidak dapat dihapus.");
        }

        $judul = $pengadaan->judul;
        $pengadaan->delete();

        return redirect()->route('toolman.pengadaan.index')
            ->with('success', "Usulan RAB \"{$judul}\" berhasil dihapus.");
    }

    /**
     * Konfirmasi penerimaan fisik barang pengadaan (Restock barang fisik sesuai PRD 3.9).
     */
    public function receive(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $pengadaan = Pengadaan::with('detailPengadaans')->where('bengkel_id', $bengkelId)->findOrFail($id);

        if ($pengadaan->status !== 'approved') {
            return redirect()->back()->with('error', "Penerimaan fisik barang hanya dapat dilakukan untuk usulan RAB yang telah disetujui (Approved).");
        }

        DB::transaction(function () use ($pengadaan, $user, $bengkelId, $bengkel) {
            foreach ($pengadaan->detailPengadaans as $detail) {
                if ($detail->barang_id) {
                    // Barang sudah ada di master -> Tambah stoknya
                    $barang = Barang::where('bengkel_id', $bengkelId)->lockForUpdate()->find($detail->barang_id);
                    if ($barang) {
                        $barang->increment('stok_total', $detail->jumlah);
                        $barang->increment('stok_tersedia', $detail->jumlah);

                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'stok_masuk',
                            'jumlah' => $detail->jumlah,
                            'keterangan' => "Penerimaan fisik barang pengadaan #RAB-" . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) . " ({$pengadaan->judul})",
                            'referensi_tipe' => 'pengadaan',
                            'referensi_id' => $pengadaan->id,
                            'created_at' => now(),
                        ]);
                    }
                } else {
                    // Barang baru belum ada di master -> Buat master barang baru
                    $bengkelCode = preg_replace('/[^A-Za-z0-9]/', '', $bengkel->kode ?? 'BGL');
                    $kodeBarangBaru = 'BRG-' . strtoupper($bengkelCode) . '-' . date('ymd') . rand(100, 999);

                    // Pastikan kode unik
                    while (Barang::where('bengkel_id', $bengkelId)->where('kode_barang', $kodeBarangBaru)->exists()) {
                        $kodeBarangBaru = 'BRG-' . strtoupper($bengkelCode) . '-' . date('ymd') . rand(100, 999);
                    }

                    $newBarang = Barang::create([
                        'bengkel_id' => $bengkelId,
                        'kode_barang' => $kodeBarangBaru,
                        'nama' => $detail->nama_barang,
                        'jenis_barang' => 'inventaris',
                        'satuan' => $detail->satuan,
                        'stok_total' => $detail->jumlah,
                        'stok_tersedia' => $detail->jumlah,
                        'stok_dipinjam' => 0,
                        'stok_rusak' => 0,
                        'minimum_stok' => 2,
                        'deskripsi' => $detail->spesifikasi,
                    ]);

                    $detail->update(['barang_id' => $newBarang->id]);

                    StockMovement::create([
                        'barang_id' => $newBarang->id,
                        'user_id' => $user->id,
                        'jenis' => 'stok_masuk',
                        'jumlah' => $detail->jumlah,
                        'keterangan' => "Penerimaan fisik barang baru pengadaan #RAB-" . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT),
                        'referensi_tipe' => 'pengadaan',
                        'referensi_id' => $pengadaan->id,
                        'created_at' => now(),
                    ]);
                }
            }

            // Tandai pengadaan selesai (barang fisik telah diterima)
            $pengadaan->update([
                'status' => 'selesai',
            ]);
        });

        return redirect()->route('toolman.pengadaan.show', $pengadaan->id)
            ->with('success', "Konfirmasi penerimaan barang fisik berhasil! Seluruh kuota stok telah ditambahkan ke inventaris bengkel dan dicatat pada riwayat mutasi stok.");
    }
}
