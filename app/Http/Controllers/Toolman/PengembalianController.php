<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengembalianController extends Controller
{
    public function index()
    {
        return view('toolman.pengembalian.index');
    }

    public function check($id)
    {
        return view('toolman.pengembalian.check', compact('id'));
    }

    public function processCheck(Request $request, $id)
    {
        // Catat jumlah baik, rusak, hilang
    }
}
