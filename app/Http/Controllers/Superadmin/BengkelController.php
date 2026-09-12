<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BengkelController extends Controller
{
    public function index()
    {
        $bengkels = \App\Models\Bengkel::with(['users' => function ($q) {
            $q->where('role', 'toolman')->take(1);
        }])
            ->withCount([
                'barangs as inventaris_count' => function ($query) {
                    $query->where('jenis_barang', 'inventaris');
                },
                'barangs as bhp_count' => function ($query) {
                    $query->where('jenis_barang', 'bhp');
                }
            ])
            ->get();

        return view('superadmin.bengkel.index', compact('bengkels'));
    }

    public function create()
    {
        return view('superadmin.bengkel.create');
    }

    public function store(Request $request)
    {
        // Store bengkel
    }

    public function edit($id)
    {
        return view('superadmin.bengkel.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update bengkel
    }

    public function destroy($id)
    {
        // Delete bengkel
    }
}
