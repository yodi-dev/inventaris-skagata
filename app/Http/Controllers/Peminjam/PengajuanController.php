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
    public function create(Request $request)
    {
        $user = auth()->user();
        $barang = null;

        if ($barangId = $request->input('barang_id')) {
            $barang = Barang::with(['bengkel', 'lokasiPenyimpanan'])->find($barangId);
        }

        if ($user->isGuru()) {
            $bengkel = $barang?->bengkel ?? Bengkel::find($request->input('bengkel_id')) ?? Bengkel::first();
        } else {
            $bengkel = $user->bengkel ?? Bengkel::find($user->bengkel_id);
        }

        // Ambil barang-barang tersedia di bengkel untuk dropdown alternatif
        $availableBarangs = Barang::where('bengkel_id', $bengkel?->id)
            ->where('stok_tersedia', '>', 0)
            ->orderBy('nama')
            ->get();

        return view('peminjam.pengajuan.create', compact('barang', 'bengkel', 'user', 'availableBarangs'));
    }

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
                $firstItem = Barang::find($rawItems[0]['barang_id'] ?? null);
                $bengkelId = $firstItem?->bengkel_id ?? Bengkel::first()?->id;
            }
        } else {
            $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        }

        // Validasi input keperluan
        $request->validate([
            'keperluan' => 'required|string|max:1000',
            'batas_kembali' => 'nullable|date',
        ]);

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
                    throw new \Exception("Stok untuk barang {$barang->nama} tidak mencukupi (tersedia: {$barang->stok_tersedia}, diminta: {$qty}).");
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

            // Batas kembali: Wajib untuk tiket yang berisi barang inventaris (PRD 3.5 & 6.4)
            $batasKembali = $request->input('batas_kembali');
            if ($hasInventaris && !$batasKembali) {
                // Default ke akhir hari ini (16:00 WIB)
                $batasKembali = now()->setTime(16, 0);
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
    }
}
