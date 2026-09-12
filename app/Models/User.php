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

    public function isWaka(): bool
    {
        return $this->role === 'waka';
    }

    public function isToolman(): bool
    {
        return $this->role === 'toolman';
    }

    public function isPeminjam(): bool
    {
        return $this->role === 'peminjam';
    }

    public function isSiswa(): bool
    {
        return $this->isPeminjam() && $this->jenis_peminjam === 'siswa';
    }

    public function isGuru(): bool
    {
        return $this->isPeminjam() && $this->jenis_peminjam === 'guru';
    }

    public function isActive(): bool
    {
        return $this->status === 'aktif';
    }

    public function isPending(): bool
    {
        return $this->status === 'menunggu_acc';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspend';
    }
}
