<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PeminjamController extends Controller
{
    public function index()
    {
        return view('toolman.peminjam.index');
    }

    public function show($id)
    {
        return view('toolman.peminjam.show', compact('id'));
    }

    public function approveUser($id)
    {
        // Approve registrasi
    }

    public function suspendUser($id)
    {
        // Suspend user
    }

    public function activateUser($id)
    {
        // Reactivate user
    }
}
