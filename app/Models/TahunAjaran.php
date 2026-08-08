<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TahunAjaran extends Model
{
    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'tahun',
        'status',
        'started_at',
        'closed_at',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'date',
        'closed_at'  => 'date',
    ];

    // ================================================================
    // Scopes
    // ================================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    // ================================================================
    // Static Helpers
    // ================================================================

    /**
     * Ambil tahun ajaran aktif saat ini.
     * Cached untuk performa.
     */
    public static function getActive(): ?string
    {
        return AttendanceSetting::get('active_tahun_ajaran', '2026/2027');
    }

    /**
     * Ambil model tahun ajaran aktif.
     */
    public static function getActiveModel(): ?self
    {
        $tahun = self::getActive();
        return self::where('tahun', $tahun)->first();
    }

    // ================================================================
    // Relationships
    // ================================================================

    public function students(): HasMany
    {
        return $this->hasMany(AttendanceStudent::class, 'tahun_ajaran', 'tahun');
    }

    public function records(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'tahun_ajaran', 'tahun');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ================================================================
    // Helpers
    // ================================================================

    /**
     * Ambil tahun kedua dari format "2026/2027" → "2027"
     */
    public function getEndYear(): int
    {
        return (int) explode('/', $this->tahun)[1];
    }

    /**
     * Ambil tahun pertama dari format "2026/2027" → "2026"
     */
    public function getStartYear(): int
    {
        return (int) explode('/', $this->tahun)[0];
    }

    /**
     * Suggest tahun ajaran berikutnya.
     * "2026/2027" → "2027/2028"
     */
    public function suggestNextYear(): string
    {
        $end = $this->getEndYear();
        return $end . '/' . ($end + 1);
    }

    /**
     * Check apakah tahun ini aktif.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
