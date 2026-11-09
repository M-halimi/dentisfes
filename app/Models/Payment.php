<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['amount_cents', 'paid_on', 'method', 'reference', 'receipt_number', 'submission_key', 'recorded_by', 'receipt_snapshot', 'cancelled_at'];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer', 'paid_on' => 'date', 'receipt_snapshot' => 'array', 'cancelled_at' => 'datetime'];
    }

    public function patientService(): BelongsTo
    {
        return $this->belongsTo(PatientService::class);
    }

    public function audits(): HasMany
    {
        return $this->hasMany(PaymentAudit::class);
    }
}
