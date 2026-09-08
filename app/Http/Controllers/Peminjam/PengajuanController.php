<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function create()
    {
        return view('peminjam.pengajuan.create');
    }

    public function store(Request $request)
    {
        // Simpan tiket peminjaman
    }
}
