<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bengkel;
use App\Models\User;
use Illuminate\Http\Request;

class ToolmanController extends Controller
{
    public function index(Request $request)
    {
        $bengkels = Bengkel::all();

        $query = User::with('bengkel')
            ->where('role', 'toolman')
            ->orderBy('name', 'asc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nomor_identitas', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('bengkel')) {
            $query->where('bengkel_id', $request->bengkel);
        }

        $toolmans = $query->paginate(15)->withQueryString();

        return view('superadmin.toolman.index', compact('toolmans', 'bengkels'));
    }

    public function create()
    {
        return view('superadmin.toolman.create');
    }

    public function store(Request $request)
    {
        // Store toolman
    }

    public function edit($id)
    {
        return view('superadmin.toolman.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update toolman
    }

    public function destroy($id)
    {
        // Delete toolman
    }
}
