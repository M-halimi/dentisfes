<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    protected $fillable = ['full_name', 'phone', 'email'];

    protected static function booted(): void
    {
        static::creating(function (Patient $patient): void {
            $patient->reference = 'PAT-'.Str::ulid();
        });
        static::saving(function (Patient $patient): void {
            $patient->identity_key = self::identityKey($patient->full_name, $patient->phone);
        });
    }

    public static function identityKey(string $name, string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);
        $digits = preg_replace('/^(?:00212|212)/', '0', $digits);
        $name = mb_strtolower(preg_replace('/\s+/u', ' ', trim($name)));

        return hash('sha256', $name.'|'.$digits);
    }

    public static function fromContact(array $contact): self
    {
        return static::query()->firstOrCreate(
            ['identity_key' => self::identityKey($contact['full_name'], $contact['phone'])],
            Arr::only($contact, ['full_name', 'phone', 'email'])
        );
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(PatientService::class);
    }

    public function payments(): HasManyThrough
    {
        return $this->hasManyThrough(Payment::class, PatientService::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(PatientNote::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(PatientDocument::class);
    }
}
