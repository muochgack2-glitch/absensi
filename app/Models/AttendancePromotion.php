<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendancePromotion extends Model
{
    protected $table = 'attendance_promotions';

    protected $fillable = [
        'from_tahun_ajaran',
        'to_tahun_ajaran',
        'processed_by',
        'total_promoted',
        'total_graduated',
        'promotion_summary',
        'student_details',
        'notes',
        'is_rolled_back',
        'rolled_back_at',
        'rolled_back_by',
        'processed_at',
    ];

    protected $casts = [
        'promotion_summary' => 'array',
        'student_details'   => 'array',
        'is_rolled_back'    => 'boolean',
        'processed_at'      => 'datetime',
        'rolled_back_at'    => 'datetime',
    ];

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function rolledBackBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rolled_back_by');
    }

    /**
     * Check apakah promosi ini bisa di-rollback.
     * Hanya promosi terakhir yang belum di-rollback yang bisa.
     */
    public function canRollback(): bool
    {
        if ($this->is_rolled_back) {
            return false;
        }

        if (empty($this->student_details)) {
            return false;
        }

        $latestActive = self::where('is_rolled_back', false)
            ->orderByDesc('processed_at')
            ->first();

        return $this->id === $latestActive?->id;
    }
}
