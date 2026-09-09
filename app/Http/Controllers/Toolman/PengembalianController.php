<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        // Ambil peminjaman yang sedang aktif / terlambat dengan barang inventaris
        $query = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->whereIn('status', ['aktif', 'terlambat'])
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

        $peminjaman = Peminjaman::with(['user', 'bengkel', 'detailPeminjamans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        return view('toolman.pengembalian.check', compact('peminjaman', 'bengkel'));
    }

    public function processCheck(Request $request, $id)
    {
        // Catat jumlah baik, rusak, hilang
    }
}
