<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengadaanController extends Controller
{
    public function index()
    {
        return view('superadmin.pengadaan.index');
    }

    public function show($id)
    {
        return view('superadmin.pengadaan.show', compact('id'));
    }

    public function review(Request $request, $id)
    {
        // Approve, Revisi, Reject
    }
}
