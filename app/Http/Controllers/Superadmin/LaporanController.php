<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function mutasi()
    {
        return view('superadmin.laporan.mutasi');
    }

    public function konsumsi()
    {
        return view('superadmin.laporan.konsumsi');
    }
}
