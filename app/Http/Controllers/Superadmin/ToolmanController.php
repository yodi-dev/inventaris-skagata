<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ToolmanController extends Controller
{
    public function index()
    {
        return view('superadmin.toolman.index');
    }

    public function create()
    {
        return view('superadmin.toolman.create');
    }

    public function store(Request $request)
    {
        // Store toolman
    }

    public function edit($id)
    {
        return view('superadmin.toolman.edit', compact('id'));
    }

    public function update(Request $request, $id)
    {
        // Update toolman
    }

    public function destroy($id)
    {
        // Delete toolman
    }
}
