<?php

namespace App\Enums;

class WarehouseType
{
    const MAIN = 1;
    const BRANCH = 2;
    const VIRTUAL = 3;
    const THIRD_PARTY = 4;

    public static function getWarehouseTypeName($warehouseType)
    {
        switch ($warehouseType) {
            case self::MAIN:
                return 'Main';
            case self::BRANCH:
                return 'Branch';
            case self::VIRTUAL:
                return 'Virtual';
            case self::THIRD_PARTY:
                return 'Third Party';
            default:
                return null;
        }
    }
}