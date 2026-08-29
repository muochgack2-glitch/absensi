<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'verification_code', 'password', 'role', 'kelas_id'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function kelas()
    {
        return $this->belongsTo(AttendanceClass::class, 'kelas_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isWaliKelas(): bool
    {
        return $this->role === 'wali_kelas';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'petugas';
    }

    public function isKepalaSekolah(): bool
    {
        return $this->role === 'kepala_sekolah';
    }

    public function isWakaKesiswaan(): bool
    {
        return $this->role === 'waka_kesiswaan';
    }

    /** Petugas + Waka = operasional staff */
    public function isOperasional(): bool
    {
        return in_array($this->role, ['petugas', 'waka_kesiswaan']);
    }

    /** Semua role yang bisa login ke panel admin */
    public function isStaff(): bool
    {
        return in_array($this->role, ['admin', 'petugas', 'kepala_sekolah', 'waka_kesiswaan']);
    }
}
