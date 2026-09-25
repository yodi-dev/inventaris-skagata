<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengajuanController extends Controller
{
    /**
     * Redirect direct pengajuan create requests to Katalog with open cart.
     */
    public function create()
    {
        return redirect()->route('peminjam.katalog.index', ['open_cart' => 1]);
    }

    /**
     * Simpan pengajuan peminjaman baru (dari keranjang multi-item atau single item).
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        // Parse items jika dikirim dalam bentuk JSON (dari keranjang katalog)
        $rawItems = $request->input('items');
        if (is_string($rawItems)) {
            $rawItems = json_decode($rawItems, true);
        }

        // Jika bukan array items, cek single input form
        if (empty($rawItems) && $request->filled('barang_id')) {
            $rawItems = [
                [
                    'barang_id' => $request->input('barang_id'),
                    'jumlah' => $request->input('jumlah', 1),
                ]
            ];
        }

        if (empty($rawItems) || !is_array($rawItems)) {
            return back()->with('error', 'Tidak ada barang yang dipilih untuk diajukan.');
        }

        // Tentukan bengkel transaksi
        if ($user->isGuru()) {
            $bengkelId = $request->input('bengkel_id');
            if (!$bengkelId) {
                $firstItem = Barang::find($rawItems[0]['barang_id'] ?? $rawItems[0]['id'] ?? null);
                $bengkelId = $firstItem?->bengkel_id ?? Bengkel::first()?->id;
            }
        } else {
            $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        }

        // Validasi input keperluan & batas waktu
        $request->validate([
            'keperluan' => 'required|string|min:5|max:1000',
            'batas_kembali' => 'nullable|date|after:now',
        ], [
            'keperluan.required' => 'Keperluan peminjaman wajib diisi.',
            'keperluan.min' => 'Keperluan peminjaman minimal 5 karakter.',
            'batas_kembali.after' => 'Batas waktu pengembalian tidak boleh di masa lampau.',
        ]);

        try {
            return DB::transaction(function () use ($user, $bengkelId, $rawItems, $request) {
                $hasInventaris = false;

                // Validasi setiap barang
                $itemsToInsert = [];
                foreach ($rawItems as $item) {
                    $barangId = $item['barang_id'] ?? $item['id'] ?? null;
                    $qty = (int) ($item['jumlah'] ?? $item['qty'] ?? 1);

                    if (!$barangId || $qty <= 0) continue;

                    $barang = Barang::where('bengkel_id', $bengkelId)->find($barangId);
                    if (!$barang) {
                        throw new \Exception("Barang dengan ID {$barangId} tidak ditemukan pada bengkel terpilih.");
                    }

                    if ($qty > $barang->stok_tersedia) {
                        throw new \Exception("Stok untuk barang '{$barang->nama}' tidak mencukupi (tersedia: {$barang->stok_tersedia}, diminta: {$qty}).");
                    }

                    if ($barang->jenis_barang === 'inventaris') {
                        $hasInventaris = true;
                    }

                    $itemsToInsert[] = [
                        'barang_id' => $barang->id,
                        'jumlah' => $qty,
                    ];
                }

                if (empty($itemsToInsert)) {
                    throw new \Exception('Daftar barang tidak valid atau kosong.');
                }

                // Batas kembali: Wajib untuk tiket yang berisi barang inventaris
                $batasKembali = $request->input('batas_kembali');
                if ($hasInventaris && !$batasKembali) {
                    // Default cerdas: jika lewat jam 15:00, default ke besok 16:00
                    $batasKembali = now()->hour >= 15
                        ? now()->addDay()->setTime(16, 0)
                        : now()->setTime(16, 0);
                }

                $peminjaman = Peminjaman::create([
                    'user_id' => $user->id,
                    'bengkel_id' => $bengkelId,
                    'tanggal_pinjam' => now(),
                    'batas_kembali' => $hasInventaris ? $batasKembali : null,
                    'keperluan' => $request->input('keperluan'),
                    'status' => 'pending',
                ]);

                foreach ($itemsToInsert as $itemData) {
                    DetailPeminjaman::create([
                        'peminjaman_id' => $peminjaman->id,
                        'barang_id' => $itemData['barang_id'],
                        'jumlah' => $itemData['jumlah'],
                        'jumlah_baik' => 0,
                        'jumlah_rusak' => 0,
                        'jumlah_hilang' => 0,
                    ]);
                }

                return redirect()->route('peminjam.tiket.index')
                    ->with('success', "Tiket peminjaman #TRX-" . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) . " berhasil diajukan dan sedang menunggu persetujuan Toolman!");
            });
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
