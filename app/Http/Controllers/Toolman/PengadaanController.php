<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\Pengadaan;
use Illuminate\Http\Request;

class PengadaanController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $filterStatus = $request->query('status', '');

        $query = Pengadaan::with(['dibuatOleh', 'direviewOleh', 'detailPengadaans'])
            ->where('bengkel_id', $bengkelId)
            ->latest('created_at');

        if ($filterStatus && in_array($filterStatus, ['draft', 'pending', 'revisi', 'approved', 'rejected'])) {
            $query->where('status', $filterStatus);
        }

        $pengadaans = $query->paginate(10)->withQueryString();

        return view('toolman.pengadaan.index', compact('pengadaans', 'bengkel', 'filterStatus'));
    }

    public function create()
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        // Rekomendasi barang yang menipis atau rusak untuk fitur generate
        $limitItems = Barang::where('bengkel_id', $bengkelId)
            ->where(function ($q) {
                $q->whereColumn('stok_tersedia', '<=', 'minimum_stok')
                    ->orWhere('stok_rusak', '>', 0);
            })
            ->get();

        return view('toolman.pengadaan.create', compact('bengkel', 'limitItems'));
    }

    public function store(Request $request)
    {
        // Store RAB draft
    }

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

    public function edit($id)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $pengadaan = Pengadaan::with(['detailPengadaans.barang'])
            ->where('bengkel_id', $bengkelId)
            ->findOrFail($id);

        return view('toolman.pengadaan.edit', compact('pengadaan', 'bengkel'));
    }

    public function update(Request $request, $id)
    {
        // Update RAB draft
    }

    public function submit($id)
    {
        // Kirim draft ke Waka (status Pending)
    }
}
