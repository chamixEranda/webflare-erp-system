<?php

namespace App\Enums;

class TaxType
{
    const INCLUSIVE = 1;
    const EXCLUSIVE = 2;

    public static function getTaxTypeName($taxType)
    {
        switch ($taxType) {
            case self::INCLUSIVE:
                return 'Inclusive';
            case self::EXCLUSIVE:
                return 'Exclusive';
            default:
                return null;
        }
    }
}