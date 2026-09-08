<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $fillable = [
        'bengkel_id',
        'kode_barang',
        'nama',
        'jenis_barang',
        'satuan',
        'stok_total',
        'stok_tersedia',
        'stok_dipinjam',
        'stok_rusak',
        'minimum_stok',
        'deskripsi',
    ];

    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class);
    }

    public function detailPeminjamans()
    {
        return $this->hasMany(DetailPeminjaman::class);
    }

    public function detailPengadaans()
    {
        return $this->hasMany(DetailPengadaan::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
