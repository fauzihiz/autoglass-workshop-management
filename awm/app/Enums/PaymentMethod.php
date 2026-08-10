<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Transfer = 'transfer';
    case QRIS = 'qris';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Cash',
            self::Transfer => 'Transfer',
            self::QRIS => 'QRIS',
            self::Other => 'Other',
        };
    }
}
