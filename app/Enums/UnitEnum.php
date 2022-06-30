<?php

namespace App\Enums;

enum UnitEnum : string
{
    case LITERS = 'l';
    case PIECE = 'piece';
    case GRAM = 'g';

    public static function values() : array
    {
        return array_column(self::cases(), 'value');
    }
}
