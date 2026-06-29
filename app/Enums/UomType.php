<?php

namespace App\Enums;

class UomType
{
    const WEIGHT = 1;

    const LENGTH = 2;

    const VOLUME = 3;

    const AREA = 4;

    const TIME = 5;

    const TEMPERATURE = 6;

    const SPEED = 7;

    const PRESSURE = 8;

    const ENERGY = 9;

    const POWER = 10;

    const FREQUENCY = 11;

    const DATA_STORAGE = 12;

    const CURRENCY = 13;

    public static function all(): array
    {
        return [
            self::WEIGHT => 'Weight',
            self::LENGTH => 'Length',
            self::VOLUME => 'Volume',
            self::AREA => 'Area',
            self::TIME => 'Time',
            self::TEMPERATURE => 'Temperature',
            self::SPEED => 'Speed',
            self::PRESSURE => 'Pressure',
            self::ENERGY => 'Energy',
            self::POWER => 'Power',
            self::FREQUENCY => 'Frequency',
            self::DATA_STORAGE => 'Data Storage',
            self::CURRENCY => 'Currency',
        ];
    }

    public static function getUomTypeName($uomType)
    {
        switch ($uomType) {
            case self::WEIGHT:
                return 'Weight';
            case self::LENGTH:
                return 'Length';
            case self::VOLUME:
                return 'Volume';
            case self::AREA:
                return 'Area';
            case self::TIME:
                return 'Time';
            case self::TEMPERATURE:
                return 'Temperature';
            case self::SPEED:
                return 'Speed';
            case self::PRESSURE:
                return 'Pressure';
            case self::ENERGY:
                return 'Energy';
            case self::POWER:
                return 'Power';
            case self::FREQUENCY:
                return 'Frequency';
            case self::DATA_STORAGE:
                return 'Data Storage';
            case self::CURRENCY:
                return 'Currency';
            default:
                return null;
        }
    }
}
