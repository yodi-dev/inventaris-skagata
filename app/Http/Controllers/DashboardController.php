<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $total_barang = Item::sum('quantity');
        $total_kategori = Category::count();
        $total_ruangan = Room::count();

        $recent_items = Item::with(['category', 'room'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'total_barang',
            'total_kategori',
            'total_ruangan',
            'recent_items'
        ));
    }
}
