<?php

namespace App\Enums;

enum CurrencyEnum : string
{
    case PLN = 'zł';
    case EUR = 'euro';
    case USD = 'usd';

    public static function values() : array
    {
        return array_column(self::cases(), 'value');
    }
}
