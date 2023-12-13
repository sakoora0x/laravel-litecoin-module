<?php

namespace Mollsoft\LaravelLitecoinModule\Enums;

enum AddressType: string
{
    case LEGACY = 'legacy';
    case P2SH_SEGWIT = 'p2sh-segwit';
    case BECH32 = 'bech32';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
