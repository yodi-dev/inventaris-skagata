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
        'jenis_barang',
        'minimum_stok',
    ];

    protected function casts(): array
    {
        return [
            'minimum_stok' => 'integer',
            'harga_satuan' => 'decimal:2',
        ];
    }

    public function pengadaan()
    {
        return $this->belongsTo(Pengadaan::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    /**
     * Mengambil jenis barang efektif (dari master barang jika linked, atau nilai tersimpan di usulan).
     */
    public function getEffectiveJenisBarangAttribute(): ?string
    {
        if ($this->barang_id && $this->barang) {
            return $this->barang->jenis_barang;
        }

        return $this->jenis_barang;
    }

    /**
     * Mengambil batas minimum stok efektif (dari master barang jika linked, atau nilai tersimpan di usulan).
     */
    public function getEffectiveMinimumStokAttribute(): ?int
    {
        if ($this->barang_id && $this->barang) {
            return (int) $this->barang->minimum_stok;
        }

        return $this->minimum_stok !== null ? (int) $this->minimum_stok : null;
    }
}
