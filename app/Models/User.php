<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'bengkel_id',
        'name',
        'email',
        'password',
        'role',
        'jenis_peminjam',
        'nomor_identitas',
        'nomor_wa',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function bengkel()
    {
        return $this->belongsTo(Bengkel::class);
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function pengadaans()
    {
        return $this->hasMany(Pengadaan::class, 'dibuat_oleh');
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
