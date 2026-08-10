<?php

namespace App\Enums;

enum StockMovementType: string
{
    case In = 'in';
    case Out = 'out';
    case Transfer = 'transfer';
    case Adjustment = 'adjustment';

    public function label(): string
    {
        return match ($this) {
            self::In => 'Stock In',
            self::Out => 'Stock Out',
            self::Transfer => 'Transfer',
            self::Adjustment => 'Adjustment',
        };
    }
}
