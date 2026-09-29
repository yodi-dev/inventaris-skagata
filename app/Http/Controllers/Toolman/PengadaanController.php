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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PengadaanController extends Controller
{
    /**
     * Tampilkan daftar usulan RAB pengadaan bengkel dengan filter status dan pencarian.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);

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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);

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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
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
            'items.*.jenis_barang' => 'nullable|in:inventaris,bhp',
            'items.*.minimum_stok' => 'nullable|integer|min:0',
            'items.*.barang_id' => [
                'nullable',
                Rule::exists('barangs', 'id')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                }),
            ],
        ], [
            'items.required' => 'Usulan pengadaan harus mencantumkan minimal 1 item barang.',
            'items.min' => 'Usulan pengadaan harus mencantumkan minimal 1 item barang.',
            'items.*.nama.required' => 'Nama barang pada setiap baris wajib diisi.',
            'items.*.jumlah.min' => 'Jumlah barang minimal 1.',
        ]);

        $isSubmit = $request->input('action') === 'submit';

        // Validasi tipe barang wajib dipilih saat usulan diajukan (submit) untuk setiap item baru
        if ($isSubmit) {
            foreach ($request->items as $idx => $it) {
                if (empty($it['barang_id']) && (empty($it['jenis_barang']) || !in_array($it['jenis_barang'], ['inventaris', 'bhp'], true))) {
                    return back()->withErrors(["items.{$idx}.jenis_barang" => "Tipe barang (Inventaris atau BHP) wajib dipilih untuk usulan barang baru '{$it['nama']}'."])->withInput();
                }
            }
        }

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

                $barangId = !empty($item['barang_id']) ? $item['barang_id'] : null;
                $linkedBarang = $barangId ? Barang::find($barangId) : null;
                $jenisBarang = $linkedBarang ? $linkedBarang->jenis_barang : ($item['jenis_barang'] ?? null);
                $minStok = $linkedBarang ? $linkedBarang->minimum_stok : (isset($item['minimum_stok']) && $item['minimum_stok'] !== '' ? (int) $item['minimum_stok'] : null);

                DetailPengadaan::create([
                    'pengadaan_id' => $rab->id,
                    'barang_id' => $barangId,
                    'nama_barang' => trim($item['nama']),
                    'spesifikasi' => !empty($item['spesifikasi']) ? trim($item['spesifikasi']) : null,
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'unit'),
                    'harga_satuan' => (float) ($item['harga_satuan'] ?? 0),
                    'jenis_barang' => $jenisBarang,
                    'minimum_stok' => $minStok,
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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);

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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);

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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }

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
            'items.*.jenis_barang' => 'nullable|in:inventaris,bhp',
            'items.*.minimum_stok' => 'nullable|integer|min:0',
            'items.*.barang_id' => [
                'nullable',
                Rule::exists('barangs', 'id')->where(function ($query) use ($bengkelId) {
                    return $query->where('bengkel_id', $bengkelId);
                }),
            ],
        ]);

        $isSubmit = $request->input('action') === 'submit';

        // Validasi tipe barang wajib dipilih saat usulan diajukan (submit) untuk setiap item baru
        if ($isSubmit) {
            foreach ($request->items as $idx => $it) {
                if (empty($it['barang_id']) && (empty($it['jenis_barang']) || !in_array($it['jenis_barang'], ['inventaris', 'bhp'], true))) {
                    return back()->withErrors(["items.{$idx}.jenis_barang" => "Tipe barang (Inventaris atau BHP) wajib dipilih untuk usulan barang baru '{$it['nama']}'."])->withInput();
                }
            }
        }

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

                $barangId = !empty($item['barang_id']) ? $item['barang_id'] : null;
                $linkedBarang = $barangId ? Barang::find($barangId) : null;
                $jenisBarang = $linkedBarang ? $linkedBarang->jenis_barang : ($item['jenis_barang'] ?? null);
                $minStok = $linkedBarang ? $linkedBarang->minimum_stok : (isset($item['minimum_stok']) && $item['minimum_stok'] !== '' ? (int) $item['minimum_stok'] : null);

                DetailPengadaan::create([
                    'pengadaan_id' => $pengadaan->id,
                    'barang_id' => $barangId,
                    'nama_barang' => trim($item['nama']),
                    'spesifikasi' => !empty($item['spesifikasi']) ? trim($item['spesifikasi']) : null,
                    'jumlah' => (int) $item['jumlah'],
                    'satuan' => trim($item['satuan'] ?? 'unit'),
                    'harga_satuan' => (float) ($item['harga_satuan'] ?? 0),
                    'jenis_barang' => $jenisBarang,
                    'minimum_stok' => $minStok,
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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }

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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }

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
        $bengkelId = $user->bengkel_id;
        if (!$bengkelId) {
            abort(403, 'Akun Toolman Anda belum ditugaskan ke unit bengkel manapun. Silakan hubungi Waka Sarpras.');
        }
        $bengkel = $user->bengkel ?? Bengkel::findOrFail($bengkelId);

        try {
            $formattedRabCode = DB::transaction(function () use ($id, $bengkelId, $bengkel, $user, $request) {
                // 1. Kunci dan ambil record Pengadaan (Lock Order #1)
                $pengadaan = Pengadaan::where('bengkel_id', $bengkelId)
                    ->where('id', $id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // 2. Validasi status di DALAM transaksi yang terkunci
                if ($pengadaan->status !== 'approved') {
                    throw new \DomainException("Penerimaan fisik barang hanya dapat dilakukan untuk usulan RAB yang telah disetujui (status saat ini: {$pengadaan->status}).");
                }

                $details = $pengadaan->detailPengadaans()->lockForUpdate()->get();
                if ($details->isEmpty()) {
                    throw new \DomainException("Usulan RAB #RAB-" . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT) . " tidak memiliki rincian barang.");
                }

                // 3. Kunci seluruh barang existing yang direferensikan dalam urutan id menaik (Lock Order #2: ORDER BY id ASC)
                $existingBarangIds = $details->pluck('barang_id')->filter()->unique()->sort()->values()->all();
                $existingBarangs = Barang::whereIn('id', $existingBarangIds)
                    ->lockForUpdate()
                    ->orderBy('id', 'asc')
                    ->get()
                    ->keyBy('id');

                // 4. Validasi kepemilikan bengkel dan integritas seluruh barang existing
                foreach ($details as $detail) {
                    if ($detail->barang_id) {
                        $barang = $existingBarangs->get($detail->barang_id);
                        if (!$barang || (int) $barang->bengkel_id !== (int) $bengkelId) {
                            throw new \DomainException("Barang '{$detail->nama_barang}' (ID #{$detail->barang_id}) tidak ditemukan atau bukan milik bengkel ini.");
                        }
                    }
                    if ($detail->jumlah <= 0) {
                        throw new \DomainException("Kuantitas barang '{$detail->nama_barang}' harus lebih dari 0.");
                    }
                }

                $rabCode = '#RAB-' . str_pad($pengadaan->id, 4, '0', STR_PAD_LEFT);

                // 5. Terapkan penambahan stok / pembuatan master barang dan StockMovement
                foreach ($details as $detail) {
                    if ($detail->barang_id) {
                        // Barang sudah ada di master -> Tambah stoknya
                        $barang = $existingBarangs->get($detail->barang_id);
                        $barang->increment('stok_total', $detail->jumlah);
                        $barang->increment('stok_tersedia', $detail->jumlah);

                        StockMovement::create([
                            'barang_id' => $barang->id,
                            'user_id' => $user->id,
                            'jenis' => 'stok_masuk',
                            'jumlah' => $detail->jumlah,
                            'keterangan' => "Penerimaan fisik barang pengadaan {$rabCode} ({$pengadaan->judul})",
                            'referensi_tipe' => 'pengadaan',
                            'referensi_id' => $pengadaan->id,
                            'created_at' => now(),
                        ]);
                    } else {
                        // Barang baru belum ada di master -> Tentukan jenis_barang & minimum_stok
                        $confirmedType = $detail->jenis_barang;
                        $confirmedMinStok = $detail->minimum_stok;

                        // Periksa apakah ada input konfirmasi saat penerimaan (misalnya untuk RAB lama yang jenis_barang-nya belum ada)
                        $reqType = $request->input("items_classification.{$detail->id}.jenis_barang")
                            ?? $request->input("items.{$detail->id}.jenis_barang");
                        $reqMinStok = $request->input("items_classification.{$detail->id}.minimum_stok")
                            ?? $request->input("items.{$detail->id}.minimum_stok");

                        if (!empty($reqType) && in_array($reqType, ['inventaris', 'bhp'], true)) {
                            $confirmedType = $reqType;
                            if ($reqMinStok !== null && $reqMinStok !== '') {
                                $confirmedMinStok = (int) $reqMinStok;
                            }
                        }

                        // JIKA JENIS BARANG BELUM DITETAPKAN: Wajib konfirmasi dari pengguna yang berwenang, tidak boleh menebak!
                        if (empty($confirmedType) || !in_array($confirmedType, ['inventaris', 'bhp'], true)) {
                            throw new \DomainException("Item '{$detail->nama_barang}' merupakan barang baru yang belum memiliki klasifikasi tipe barang (Inventaris atau BHP). Silakan konfirmasi jenis barang terlebih dahulu sebelum memproses penerimaan fisik.");
                        }

                        // Update rincian usulan jika dikonfirmasi saat penerimaan
                        if ($detail->jenis_barang !== $confirmedType || ($confirmedMinStok !== null && $detail->minimum_stok !== $confirmedMinStok)) {
                            $detail->update([
                                'jenis_barang' => $confirmedType,
                                'minimum_stok' => $confirmedMinStok ?? $detail->minimum_stok,
                            ]);
                        }

                        $prefix = ($confirmedType === 'bhp') ? 'BHP' : 'INV';
                        $finalMinimumStok = $confirmedMinStok ?? 0;

                        $bengkelCode = preg_replace('/[^A-Za-z0-9]/', '', strtoupper($bengkel->kode ?? 'BGL'));
                        $datePrefix = date('ymd');
                        $seq = 1;
                        do {
                            $kodeBarangBaru = "{$prefix}-{$bengkelCode}-{$datePrefix}" . str_pad((string) $seq, 3, '0', STR_PAD_LEFT);
                            $seq++;
                        } while (Barang::where('bengkel_id', $bengkelId)->where('kode_barang', $kodeBarangBaru)->exists());

                        $newBarang = Barang::create([
                            'bengkel_id' => $bengkelId,
                            'lokasi_penyimpanan_id' => null,
                            'sumber_dana_id' => null,
                            'kode_barang' => $kodeBarangBaru,
                            'nama' => $detail->nama_barang,
                            'jenis_barang' => $confirmedType,
                            'satuan' => $detail->satuan,
                            'harga' => $detail->harga_satuan ?? 0,
                            'stok_total' => $detail->jumlah,
                            'stok_tersedia' => $detail->jumlah,
                            'stok_dipinjam' => 0,
                            'stok_rusak' => 0,
                            'minimum_stok' => $finalMinimumStok,
                            'deskripsi' => $detail->spesifikasi,
                        ]);

                        $detail->update(['barang_id' => $newBarang->id]);

                        StockMovement::create([
                            'barang_id' => $newBarang->id,
                            'user_id' => $user->id,
                            'jenis' => 'stok_masuk',
                            'jumlah' => $detail->jumlah,
                            'keterangan' => "Penerimaan fisik barang baru pengadaan {$rabCode} ({$pengadaan->judul})",
                            'referensi_tipe' => 'pengadaan',
                            'referensi_id' => $pengadaan->id,
                            'created_at' => now(),
                        ]);
                    }
                }

                // 6. Tandai pengadaan selesai
                $pengadaan->update([
                    'status' => 'selesai',
                ]);

                return $rabCode;
            });

            return redirect()->route('toolman.pengadaan.show', $id)
                ->with('success', "Konfirmasi penerimaan barang fisik {$formattedRabCode} berhasil! Seluruh kuota stok telah ditambahkan ke inventaris bengkel dan dicatat pada riwayat mutasi stok.");
        } catch (\DomainException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error("Gagal memproses penerimaan barang RAB #{$id}: " . $e->getMessage());
            return redirect()->back()->with('error', "Gagal memproses penerimaan barang fisik: Terjadi kesalahan sistem atau konflik transaksi.");
        }
    }
}
