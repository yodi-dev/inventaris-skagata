<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\Peminjaman;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $tab = $request->input('tab', 'pending'); // 'pending' | 'riwayat'

        $query = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang'])
            ->where('bengkel_id', $bengkelId);

        if ($tab === 'pending') {
            $query->whereIn('status', ['pending', 'menunggu_acc'])->latest('tanggal_pinjam');
        } else {
            $query->whereNotIn('status', ['pending', 'menunggu_acc'])->latest('tanggal_pinjam');
        }

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nomor_identitas', 'like', "%{$search}%");
            });
        }

        $peminjamans = $query->paginate(10)->withQueryString();

        $pendingCount = Peminjaman::where('bengkel_id', $bengkelId)
            ->whereIn('status', ['pending', 'menunggu_acc'])
            ->count();

        $riwayatCount = Peminjaman::where('bengkel_id', $bengkelId)
            ->whereNotIn('status', ['pending', 'menunggu_acc'])
            ->count();

        return view('toolman.peminjaman.index', compact('peminjamans', 'bengkel', 'tab', 'pendingCount', 'riwayatCount'));
    }

    public function show($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $peminjaman = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang', 'diprosesOleh'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        return view('toolman.peminjaman.show', compact('peminjaman', 'bengkel'));
    }

    public function approve($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $peminjaman = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        // Validasi status
        if (!in_array($peminjaman->status, ['pending', 'menunggu_acc'])) {
            return redirect()->back()->with('error', "Tiket peminjaman #{$peminjaman->id} sudah tidak dalam status menunggu persetujuan (status saat ini: {$peminjaman->status}).");
        }

        if ($peminjaman->detailPeminjamans->isEmpty()) {
            return redirect()->back()->with('error', "Tiket peminjaman #{$peminjaman->id} tidak memiliki rincian barang.");
        }

        // Validasi ketersediaan stok
        foreach ($peminjaman->detailPeminjamans as $detail) {
            $barang = $detail->barang;
            if (!$barang) {
                return redirect()->back()->with('error', "Data barang #{$detail->barang_id} tidak ditemukan.");
            }
            if ($barang->stok_tersedia < $detail->jumlah) {
                return redirect()->back()->with('error', "Stok barang '{$barang->nama}' tidak mencukupi! (Tersedia: {$barang->stok_tersedia} {$barang->satuan}, Diminta: {$detail->jumlah} {$barang->satuan}).");
            }
        }

        try {
            DB::transaction(function () use ($peminjaman, $user) {
                $hasInventaris = false;

                foreach ($peminjaman->detailPeminjamans as $detail) {
                    $barang = Barang::where('id', $detail->barang_id)->lockForUpdate()->first();

                    if ($barang->stok_tersedia < $detail->jumlah) {
                        throw new \Exception("Stok barang '{$barang->nama}' tidak mencukupi saat proses approval! (Tersedia: {$barang->stok_tersedia}, Diminta: {$detail->jumlah}).");
                    }

                    if ($barang->jenis_barang === 'inventaris') {
                        $hasInventaris = true;
                        $barang->stok_tersedia -= $detail->jumlah;
                        $barang->stok_dipinjam += $detail->jumlah;
                        $barang->save();

                        StockMovement::create([
                            'barang_id'      => $barang->id,
                            'user_id'        => $user->id,
                            'jenis'          => 'peminjaman',
                            'jumlah'         => $detail->jumlah,
                            'referensi_tipe' => 'peminjamans',
                            'referensi_id'   => $peminjaman->id,
                            'keterangan'     => "Peminjaman alat #TRX-" . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) . " disetujui untuk " . ($peminjaman->user->name ?? 'Peminjam'),
                            'created_at'     => now(),
                        ]);
                    } else {
                        // BHP: Dikonsumsi habis, stok_tersedia dan stok_total berkurang permanen
                        $barang->stok_tersedia -= $detail->jumlah;
                        $barang->stok_total -= $detail->jumlah;
                        $barang->save();

                        StockMovement::create([
                            'barang_id'      => $barang->id,
                            'user_id'        => $user->id,
                            'jenis'          => 'bhp_keluar',
                            'jumlah'         => $detail->jumlah,
                            'referensi_tipe' => 'peminjamans',
                            'referensi_id'   => $peminjaman->id,
                            'keterangan'     => "Pengambilan BHP #TRX-" . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT) . " disetujui untuk " . ($peminjaman->user->name ?? 'Peminjam'),
                            'created_at'     => now(),
                        ]);
                    }
                }

                // Tentukan status akhir tiket
                // Sesuai PRD 3.7: Jika BHP-only langsung 'selesai', jika ada inventaris maka 'active'
                $finalStatus = $hasInventaris ? 'active' : 'selesai';

                $peminjaman->update([
                    'status'        => $finalStatus,
                    'diproses_oleh' => $user->id,
                    'diproses_pada' => now(),
                ]);
            });

            $trxCode = '#TRX-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT);
            return redirect()->route('toolman.peminjaman.index', ['tab' => 'riwayat'])
                ->with('success', "Tiket {$trxCode} berhasil disetujui dan diserahkan kepada peminjam.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal memproses persetujuan: " . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;

        $request->validate([
            'alasan_penolakan' => 'required|string|min:3|max:500',
        ], [
            'alasan_penolakan.required' => 'Alasan penolakan wajib diisi agar peminjam mengetahui alasan pembatalan.',
            'alasan_penolakan.min'      => 'Alasan penolakan minimal 3 karakter.',
            'alasan_penolakan.max'      => 'Alasan penolakan maksimal 500 karakter.',
        ]);

        $peminjaman = Peminjaman::where('bengkel_id', $bengkelId)->findOrFail($id);

        if (!in_array($peminjaman->status, ['pending', 'menunggu_acc'])) {
            return redirect()->back()->with('error', "Tiket peminjaman #{$peminjaman->id} sudah tidak dalam status menunggu persetujuan (status saat ini: {$peminjaman->status}).");
        }

        $peminjaman->update([
            'status'           => 'ditolak',
            'alasan_penolakan' => $request->input('alasan_penolakan'),
            'diproses_oleh'    => $user->id,
            'diproses_pada'    => now(),
        ]);

        $trxCode = '#TRX-' . str_pad($peminjaman->id, 4, '0', STR_PAD_LEFT);
        return redirect()->route('toolman.peminjaman.index', ['tab' => 'riwayat'])
            ->with('success', "Pengajuan peminjaman {$trxCode} berhasil ditolak.");
    }
}
