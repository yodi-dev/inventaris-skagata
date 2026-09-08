<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bengkel extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'kode',
        'nama',
        'deskripsi',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function pengadaans()
    {
        return $this->hasMany(Pengadaan::class);
    }
}
