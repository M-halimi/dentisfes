<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClinicSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Return the clinic configuration with values saved from the admin panel.
     *
     * The fallback keeps the site usable before the first migration is run.
     */
    public static function apply(array $clinic): array
    {
        try {
            $values = static::query()->pluck('value', 'key')->all();
        } catch (\Throwable) {
            return $clinic;
        }

        foreach ([
            'name',
            'profession',
            'city',
            'address',
            'phone',
            'email',
            'instagram',
            'whatsapp_number',
            'hours',
        ] as $key) {
            if (array_key_exists($key, $values) && $values[$key] !== null) {
                $clinic[$key] = $values[$key];
            }
        }

        return $clinic;
    }

    public static function value(string $key, mixed $default = null): mixed
    {
        try {
            return static::query()->where('key', $key)->value('value') ?? $default;
        } catch (\Throwable) {
            return $default;
        }
    }
}
