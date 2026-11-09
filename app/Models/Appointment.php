<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'full_name',
        'phone',
        'email',
        'service',
        'preferred_date',
        'preferred_time',
        'message',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Appointment $appointment): void {
            if ($appointment->patient_id === null) {
                $appointment->patient_id = Patient::fromContact($appointment->only(['full_name', 'phone', 'email']))->id;
            }
        });
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
        ];
    }
}
