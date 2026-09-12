<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\Peminjaman;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        // Ambil peminjaman yang sedang active / terlambat / menunggu pengecekan dengan barang inventaris
        $query = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->whereIn('status', ['active', 'terlambat', 'menunggu_pengecekan'])
            ->whereHas('detailPeminjamans.barang', function ($q) {
                $q->where('jenis_barang', 'inventaris');
            })
            ->orderBy('batas_kembali');

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nomor_identitas', 'like', "%{$search}%");
            });
        }

        $peminjamans = $query->paginate(10)->withQueryString();

        return view('toolman.pengembalian.index', compact('peminjamans', 'bengkel'));
    }

    public function check($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $peminjaman = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang.lokasiPenyimpanan'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        if (!in_array($peminjaman->status, ['active', 'terlambat', 'menunggu_pengecekan'])) {
            return redirect()->route('toolman.pengembalian.index')
                ->with('error', "Tiket peminjaman #{$peminjaman->id} sudah tidak dalam status yang dapat dicek fisik (status: {$peminjaman->status}).");
        }

        return view('toolman.pengembalian.check', compact('peminjaman', 'bengkel'));
    }

    public function processCheck(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $peminjaman = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        // Validasi status peminjaman
        if (!in_array($peminjaman->status, ['active', 'terlambat', 'menunggu_pengecekan'])) {
            return redirect()->route('toolman.pengembalian.index')
                ->with('error', "Tiket peminjaman #{$peminjaman->id} sudah tidak dalam status yang dapat dicek fisik (status saat ini: {$peminjaman->status}).");
        }

        // Filter detail inventaris saja yang perlu dicek kondisi pengembaliannya
        $inventarisDetails = $peminjaman->detailPeminjamans->filter(function ($detail) {
            return $detail->barang && $detail->barang->jenis_barang === 'inventaris';
        });

        if ($inventarisDetails->isEmpty()) {
            return redirect()->route('toolman.pengembalian.index')
                ->with('error', "Tiket peminjaman #{$peminjaman->id} tidak memuat barang inventaris untuk dicek fisik.");
        }

        $items = $request->input('items', []);

        // Validasi kelengkapan dan kecocokan jumlah untuk setiap detail barang inventaris
        foreach ($inventarisDetails as $detail) {
            if (!isset($items[$detail->id])) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Data inspeksi untuk barang '{$detail->barang->nama}' belum diisi.");
            }

            $itemData = $items[$detail->id];
            $baik = isset($itemData['jumlah_baik']) ? (int) $itemData['jumlah_baik'] : 0;
            $rusak = isset($itemData['jumlah_rusak']) ? (int) $itemData['jumlah_rusak'] : 0;
            $hilang = isset($itemData['jumlah_hilang']) ? (int) $itemData['jumlah_hilang'] : 0;

            if ($baik < 0 || $rusak < 0 || $hilang < 0) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Jumlah kondisi untuk barang '{$detail->barang->nama}' tidak boleh bernilai negatif.");
            }

            $totalCheck = $baik + $rusak + $hilang;
            if ($totalCheck !== (int) $detail->jumlah) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Total kuantitas pemeriksaan barang '{$detail->barang->nama}' (Baik: {$baik} + Rusak: {$rusak} + Hilang: {$hilang} = {$totalCheck}) tidak sesuai dengan jumlah dipinjam ({$detail->jumlah}).");
            }
        }

        try {
            DB::transaction(function () use ($peminjaman, $inventarisDetails, $items, $user) {
                foreach ($inventarisDetails as $detail) {
                    $itemData = $items[$detail->id];
                    $baik = (int) $itemData['jumlah_baik'];
                    $rusak = (int) $itemData['jumlah_rusak'];
                    $hilang = (int) $itemData['jumlah_hilang'];
                    $catatan = isset($itemData['catatan']) ? trim($itemData['catatan']) : null;

                    // Kunci record barang untuk update
                    $barang = Barang::where('id', $detail->barang_id)->lockForUpdate()->first();

                    // Update stok barang sesuai PRD 3.6:
                    // 1. Barang kembali baik: kembali ke stok_tersedia
                    // 2. Barang kembali rusak: pindah dari stok_dipinjam ke stok_rusak
                    // 3. Barang hilang: mengurangi stok_dipinjam sekaligus stok_total secara permanen
                    $barang->stok_tersedia += $baik;
                    $barang->stok_rusak += $rusak;
                    $barang->stok_dipinjam -= ($baik + $rusak + $hilang);
                    $barang->stok_total -= $hilang;

                    // Mencegah nilai negatif jika terjadi anomali
                    if ($barang->stok_dipinjam < 0) $barang->stok_dipinjam = 0;
                    if ($barang->stok_tersedia < 0) $barang->stok_tersedia = 0;
                    if ($barang->stok_total < 0) $barang->stok_total = 0;

                    $barang->save();

                    // Simpan data kondisi di detail_peminjamans
                    $detail->update([
                        'jumlah_baik'   => $baik,
                        'jumlah_rusak'  => $rusak,
                        'jumlah_hilang' => $hilang,
                    ]);

                    $trxCode = '#TRX-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT);
                    $peminjamName = $peminjaman->user->name ?? 'Peminjam';

                    // Catat StockMovement:
                    if ($baik > 0) {
                        StockMovement::create([
                            'barang_id'      => $barang->id,
                            'user_id'        => $user->id,
                            'jenis'          => 'pengembalian_baik',
                            'jumlah'         => $baik,
                            'referensi_tipe' => 'peminjamans',
                            'referensi_id'   => $peminjaman->id,
                            'keterangan'     => "Pengembalian barang kondisi baik {$trxCode} dari {$peminjamName}",
                            'created_at'     => now(),
                        ]);
                    }

                    if ($rusak > 0) {
                        $ketRusak = "Pengembalian barang kondisi rusak {$trxCode} dari {$peminjamName}";
                        if ($catatan) {
                            $ketRusak .= " (Catatan: {$catatan})";
                        }
                        StockMovement::create([
                            'barang_id'      => $barang->id,
                            'user_id'        => $user->id,
                            'jenis'          => 'pengembalian_rusak',
                            'jumlah'         => $rusak,
                            'referensi_tipe' => 'peminjamans',
                            'referensi_id'   => $peminjaman->id,
                            'keterangan'     => $ketRusak,
                            'created_at'     => now(),
                        ]);
                    }

                    if ($hilang > 0) {
                        $ketHilang = "Barang hilang pada tiket peminjaman {$trxCode} oleh {$peminjamName}";
                        if ($catatan) {
                            $ketHilang .= " (Catatan: {$catatan})";
                        }
                        StockMovement::create([
                            'barang_id'      => $barang->id,
                            'user_id'        => $user->id,
                            'jenis'          => 'barang_hilang',
                            'jumlah'         => $hilang,
                            'referensi_tipe' => 'peminjamans',
                            'referensi_id'   => $peminjaman->id,
                            'keterangan'     => $ketHilang,
                            'created_at'     => now(),
                        ]);
                    }
                }

                // Selesaikan transaksi peminjaman
                $peminjaman->update([
                    'status' => 'selesai',
                ]);
            });

            $trxCode = '#TRX-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT);
            return redirect()->route('toolman.pengembalian.index')
                ->with('success', "Pengecekan fisik berhasil! Peminjaman {$trxCode} telah selesai diperiksa dan stok bengkel telah diperbarui.");
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', "Gagal memproses pengecekan fisik: " . $e->getMessage());
        }
    }
}
