<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhonePendingUpdate extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'student_id',
        'no_hp_ortu',
        'no_hp_ortu2',
        'submitted_at',
        'is_applied',
        'applied_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'applied_at'   => 'datetime',
        'is_applied'   => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(AttendanceStudent::class, 'student_id');
    }
}
