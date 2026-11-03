<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TrustedDevice extends Model
{
    protected $fillable = [
        'device_token',
        'user_id',
        'device_name',
        'browser_info',
        'ip_address',
        'is_active',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke user yang mengotorisasi device ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Generate token unik 64 karakter.
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Cari device aktif berdasarkan token.
     */
    public static function findByToken(string $token): ?self
    {
        return static::where('device_token', $token)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Update waktu terakhir digunakan.
     */
    public function touchLastUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Revoke (nonaktifkan) device ini.
     */
    public function revoke(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Ringkasan browser dari User-Agent.
     */
    public static function parseBrowserInfo(string $userAgent): string
    {
        if (preg_match('/Chrome\/[\d.]+/', $userAgent, $m)) return 'Chrome';
        if (preg_match('/Firefox\/[\d.]+/', $userAgent, $m)) return 'Firefox';
        if (preg_match('/Safari\/[\d.]+/', $userAgent) && !str_contains($userAgent, 'Chrome')) return 'Safari';
        if (preg_match('/Edge\/[\d.]+/', $userAgent, $m)) return 'Edge';
        return Str::limit($userAgent, 50);
    }
}

