<?php

namespace App\Enums;

enum AcceptableChangeStatusEnum : string
{
    case PROCESSING = 'processing';
    case TO_IMPROVE = 'toImprove';
    case ACCEPTED = 'accepted';
    case REJECTED = 'rejected';
    case DELETED = 'deleted';

    /**
     * @return array
     */

    public static function values() : array
    {
        return array_column(self::cases(), 'value');
    }
}
