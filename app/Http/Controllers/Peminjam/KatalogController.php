<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Bengkel;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isGuru = $user->isGuru();

        $bengkels = Bengkel::orderBy('nama')->get();

        if ($isGuru) {
            // Guru dapat memilih bengkel manapun atau default ke bengkel pertama
            $selectedBengkelId = $request->input('bengkel_id');
            if ($selectedBengkelId && $selectedBengkelId !== 'all') {
                $bengkel = Bengkel::find($selectedBengkelId) ?? Bengkel::first();
            } else {
                $bengkel = Bengkel::first();
            }
            $bengkelId = $bengkel?->id;
        } else {
            // Siswa terikat hanya pada bengkelnya sendiri
            $bengkelId = $user->bengkel_id ?? Bengkel::first()?->id;
            $bengkel = $user->bengkel ?? Bengkel::find($bengkelId);
        }

        $query = Barang::with(['bengkel', 'lokasiPenyimpanan']);

        if ($isGuru) {
            $filterBengkel = $request->input('bengkel_id');
            if ($filterBengkel && $filterBengkel === 'all') {
                // Guru melihat seluruh bengkel
            } elseif ($filterBengkel) {
                $query->where('bengkel_id', $filterBengkel);
            } else {
                // Default ke bengkel aktif pertama
                if ($bengkelId) {
                    $query->where('bengkel_id', $bengkelId);
                }
            }
        } else {
            $query->where('bengkel_id', $bengkelId);
        }

        // Filter pencarian teks
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('kode_barang', 'like', "%{$search}%")
                    ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        // Filter jenis/kategori barang: all | inventaris | bhp
        if ($tipe = $request->input('tipe')) {
            if (in_array($tipe, ['inventaris', 'bhp'])) {
                $query->where('jenis_barang', $tipe);
            }
        }

        // Filter ketersediaan stok
        if ($status = $request->input('status')) {
            if ($status === 'tersedia') {
                $query->where('stok_tersedia', '>', 0);
            } elseif ($status === 'mepet' || $status === 'limit') {
                $query->where('stok_tersedia', '>', 0)
                    ->whereColumn('stok_tersedia', '<=', 'minimum_stok');
            } elseif ($status === 'habis' || $status === 'dipinjam') {
                $query->where('stok_tersedia', 0);
            }
        }

        $barangs = $query->orderBy('nama')->paginate(12)->withQueryString();

        // Query untuk counter badge kategori pada konteks bengkel saat ini
        $countQuery = Barang::query();
        if ($isGuru) {
            $filterBengkel = $request->input('bengkel_id');
            if ($filterBengkel && $filterBengkel !== 'all') {
                $countQuery->where('bengkel_id', $filterBengkel);
            } elseif (!$filterBengkel && $bengkelId) {
                $countQuery->where('bengkel_id', $bengkelId);
            }
        } else {
            $countQuery->where('bengkel_id', $bengkelId);
        }

        $totalCount = (clone $countQuery)->count();
        $inventarisCount = (clone $countQuery)->where('jenis_barang', 'inventaris')->count();
        $bhpCount = (clone $countQuery)->where('jenis_barang', 'bhp')->count();

        return view('peminjam.katalog.index', compact(
            'barangs',
            'bengkels',
            'bengkel',
            'user',
            'isGuru',
            'totalCount',
            'inventarisCount',
            'bhpCount'
        ));
    }
}
