<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SumberDana extends Model
{
    use HasFactory;

    protected $table = 'sumber_danas';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
    ];

    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }
}

