<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiPenyimpanan extends Model
{
    use HasFactory;

    protected $fillable = [
        'bengkel_id',
        'kode',
        'nama',
        'deskripsi',
    ];

    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class);
    }

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}
