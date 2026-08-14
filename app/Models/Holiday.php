<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'type',
        'source',
        'ekaldik_activity_id',
        'description',
        'is_active',
        'synced_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'synced_at' => 'datetime',
    ];

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDate($query, string $date)
    {
        return $query->active()
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
    }

    public function scopeFromEkaldik($query)
    {
        return $query->where('source', 'ekaldik');
    }

    public function scopeManual($query)
    {
        return $query->where('source', 'manual');
    }

    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->active()
            ->where('start_date', '>=', Carbon::today())
            ->where('start_date', '<=', Carbon::today()->addDays($days))
            ->orderBy('start_date');
    }

    // ==========================================
    // Static Helpers
    // ==========================================

    /**
     * Check if a specific date is a holiday.
     */
    public static function isHoliday(?string $date = null): bool
    {
        $date = $date ?? Carbon::today()->toDateString();
        return static::forDate($date)->exists();
    }

    /**
     * Get holiday info for a specific date.
     */
    public static function getForDate(?string $date = null): ?self
    {
        $date = $date ?? Carbon::today()->toDateString();
        return static::forDate($date)->first();
    }

    /**
     * Get all holidays in a date range (for rekap).
     */
    public static function getInRange(string $startDate, string $endDate)
    {
        return static::active()
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->orderBy('start_date')
            ->get();
    }

    /**
     * Count holiday days in a date range (for rekap percentage).
     */
    public static function countHolidayDays(string $startDate, string $endDate): int
    {
        $holidays = static::getInRange($startDate, $endDate);
        $count = 0;
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        foreach ($holidays as $holiday) {
            $hStart = $holiday->start_date->max($start);
            $hEnd = $holiday->end_date->min($end);
            $count += $hStart->diffInDays($hEnd) + 1;
        }

        return $count;
    }

    // ==========================================
    // Helpers
    // ==========================================

    public function getDurationAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }

    public function isOngoing(): bool
    {
        $today = Carbon::today();
        return $this->start_date <= $today && $this->end_date >= $today;
    }
}
