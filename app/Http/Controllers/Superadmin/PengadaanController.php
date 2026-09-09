<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Pengadaan;
use Illuminate\Http\Request;

class PengadaanController extends Controller
{
    public function index(Request $request)
    {
        $filterStatus = $request->query('status', '');

        $query = Pengadaan::with(['bengkel', 'dibuatOleh', 'detailPengadaans'])
            ->whereIn('status', ['pending', 'revisi', 'approved', 'rejected'])
            ->orderByRaw("FIELD(status, 'pending', 'revisi', 'approved', 'rejected')")
            ->orderByDesc('diajukan_pada');

        if ($filterStatus && in_array($filterStatus, ['pending', 'revisi', 'approved', 'rejected'])) {
            $query->where('status', $filterStatus);
        }

        $pengadaans = $query->paginate(15)->withQueryString();

        return view('superadmin.pengadaan.index', compact('pengadaans', 'filterStatus'));
    }

    public function show($id)
    {
        $pengadaan = Pengadaan::with(['bengkel', 'dibuatOleh', 'detailPengadaans'])->findOrFail($id);
        return view('superadmin.pengadaan.show', compact('pengadaan'));
    }

    public function review(Request $request, $id)
    {
        $request->validate([
            'action'         => 'required|in:approved,revisi,rejected',
            'catatan_review' => 'nullable|string|max:1000',
        ]);

        $pengadaan = Pengadaan::findOrFail($id);

        $pengadaan->update([
            'status'         => $request->action,
            'catatan_review' => $request->catatan_review,
            'direview_oleh'  => auth()->id(),
            'direview_pada'  => now(),
        ]);

        $label = match ($request->action) {
            'approved' => 'disetujui',
            'revisi'   => 'dikembalikan untuk revisi',
            'rejected' => 'ditolak',
        };

        return redirect()->route('superadmin.pengadaan.index')
            ->with('success', "Pengajuan RAB \"{$pengadaan->judul}\" berhasil {$label}.");
    }
}

