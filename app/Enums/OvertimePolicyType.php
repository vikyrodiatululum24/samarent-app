<?php

namespace App\Enums;

enum OvertimePolicyType: string
{
    case Flat = 'flat';
    case Government = 'government';
    case Custom = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::Flat => 'Flat',
            self::Government => 'Government',
            self::Custom => 'Custom',
        };
    }
}
