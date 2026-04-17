<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::latest()->get();
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        return view('rooms.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'person_in_charge' => 'nullable|string'
        ]);

        Room::create($request->all());

        return redirect()->route('rooms.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit(Room $room)
    {
        return view('rooms.edit', compact('room'));
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'person_in_charge' => 'nullable|string'
        ]);

        $room->update($request->all());

        return redirect()->route('rooms.index')->with('success', 'Kategori berhasil diupdate!');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Kategori berhasil dihapus!');
    }
}
