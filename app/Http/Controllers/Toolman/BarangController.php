<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use App\Models\LokasiPenyimpanan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $query = Barang::with(['lokasiPenyimpanan', 'bengkel'])
            ->where('bengkel_id', $bengkelId);

        // Pencarian Nama / Kode Barang
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        // Filter Tipe Barang
        if ($jenis = $request->input('jenis')) {
            if (in_array($jenis, ['inventaris', 'bhp'])) {
                $query->where('jenis_barang', $jenis);
            }
        }

        // Filter Status Barang
        if ($status = $request->input('status')) {
            if ($status === 'tersedia') {
                $query->where('stok_tersedia', '>', 0);
            } elseif ($status === 'dipinjam') {
                $query->where('stok_dipinjam', '>', 0);
            } elseif ($status === 'rusak') {
                $query->where('stok_rusak', '>', 0);
            } elseif ($status === 'limit') {
                $query->whereColumn('stok_tersedia', '<=', 'minimum_stok');
            }
        }

        $barangs = $query->orderBy('nama')->paginate(10)->withQueryString();

        return view('toolman.barang.index', compact('barangs', 'bengkel'));
    }

    public function create()
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);
        $lokasiPenyimpanans = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->get();

        return view('toolman.barang.create', compact('bengkel', 'lokasiPenyimpanans'));
    }

    public function store(Request $request)
    {
        // Store barang
    }

    public function edit($id = null)
    {
        $user = auth()->user();
        $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
        $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);

        $barang = null;
        if ($id) {
            $barang = Barang::with('lokasiPenyimpanan')
                ->where('bengkel_id', $bengkelId)
                ->findOrFail($id);
        } else {
            $barang = Barang::with('lokasiPenyimpanan')
                ->where('bengkel_id', $bengkelId)
                ->firstOrFail();
        }

        $lokasiPenyimpanans = LokasiPenyimpanan::where('bengkel_id', $bengkelId)->get();

        return view('toolman.barang.edit', compact('barang', 'bengkel', 'lokasiPenyimpanans'));
    }

    public function update(Request $request, $id)
    {
        // Update barang
    }

    public function destroy($id)
    {
        // Delete barang
    }
}
