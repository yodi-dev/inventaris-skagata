<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Room;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['category', 'room'])->latest()->get();
        return view('items.index', compact('items'));
    }

    public function create()
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();

        return view('items.create', compact('categories', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string|unique:items,item_code',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'room_id' => 'required|exists:rooms,id',
            'quantity' => 'required|integer|min:1',
            'condition' => 'required|in:good,slightly_damaged,heavily_damaged',
        ]);

        Item::create($request->all());

        return redirect()->route('items.index')->with('success', 'Barang berhasil ditambahkan!');
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name', 'asc')->get();
        $rooms = Room::orderBy('name', 'asc')->get();

        return view('items.edit', compact('item', 'categories', 'rooms'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'item_code' => 'required|string|unique:items,item_code,' . $item->id,
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'room_id' => 'required|exists:rooms,id',
            'quantity' => 'required|integer|min:0',
            'condition' => 'required|in:good,slightly_damaged,heavily_damaged',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Data barang berhasil diupdate!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.index')->with('success', 'Barang berhasil dihapus!');
    }
}
