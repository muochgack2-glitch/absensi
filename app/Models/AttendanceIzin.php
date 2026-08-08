<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceIzin extends Model
{
    protected $table = 'attendance_izin';

    /**
     * Auto-set tahun_ajaran saat membuat izin baru.
     */
    protected static function booted(): void
    {
        static::creating(function ($izin) {
            if (empty($izin->tahun_ajaran)) {
                $izin->tahun_ajaran = AttendanceSetting::get('active_tahun_ajaran', '2026/2027');
            }
        });
    }

    protected $fillable = [
        'student_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis',
        'alasan',
        'nama_pelapor',
        'no_hp_pelapor',
        'lampiran',
        'status',
        'catatan_admin',
        'approved_by',
        'approved_at',
        'tahun_ajaran',
    ];

    protected $casts = [
        'tanggal_mulai'  => 'date',
        'tanggal_selesai' => 'date',
        'approved_at'    => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(AttendanceStudent::class, 'student_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'approved_by');
    }

    public function getDurasiAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak'   => 'Ditolak',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending'   => 'yellow',
            'disetujui' => 'green',
            'ditolak'   => 'red',
            default     => 'gray',
        };
    }
}
