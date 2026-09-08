<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengadaanController extends Controller
{
    public function index()
    {
        return view('toolman.pengadaan.index');
    }

    public function create()
    {
        return view('toolman.pengadaan.create');
    }

    public function store(Request $request)
    {
        // Store RAB draft
    }

    public function show($id)
    {
        return view('toolman.pengadaan.show', compact('id'));
    }

    public function edit($id)
    {
        return view('toolman.pengadaan.edit', compact('id'));
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
