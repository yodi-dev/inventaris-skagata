<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    use HasFactory;

    protected $table = 'satuans';

    protected $fillable = [
        'nama',
        'singkatan',
        'deskripsi',
    ];

    /**
     * Relasi ke Barang berdasarkan nama atau singkatan satuan.
     */
    public function barangs()
    {
        return $this->hasMany(Barang::class, 'satuan', 'nama');
    }

    /**
     * Hitung total barang yang memakai satuan ini (baik via nama atau singkatan).
     */
    public function getBarangTerhubungCountAttribute(): int
    {
        return Barang::where(function ($q) {
            $q->where('satuan', $this->nama);
            if (!empty($this->singkatan)) {
                $q->orWhere('satuan', $this->singkatan);
            }
        })->count();
    }
}
