<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        return view('toolman.barang.index');
    }

    public function create()
    {
        return view('toolman.barang.create');
    }

    public function store(Request $request)
    {
        // Store barang
    }

    public function edit($id)
    {
        return view('toolman.barang.edit', compact('id'));
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
