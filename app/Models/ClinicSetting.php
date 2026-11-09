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
            'practitioner_name',
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

    /**
     * @param  array{whatsapp_number: string|null, phone: string}  $clinic
     */
    public static function whatsappUrl(array $clinic): string
    {
        $number = preg_replace('/[^0-9]/', '', (string) $clinic['whatsapp_number']);

        if ($number === '') {
            $number = preg_replace('/[^0-9]/', '', $clinic['phone']);
        }

        if (str_starts_with($number, '00')) {
            $number = substr($number, 2);
        }

        if (preg_match('/^0[0-9]{9}$/', $number)) {
            $number = '212'.substr($number, 1);
        }

        return 'https://wa.me/'.$number.'?text='.rawurlencode(__('site.whatsapp_message'));
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
