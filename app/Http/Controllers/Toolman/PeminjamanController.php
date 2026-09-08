<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PeminjamanController extends Controller
{
    public function index()
    {
        return view('toolman.peminjaman.index');
    }

    public function show($id)
    {
        return view('toolman.peminjaman.show', compact('id'));
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
