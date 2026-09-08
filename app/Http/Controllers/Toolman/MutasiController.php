<?php

namespace App\Http\Controllers\Toolman;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MutasiController extends Controller
{
    public function index()
    {
        return view('toolman.mutasi.index');
    }
}
