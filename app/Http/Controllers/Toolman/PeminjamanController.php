<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\Peminjaman;
use Illuminate\Http\Request;

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
            $query->where('status', 'menunggu_acc')->latest('tanggal_pinjam');
        } else {
            $query->where('status', '!=', 'menunggu_acc')->latest('tanggal_pinjam');
        }

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nomor_identitas', 'like', "%{$search}%");
            });
        }

        $peminjamans = $query->paginate(10)->withQueryString();

        $pendingCount = Peminjaman::where('bengkel_id', $bengkelId)
            ->where('status', 'menunggu_acc')
            ->count();

        $riwayatCount = Peminjaman::where('bengkel_id', $bengkelId)
            ->where('status', '!=', 'menunggu_acc')
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
        // Approve peminjaman
    }

    public function reject(Request $request, $id)
    {
        // Reject peminjaman
    }
}
