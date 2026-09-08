<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengadaan extends Model
{
    use HasFactory;

    protected $table = 'pengadaans';

    protected $fillable = [
        'bengkel_id',
        'dibuat_oleh',
        'judul',
        'status',
        'catatan',
        'catatan_review',
        'diajukan_pada',
        'direview_oleh',
        'direview_pada',
    ];

    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class);
    }

    public function dibuatOleh()
    {
        return $this->belongsTo(User::class, 'dibuat_oleh');
    }

    public function direviewOleh()
    {
        return $this->belongsTo(User::class, 'direview_oleh');
    }

    public function detailPengadaans()
    {
        return $this->hasMany(DetailPengadaan::class);
    }
}
