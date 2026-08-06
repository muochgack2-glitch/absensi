<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'phone',
        'phone_normalized',
        'message',
        'status',
        'type',
        'student_id',
        'template_id',
        'sent_by',
        'error_message',
        'sent_at',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Relasi ke AttendanceStudent
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(AttendanceStudent::class, 'student_id');
    }

    /**
     * Relasi ke WhatsApp Template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(WhatsAppTemplate::class, 'template_id');
    }

    /**
     * Relasi ke User (yang mengirim)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope untuk pesan yang berhasil terkirim
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    /**
     * Scope untuk pesan yang gagal
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope untuk pesan pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk pesan hari ini
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Mark message as sent
     */
    public function markAsSent($metadata = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now(),
            'metadata' => $metadata,
        ]);
    }

    /**
     * Mark message as failed
     */
    public function markAsFailed($errorMessage, $metadata = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'sent' => 'success',
            'failed' => 'danger',
            'pending' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'sent' => 'Terkirim',
            'failed' => 'Gagal',
            'pending' => 'Pending',
            default => 'Unknown',
        };
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return match($this->type) {
            'manual' => 'Manual',
            'check_in' => 'Check-In',
            'check_out' => 'Check-Out',
            'absent' => 'Alpha',
            'broadcast' => 'Broadcast',
            'diagnostic_test' => 'Test Diagnostik',
            default => ucfirst($this->type),
        };
    }

    /**
     * Normalize phone number to standard format (62xxx)
     * 
     * @param string $phone
     * @return string|null
     */
    public static function normalizePhone($phone)
    {
        if (empty($phone)) {
            return null;
        }

        // Remove +, -, spaces
        $phone = str_replace(['+', '-', ' '], '', trim($phone));

        // Convert 08xxx to 628xxx
        if (substr($phone, 0, 2) === '08') {
            $phone = '62' . substr($phone, 1);
        }

        // Ensure starts with 62
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Boot method to auto-fill phone_normalized
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (!empty($log->phone) && empty($log->phone_normalized)) {
                $log->phone_normalized = self::normalizePhone($log->phone);
            }
        });

        static::updating(function ($log) {
            if ($log->isDirty('phone')) {
                $log->phone_normalized = self::normalizePhone($log->phone);
            }
        });
    }
}
