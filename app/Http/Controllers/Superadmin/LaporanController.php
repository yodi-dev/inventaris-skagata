<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function mutasi(Request $request)
    {
        $bengkels = \App\Models\Bengkel::all();

        $query = \App\Models\StockMovement::with(['barang.bengkel', 'user'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('bengkel')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('bengkel_id', $request->bengkel);
            });
        }

        $movements = $query->paginate(15)->withQueryString();

        return view('superadmin.laporan.mutasi', compact('movements', 'bengkels'));
    }

    public function konsumsi(Request $request)
    {
        $bengkels = \App\Models\Bengkel::all();

        $query = \App\Models\StockMovement::with(['barang.bengkel', 'user'])
            ->where('jenis', 'bhp_keluar')
            ->orderBy('created_at', 'desc');

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('bengkel')) {
            $query->whereHas('barang', function ($q) use ($request) {
                $q->where('bengkel_id', $request->bengkel);
            });
        }

        $konsumsi = $query->paginate(15)->withQueryString();

        return view('superadmin.laporan.konsumsi', compact('konsumsi', 'bengkels'));
    }
}
