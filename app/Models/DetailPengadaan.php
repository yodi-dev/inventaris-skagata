<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPengadaan extends Model
{
    use HasFactory;

    protected $table = 'detail_pengadaans';

    protected $fillable = [
        'pengadaan_id',
        'barang_id',
        'nama_barang',
        'spesifikasi',
        'jumlah',
        'satuan',
        'harga_satuan',
    ];

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
