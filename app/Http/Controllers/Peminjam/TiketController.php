<?php

namespace App\Http\Controllers\Peminjam;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TiketController extends Controller
{
    public function index()
    {
        return view('peminjam.tiket.index');
    }

    public function show($id)
    {
        return view('peminjam.tiket.show', compact('id'));
    }
}
