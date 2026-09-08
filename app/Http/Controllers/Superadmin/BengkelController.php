<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BengkelController extends Controller
{
    public function index()
    {
        return view('superadmin.bengkel.index');
    }

    public function create()
    {
        return view('superadmin.bengkel.create');
    }

    public function store(Request $request)
    {
        // Store bengkel
    }

    public function edit($id)
    {
        return view('superadmin.bengkel.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update bengkel
    }

    public function destroy($id)
    {
        // Delete bengkel
    }
}
