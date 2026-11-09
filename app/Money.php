<?php

namespace App;

use Illuminate\Validation\ValidationException;

final class Money
{
    public static function cents(string $amount): int
    {
        if (! preg_match('/^\d{1,8}(?:[.,]\d{1,2})?$/D', $amount)) {
            throw ValidationException::withMessages(['amount' => __('patients.invalid_amount')]);
        }
        $parts = explode('.', str_replace(',', '.', $amount));

        return ((int) $parts[0] * 100) + (int) str_pad($parts[1] ?? '', 2, '0');
    }

    public static function decimal(int $cents): string
    {
        return intdiv($cents, 100).'.'.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT);
    }

    public static function format(int $cents): string
    {
        $whole = preg_replace('/\B(?=(\d{3})+(?!\d))/', ' ', (string) intdiv($cents, 100));
        $separator = app()->getLocale() === 'en' ? '.' : ',';

        return $whole.$separator.str_pad((string) ($cents % 100), 2, '0', STR_PAD_LEFT).' MAD';
    }
}
