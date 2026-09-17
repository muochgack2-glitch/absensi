<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\AttendanceStudent;

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
        'gateway',
        'message_id',
        'ack_status',
    ];

    protected $casts = [
        'sent_at'    => 'datetime',
        'metadata'   => 'array',
        'ack_status' => 'integer',
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
     * Relasi ke User berdasarkan nomor HP penerima (untuk log tanpa student_id)
     */
    public function recipientUser(): HasOne
    {
        return $this->hasOne(User::class, 'phone', 'phone_normalized');
    }

    /**
     * Relasi ke siswa berdasarkan no HP orang tua (no_hp_ortu/no_hp_ortu2)
     * Digunakan untuk log tanpa student_id (misal broadcast ke orang tua)
     */
    public function studentByOrangTua(): HasOne
    {
        return $this->hasOne(AttendanceStudent::class, 'no_hp_ortu', 'phone')
            ->withoutGlobalScope('tahun_ajaran');
    }

    public function studentByOrangTua2(): HasOne
    {
        return $this->hasOne(AttendanceStudent::class, 'no_hp_ortu2', 'phone')
            ->withoutGlobalScope('tahun_ajaran');
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
     * Mark message as sent.
     * Ekstrak message_id dari response gateway jika ada.
     */
    public function markAsSent($metadata = null)
    {
        $updates = [
            'status'     => 'sent',
            'sent_at'    => now(),
            'metadata'   => $metadata,
            'ack_status' => 1, // Server ACK — pesan diterima server WA
        ];

        // Ekstrak messageId dari response gateway
        if (is_array($metadata)) {
            $messageId = $metadata['messageId']
                ?? $metadata['message_id']
                ?? $metadata['data']['messageId']
                ?? null;
            if ($messageId) {
                $updates['message_id'] = $messageId;
            }
        }

        $this->update($updates);
    }

    /**
     * Update ACK status dari webhook gateway.
     * Hanya naik, tidak pernah turun (misal: 3 -> 2 diabaikan).
     */
    public function updateAck(int $ack): void
    {
        if ($ack > ($this->ack_status ?? 0)) {
            $this->update(['ack_status' => $ack]);
        }
    }

    /**
     * Label ACK untuk UI
     */
    public function getAckLabelAttribute(): string
    {
        return match($this->ack_status) {
            -1      => 'Error',
             1      => 'Terkirim',
             2      => 'Diterima',
             3      => 'Dibaca',
            default => '-',
        };
    }

    /**
     * Icon centang ACK (HTML, aman untuk {!! !!})
     */
    public function getAckIconAttribute(): string
    {
        return match($this->ack_status) {
            -1 => '<span title="Error" class="text-danger">&#x2717;</span>',
             1 => '<span title="Terkirim ke server WA" class="ack-sent">&#x2713;</span>',
             2 => '<span title="Diterima di HP" class="ack-delivered">&#x2713;&#x2713;</span>',
             3 => '<span title="Sudah dibaca" class="ack-read">&#x2713;&#x2713;</span>',
            default => '<span title="Menunggu konfirmasi" class="text-muted">&#x23F1;</span>',
        };
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
            'manual'           => 'Manual',
            'check_in'         => 'Check-In',
            'check_out'        => 'Check-Out',
            'absent'           => 'Alpha',
            'broadcast'        => 'Broadcast',
            'late_warning'     => 'Peringatan Terlambat',
            'bk_notify'        => 'Notif BK',
            'diagnostic_test'  => 'Test Diagnostik',
            default            => ucfirst(str_replace('_', ' ', $this->type)),
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
